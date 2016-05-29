//-------------------------------------------
// Meta Boxes manipulations
//-------------------------------------------
jQuery(document).ready(function() {
	"use strict";

	// jQuery Tabs
	jQuery('#room_meta_box_tabs').tabs();

	// Toggle inherit button and cover
	jQuery('#room_meta_box_tabs').on('click', '.room_meta_box_inherit_lock,.room_meta_box_inherit_cover', function (e) {
		"use strict";
		var parent = jQuery(this).parents('.room_meta_box_item');
		var inherit = parent.hasClass('room_meta_box_inherit_on');
		if (inherit) {
			parent.removeClass('room_meta_box_inherit_on').addClass('room_meta_box_inherit_off');
			parent.find('.room_meta_box_inherit_cover').fadeOut().find('input[type="hidden"]').val('');
		} else {
			parent.removeClass('room_meta_box_inherit_off').addClass('room_meta_box_inherit_on');
			parent.find('.room_meta_box_inherit_cover').fadeIn().find('input[type="hidden"]').val('inherit');
			
		}
		e.preventDefault();
		return false;
	});

});