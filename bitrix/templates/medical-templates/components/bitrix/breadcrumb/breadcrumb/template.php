<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';

$strReturn .= '<div class="breadcrumb"><div class="container">'
	. '<ol class="breadcrumb__list" itemscope itemtype="https://schema.org/BreadcrumbList">';

$itemSize = count($arResult);
for($index = 0; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	$link = $arResult[$index]["LINK"];
	$position = $index + 1;

	$titleShow = ($index > 0) ? $title : "";
	$icon = (!$index) ? "icon-home" : "";

	$strReturn .= '
			<li class="breadcrumb__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';

	if($link <> "" && $index != $itemSize-1)
	{
		$strReturn .= '
				<a href="'.$link.'" title="'.$title.'" itemprop="item" class="'.$icon.'">
					<span itemprop="name">'.$titleShow.'</span>
				</a>';
	}
	else
	{
		$strReturn .= '
				<span itemprop="name">'.$titleShow.'</span>';
		if($link <> "")
		{
			$strReturn .= '<meta itemprop="item" content="'.htmlspecialcharsbx($link).'">';
		}
	}

	$strReturn .= '
				<meta itemprop="position" content="'.$position.'">
			</li>';
}

$strReturn .= '</ol></div></div>';

return $strReturn;
