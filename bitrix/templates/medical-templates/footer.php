<? if(!CSite::InDir('/index.php')): ?>
</div>
<?endif;?>

<div class="more_options"></div>

<div class="footer">
    <div class="container">
        <div class="footer__wrapper footer__row">
            <div class="footer__col">
                <div class="footer__subscribe">
                    <i class="icon-email_big"></i>
                    <span><? $APPLICATION->IncludeComponent("bitrix:main.include", "", ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/subscribe_desc.php"], false ); ?></span>
                </div>
				<div>
					<div class="footer__info">
						<div class="footer__title"><? $APPLICATION->IncludeComponent("bitrix:main.include", "", ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/title.php"], false ); ?></div>
						<div class="footer__desc"><? $APPLICATION->IncludeComponent("bitrix:main.include", "", ["AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/subtitle.php"], false ); ?></div>
					</div>
				</div>
            </div>
            <div class="footer__col">

                <?$APPLICATION->IncludeComponent(
	"bitrix:sender.subscribe", 
	"sender.subscribe", 
	array(
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CONFIRMATION" => "N",
		"HIDE_MAILINGS" => "N",
		"SET_TITLE" => "N",
		"SHOW_HIDDEN" => "N",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "1",
		"USER_CONSENT_IS_CHECKED" => "N",
		"USER_CONSENT_IS_LOADED" => "N",
		"USE_PERSONALIZATION" => "Y",
		"COMPONENT_TEMPLATE" => "sender.subscribe",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>

            </div>
        </div>
    </div>
    <hr>
    <div class="container">
        <div class="footer__row">
            <div class="footer__col">

                <?$APPLICATION->IncludeComponent("bitrix:menu", "bottom.menu", Array(
                    "ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
                    "CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
                    "DELAY" => "N",	// Откладывать выполнение шаблона меню
                    "MAX_LEVEL" => "2",	// Уровень вложенности меню
                    "MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
                    "MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
                    "MENU_CACHE_TYPE" => "N",	// Тип кеширования
                    "MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
                    "ROOT_MENU_TYPE" => "bottom",	// Тип меню для первого уровня
                    "USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
                    "COMPONENT_TEMPLATE" => "catalog_horizontal",
                    "MENU_THEME" => "site",	// Тема меню
                ),
                    false
                );?>

            </div>
            <div class="footer__col flex-3">
                <div class="footer__phone icon-phone">
                    <img src="<?=SITE_TEMPLATE_PATH?>/img/footer_phone.jpg" alt="phone">
                    <span>88005555550</span>
                </div>
                <a href="javascript:void(0);" class="footer__callback footer__link callback-btn">Заказать звонок</a>
                <div class="footer__email icon-email">
                    <a href="mailto:<?=tplvar('email');?>" class="footer__link roi_visit"><?=tplvar('email', true);?></a>
                    (Для заказов)
                </div>
            </div>
            <div class="footer__col flex-2">
                <div class="footer__copy icon-copyright"><?=date("Y")?> Все права защищены.</div>
				<br>
                <a href="https://prime-ltd.su/" target="_blank" style="position: relative; right: 11px;">
  					<img src="https://prime-ltd.su/logo/white.svg" style="width: 70%; height: auto;" title="Продвижение сайтов" alt="Продвижение сайтов">
				</a>
            </div>
        </div>
		<?php
		$riesterLegal = include $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/include/legal/config.php';
		$legalUrls = $riesterLegal['images'];
		?>
		<div><noindex>Мы используем файлы <a target="_blank" style="color: #fff;" href="<?= htmlspecialcharsbx($legalUrls['cookie']) ?>">cookies</a> для улучшения работы сайта, настройки рекламы и анализа посещаемости. Продолжая пользоваться сайтом, вы даёте <a target="_blank" style="color: #fff;" href="<?= htmlspecialcharsbx($legalUrls['consent']) ?>">согласие на обработку персональных данных</a>, соглашаетеся с <a target="_blank" style="color: #fff;" href="<?= htmlspecialcharsbx($legalUrls['personal_data']) ?>">политикой обработки персональных данных</a> и ознакомлены с <a target="_blank" style="color: #fff;" href="<?= htmlspecialcharsbx($legalUrls['recommendation']) ?>">правилами применения рекомендательных технологий</a>. Для отказа от использования cookies отключите их сохранение в настройках браузера.</noindex></div>
    </div>
</div>




<?
$APPLICATION->IncludeComponent(
	"nbrains:main.feedback", 
	"popup-callback", 
	array(
		"EMAIL_TO" => "info@riester.su",
		"EVENT_NAME" => "CALLBACK",
		"EVENT_MESSAGE_ID" => array(
			0 => "53",
		),
		"IBLOCK_ID" => "37",
		"IBLOCK_TYPE" => "feedback",
		"OK_TEXT" => "Спасибо, ваше сообщение принято.",
		"PROPERTY_CODE" => array(
			0 => "NAME",
			1 => "URL",
			2 => "PHONE",
			3 => "MAIL",
			4 => "QUERY",
		),
		"USE_CAPTCHA" => "N",
		"COMPONENT_TEMPLATE" => "popup-callback",
		"COMPOSITE_FRAME_MODE" => "N",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>




<script src="<?=SITE_TEMPLATE_PATH?>/js/main.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/alertify.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.maskinput.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.bpopup.min.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/lightgallery.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery-ui.js"></script>
<script src="<?=SITE_TEMPLATE_PATH?>/js/functions.js"></script>


<!-- Yandex.Metrika counter от Prime -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(55573843, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/55573843" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

<!-- Сквозная от Prime -->
<script>
(function(w, d, s, h, id) {
    w.roistatProjectId = id; w.roistatHost = h;
    var p = d.location.protocol == "https:" ? "https://" : "http://";
    var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js" : "/api/site/1.0/"+id+"/init";
    var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);
})(window, document, 'script', 'cloud.roistat.com', 'f504a26807eaae0c8a718305b990b815');
</script>


</body>
</html>