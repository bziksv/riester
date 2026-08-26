<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

return [
    'operator_name' => 'ООО «ВИЛМЕД»',
    'operator_short' => 'ООО «ВИЛМЕД»',
    'operator_legal_form' => 'ООО',
    'inn' => '3662302802',
    'ogrn' => '1223600020599',
    'kpp' => '366201001',
    'site' => 'https://riester.su/',
    'site_host' => 'riester.su',
    'email' => 'info@riester.su',
    'email_director' => 'director@riester.su',
    'phone' => '+7 (495) 133-16-94',
    'phone_tel' => '+74951331694',
    'phone_free' => '8-800-555-55-50',
    'phone_free_tel' => '88005555550',
    'address_legal' => '394024, Россия, Воронежская обл., г. Воронеж, Московский проспект, д. 19, офис 1/19',
    'images' => [
        'consent' => '/upload/legal/legal-consent.png',
        'personal_data' => '/upload/legal/legal-personal-data.png',
        'cookie' => '/upload/legal/legal-cookie.png',
        'recommendation' => '/upload/legal/legal-recommendation.png',
    ],
    'third_parties' => include __DIR__ . '/third_parties_data.php',
];
