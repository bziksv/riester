<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$compositeStub = (isset($arResult['COMPOSITE_STUB']) && $arResult['COMPOSITE_STUB'] == 'Y');
?>
<?if($arResult['TOTAL_PRICE']):?>
<a href="<?= $arParams['PATH_TO_BASKET'] ?>" class="header__basket">
	<i class="icon-cart" data-count="<? echo $arResult['NUM_PRODUCTS']; ?>"></i>
	<span class="header__basket_content">
		<span class="header__basket_price"><?=$arResult['TOTAL_PRICE'] ?></span>
		<span class="header__desc">Мои покупки</span>
	</span>
</a>
<?endif;?>