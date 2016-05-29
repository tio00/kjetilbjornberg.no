/* global room_color_schemes, room_dependencies, Color */
/**
 * Add a listener to the Color Scheme control to update other color controls to new values/defaults.
 * Also trigger an update of the Color Scheme CSS when a color is changed.
 */

( function( api ) {
	"use strict";
	var cssTemplate = wp.template( 'room-color-scheme' ),
		updateCSS = true;

	// Set initial state of controls
	api.bind('ready', function() {
		jQuery('#customize-theme-controls .control-section').each(function () {
			room_customizer_check_dependencies(jQuery(this));
		});
	});
	
	// On change any control - check for dependencies
	api.bind('change', function(obj) {
		room_customizer_check_dependencies(jQuery('#customize-theme-controls #customize-control-'+obj.id).parents('.control-section'));
		room_customizer_refresh_preview(obj);
	});

	// Check for dependencies
	function room_customizer_check_dependencies(cont) {
		"use strict";
		cont.find('.customize-control').each(function() {
			"use strict";
			var id = jQuery(this).attr('id');
			if (id == undefined) return;
			id = id.replace('customize-control-', '');
			var field = jQuery(this).find('[data-customize-setting-link="'+id+'"]');
			var value = field.attr('type') != 'checkbox' || field.get(0).checked ? field.val() : '';
			var depend = false;
			for (var fld in room_dependencies) {
				if (fld == id) {
					depend = room_dependencies[id];
					break;
				}
			}
			if (depend) {
				var dep_cnt = 0, dep_all = 0;
				var dep_cmp = typeof depend.compare != 'undefined' ? depend.compare.toLowerCase() : 'and';
				var fld=null, val='';
				for (var i in depend) {
					if (i == 'compare') continue;
					dep_all++;
					fld = cont.find('[data-customize-setting-link="'+i+'"]');
					if (fld.length > 0) {
						val = fld.attr('type')=='checkbox' || fld.attr('type')=='radio' 
									? cont.find('[data-customize-setting-link="'+i+'"]:checked').val()
									: fld.val();
						if (val===undefined) val = '';
						for (var j in depend[i]) {
							if ( 
								   (depend[i][j]=='not_empty' && val!='') 										// Main field value is not empty - show current field
								|| (depend[i][j]=='is_empty' && val=='')										// Main field value is empty - show current field
								|| (val!='' && val.indexOf(depend[i][j])==0)									// Main field value equal to specified value - show current field
								|| (val!='' && depend[i][j].charAt(0)=='^' && val.indexOf(depend[i][j].substr(1))==-1)	// Main field value not equal to specified value - show current field
							) {
								dep_cnt++;
								break;
							}
						}
					}
					if (dep_cnt > 0 && dep_cmp == 'or')
						break;
				}
				if ((dep_cnt > 0 && dep_cmp == 'or') || (dep_cnt == dep_all && dep_cmp == 'and')) {
					jQuery(this).show().removeClass('room_options_no_use');
				} else {
					jQuery(this).hide().addClass('room_options_no_use');
				}
			}
		});
	}

	// Refresh preview area on change any control
	function room_customizer_refresh_preview(obj) {
		"use strict";
		if (obj.transport!='postMessage') return;
		var id = obj.id, val = obj();
		var processed = false;
		// Update the CSS whenever a color setting is changed.
		if (id == 'color_scheme') {
			processed = true;
			room_customizer_change_color_scheme(val);
		} else {
			for (var opt in room_color_schemes['default'].colors) {
				if (opt == id) {
					room_customizer_update_css();
					processed = true;
					break;
				}
			}
		}
		// Send message to previewer
		if (!processed) {
			api.previewer.send( 'refresh-other-controls', {id: id, value: val} );
		}
	}
	

	// Change color scheme - update colors and generate css
	function room_customizer_change_color_scheme(value) {
		"use strict";
		updateCSS = false;
		for (var opt in room_color_schemes[value].colors) {
			api( opt ).set( room_color_schemes[value].colors[opt] );
			api.control( opt ).container.find( '.color-picker-hex' )
				.data( 'data-default-color', room_color_schemes[value].colors[opt] )
				.wpColorPicker( 'defaultColor', room_color_schemes[value].colors[opt] );
		}
		updateCSS = true;
		room_customizer_update_css();
	}
	
	// Generate the CSS for the current Color Scheme and send it to the preview window
	function room_customizer_update_css() {
		"use strict";

		if (!updateCSS) return;
		
		var scheme = api('color_scheme')(),
			simple = api('color_settings')()=='simple',
			colors = [];

		// Merge in color scheme overrides.
		for (var opt in room_color_schemes[scheme].colors) {
			colors[opt] = api(opt)();
		}
		// If color_settings==simple - set alter_link equal to text_link
		if (simple && colors['text_link']!=room_color_schemes[scheme].colors['text_link']) {

			colors['alter_link'] = colors['text_link'];
			api('alter_link').set( colors['text_link'] );
			api.control( 'alter_link' ).container.find( '.color-picker-hex' )
				.data( 'data-default-color', colors['text_link'] )
				.wpColorPicker( 'defaultColor', colors['text_link'] );

			colors['alter_hover'] = colors['text_hover'];
			api('alter_hover').set( colors['text_hover'] );
			api.control( 'alter_hover' ).container.find( '.color-picker-hex' )
				.data( 'data-default-color', colors['text_hover'] )
				.wpColorPicker( 'defaultColor', colors['text_hover'] );
		}
		// Make theme specific colors and tints
		colors.text_link_alpha = Color( colors.text_link ).toCSS( 'rgba', 0.6 );
		colors.bg_color_alpha = Color( colors.bg_color ).toCSS( 'rgba', 0.6 );
		colors.alter_bg_color_alpha = Color( colors.alter_bg_color ).toCSS( 'rgba', 0.7 );
		colors.alter_bd_color_alpha = Color( colors.alter_bd_color ).toCSS( 'rgba', 0.6 );
		colors.alter_link_alpha = Color( colors.alter_link ).toCSS( 'rgba', 0.85 );

		var css = cssTemplate( colors );

		api.previewer.send( 'refresh-color-scheme-css', css );
	}

} )( wp.customize );
