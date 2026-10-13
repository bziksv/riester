<?php
if(!$arParams['SECTION']['UF_TAGS_ACTIVE'])
    return false;

\Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/tags_list.css');
?>

<div class="tags-list">

    <div class="subcategories">
        <ul class="tag-slider sub-links-2">
            <? foreach ($arParams['SECTION']['UF_TAGS_LIST'] as $tags):
                $arTags = explode('@', $tags, 2);
                ?>
            <li><a href="<?php
                $tagHref = (string)($arTags[1] ?? '');
                if (preg_match('#^(/search/\?q=)(.+)$#u', $tagHref, $qm)) {
                    $q = $qm[2];
                    $extra = '';
                    if (($p = strpos($q, '&')) !== false) {
                        $extra = substr($q, $p);
                        $q = substr($q, 0, $p);
                    }
                    $tagHref = $qm[1] . rawurlencode(rawurldecode(str_replace('+', ' ', $q))) . $extra;
                }
                echo htmlspecialcharsbx($tagHref);
            ?>"><?=htmlspecialcharsbx($arTags[0])?></a></li>
            <? endforeach; ?>
        </ul>
        <div class="navi">
            <span class="open">Показать все</span>
            <span hidden class="close">Свернуть</span>
        </div>
    </div>

</div>


<link rel="stylesheet" type="text/css" href="<?= SITE_TEMPLATE_PATH ?>/css/slick/slick.css"/>
<script type="text/javascript" src="<?= SITE_TEMPLATE_PATH ?>/js/slick.min.js"></script>
<script>

    $('.tag-slider').slick({
        dots: false,
        arrows: true,
        infinite: true,
        autoplay: true,
        variableWidth: true,
        centerMode: true,
        slidesToShow: 3,
        responsive: [
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $(document).ready(function() {
        $(".subcategories .open").click(function(){
            $(this).hide();
            $(".subcategories .close").show();
            $(".subcategories .sub-links-2").addClass("open");
            $('.tag-slider').slick('unslick');
        });
        $(".subcategories .close").click(function(){
            $(this).hide();
            $(".subcategories .open").show();
            $(".subcategories .sub-links-2").removeClass("open");
            $('.tag-slider').slick({
                dots: false,
                arrows: true,
                infinite: true,
                autoplay: true,
                variableWidth: true,
                centerMode: true,
                slidesToShow: 3,
            });
        });
    });

</script>
