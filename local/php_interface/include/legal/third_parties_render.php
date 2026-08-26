<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

require_once __DIR__ . '/legal_export_helpers.php';

function riesterLegalThirdPartiesData(): array
{
    static $data = null;
    if ($data === null) {
        $data = include __DIR__ . '/third_parties_data.php';
    }

    return $data;
}

function riesterLegalRenderThirdPartyServiceName(array $service): string
{
    $name = legal_var($service['name']);
    if (!empty($service['inn'])) {
        $name .= ' (ИНН ' . legal_var($service['inn']) . ')';
    }

    return $name;
}

function riesterLegalRenderThirdPartyBlockLine(array $block): string
{
    $links = [];
    foreach ($block['urls'] as $url) {
        $links[] = '<a href="' . legal_h($url) . '" target="_blank" rel="noopener">'
            . legal_var($url) . '</a>';
    }

    return implode(', ', $links) . ' — ' . legal_var($block['text']);
}

/**
 * Единый список сторонних сервисов (URL + описание) для всех legal-документов.
 */
function riesterLegalRenderThirdPartyListItems(): string
{
    $html = '';
    foreach (riesterLegalThirdPartiesData()['services'] as $service) {
        if (empty($service['recommendation'])) {
            continue;
        }

        foreach ($service['recommendation'] as $block) {
            $line = riesterLegalRenderThirdPartyServiceName($service) . ' — '
                . riesterLegalRenderThirdPartyBlockLine($block);
            $html .= '<li' . legal_li_attr() . '>' . $line . ';</li>' . "\n        ";
        }
    }

    return $html;
}

function riesterLegalRenderThirdPartyGenericPolicyListItems(): string
{
    $html = '';
    foreach (riesterLegalThirdPartiesData()['generic_policy'] as $item) {
        $html .= '<li' . legal_li_attr() . '>' . legal_var($item) . ';</li>' . "\n        ";
    }

    return $html;
}

function riesterLegalRenderThirdPartyGenericConsentListItems(): string
{
    $html = '';
    foreach (riesterLegalThirdPartiesData()['generic_consent'] as $item) {
        $html .= '<li' . legal_li_attr() . '>' . legal_var($item) . ';</li>' . "\n        ";
    }

    return $html;
}

function riesterLegalRenderThirdPartyPolicyListItems(): string
{
    return riesterLegalRenderThirdPartyListItems()
        . riesterLegalRenderThirdPartyGenericPolicyListItems();
}
