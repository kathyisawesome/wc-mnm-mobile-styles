jQuery( document ).ready( function( $ ) {

	$.fn.isInViewport = function() {
		var elementTop = $(this).offset().top;
		var elementBottom = elementTop + $(this).outerHeight();var viewportTop = $(window).scrollTop();
		var viewportBottom = viewportTop + $(window).height();return elementBottom > viewportTop && elementTop < viewportBottom;
	};

	$(window).on('resize scroll', function() {
	
		if( $( "form.mnm_form .products" ).isInViewport() ) {
			$( "form.mnm_form" ).find('.mnm_message').addClass('fixed');
		} else {
			$( "form.mnm_form" ).find('.mnm_message').removeClass('fixed');
		}
	});

} );


