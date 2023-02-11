;( function( $ ) {

	$.fn.isInViewport = function() {
		var elementTop = $(this).offset().top;
		var elementBottom = elementTop + $(this).outerHeight();var viewportTop = $(window).scrollTop();
		var viewportBottom = viewportTop + $(window).height();return elementBottom > viewportTop && elementTop < viewportBottom;
	};


	/**
	 * Main container object.
	 */
	function WC_MNM_Mobile_Styles( container ) {

		var self       = this;
		this.container = container;
		this.$form     = container.$mnm_form;
		
		// Define the footer.
		this.$mobile_footer = $( '#mnm-mobile-container' );

		// Define the element that we will test is in view... different between tabular|grid layouts.
		this.$scroll_selector = container.$mnm_form.length ? container.$mnm_form : container.$mnm_form.find( 'table tbody' );

		/**
		 * Init.
		 */
		this.initialize = function() {

			this.renderFooter();
			this.addEventHandlers();

			// Switch the buttons to the footer.
			container.$mnm_original_button = container.$mnm_button;
			container.$mnm_button = this.$mobile_footer.find( '.single_add_to_cart_button' );
			container.$mnm_reset  = this.$mobile_footer.find( '.mnm_reset' );

			
		};

		/**
		 * Is in viewport.
		 */
		this.isInViewport = function( $el ) {
			var elementTop = $el.offset().top;
			var elementBottom = elementTop + $el.outerHeight();var viewportTop = $(window).scrollTop();
			var viewportBottom = viewportTop + $(window).height();return elementBottom > viewportTop && elementTop < viewportBottom;
		};

		/**
		 * Container-Level Event Handlers.
		 */
		this.addEventHandlers = function() {

			// Hide/show the footer depending on viewport.
			$(window).on( 'resize.wc-mnm-mobile-styles scroll.wc-mnm-mobile-styles', this.maybeShowFooter ).trigger('resize.wc-mnm-mobile-styles');

			// Handle update of the footer.
			$(document).on( 'wc-mnm-update-mobile-footer', this.updateFooter );

			// Handle form events.
			this.$form.on( 'wc-mnm-form-updated', this.relayUpdate );
			this.$form.on( 'wc-mnm-display-add-to-cart-button', this.handleValidForm );
			this.$form.on( 'wc-mnm-hide-add-to-cart-button', this.handleNotValidForm );	

			// Relay footer events to main form.
			this.$mobile_footer.on( 'click', '.single_add_to_cart_button', { container: container }, this.relayAddToCart );
			this.$mobile_footer.on( 'click', '.mnm_reset', { container: container }, this.relayReset );
			this.$mobile_footer.on( 'click', '.wc-mnm-cancel-edit', this.relayCancel );

			// Integrations.
			$( document ).on( 'wc_mnm_variation_form_loaded', '.variable_mnm_form', this.hideVariationLoaded );
			$( document ).on( 'wc_mnm_variation_reset', '.variable_mnm_form', this.handleVariationReset );

		};

		/**
		 * Render the footer in the DOM.
		 */
		this.renderFooter = function() {

			let template       = wp.template( 'wc-mnm-mobile-footer' );
			let button_text    = container.$mnm_button.text();
			let stock_html     = container.$mnm_data.data( 'stock_html' );
			let context        = container.$mnm_form.data( 'validation_context' ) || 'add-to-cart';

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
	  
			this.$mobile_footer.html( $template_html );

		};

		/**
		 * Maybe show the footer when form is in view.
		 */
		this.maybeShowFooter = function() {
			let show = self.$scroll_selector.length && self.$mobile_footer.children().length && self.isInViewport( self.$scroll_selector );
			self.$mobile_footer.toggle( show );
		};

		/**
		 * Trigger an update of the footer.
		 */
		this.relayUpdate = function(e, container) {
			$( document ).trigger( 'wc-mnm-update-mobile-footer', [ container ] );
		};

		/**
		 * Maybe show the footer when form is in view.
		 */
		this.handleValidForm = function() {
			let show = self.$scroll_selector.length && self.$mobile_footer.children().length && self.isInViewport( self.$scroll_selector );
			self.$mobile_footer.toggle( show );
		};

		/**
		 * Maybe show the footer when form is in view.
		 */
		this.handleNotValidForm = function() {
			let show = self.$scroll_selector.length && self.$mobile_footer.children().length && self.isInViewport( self.$scroll_selector );
			self.$mobile_footer.toggle( show );
		};	

		/**
		 * Maybe show the footer when form is in view.
		 */
		this.updateFooter = function(e, container) {

			let $mobile_message  = self.$mobile_footer.find( '.mnm_message' );
			let $mobile_reset    = self.$mobile_footer.find( '.mnm_reset' );
			let $mobile_progress = self.$mobile_footer.find( 'progress.mnm-container-progress' );
	
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
			$mobile_reset.toggle( container.api.get_container_size() > 0 );

		};

		/**
		 * Handle Add to cart button click
		 */
		this.relayAddToCart = function(e) {

			e.preventDefault();

			// Add a loading class to this button.
			$(this).addClass('loading adding');

			e.data.container.$mnm_original_button.trigger('click');
		};

		/**
		 * Handle reset click
		 */
		this.relayReset = function(e) {

			e.preventDefault();
			
			if (window.confirm(wc_mnm_params.i18n_confirm_reset)) {
				e.data.container.$mnm_form.trigger('wc-mnm-container-reset');
			}	
		};
			
		/**
		 * Handle cancel click - Only relevant in edit context.
		 */
		this.relayCancel = function(e) {
			e.preventDefault();
			$( '.woocommerce-MyAccount-content .wc-mnm-cancel-edit' ).trigger( 'click' );
		};
			
		/*-----------------------------------------------------------------*/
		/*  Variable Mix and Match support.                                                  */
		/*-----------------------------------------------------------------*/

		/**
		 * Handle cancel click - Only relevant in edit context.
		 */
		this.hideVariationLoaded = function() {
			$( this ).find( '.wc-mnm-edit-subscription-actions' ).hide();
		};
	
		/**
		 * Handle cancel click - Only relevant in edit context.
		 */
		this.handleVariationReset = function() {
			self.$mobile_footer.empty().hide();
			$( this ).find( '.wc-mnm-edit-subscription-actions' ).show();
		};
	


	} // End WC_MNM_Mobile_Styles.

	/*-----------------------------------------------------------------*/
	/*  Initialization.                                                */
	/*-----------------------------------------------------------------*/

	$( 'body' ).on( 'wc-mnm-initializing', function( e, container ) {
		let footer = new WC_MNM_Mobile_Styles( container );
		footer.initialize();
	});

} ) ( jQuery );