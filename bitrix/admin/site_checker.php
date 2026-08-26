<?
if ($_SERVER['REQUEST_METHOD'] != 'GET') {          
    header("Status: 404 Not Found");
    die();
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/site_checker.php");?>