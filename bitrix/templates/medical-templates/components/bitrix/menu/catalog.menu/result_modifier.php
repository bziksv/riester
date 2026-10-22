<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

$page = $APPLICATION->GetCurPage();
$code = explode('/', (string)$page);
$sectionCode = $code[2] ?? '';
if ($sectionCode !== '')
{
	$dbRes = CIBlockSection::GetList(array(), ["IBLOCK_ID" => IBLOCK_CATALOG, "ACTIVE" => "Y", "CODE" => $sectionCode], false, array("ID", "UF_*"));
	if ($arCurSection = $dbRes->Fetch())
		$arResult['PROPERTIES'] = $arCurSection;
}

//determine if child selected
$bWasSelected = false;
$arParents = array();
$depth = 1;

foreach($arResult as $i => $arMenu)
{
	if (!is_array($arMenu) || !isset($arMenu['DEPTH_LEVEL']))
		continue;

	$depth = $arMenu['DEPTH_LEVEL'];

	if($arMenu['IS_PARENT'] == true)
	{
		$arParents[$arMenu['DEPTH_LEVEL']-1] = $i;
	}
	elseif(($arMenu['SELECTED'] ?? false) == true)
	{
		$bWasSelected = true;
		break;
	}
}

if($bWasSelected)
{
	for($i=0; $i<$depth-1; $i++)
		$arResult[$arParents[$i]]['CHILD_SELECTED'] = true;
}
?>
