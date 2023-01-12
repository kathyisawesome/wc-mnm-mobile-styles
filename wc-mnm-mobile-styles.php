<?php
/**
 * Plugin Name: WooCommerce Mix and Match - Mobile Styles
 * Plugin URI: https://woocommerce.com/products/woocommerce-mix-and-match-products/
 * Description: Turns the Mix and Match counter message into a sticky footer.
 * Version: 2.0.0-rc.3
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com/
 *
 * Text Domain: wc-mnm-mobile-styles
 * Domain Path: /languages/
 *
 * Requires at least: 5.1.0
 * Tested up to: 6.0.0
 *
 * WC requires at least: 6.0.0
 * WC tested up to: 7.0.0
 *
 * GitHub Plugin URI: https://github.com/kathyisawesome/wc-mnm-mobile-styles
 * Primary Branch: trunk
 * Release Asset: true
 *
 * Copyright: © 2019 Kathy Darling
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_MNM_Mobile_Styles {

	const REQ_MNM_VERSION = '2.2.0-beta.3';
	const VERSION = '2.0.0-rc.3';

	/**
	 * The Mix and Match product object.
	 *
	 * @var string
	 */
	private static $container = false;
	

	/**
	 * Fire in the hole!
	 */
	public static function init() {

		// Quietly quit if Mix and Match is not active or below 2.0.
		if ( ! function_exists( 'wc_mix_and_match' ) || version_compare( wc_mix_and_match()->version, self::REQ_MNM_VERSION ) < 0 ) {
			return false;
		}

		/**
		 * Display.
		 */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 100 );

		// Load template.
        add_action( 'woocommerce_mix-and-match_add_to_cart', array( __CLASS__, 'add_template_to_footer' ), 99 );
		
		/**
		 * Grouped MNM support.
		 */
		add_action( 'woocommerce_grouped-mnm_add_to_cart', array( __CLASS__, 'add_template_to_footer' ), 99 );
    
		/**
		 * Variable MNM support.
		 */
		add_action( 'woocommerce_variable-mix-and-match_add_to_cart', array( __CLASS__, 'add_template_to_footer' ), 99 );

		/**
		 * Subscription editing.
		 */
		add_action( 'wc_mnm_subscription_editing_enqueue_scripts', array( __CLASS__, 'add_template_to_footer' ), 99 );

	}


	/*-----------------------------------------------------------------------------------*/
	/* Front End Display */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register the scripts and styles
	 */
	public static function register_scripts() {
		$suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '': '.min';
		$style_path    = 'assets/css/frontend/wc-mnm-mobile-styles' . $suffix . '.css';
		$style_url     = self::plugin_url() . $style_path;
		$style_version = WC_Mix_and_Match()->get_file_version( self::plugin_path() . $style_path, self::VERSION );

		wp_enqueue_style( 'wc_mnm_mobile', $style_url, array( 'wc-mnm-frontend' ), $style_version );
		wp_style_add_data( 'wc_mnm_mobile', 'rtl', 'replace' );

		if ( $suffix ) {
			wp_style_add_data( 'wc_mnm_mobile', 'suffix', '.min' );
		}

		$script_path    = 'assets/js/frontend/wc-mnm-mobile-styles' . $suffix . '.js';
		$script_url     = self::plugin_url() . $script_path;
		$script_version = WC_Mix_and_Match()->get_file_version( self::plugin_path() . $script_path, self::VERSION );

		wp_register_script( 'wc_mnm_mobile', $script_url, array( 'wc-add-to-cart-mnm', 'wp-util' ), $script_version, true );

	}
	
	/**
	 * Add a target element for screen reader.
	 * 
	 * @since 2.0.0
	 */
	public static function add_target_link() {
		echo '<div id="mnm-child-items" class="screen-reader-text"></div>';
	}
		
	/**
	 * Add a skip link for screen reader.
	 * 
	 * @since 2.0.0
	 */
	public static function add_skip_link() {

		if ( doing_action( 'wc_ajax_mnm_get_edit_container_order_item_form' )
			|| ( isset( $_POST['validation_context'] ) && wc_clean( $_POST['validation_context'] ) === 'edit' )
		) {
			$button_text = esc_html__( 'Skip to update button.', 'wc-mnm-mobile-styles' );
		} else {
			$button_text = esc_html__( 'Skip to add to cart button', 'wc-mnm-mobile-styles' );
		}
		echo '<a href="#mnm-mobile-container" class="AAAAAscreen-reader-text">' . esc_html( $button_text ) . '</a>';
	}

	/**
	 * Show different button depending on context
	 * 
	 * @since 2.0.0
	 * 
     * @param WC_Product_Mix_and_Match $container
	 * @param WC_Order_Item $order_item - FALSE in add-to-cart context
	 * @param WC_Subscription $subscription - FALSE in add-to-cart context
	 * @param string $context
	 */
	public static function container_data_attributes( $attributes, $container ) {
		$attributes[ 'stock_html' ]  = wc_get_stock_html( $container );
		return $attributes;
	}


	/**
	 * Add the mobile template
	 */
	public static function add_template_to_footer() {

		// Accessibility.
		add_action( 'wc_mnm_before_child_items', array( __CLASS__, 'add_target_link' ) );
		add_action( 'wc_mnm_after_child_items', array( __CLASS__, 'add_skip_link' ), 101 );

		// Add additional data attributes.
		add_filter( 'wc_mnm_container_data_attributes', array( __CLASS__, 'container_data_attributes' ), 10, 2 );

		wp_enqueue_script( 'wc_mnm_mobile' );
		add_action( 'wp_footer', array( __CLASS__, 'footer_template' ), 99 );
	}


	/**
	 * Add the mobile template
	 */
	public static function footer_template( $container = false, $container_item = false, $subscription = 'false', $context = 'add-to-cart' ) {

		wc_get_template(
			'single-product/mnm/mobile-footer-tmpl.php',
			array(),
			'',
			self::plugin_path() . '/templates/'
		);

	}

	/*-----------------------------------------------------------------------------------*/
	/*  Helper Functions                                                                 */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public static function plugin_url() {
		return trailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public static function plugin_path() {
		return trailingslashit( plugin_dir_path( __FILE__ ) );
	}

}
add_action( 'plugins_loaded', array( 'WC_MNM_Mobile_Styles', 'init' ), 20 );