;( function( $ ) {

	$.fn.isInViewport = function() {
		var elementTop = $(this).offset().top;
		var elementBottom = elementTop + $(this).outerHeight();var viewportTop = $(window).scrollTop();
		var viewportBottom = viewportTop + $(window).height();return elementBottom > viewportTop && elementTop < viewportBottom;
	};

	// Listen for Mix and Match events.
	$( document )
		.on( 'wc-mnm-initializing', function( event, container ) {

			// Render the footer.
			var $mobile_footer = $( '#mnm-mobile-container' );
			var template       = wp.template( 'wc-mnm-mobile-footer' );
			var button_text    = container.$mnm_button.text();
			var stock_html     = container.$mnm_data.data( 'stock_html' );
			var context        = container.$mnm_form.data( 'validation_context' ) || 'add-to-cart';
			
			let $template_html = template( {
				is_purchasable    : container.api.is_purchasable(),
				is_in_stock       : container.api.is_in_stock(),
				min_container_size: container.api.get_min_container_size(),
				max_container_size: container.api.get_max_container_size(),
				container_id      : container.container_id,
				button_text       : button_text,
				stock_html        : stock_html,
				context           : context
			} );
			$template_html = $template_html.replace( '/*<![CDATA[*/', '' );
			$template_html = $template_html.replace( '/*]]>*/', '' );
	  
			$mobile_footer.html( $template_html );

			// Hide/Show the footer when form is in view.
			var $products = container.$mnm_form;
			var $scroll_selector = $products.length ? $products : container.$mnm_form.find( 'table tbody' );

			$(window).on( 'resize.wc-mnm-mobile-styles scroll.wc-mnm-mobile-styles', function() {
				if( $scroll_selector.length && $mobile_footer.children().length && $scroll_selector.isInViewport() ) {
					$mobile_footer.show();
				} else {
					$mobile_footer.hide();
				}
			}).trigger('resize.wc-mnm-mobile-styles');

			// Switch the buttons to the footer.
			container.$mnm_button = $mobile_footer.find( '.single_add_to_cart_button' );
			container.$mnm_reset  = $mobile_footer.find( '.mnm_reset' );

			// Relay footer add to cart click to form button.
			$mobile_footer.on( 'click', '.single_add_to_cart_button', function( e ) {
				e.preventDefault();

				// Add a loading class to this button.
				$(this).addClass('loading');
				container.$mnm_form.trigger('submit');
			} );

			// Relay footer reset to form reset.
			$mobile_footer.on( 'click', '.mnm_reset', function( e ) {
				e.preventDefault();
				if (window.confirm(wc_mnm_params.i18n_confirm_reset)) {
					container.$mnm_form.trigger('wc-mnm-container-reset');
				}	
			} );

			// Relay cancel to form cancel for subscription editing.
			$mobile_footer.on( 'click', '.wc-mnm-cancel-edit', function( e ) {
				e.preventDefault();
				$( '.woocommerce-MyAccount-content .wc-mnm-cancel-edit' ).trigger( 'click' );
			} );

		} )
		.on( 'wc-mnm-form-updated', function( event, container ) {
			$( document ).trigger( 'wc-mnm-update-mobile-footer', [ container ] );
		} )
		.on( 'wc-mnm-hide-add-to-cart-button', function() {
			$( '#mnm-mobile-container' ).toggleClass( 'valid', false );
			$( '#mnm-mobile-container' ).find( '.single_add_to_cart_button' ).toggleClass( 'disabled', true ).prop( 'disabled', true );
		} )
		.on( 'wc-mnm-display-add-to-cart-button', function() {
			$( '#mnm-mobile-container' ).toggleClass( 'valid', true );
			$( '#mnm-mobile-container' ).find( '.single_add_to_cart_button' ).toggleClass( 'disabled', false ).prop( 'disabled', false );
		} );

	// Update the footer content.
	$( document ).on( 'wc-mnm-update-mobile-footer', function( event, container ) { 

		var $mobile_footer   = $( '#mnm-mobile-container' );
		var $mobile_message  = $mobile_footer.find( '.mnm_message' );
		var $mobile_reset    = $mobile_footer.find( '.mnm_reset' );
		var $mobile_progress = $mobile_footer.find( 'progress.mnm-container-progress' );

		// Display the price and status counter.
		$( '#mnm-mobile-container' ).find( '.mnm_price' ).html( container.$mnm_price.html() );

		// Update the progress bar value.
		$mobile_progress.val( container.api.get_container_size() );

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

	/**
	 * Variable Mix and Match support.
	 */
	$( document ).on( 'wc_mnm_variation_form_loaded', '.variable_mnm_form', function() {
		$( this ).find( '.wc-mnm-edit-subscription-actions' ).hide();
	});

	$( document ).on( 'wc_mnm_variation_reset', '.variable_mnm_form', function() {
		$( '#mnm-mobile-container' ).empty().hide();
		$( this ).find( '.wc-mnm-edit-subscription-actions' ).show();
	});

	/**
	 * All Products for Subscriptions patch for forced subscription scheme.
	 */
	$( document ).on ( 'wcsatt-updated-mnm-subscription-totals', '.mnm_form', function( event, container, MNM_Integration ) {

		const $mobile_footer_price       = $( '#mnm-mobile-container' ).find( '.mnm_price .price' );
		const container_price_html       = container.get_price_html();
		const container_price_inner_html = $( container_price_html ).html();

		// If only a single option is present, we can tweak the footer price.
		if ( MNM_Integration.has_single_forced_subscription() ) {
			$mobile_footer_price.html( container.satt_schemes[0].data.option_details_html.replace( /%p/g, container_price_inner_html ) );
		}
	} );
} ) ( jQuery );