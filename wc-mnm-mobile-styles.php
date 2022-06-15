<?php
/*
* Plugin Name: WooCommerce Mix and Match - Mobile Styles
* Plugin URI: https://woocommerce.com/products/woocommerce-mix-and-match-products/
* Description: Turns the Mix and Match counter message into a sticky footer.
* Version: 1.2.0
* Author: Kathy Darling
* Author URI: http://kathyisawesome.com/
*
* Text Domain: wc-mnm-mobile-styles
* Domain Path: /languages/
*
* Requires at least: 5.1.0
* Tested up to: 6.0.0
*
* WC requires at least: 5.9.0
* WC tested up to: 6.6.0
*
* GitHub Plugin URI: kathyisawesome/wc-mnm-mobile-styles
* GitHub Plugin URI: https://github.com/kathyisawesome/wc-mnm-mobile-styles
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

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public static $version = '1.2.0';


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

		// Quietly quit if MNM is not active.
		if ( ! function_exists( 'wc_mix_and_match' ) ) {
			return false;
		}

		/**
		 * Display.
		 */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 20 );
        add_action( 'woocommerce_mix-and-match_add_to_cart', array( __CLASS__, 'add_template_to_footer' ), 99 );
        
		/**
		 * Grouped MNM support.
		 */      
        add_action( 'woocommerce_grouped-mnm_add_to_cart', array( __CLASS__, 'grouped_add_template_to_footer' ), 99 );
        add_filter( 'wc_mnm_grouped_add_to_cart_fragments', array( __CLASS__, 'ajax_load_footer' ), 10, 2 );
        
	}


	/*-----------------------------------------------------------------------------------*/
	/* Front End Display */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Register the scripts and styles
	 */
	public static function register_scripts() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style( 'wc_mnm_mobile', self::plugin_url() . '/assets/css/frontend/wc-mnm-mobile-styles' . $suffix . '.css', array( 'wc-mnm-frontend' ), self::$version );
		wp_style_add_data( 'wc_mnm_mobile', 'rtl', 'replace' );

		wp_register_script( 'wc_mnm_mobile', self::plugin_url() . '/assets/js/frontend/wc-mnm-mobile-styles' . $suffix . '.js', array( 'wc-add-to-cart-mnm' ), self::$version, true );

	}

	/**
	 * Add the mobile template
	 */
	public static function add_template_to_footer() {

        global $product;

		wp_enqueue_script( 'wc_mnm_mobile' );

		self::$container = $product;

		add_action( 'wp_footer', array( __CLASS__, 'footer_template' ), 99 );
	}

	/**
	 * Add the mobile template
	 */
	public static function grouped_add_template_to_footer() {

        global $product;

        $selection       = get_query_var( 'mnm' );
        $has_selection   = in_array( intval( $selection ), $product->get_children() );
        $current_product = wc_get_product( $selection );

        self::$container = $has_selection && $current_product ? $current_product : $product;

		wp_enqueue_script( 'wc_mnm_mobile' );

		add_action( 'wp_footer', array( __CLASS__, 'footer_template' ), 99 );
	}

	/**
	 * Add the mobile template
	 */
	public static function footer_template( $container = false ) {

        // Default to stashed product.
        if ( ! $container ) {
            $container = self::$container;
        }

		wc_get_template(
			'single-product/mnm/mobile-footer.php',
			array(
				'container' => $container,
			),
			'',
			self::plugin_path() . '/templates/'
		);

		self::$container = false;

	}

    /**
	 * Ajax load the mobile template
     * 
     * @param array $fragments array of [element => HTML content]
     * @param WC_Product_Mix_and_Match $container
     * @return array
	 */
	public static function ajax_load_footer( $fragments, $container ) {
        ob_start();
		self::footer_template( $container );
		$footer = ob_get_clean();

        $fragments[ '#mnm-mobile-container' ] = $footer;
        return $fragments;
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
add_action( 'plugins_loaded', array( 'WC_MNM_Mobile_Styles', 'init' ), 20 );