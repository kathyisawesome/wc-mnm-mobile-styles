<?php
/*
* Plugin Name: WooCommerce Mix and Match: Mobile Styles
* Plugin URI: https://woocommerce.com/products/woocommerce-mix-and-match-products/
* Description: Add some styles for mobile.
* Version: 1.0.0-beta-2
* Author: Kathy Darling
* Author URI: http://kathyisawesome.com/
*
* Text Domain: wc-mnm-mobile-styles
* Domain Path: /languages/
*
* Requires at least: 5.1.0
* Tested up to: 5.3.0
*
* WC requires at least: 3.7.0
* WC tested up to: 3.8.0
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

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public static $version = '1.0.0-beta-2';


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

		/*
		 * Display.
		 */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );
		add_action( 'woocommerce_mnm_add_to_cart_wrap', array( __CLASS__, 'add_template_to_footer' ), 99 );
		
	}


	/*-----------------------------------------------------------------------------------*/
	/* Front End Display */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Register the scripts and styles
	 */
	public static function register_scripts() {
		wp_enqueue_style( 'wc_mnm_mobile', self::plugin_url() . '/assets/css/wc-mnm-mobile-styles.css', array( 'wc-mnm-frontend' ), self::$version );
		wp_style_add_data( 'wc_mnm_mobile', 'rtl', 'replace' );

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_register_script( 'wc_mnm_mobile', self::plugin_url() . '/assets/js/wc-mnm-mobile-styles' . $suffix . '.js', array( 'wc-add-to-cart-mnm' ), self::$version, true );

	}


	/**
	 * Load the script only when needed
	 */
	public static function enqueue_script() {
		
	}


	/**
	 * Add the mobile template
	 */
	public static function add_template_to_footer( $container ) {

		wp_enqueue_script( 'wc_mnm_mobile' );

		self::$container = $container;

		add_action( 'wp_footer', array( __CLASS__, 'footer_template' ), 99 );
	}

	/**
	 * Add the mobile template
	 */
	public static function footer_template() {

		wc_get_template(
			'single-product/mnm/mobile-footer.php',
			array(
				'container' => self::$container,
			),
			'',
			self::plugin_path() . '/templates/'
		);

		self::$container = false;

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
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public static function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}
add_action( 'woocommerce_mnm_loaded', array( 'WC_MNM_Mobile_Styles', 'init' ) );