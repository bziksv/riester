"use strict";
$(function() {
    function fitSliderToOuter($ul) {
        if (!$ul || !$ul.length) return;
        var $outer = $ul.closest(".lSSlideOuter");
        if (!$outer.length) return;
        var w = Math.round($outer.width());
        if (!w) return;
        var $slides = $ul.children(".lslide");
        var item = 1;
        var bw = window.innerWidth || document.documentElement.clientWidth;
        if ($ul.hasClass("goods__slider")) {
            if (bw >= 991) item = 4;
            else if (bw >= 768) item = 3;
            else if (bw >= 480) item = 2;
            else item = 1;
        }
        var slideW = Math.floor(w / item);
        $slides.css({ width: slideW + "px", "margin-right": "0px" });
        $ul.css("width", $slides.length * slideW + "px");
    }

    $(".slider__wrapper").lightSlider({
        adaptiveHeight: !0,
        item: 1,
        auto:true,
        loop:true,
        pause:5000,
        slideMargin: 0,
        enableDrag: false,
        prevHtml: '<i class="icon-arrow_prev"></i>',
        nextHtml: '<i class="icon-arrow_next"></i>'
    }), function() {
        var $full = $(".full-slider__wrapper");
        if (!$full.length) return;
        var fullSlider = $full.lightSlider({
            adaptiveHeight: !0,
            item: 1,
            slideMargin: 0,
            pager: !1,
            enableDrag: !1,
            prevHtml: '<i class="icon-arrow_prev"></i>',
            nextHtml: '<i class="icon-arrow_next"></i>',
            onSliderLoad: function() {
                fitSliderToOuter($full);
                $full.find("img").on("load", function() {
                    fitSliderToOuter($full);
                    if (fullSlider && typeof fullSlider.refresh === "function") {
                        fullSlider.refresh();
                        fitSliderToOuter($full);
                    }
                });
            }
        });
        fitSliderToOuter($full);
        $(window).on("load resize", function() {
            if (fullSlider && typeof fullSlider.refresh === "function") {
                fullSlider.refresh();
            }
            fitSliderToOuter($full);
        });
    }(), function() {
        var $goods = $(".goods__slider");
        if (!$goods.length) return;
        var goodsSlider = $goods.lightSlider({
            adaptiveHeight: !0,
            item: 4,
            loop: true,
            slideMargin: 0,
            pager: !1,
            enableDrag: !1,
            prevHtml: '<i class="icon-arrow_prev"></i>',
            nextHtml: '<i class="icon-arrow_next"></i>',
            responsive: [{
                breakpoint: 991,
                settings: {
                    item: 3,
                    slideMargin: 0
                }
            }, {
                breakpoint: 768,
                settings: {
                    item: 2,
                    slideMargin: 0
                }
            }, {
                breakpoint: 480,
                settings: {
                    item: 1,
                    slideMargin: 0
                }
            }],
            onSliderLoad: function() {
                fitSliderToOuter($goods);
                $goods.find("img").on("load", function() {
                    fitSliderToOuter($goods);
                    if (goodsSlider && typeof goodsSlider.refresh === "function") {
                        goodsSlider.refresh();
                        fitSliderToOuter($goods);
                    }
                });
            }
        });
        fitSliderToOuter($goods);
        $(window).on("load resize", function() {
            if (goodsSlider && typeof goodsSlider.refresh === "function") {
                goodsSlider.refresh();
            }
            fitSliderToOuter($goods);
        });
    }(), $(".cart__slider").lightSlider({
        prevHtml: '<i class="icon-arrow_prev"></i>',
        nextHtml: '<i class="icon-arrow_next"></i>',
        gallery: true,
        enableDrag: false,
        item: 1,
        thumbItem: 5,
        thumbMargin: 10,
        slideMargin: 0,
        onSliderLoad: function(el) {
            el.lightGallery({
                selector: '.cart__slider .lslide',
            });
        },
        responsive: [{
            breakpoint: 991,
            settings: {
                thumbMargin: 8,
                thumbItem: 3
            }
        }, {
            breakpoint: 768,
            settings: {
                thumbItem: 4
            }
        }]
    }), $(".iselect").select2(), $(".catalog__submenu_toggle").click(function(e) {
        e.preventDefault(), $(this).toggleClass("active").siblings(".catalog__submenu").stop().slideToggle()
    }), $(".goods__counter_add").click(function(e) {
        e.preventDefault();
        var t = $(this),
            i = t.siblings(".goods__counter_input"),
            a = i.val();
        console.log(a), i.val(++a)
    }), $(".goods__counter_subtract").click(function(e) {
        e.preventDefault();
        var t = $(this),
            i = t.siblings(".goods__counter_input"),
            a = i.val();
        a > 1 && i.val(--a)
    }), $(".toggle__menu").click(function() {
        // Class toggle — slideToggle breaks on flex .nav at 768–900px (stuck height)
        return $(this).toggleClass("on"), !1
    }), $(".header__contact_mobile").click(function() {
        $(this).toggleClass("active"), $(".header__contact").toggleClass("active")
    }), $("body").innerWidth() > 752 && $(".tabs__nav").eq(0).addClass("active"), $(".tabs__nav").click(function(e) {
        e.preventDefault(), $(this).addClass("active").siblings(".tabs__nav").removeClass("active")
    });

    $('[data-text]').each(function(i, el){
        let self = $(el);
        self.html(self.data('text'));
    });
});
