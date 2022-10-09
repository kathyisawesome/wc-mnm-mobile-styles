;( function( $ ) {

	$.fn.isInViewport = function() {
		var elementTop = $(this).offset().top;
		var elementBottom = elementTop + $(this).outerHeight();var viewportTop = $(window).scrollTop();
		var viewportBottom = viewportTop + $(window).height();return elementBottom > viewportTop && elementTop < viewportBottom;
	};

	// Listen for Mix and Match events.
	$( document )
		.on( 'wc-mnm-initializing', function( event, container ) {

			var $mobile_footer = $( '#mnm-mobile-container' );

			var $products = container.$mnm_form.find( '.mnm_child_products' ).first();
			var $scroll_selector = $products.length ? $products : container.$mnm_form.find( 'table tbody' );

			// Hide/Show the footer when form is in view.
			$(window).on( 'resize scroll', function() {
				if( $scroll_selector.length && $mobile_footer.has( ':visible' ) && $scroll_selector.isInViewport() ) {
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
			$( document ).trigger( 'wc-mnm-update-mobile-footer', [ container ] );
		} )
		.on( 'wc-mnm-hide-add-to-cart-button', function() {
			$( '#mnm-mobile-container' ).toggleClass( 'valid', false );
			$( '#mnm-mobile-container' ).find( '.mnm_add_to_cart_button' ).toggleClass( 'disabled', true ).prop( 'disabled', true );
		} )
		.on( 'wc-mnm-display-add-to-cart-button', function() {
			$( '#mnm-mobile-container' ).toggleClass( 'valid', true );
			$( '#mnm-mobile-container' ).find( '.mnm_add_to_cart_button' ).toggleClass( 'disabled', false ).prop( 'disabled', false );
		} );

	// Update the footer content.
	$( document ).on( 'wc-mnm-update-mobile-footer', function( event, container ) { 

		var $mobile_footer  = $( '#mnm-mobile-container' );
		var $mobile_message = $mobile_footer.find( '.mnm_message' );
		var $mobile_reset   = $mobile_footer.find( '.mnm_reset' );

		$( '#mnm-mobile-container' ).find( '.mnm_price' ).html( container.get_status_html() );

		// Display the status/error messages.
		if ( container.has_status_messages() || false === container.passes_validation() ) {
			$mobile_message.html( container.$mnm_message.html() ).show();
		} else {
			$mobile_message.hide();
		}

		// Hide/Show Reset Link.
		if ( container.api.get_container_size() > 0 ) {
			$mobile_reset.show();
		} else {
			$mobile_reset.hide();
		}
	} );

	// Variable Mix and Match support.
	$( '.variable_mnm_form' ).on( 'wc_mnm_variation_found', function( event, variation ) { 
		if ( 'undefined' !== typeof variation.mix_and_match_footer_html ) {
		
			var $jQueryObject = $( '<div/>' ).html(variation.mix_and_match_footer_html);
			$( '#mnm-mobile-container' ).html( $jQueryObject.find( '#mnm-mobile-container' ).contents() );

			var container = $(event.currentTarget).wc_get_mnm_script();

			if ( 'undefined' !== typeof container && false !== container ) {
				$( document ).trigger( 'wc-mnm-update-mobile-footer', [ container ] );
			}
			
		}
	} );


} ) ( jQuery );