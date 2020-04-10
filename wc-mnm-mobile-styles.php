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
	 * Fire in the hole!
	 */
	public static function init() {
		

		/*
		 * Display.
		 */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );
		add_action( 'woocommerce_mix-and-match_add_to_cart', array( __CLASS__, 'enqueue_script' ) );

		add_action( 'woocommerce_before_add_to_cart_button', array( __CLASS__, 'wrap_quantity_open' ) );
		add_action( 'woocommerce_after_add_to_cart_button', array( __CLASS__, 'wrap_quantity_close' ) );
		
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
		wp_enqueue_script( 'wc_mnm_mobile' );
	}


	/**
	 * Echo opening markup if necessary.
	 */
	public static function wrap_quantity_open() {
		global $product;
		if( $product instanceof WC_Product && $product->is_type( 'mix-and-match' ) ) {
			echo '</div><!--.mnm_button_wrap -->
			<div class="mnm_button_wrap">';
		}
	}

	/**
	 * Echo opening markup if necessary.
	 */
	public static function wrap_quantity_close() {
		global $product;		
		if( $product instanceof WC_Product && $product->is_type( 'mix-and-match' ) ) {
			echo '</div><!--.mnm_button_wrap -->';
		}
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