<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/legal/legal_export_helpers.php';
$legal = include $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/legal/config.php';

$mainText = 'Этот сайт использует cookie-файлы для настройки рекламы и сбора статистики. Оставаясь на сайте, вы соглашаетесь на обработку ваших персональных данных в соответствии с нашей '
    . legal_doc_link_nofollow($legal, 'cookie', 'политикой cookie')
    . '.';

$arResult['MAINTEXT'] = CNigesCookiesAcceptHelper::sanitizeHtml($mainText);
$arResult['MAINTEXT'] = str_replace(
    'rel="noopener noreferrer"',
    'rel="nofollow noopener noreferrer"',
    $arResult['MAINTEXT']
);

$arResult['TEXTVER'] = '3';
$arResult['COOKIE_NAME'] = CNigesCookiesAcceptHelper::getCookieName(3);
