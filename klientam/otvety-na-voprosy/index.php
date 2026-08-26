<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Ответы на вопросы - интернет магазин медицинского оборудования Riester с доставкой по РФ");
$APPLICATION->SetPageProperty("keywords", "Ответы на вопросы, оборудование Riester");
$APPLICATION->SetPageProperty("description", "Ответы на вопросы - интернет магазин медицинского оборудования Riester, выгодные цены +7(495)133-16-94 Звоните! Доставка по РФ");
$APPLICATION->SetTitle("Ответы на вопросы");
?><div class="wrapper">
	<div class="sidebar">
		 <?$APPLICATION->IncludeComponent(
	"bitrix:news.line",
	"banners",
	Array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "300",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => "banners",
		"DETAIL_URL" => "",
		"FIELD_CODE" => array(0=>"CODE",1=>"PREVIEW_PICTURE",2=>"",),
		"IBLOCKS" => array(0=>"6",),
		"IBLOCK_TYPE" => "sliders",
		"NEWS_COUNT" => "3",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC"
	)
);?>
	</div>
	<div class="main text">
		<div class="content">
			 <?
                // включаемая область для раздела
                $APPLICATION->IncludeFile($APPLICATION->GetCurDir()."content.php", Array(), Array(
                    "MODE"      => "html",                                           // будет редактировать в веб-редакторе
                    "NAME"      => "Редактирование включаемой области раздела",      // текст всплывающей подсказки на иконке
                    "TEMPLATE"  => ""                    // имя шаблона для нового файла
                ));
                ?>
		</div>
	</div>
</div><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>