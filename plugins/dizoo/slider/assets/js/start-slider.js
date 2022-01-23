(function($) {

    "use strict";

    var background_image = function() {
        var bgImgSelector = $("[data-bg-img]");
        if (bgImgSelector.length) {
            bgImgSelector.each(function() {
                var attr = $(this).attr('data-bg-img');
                if (typeof attr !== typeof undefined && attr !== false && attr !== "") {
                    $(this).css('background-image', 'url(' + attr + ')');
                }
            });
        }
    };
    
    var owl_carousel = function() {
        var owlSelector = $('.owl-carousel');
        if (owlSelector.length) {
            $('.owl-carousel').each(function() {
                var carousel = $(this),
                    autoplay_hover_pause = carousel.data('autoplay-hover-pause'),
                    loop = carousel.data('loop'),
                    items_general = carousel.data('items'),
                    margin = carousel.data('margin'),
                    autoplay = carousel.data('autoplay'),
                    autoplayTimeout = carousel.data('autoplay-timeout'),
                    smartSpeed = carousel.data('smart-speed'),
                    nav_general = carousel.data('nav'),
                    navSpeed = carousel.data('nav-speed'),
                    xxs_items = carousel.data('xxs-items'),
                    xxs_nav = carousel.data('xxs-nav'),
                    xs_items = carousel.data('xs-items'),
                    xs_nav = carousel.data('xs-nav'),
                    sm_items = carousel.data('sm-items'),
                    sm_nav = carousel.data('sm-nav'),
                    md_items = carousel.data('md-items'),
                    md_nav = carousel.data('md-nav'),
                    lg_items = carousel.data('lg-items'),
                    lg_nav = carousel.data('lg-nav'),
                    center = carousel.data('center'),
                    dots_global = carousel.data('dots'),
                    xxs_dots = carousel.data('xxs-dots'),
                    xs_dots = carousel.data('xs-dots'),
                    sm_dots = carousel.data('sm-dots'),
                    md_dots = carousel.data('md-dots'),
                    lg_dots = carousel.data('lg-dots');

                carousel.owlCarousel({
                    autoplayHoverPause: autoplay_hover_pause,
                    loop: (loop ? loop : false),
                    items: (items_general ? items_general : 1),
                    lazyLoad: true,
                    margin: (margin ? margin : 0),
                    autoplay: (autoplay ? autoplay : false),
                    autoplayTimeout: (autoplayTimeout ? autoplayTimeout : 1000),
                    smartSpeed: (smartSpeed ? smartSpeed : 250),
                    dots: (dots_global ? dots_global : false),
                    nav: (nav_general ? nav_general : false),
                    navText: ["<i class='fa fa-angle-left' aria-hidden='true'></i>", "<i class='fa fa-angle-right' aria-hidden='true'></i>"],
                    navSpeed: (navSpeed ? navSpeed : false),
                    center: (center ? center : false),
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: (xxs_items ? xxs_items : (items_general ? items_general : 1)),
                            nav: (xxs_nav ? xxs_nav : (nav_general ? nav_general : false)),
                            dots: (xxs_dots ? xxs_dots : (dots_global ? dots_global : false))
                        },
                        480: {
                            items: (xs_items ? xs_items : (items_general ? items_general : 1)),
                            nav: (xs_nav ? xs_nav : (nav_general ? nav_general : false)),
                            dots: (xs_dots ? xs_dots : (dots_global ? dots_global : false))
                        },
                        768: {
                            items: (sm_items ? sm_items : (items_general ? items_general : 1)),
                            nav: (sm_nav ? sm_nav : (nav_general ? nav_general : false)),
                            dots: (sm_dots ? sm_dots : (dots_global ? dots_global : false))
                        },
                        992: {
                            items: (md_items ? md_items : (items_general ? items_general : 1)),
                            nav: (md_nav ? md_nav : (nav_general ? nav_general : false)),
                            dots: (md_dots ? md_dots : (dots_global ? dots_global : false))
                        },
                        1199: {
                            items: (lg_items ? lg_items : (items_general ? items_general : 1)),
                            nav: (lg_nav ? lg_nav : (nav_general ? nav_general : false)),
                            dots: (lg_dots ? lg_dots : (dots_global ? dots_global : false))
                        }
                    }
                });
            });
        }
    };
    $(document).ready(function($){
        background_image();
        owl_carousel();
    });
})(jQuery);