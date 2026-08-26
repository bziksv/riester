<? if(!$arCityes = include $_SERVER['DOCUMENT_ROOT'].'/.cityes.php') return false; ?>

<div class="mfeedback-p" id="location">
    <span class="button b-close"><span>&times;</span></span>
    <div class="mfeedback-p-head">Выбор города</div>

    <? if($arCityes['show'] && $arCityes['hide']): ?>
    <div class="city">
        <? if($arCityes['show']): ?>
        <div class="item-city">
            <? foreach ($arCityes['show'] as $s):?>
            <a href="javascript:void(0)" class="c"><?=$s?></a>
            <? endforeach; ?>
        </div>
        <? endif; ?>

        <div class="item-city">
            <a href="javascript:void(0)" class="city-show" onclick="$(this).closest('.city').find('.item-city:last-child').toggleClass('active'); return false;">Показать все города</a>
        </div>

        <? if($arCityes['hide']): ?>
        <div class="item-city">
            <? foreach ($arCityes['hide'] as $h):?>
            <a href="javascript:void(0)" class="c"><?=$h?></a>
            <? endforeach; ?>
        </div>
        <? endif; ?>
    </div>
    <? endif; ?>
</div>

<style>
    #location {
        display: none;
        width: 92%;
        max-width: 520px;
        max-height: 80vh;
        overflow-y: auto;
        text-align: left;
    }

    .city .item-city {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin: 10px auto;
        padding: 0 10px;
    }
    .city .item-city a {
        width: 140px;
        margin-bottom: 5px;
        color: #575b71;
        text-decoration: none;
    }
    .city .item-city a:hover {
        color: #c82c30;
    }
    .city .item-city a.city-show {
        flex-grow: 1;
        text-align: center;
    }
    .city .item-city:last-child{
        display: none;
    }
    .city .item-city:last-child.active{
        display: flex;
    }
</style>

<script>
    (function($) {
        var locationPopupApi = null;

        $('#location_btn').on('click', function(e) {
            e.preventDefault();
            if ($('#location').is(':visible')) {
                return false;
            }

            locationPopupApi = $('#location').bPopup({
                zIndex: 10100,
                position: ['auto', 'auto'],
                modalClose: true,
                follow: [false, false]
            });
            return false;
        });

        $('.city .item-city a.c').on('click', function() {
            var city = $(this).text();
            document.cookie = "city=" + encodeURIComponent(city) + "; path=/; max-age=31536000";
            $('#location_btn').html(city + '<i class="icon-arrow_down" style="transform: none;"></i>');

            if (locationPopupApi && typeof locationPopupApi.close === 'function') {
                locationPopupApi.close();
            } else {
                $('#location').find('.b-close').trigger('click');
            }
        });
    })(jQuery);
</script>
