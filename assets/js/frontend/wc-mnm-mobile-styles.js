;( function( $ ) {

	$.fn.isInViewport = function() {
		var elementTop = $(this).offset().top;
		var elementBottom = elementTop + $(this).outerHeight();var viewportTop = $(window).scrollTop();
		var viewportBottom = viewportTop + $(window).height();return elementBottom > viewportTop && elementTop < viewportBottom;
	};

	// Hook into Mix and Match.
	$( 'body' )
		.on( 'wc-mnm-initializing', function( event, container ) {

			var $mobile_footer = $( '#mnm-mobile-container' );

			var $products = container.$mnm_form.find( '.products' ).first();
			var $scroll_selector = $products.length ? $products : container.$mnm_form.find( 'table tbody' );

			// Hide/Show the footer when form is in view.
			$(window).on( 'resize scroll', function() {
				if( $scroll_selector.length && $scroll_selector.isInViewport() ) {
					$mobile_footer.show();
				} else {
					$mobile_footer.hide();
				}
			}).trigger('resize');

			// Relay footer add to cart click to form button.
			$mobile_footer.on( 'click', '.mnm_add_to_cart_button', function( e ) {
				e.preventDefault();
				container.$mnm_button.click();
			} );

			// Relay footer reset to form reset.
			$mobile_footer.on( 'click', '.mnm_reset', function( e ) {
				e.preventDefault();
				container.$mnm_reset.click();
			} );


		} )

		.on( 'wc-mnm-form-updated', function( event, container ) {

			var $mobile_message = $( '#mnm-mobile-container' ).find( '.mnm_message' );

			// Display the status/error messages.
			if ( container.has_status_messages() || false === container.passes_validation() ) {
				$mobile_message.html( container.$mnm_message.html() ).show();
			} else {
				$mobile_message.hide();
			}

		} )
		.on( 'wc-mnm-updated-totals', function( event, container ) {
			var $mobile_price   = $( '#mnm-mobile-container' ).find( '.mnm_price' );
			$mobile_price.html( container.$mnm_price.html() );

		} )
		.on( 'wc-mnm-hide-add-to-cart-button', function() {
			$( '#mnm-mobile-container' ).toggleClass( 'valid', false );
			$( '#mnm-mobile-container' ).find( '.mnm_add_to_cart_button' ).toggleClass( 'disabled', true ).prop( 'disabled', true );
		} )
		.on( 'wc-mnm-display-add-to-cart-button', function() {
			$( '#mnm-mobile-container' ).toggleClass( 'valid', true );
			$( '#mnm-mobile-container' ).find( '.mnm_add_to_cart_button' ).toggleClass( 'disabled', false ).prop( 'disabled', false );
		} );


} ) ( jQuery );