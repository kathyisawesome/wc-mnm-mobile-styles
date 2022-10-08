<?php
/*
* Plugin Name: WooCommerce Mix and Match - Mobile Styles
* Plugin URI: https://woocommerce.com/products/woocommerce-mix-and-match-products/
* Description: Turns the Mix and Match counter message into a sticky footer.
* Version: 2.0.0-beta.1
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

	const VERSION = '2.0.0-beta.1';
	const REQ_MNM_VERSION = '2.2.0-beta.3';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public static $version = '2.0.0-beta.1';


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
		add_action( 'wc_mnm_before_child_items', array( __CLASS__, 'add_target_link' ) );
		add_action( 'wc_mnm_after_child_items', array( __CLASS__, 'add_skip_link' ), 101 );
        add_action( 'woocommerce_mix-and-match_add_to_cart', array( __CLASS__, 'add_template_to_footer' ), 99 );
		
		/**
		 * Grouped MNM support.
		 */
		add_action( 'woocommerce_grouped-mnm_add_to_cart', array( __CLASS__, 'grouped_add_template_to_footer' ), 99 );
		add_filter( 'wc_mnm_grouped_add_to_cart_fragments', array( __CLASS__, 'ajax_load_footer' ), 10, 2 );
    
		/**
		 * Variable MNM support.
		 */
		add_action( 'woocommerce_variable-mix-and-match_add_to_cart', array( __CLASS__, 'add_template_to_footer' ), 99 );
		add_filter( 'woocommerce_available_variation', array( __CLASS__, 'available_variation' ), 10, 3 );

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

		wp_register_script( 'wc_mnm_mobile', $script_url, array( 'wc-add-to-cart-mnm' ), $script_version, true );

	}
	
	/**
	 * Add a target element for screen reader.
	 * 
	 * @since 1.3.0
	 */
	public static function add_target_link() {
		echo '<div id="mnm-child-items"></div>';
	}
		
	/**
	 * Add a skip link for screen reader.
	 * 
	 * @since 1.3.0
	 */
	public static function add_skip_link() {
		echo '<a href="#mnm-mobile-container" class="screen-reader-text">' . esc_html__( 'Skip to add to cart.', 'wc-mnm-mobile-styles' ) . '</a>';
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

	/**
	 * Add footer HTML to variation data.
	 * 
	 * @param array $data
	 * @param WC_Product_Variable_Mix_and_Match
	 * @param WC_Product_Mix_and_Match_Variation
	 */
	public static function available_variation( $data, $product, $variation ) {

		// @todo - should we always add this? Seems like it could be a lot of data in the HTML.
		if ( $variation->is_type( 'mix-and-match-variation' ) && $product->is_type( 'variable-mix-and-match' ) ) {
			$fragments = self::ajax_load_footer( array(), $variation );
			$data[ 'mix_and_match_footer_html' ] = current( $fragments );
		}

		return $data;

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