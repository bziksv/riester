<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

global $APPLICATION;

$APPLICATION->IncludeComponent("nbrains:popup.product",
        "",
        Array(
            "IBLOCK_ID" => 33,
            "ID" => $_GET['ID'],
        ),
	false
);