/**
 * Live-update changed settings in real time in the Customizer preview.
 */
( function( $ ) {
	"use strict";
	var $style = $('#room-color-scheme-css'),
		api = wp.customize;

	// Prepare inline styles in the preview window
	if ( $style.length == 0 ) {
		$style = $('head').append( '<style type="text/css" id="room-color-scheme-css" />' ).find('#room-color-scheme-css');
	}

	// Refresh preview without page reload when controls are changed
	api.bind( 'preview-ready', function() {
		"use strict";

		// Change css when color scheme or separate color controls are changed
		api.preview.bind( 'refresh-color-scheme-css', function( css ) {
			"use strict";
			$style.html( css );
		} );

		// Any other controls are changed
		api.preview.bind( 'refresh-other-controls', function( obj ) {
			"use strict";
			var id = obj.id, val = obj.value;

			if (id == 'body_style') {
				$('body').removeClass('body_style_boxed body_style_wide body_style_fullwide').addClass('body_style_'+val);

			} else if (id.indexOf('expand_content') == 0) {
				if ( $('body').hasClass('sidebar_hide') ) {
					if (val == 1)
						$('body').addClass('expand_content');
					else
						$('body').removeClass('expand_content');
				}

			} else if (id.indexOf('sidebar_position') == 0) {
				if ($('body').hasClass('sidebar_show'))
					$('body').removeClass('sidebar_left sidebar_right').addClass('sidebar_'+val);

			} else if (id == 'blogname') {
				$('.logo .logo_text').html( room_prepare_macros('{{' + val + '}}') );

			} else if (id == 'blogdescription') {
				$( '.logo .logo_slogan' ).html( val );

			} else if (id == 'copyright') {
				$( '.copyright_text' ).html( room_prepare_macros('{{' + val + '}}') );

			} else if (id == 'color_scheme') {
				$('body').removeClass('scheme_default scheme_dark').addClass('scheme_'+val);

			}
		} );
				
	} );

} )( jQuery );
