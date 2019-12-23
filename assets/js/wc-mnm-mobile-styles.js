;
(function ($) {
    $counter = 0;
    $.fn.isInViewport = function () {
        var elementTop = $(this).offset().top;
        var elementBottom = elementTop + $(this).outerHeight();
        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();
        return elementBottom > viewportTop && elementTop < viewportBottom;
    };

    // Hook into Mix and Match.
    $('.mnm_form')
            .on('wc-mnm-initializing', function (event, container) {

                var $products = container.$mnm_form.find('.products').first();
                var $scroll_selector = $products.length ? $products : container.$mnm_form.find('table tbody');

                $(window).on('resize scroll', function () {
                    if ($scroll_selector.length && $scroll_selector.isInViewport()) {
                        if ($counter === 0) {
                            $('.mnm-mobile-container').addClass('mnm-mobile-slide', true);
                            $('.mnm-mobile-btn-slide').toggleClass('up', true);
                        }
//					container.$mnm_data.toggleClass( 'fixed', true );
//					container.$mnm_message.find('div:first-child').toggleClass( 'woocommerce-info', false );
                        $counter++;
                    } else {
                        $('.mnm-mobile-container').toggleClass('mnm-mobile-slide', false);
                        $('.mnm-mobile-btn-slide').toggleClass('up', false);
//                                    container.$mnm_data.toggleClass( 'fixed', false );
//                                    container.$mnm_message.find('div:first-child').toggleClass( 'woocommerce-info', true );
                    }

                });


            })
            .on('wc-mnm-hide-add-to-cart-button', function (event, container) {
                console.log('wc-mnm-hide-add-to-cart-button');
                container.$mnm_data.toggleClass('valid', false);
                if (container.$mnm_data.hasClass('fixed')) {
                    container.$mnm_message.show();
                }
            })
            .on('wc-mnm-display-add-to-cart-button', function (event, container) {
                console.log('wc-mnm-display-add-to-cart-button');
                container.$mnm_data.toggleClass('valid', true);
                if (container.$mnm_data.hasClass('fixed')) {
                    container.$mnm_message.hide();
                }
            });

    $('.input-text.qty.text').on('change', function () {
        $('.mnm-mobile-container').addClass('mnm-mobile-slide', true);
        $('.mnm-mobile-btn-slide').toggleClass('up', true);
//                    if(! $('.mnm-mobile-container').hasClass('mnm-mobile-slide')){
//                        $('.mnm-mobile-container').addClass('mnm-mobile-slide');
////                        $('.mnm-mobile-btn-slide').removeClass('up');
//                    }
        if ($('.mnm_cart').hasClass('valid')) {
//                        $('div.mnm-mobile-content  div.mnm_message').css('display','none');
        }
    });

    $('.mnm-mobile-btn-slide').on('click', function (e) {
        e.preventDefault();
        $(this).toggleClass('up');
        var r = $('.mnm-mobile-container').toggleClass('mnm-mobile-slide');
        console.log(r);

    });
})(jQuery);