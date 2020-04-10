<?php
/**
 * Mix and Match Product Add to Cart button wrapper mobile template 
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/mobile-footer.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Kathy Darling
 * @package WooCommerce Mix and Match/Templates
 * @since   1.0.0
 * @version 1.0.0
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

?>
<div id="mnm-mobile-container" class="mnm_form mnm-mobile-container">

	<div class="mnm-mobile-content mnm_cart">

	<?php
	if ( $container->is_purchasable() ) { ?>

		<div class="column col-1">

			<div class="mnm_price"><?php echo $container->get_price_html(); ?></div>

			<div class="mnm_message">
				<ul class="msg mnm_message_content">
					<li><?php echo wc_mnm_get_quantity_message($container); ?></li>
				</ul>
			</div> 
			<div class="mnm_availability"><?php echo wc_get_stock_html( $container ); ?></div>

		</div><!--.column -->
	
		<div class="column col-2">

			<div class="mnm_button_wrap">
			
				<?php

				woocommerce_quantity_input(array(
					'min_value' => $container->is_sold_individually() ? 1 : apply_filters('woocommerce_quantity_input_min', 1, $container),
					'max_value' => $container->is_sold_individually() ? 1 : apply_filters('woocommerce_quantity_input_max', $container->backorders_allowed() ? '' : $container->get_stock_quantity(), $container),
					'input_value' => isset($_REQUEST['quantity']) ? wc_stock_amount(wp_unslash($_REQUEST['quantity'])) : $container->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
				));

				?>

				<input type="hidden" name="add-to-cart" value="<?php echo esc_attr($container->get_id()); ?>" />

				<button class="single_add_to_cart_button mnm_add_to_cart_button button alt"><?php echo $container->single_add_to_cart_text(); ?></button>

			</div>

			<a class="mnm_reset" style="" href="#"><?php _e( 'Reset configuration', 'woocommerce-mix-and-match-products' );?></a>            

		 </div><!--.column -->
		<?php
	} else {
		echo '<div class="mnm_container_unavailable woocommerce-info">' . __('This product is currently unavailable.', 'woocommerce-mix-and-match-products') . '</div>';
	} ?>	

	</div>

</div><!-- .mnm-mobile-container -->