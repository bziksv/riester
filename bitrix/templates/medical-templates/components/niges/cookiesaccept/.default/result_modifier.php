<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/legal/legal_export_helpers.php';
$legal = include $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/legal/config.php';

$mainText = 'Мы используем файлы '
    . legal_doc_link_nofollow($legal, 'cookie', 'cookies')
    . ' для улучшения работы сайта, настройки рекламы и анализа посещаемости. Продолжая пользоваться сайтом, вы даёте '
    . legal_doc_link_nofollow($legal, 'consent', 'согласие на обработку персональных данных')
    . ', соглашаетесь с '
    . legal_doc_link_nofollow($legal, 'personal_data', 'политикой обработки персональных данных')
    . ' и ознакомлены с '
    . legal_doc_link_nofollow($legal, 'recommendation', 'правилами применения рекомендательных технологий')
    . '.';

$arResult['MAINTEXT'] = CNigesCookiesAcceptHelper::sanitizeHtml($mainText);
$arResult['MAINTEXT'] = str_replace(
    'rel="noopener noreferrer"',
    'rel="nofollow noopener noreferrer"',
    $arResult['MAINTEXT']
);

$arResult['TEXTVER'] = '3';
$arResult['COOKIE_NAME'] = CNigesCookiesAcceptHelper::getCookieName(3);
