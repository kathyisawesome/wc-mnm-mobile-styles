<?php
/**
 * Footer Status UI backbone template
 *
 * This is a javascript-based template for single variations (see https://codex.wordpress.org/Javascript_Reference/wp.template).
 * The values will be dynamically replaced.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce Mix and Match/Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

?>

<div id="mnm-mobile-container" class="mnm-mobile-container mnm_form" style="display:none;"></div>

<script type="text/template" id="tmpl-wc-mnm-mobile-footer">
    <div class="mnm-mobile-content mnm_cart alignwide">

    <# if ( data.is_purchasable && data.is_in_stock ) { #>

            <div class="column col-1">

                <div class="mnm_price"></div>

                <a href="#" class="mnm_reset" style="display: none;"><?php echo esc_html_x( 'Clear selections', '[Frontend]', 'wc-mnm-mobile-styles' ); ?></a>

                <div aria-live="polite" role="status" class="mnm_message">
                    <ul class="msg mnm_message_content">
                        <li></li>
                    </ul>
                </div> 
                <div class="mnm_availability">{{{ data.stock_html }}}</div>

            </div><!--.column -->
        
            <div class="column col-2">

                <div class="mnm_button_wrap">
                    
                    <button data-product_id="{{{ data.container_id }}}" class="single_add_to_cart_button button alt<?php echo esc_attr( WC_MNM_Core_Compatibility::wp_theme_get_element_class_name( 'button' ) ); ?>">{{{ data.button_text }}}</button>

                    <# if ( data.context === 'edit' ) { #>
                        <div class="wc-mnm-edit-subscription-actions woocommerce-cart-form">
                            <div class="actions">
                            <button class="button wc-mnm-cancel-edit"><?php echo esc_html_x( 'Cancel edit', '[Frontend]', 'wc-mnm-mobile-styles' ); ?></button>
                            </div>
                        </div>

                    <# } #>
                </div>

            </div><!--.column -->
    
    <# } else if ( data.is_purchasable && ! data.is_in_stock ) { #>

        <div class="mnm_availability">{{{ data.stock_html }}}</div>

    <# } else { #>

        <div class="mnm_container_unavailable"><?php esc_html_e( 'This product is currently unavailable.', 'wc-mnm-mobile-styles' ); ?></div>

    <# } #>

        <a href="#mnm-child-items" class="screen-reader-text"><?php esc_html_e( 'Return to selections', 'wc-mnm-mobile-styles' ); ?></a>

    </div>

</script>
