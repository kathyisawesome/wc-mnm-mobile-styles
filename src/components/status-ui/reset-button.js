/**
 * External dependencies
 */
import { _x } from '@wordpress/i18n';

export default function ResetButton( { container, totalQuantity } )
{
    if ( ! totalQuantity ) {
        return null;
    }

    const onResetConfig = ( event ) => {
        const productId = event.currentTarget.getAttribute('data-form_id');

        const form = document.querySelector(
            `form[data-product_id="${ productId }"]`
        );

    if (form ) {
        const resetButton = form.querySelector('.mnm_reset');
        if (resetButton ) {
            resetButton.click();
        }
    }
    };

    return (
    <button
    type="button"
    onClick={ onResetConfig }
    className="mnm_reset"
    data-form_id={ container.id }
    >
    { _x('Clear selections', '[Frontend]', 'wc-mnm-mobile-styles') }
    </button>
    );
}
