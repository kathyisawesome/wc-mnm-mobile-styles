<?php
/**
 * Mix and Match Product Add to Cart button wrapper mobile template 
 *
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="mnm-mobile-container">
    <div class="mnm-mobile-nav"><a href="#" class="mnm-mobile-btn-slide"></a></div>
    <div class="mnm-mobile-content">

        <div class="mnm_cart mnm_data cart" <?php echo $product->get_data_attributes(); ?>>
            <?php
            if ($product->is_purchasable() ) {
                /**
                 * wc_mnm_before_add_to_cart_button_wrap hook.
                 */
                do_action('wc_mnm_before_add_to_cart_button_wrap');
                ?>
                <div class="mnm_button_wrap">

                    <div class="mnm_price"></div>

                    <div class="mnm_message">
                        <div>
                            <ul class="msg mnm_message_content">
                                <li><?php echo wc_mnm_get_quantity_message($product); ?></li>
                            </ul>
                        </div>
                    </div> 
                    <div class="mnm_availability">

                        <?php
                        // Availability html.
                        echo wc_get_stock_html($product);
                        ?>

                    </div>
                    <?php
                    /**
                     * woocommerce_before_add_to_cart_button hook.
                     */
                    do_action('woocommerce_before_add_to_cart_button');

                    /**
                     * @since 1.4.0.
                     */
                    do_action('woocommerce_before_add_to_cart_quantity');

                    woocommerce_quantity_input(array(
                        'min_value' => $product->is_sold_individually() ? 1 : apply_filters('woocommerce_quantity_input_min', 1, $product),
                        'max_value' => $product->is_sold_individually() ? 1 : apply_filters('woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product),
                        'input_value' => isset($_REQUEST['quantity']) ? wc_stock_amount(wp_unslash($_REQUEST['quantity'])) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
                    ));

                    /**
                     * @since 1.4.0.
                     */
                    do_action('woocommerce_after_add_to_cart_quantity');
                    ?>

                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" />

                    <button type="submit" class="single_add_to_cart_button mnm_add_to_cart_button button alt"><?php echo $product->single_add_to_cart_text(); ?></button>

                    <?php
                    /**
                     * woocommerce_after_add_to_cart_button hook.
                     */
                    do_action('woocommerce_after_add_to_cart_button');
                    ?>                    
                </div>
                <?php
            } else {
                echo '<div class="mnm_container_unavailable woocommerce-info">' . __('This product is currently unavailable.', 'woocommerce-mix-and-match-products') . '</div>';
            }
            ?>
        </div>

    </div>
</div><!-- .mnm-mobile-container -->