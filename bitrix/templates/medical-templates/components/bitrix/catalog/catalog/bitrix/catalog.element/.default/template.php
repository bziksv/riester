<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

	<h1 class="title"><?=htmlspecialchars_decode($arResult['NAME'])?></h1>
	<div class="cart">

		<div class="cart__gallery">
			<!--<div class="cart__rate">
				<i class="icon-star"></i>
				<i class="icon-star"></i>
				<i class="icon-star"></i>
				<i class="icon-star"></i>
				<i class="icon-star-o"></i>
			</div>-->
			
			<? if($arResult['PROPERTIES']['BADGE']['VALUE']): ?>
                <div class="goods__badge">
                    <div class="badge" style="background-color: <?=$arResult['PROPERTIES']['BADGE']['DESCRIPTION']?>">
                        <span><?=$arResult['PROPERTIES']['BADGE']['VALUE'];?></span>
                    </div>
                </div>
            <? endif; ?>

			<div class="cart__alert">new</div>

			<ul class="cart__slider">
				<? foreach(($arResult['PHOTO'] ?? []) as $photo):?>
					<?
					$arWaterMark = Array(
						array(
							"name" => "watermark",
							"position" => "center",
							"alpha_level" => "100",
							"type" => "image",
							"size" => "big",
							"file" => $_SERVER["DOCUMENT_ROOT"].'/watermark.png',
							"fill" => "exact",
						)
					);
					$arFileTmp = CFile::ResizeImageGet($photo, array("width" => 400, "height" => 400), BX_RESIZE_IMAGE_PROPORTIONAL, true, $arWaterMark);
					$arFileFull = CFile::ResizeImageGet($photo, false, BX_RESIZE_IMAGE_PROPORTIONAL, true, $arWaterMark);
					$arFileThumb = CFile::ResizeImageGet($photo, array("width" => 100, "height" => 100), BX_RESIZE_IMAGE_EXACT, true, false);
					?>
				<li class="cart__slider_item" data-thumb="<?=$arFileThumb['src']?>" data-src="<?=$arFileFull['src']?>">
					<img src="<?=$arFileTmp['src']?>" alt="">
				</li>
				<?endforeach;?>
			</ul>
		</div>

		<div class="cart__content">
			<?
			$articleDescs = $arResult['PROPERTIES']['ARTICLS']['DESCRIPTION'] ?? null;
			if (!is_array($articleDescs)) {
				$articleDescs = [];
			}
			$articleValues = $arResult['PROPERTIES']['ARTICLS']['VALUE'] ?? null;
			if (!is_array($articleValues)) {
				$articleValues = ($articleValues !== null && $articleValues !== '') ? [$articleValues] : [];
			}
			$priceValues = $arResult['PROPERTIES']['PRICES']['VALUE'] ?? null;
			if (!is_array($priceValues)) {
				$priceValues = ($priceValues !== null && $priceValues !== '') ? [$priceValues] : [];
			}
			$oldPrices = $arResult['PRICES']['BASE']['OLD'] ?? [];
			if (!is_array($oldPrices)) {
				$oldPrices = [];
			}
			$priceCurrency = $arResult['PRICES']['BASE']['CURRENCY'] ?? 'RUB';
			?>
			<? if(!empty($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'])): ?>
			<p class="article">Арт: <?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?></p>
			<br>
				<?if(!empty($articleDescs[0])):?>
				<p>Варианты:</p>
				<div class="cart__wrapper">
					<?foreach($articleDescs as $val => $art):?>
					<label class="cart__label">
						<input type="radio" name="color" data-old-price="<?=CurrencyFormat($oldPrices[$val] ?? 0,"RUB");?>" data-price="Цена <?=CurrencyFormat($priceValues[$val] ?? 0,$priceCurrency)?>" class="cart__radio" <?=($val == 0) ? "checked" : "";?> value="<?=$articleValues[$val] ?? ''?>"><i></i>
						<?=$art?>
					</label>
					<?endforeach?>
				</div>
				<?else:?>
					<span>
						<input type="radio" name="color" data-old-price="<?=CurrencyFormat($oldPrices[0] ?? 0,"RUB");?>" data-price="Цена <?=CurrencyFormat($priceValues[0] ?? 0,$priceCurrency)?>" class="cart__radio" checked value="<?=$articleValues[0] ?? ''?>">
					</span>
				<? endif; ?>
			<? endif; ?>

<!--			<div class="cart__availability">В наличии</div>-->
			<? if($arResult['PRICES']['BASE']['DISCOUNT_DIFF']): ?>
				<div class="cart_old_price">
					<?=CurrencyFormat($arResult['PRICES']['BASE']['OLD'][0],"RUB");?>
				</div>
			<? endif; ?>

			<div class="cart__price <?=(!$arResult['PRICES']['BASE']['DISCOUNT_VALUE']) ? "tooltip" : ""?>">
				<?if($arResult['PRICES']['BASE']['DISCOUNT_VALUE']):?>
					Цена <?=$arResult['PRICES']['BASE']['PRINT_DISCOUNT_VALUE']?>
				<?else:?>
					Цена по запросу
				<?endif;?>
			</div>

			<div class="cart__desc">за штуку</div>
			<div class="cart__actions">

				<div class="cart__counter goods__counter">
					<div class="goods__counter_subtract">-</div>
					<input type="text" class="goods__counter_input" value="1" readonly>
					<div class="goods__counter_add">+</div>
				</div>

				<a href="javascript:void(0)" class="cart__basket" onclick="addToBasket2(<?=$arResult['ID']?>, $('.goods__counter_input').val(),this);">Купить</a>
			</div>
		</div>
	</div>

	<div class="tabs">
		<?if($arResult['DETAIL_TEXT']):?>
		<a href="#" class="tabs__nav">Описание</a>
		<div class="tabs__content text">
			<?= riester_sanitize_html_content(
				$arResult['DETAIL_TEXT'],
				!empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
					? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
					: $arResult['NAME']
			) ?>
		</div>
		<?endif?>

		<?
		$attrDescriptions = $arResult['PROPERTIES']['CML2_ATTRIBUTES']['DESCRIPTION'] ?? null;
		if (!is_array($attrDescriptions)) {
			$attrDescriptions = [];
		}
		$attrValues = $arResult['PROPERTIES']['CML2_ATTRIBUTES']['~VALUE'] ?? ($arResult['PROPERTIES']['CML2_ATTRIBUTES']['VALUE'] ?? null);
		if (!is_array($attrValues)) {
			$attrValues = [];
		}
		$complectRaw = $arResult['PROPERTIES']['CML2_COMPLECT']['~VALUE']
			?? ($arResult['PROPERTIES']['CML2_COMPLECT']['VALUE'] ?? null);
		if (is_array($complectRaw)) {
			$complectText = (string)($complectRaw['TEXT'] ?? '');
		} else {
			$complectText = is_string($complectRaw) ? $complectRaw : '';
		}
		?>
		<?if(!empty($attrDescriptions[0])):?>
		<a href="#" class="tabs__nav">Характеристики</a>
		<div class="tabs__content text">
			<table>
				<? foreach($attrDescriptions as $key => $attr): ?>
				<tr>
					<td><?=$attr?></td>
					<td><?=$attrValues[$key] ?? ''?></td>
				</tr>
				<?endforeach;?>
			</table>
		</div>
		<?endif?>

		<?if($complectText !== ''):?>
		<a href="#" class="tabs__nav">Комплектация</a>
		<div class="tabs__content text">
			<?= riester_sanitize_html_content(
				$complectText,
				!empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
					? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
					: $arResult['NAME']
			) ?>
		</div>
		<?endif;?>

	</div>








