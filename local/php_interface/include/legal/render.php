<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @param string $legalTitle
 * @param string $contentInclude relative to /local/php_interface/include/legal/
 * @param string|null $legalSubtitle
 */
function riesterRenderLegalPage(string $legalTitle, string $contentInclude, ?string $legalSubtitle = null): void
{
    global $APPLICATION;

    $legal = include $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/legal/config.php';

    $APPLICATION->SetTitle($legalTitle);
    $APPLICATION->SetPageProperty('title', $legalTitle . ' — riester.su');
    $APPLICATION->SetPageProperty('description', $legalTitle . ' интернет-магазина медицинского оборудования Riester.');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/legal.css');

    ?>
<div class="wrapper">
    <div class="main text">
        <div class="content">
            <article class="legal-page">
                <h1 class="legal-page__title"><?= htmlspecialcharsbx($legalTitle) ?></h1>
                <?php if ($legalSubtitle !== null && $legalSubtitle !== ''): ?>
                    <p class="legal-page__subtitle"><?= htmlspecialcharsbx($legalSubtitle) ?></p>
                <?php endif; ?>
                <div class="legal-page__body">
                    <?php include $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/legal/' . $contentInclude; ?>
                </div>
            </article>
        </div>
    </div>
</div>
    <?php
}
