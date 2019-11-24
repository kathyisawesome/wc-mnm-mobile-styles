;( function( $ ) {

	$.fn.isInViewport = function() {
		var elementTop = $(this).offset().top;
		var elementBottom = elementTop + $(this).outerHeight();var viewportTop = $(window).scrollTop();
		var viewportBottom = viewportTop + $(window).height();return elementBottom > viewportTop && elementTop < viewportBottom;
	};

	// Hook into Mix and Match.
	$( '.mnm_form' )
		.on( 'wc-mnm-initializing', function( event, container ) {

			var $products = container.$mnm_form.find( '.products' ).first();
			var $scroll_selector = $products.length ? $products : container.$mnm_form.find( 'table tbody' );

			$(window).on( 'resize scroll', function() {
				if( $scroll_selector.length && $scroll_selector.isInViewport() ) {
					container.$mnm_data.toggleClass( 'fixed', true );
					container.$mnm_message.find('div:first-child').toggleClass( 'woocommerce-info', false );
				} else {
					container.$mnm_data.toggleClass( 'fixed', false );
					container.$mnm_message.find('div:first-child').toggleClass( 'woocommerce-info', true );
				}
			});


		} )
		.on( 'wc-mnm-hide-add-to-cart-button', function( event, container ) {
			container.$mnm_data.toggleClass( 'valid', false );
			if( container.$mnm_data.hasClass('fixed') ) {
				container.$mnm_message.show();
			}
		} )
		.on( 'wc-mnm-display-add-to-cart-button', function( event, container ) {
			container.$mnm_data.toggleClass( 'valid', true );
			if( container.$mnm_data.hasClass('fixed') ) {
				container.$mnm_message.hide();
			}
		} );


} ) ( jQuery );