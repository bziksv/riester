<?php
if(!$arParams['SECTION']['UF_TAGS_ACTIVE'])
    return false;

global $APPLICATION;
ob_start();
?>
<style>
    .tags-list {
        margin: 0 0 24px;
    }

    .subcategories {
        display: flex;
        flex-direction: column;
        position: relative;
    }

    /* Clip native scrollbar (sanitizer strips scrollbar-width / ::-webkit-scrollbar) */
    .tags-scroll {
        position: relative;
        min-width: 0;
        width: 100%;
        max-width: 100%;
    }

    .tags-list .tags-scroll:not(.is-expanded) {
        max-height: 42px !important;
        overflow: hidden !important;
    }

    .tags-list .tags-scroll.is-expanded {
        overflow: visible !important;
        max-height: none !important;
    }

    .tags-scroll:not(.is-expanded)::after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 40px;
        pointer-events: none;
        background: linear-gradient(to right, rgba(255,255,255,0), #fff 90%);
        z-index: 1;
    }

    .tags-list .sub-links-2 {
        list-style: none !important;
        margin: 0 !important;
        padding: 0 0 30px !important;
        display: -webkit-flex !important;
        display: -ms-flexbox !important;
        display: flex !important;
        -webkit-flex-wrap: nowrap !important;
        -ms-flex-wrap: nowrap !important;
        flex-wrap: nowrap !important;
        overflow-x: scroll !important;
        overflow-y: hidden !important;
        -webkit-overflow-scrolling: touch;
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
        box-sizing: content-box;
        position: relative;
    }

    .tags-list .sub-links-2.is-open {
        -webkit-flex-wrap: wrap !important;
        -ms-flex-wrap: wrap !important;
        flex-wrap: wrap !important;
        overflow: visible !important;
        padding-bottom: 0 !important;
    }

    .tags-list .sub-links-2 li {
        margin: 0 8px 0 0 !important;
        display: block !important;
        -webkit-box-flex: 0;
        -webkit-flex: 0 0 auto;
        -ms-flex: 0 0 auto;
        flex: 0 0 auto;
        scroll-snap-align: start;
        max-width: 100%;
        float: none !important;
    }

    .tags-list .sub-links-2 li:last-child {
        margin-right: 0 !important;
    }

    .tags-list .sub-links-2.is-open li {
        margin: 0 8px 8px 0 !important;
    }

    .tags-list .sub-links-2 a {
        display: inline-block;
        max-width: 100%;
        box-sizing: border-box;
        padding: 8px 14px;
        border: 1px solid #c5ced0;
        border-radius: 20px;
        color: #5b6273;
        background: #fff;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.3;
        text-decoration: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: color .2s ease, background .2s ease, border-color .2s ease;
    }

    .tags-list .sub-links-2.is-open a {
        white-space: normal;
    }

    .tags-list .sub-links-2 a:hover {
        color: #fff;
        border-color: #66b3d4;
        background: #66b3d4;
        text-decoration: none;
    }

    .subcategories .navi {
        display: flex;
        justify-content: flex-end;
        margin-top: 12px;
    }

    .subcategories .navi .tags-toggle {
        appearance: none;
        -webkit-appearance: none;
        border: 0;
        background: transparent;
        color: #8a9399;
        padding: 0;
        font-size: 13px;
        font-weight: 400;
        line-height: 1.2;
        cursor: pointer;
        text-decoration: underline;
        text-decoration-style: dotted;
        text-underline-offset: 3px;
    }

    .subcategories .navi .tags-toggle:hover {
        color: #5b6273;
        text-decoration-style: solid;
    }

    @media (max-width: 767px) {
        .tags-list {
            margin-bottom: 18px;
        }

        .tags-list .sub-links-2 a {
            padding: 7px 12px;
            font-size: 12px;
            max-width: min(280px, 78vw);
        }

        .tags-list .subcategories .navi .tags-toggle {
            font-size: 12px;
        }
    }
</style>
<?php
\Bitrix\Main\Page\Asset::getInstance()->addString(
	ob_get_clean(),
	true,
	\Bitrix\Main\Page\AssetLocation::BEFORE_CSS
);
?>

<div class="tags-list">
    <div class="subcategories">
        <div class="tags-scroll">
            <ul class="tag-slider sub-links-2">
                <? foreach ($arParams['SECTION']['UF_TAGS_LIST'] as $tags):
                    $arTags = explode('@', $tags, 2);
                    $tagHref = isset($arTags[1]) ? trim($arTags[1]) : '';
                    if ($tagHref !== '' && preg_match('#^(.*?/search/\?q=)([^&]*)(.*)$#u', $tagHref, $tagM)) {
                        $tagHref = $tagM[1] . rawurlencode(rawurldecode(str_replace('+', ' ', $tagM[2]))) . $tagM[3];
                    }
                    ?>
                <li><a href="<?=htmlspecialcharsbx($tagHref)?>"><?=htmlspecialcharsbx($arTags[0])?></a></li>
                <? endforeach; ?>
            </ul>
        </div>
        <div class="navi">
            <button type="button" class="tags-toggle open">Показать все</button>
            <button type="button" class="tags-toggle close" hidden>Свернуть</button>
        </div>
    </div>
</div>

<script>
(function ($) {
    $(function () {
        var $scroll = $(".tags-list .tags-scroll");
        var $list = $(".tags-list .sub-links-2");
        var $open = $(".tags-list .open");
        var $close = $(".tags-list .close");

        $open.on("click", function () {
            $open.prop("hidden", true);
            $close.prop("hidden", false);
            $list.addClass("is-open");
            $scroll.addClass("is-expanded");
        });

        $close.on("click", function () {
            $close.prop("hidden", true);
            $open.prop("hidden", false);
            $list.removeClass("is-open");
            $scroll.removeClass("is-expanded");
            $scroll.find(".sub-links-2").scrollLeft(0);
        });
    });
})(jQuery);
</script>
