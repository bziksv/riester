#!/usr/bin/env node
/**
 * Generates PNG images of legal documents from print pages (no site header/footer).
 *
 * Usage:
 *   node scripts/generate-legal-images.mjs
 *   BASE_URL=http://127.0.0.1:8103 node scripts/generate-legal-images.mjs
 */

import { chromium } from 'playwright';
import { mkdir, rename, writeFile } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const projectRoot = path.resolve(__dirname, '..');
const baseUrl = (process.env.BASE_URL || 'http://127.0.0.1:8103').replace(/\/$/, '');
const uploadDir = path.join(projectRoot, 'upload', 'legal');

const documents = [
    {
        slug: 'riester-soglasie-obrabotki-pd',
        fileName: 'legal-consent.png',
    },
    {
        slug: 'riester-politika-obrabotki-pd',
        fileName: 'legal-personal-data.png',
    },
    {
        slug: 'riester-politika-cookie',
        fileName: 'legal-cookie.png',
    },
    {
        slug: 'riester-pravila-rekomendatelnyh-tehnologiy',
        fileName: 'legal-recommendation.png',
    },
];

async function ensureServerReady() {
    const response = await fetch(`${baseUrl}/`);
    if (!response.ok) {
        throw new Error(`Local site is unavailable at ${baseUrl} (HTTP ${response.status})`);
    }
}

async function captureDocument(browser, doc) {
    const printUrl = `${baseUrl}/legal/print/${doc.slug}/`;
    const outputPath = path.join(uploadDir, doc.fileName);
    const tempPath = `${outputPath}.tmp.png`;

    const page = await browser.newPage({
        viewport: { width: 900, height: 1200 },
        deviceScaleFactor: 2,
    });

    try {
        await page.goto(printUrl, { waitUntil: 'networkidle', timeout: 120000 });
        await page.waitForSelector('.legal-print', { timeout: 30000 });
        await page.evaluate(async () => {
            if (document.fonts && document.fonts.ready) {
                await document.fonts.ready;
            }
        });

        const body = page.locator('.legal-print');
        await body.screenshot({
            path: tempPath,
            type: 'png',
            animations: 'disabled',
        });

        await rename(tempPath, outputPath);

        return {
            ...doc,
            printUrl,
            outputPath,
            publicPath: `/upload/legal/${doc.fileName}`,
        };
    } finally {
        await page.close();
    }
}

async function writeManifest(results) {
    const manifestPath = path.join(uploadDir, 'legal-images-manifest.json');
    const manifest = {
        generatedAt: new Date().toISOString(),
        baseUrl,
        documents: results.map((item) => ({
            title: item.slug,
            image: item.publicPath,
            imageUrl: `${baseUrl}${item.publicPath}`,
            printUrl: item.printUrl,
            file: path.relative(projectRoot, item.outputPath),
        })),
    };

    await writeFile(manifestPath, `${JSON.stringify(manifest, null, 2)}\n`, 'utf8');

    return manifestPath;
}

async function main() {
    await mkdir(uploadDir, { recursive: true });
    await ensureServerReady();

    const browser = await chromium.launch({ headless: true });

    try {
        const results = [];
        for (const doc of documents) {
            process.stdout.write(`Capturing ${doc.slug}...\n`);
            results.push(await captureDocument(browser, doc));
        }

        const manifestPath = await writeManifest(results);

        process.stdout.write('\nGenerated legal images:\n');
        for (const item of results) {
            process.stdout.write(`- ${item.publicPath}  ←  ${item.printUrl}\n`);
        }
        process.stdout.write(`\nManifest: ${manifestPath}\n`);
    } finally {
        await browser.close();
    }
}

main().catch((error) => {
    console.error(error);
    process.exit(1);
});
