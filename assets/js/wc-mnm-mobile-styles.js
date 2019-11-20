jQuery( document ).ready( function( $ ) {

	var wc_mnm_styles_timer = null;

	$( "form.mnm_form" ).on( 'wc-mnm-form-updated', function( event, container ) {

		container.$mnm_message.addClass( 'mnm_quantity_updated' );

		if (wc_mnm_styles_timer) {
			clearTimeout(wc_mnm_styles_timer); //cancel the previous timer.
			wc_mnm_styles_timer = null;
		}
		wc_mnm_styles_timer = setTimeout(function(){
			container.$mnm_message.removeClass( 'mnm_quantity_updated');
		}, 2000 );

	} );

} );


