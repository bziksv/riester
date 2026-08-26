<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("dev");
?>

<? $APPLICATION->IncludeComponent("bitrix:main.include", "", ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/subscribe_desc.php"], false ); ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>