/* global jQuery:false */
/* global ROOM_STORAGE:false */

jQuery(document).ready(function() {
	"use strict";
	// Init Media manager variables
	ROOM_STORAGE['media_frame'] = null;
	ROOM_STORAGE['media_link'] = '';
	jQuery('.room_media_selector').on('click', function(e) {
		room_show_media_manager(this);
		e.preventDefault();
		return false;
	});


    // Point
    if(jQuery("#side-sortables .room_meta_box_hot_spot").length > 0) {
        var HotSpot = jQuery("#side-sortables .room_meta_box_hot_spot");
        var attachment = jQuery("#postimagediv #set-post-thumbnail img");
        var attachment_block = jQuery("#postimagediv #set-post-thumbnail");
        if(attachment.length > 0) {
            set_points(HotSpot,attachment,attachment_block);
        }
        HotSpot.change(function(){
            if(attachment.length > 0) {
                set_points(HotSpot,attachment,attachment_block);
            }
        });
    }

    function set_points(HotSpot,attachment,attachment_block){
        if(attachment.length > 0) {
            attachment_block.find('.point').remove();
            // point 1
            var left = HotSpot.find("input[name='room_options_hot_spot_left']").val();
            var top = HotSpot.find("input[name='room_options_hot_spot_top']").val();
            if (left && top) { attachment_block.append('<span class="point" style="top:'+top+'%;left:'+left+'%;">1</span>'); }

            // point 2
            var left_2 = HotSpot.find("input[name='room_options_hot_spot_left_2']").val();
            var top_2 = HotSpot.find("input[name='room_options_hot_spot_top_2']").val();
            if (left_2 && top_2) { attachment_block.append('<span class="point" style="top:'+top_2+'%;left:'+left_2+'%;">2</span>'); }

            // point 3
            var left_3 = HotSpot.find("input[name='room_options_hot_spot_left_3']").val();
            var top_3 = HotSpot.find("input[name='room_options_hot_spot_top_3']").val();
            if (left_3 && top_3) { attachment_block.append('<span class="point" style="top:'+top_3+'%;left:'+left_3+'%;">3</span>'); }

            // point 4
            var left_4 = HotSpot.find("input[name='room_options_hot_spot_left_4']").val();
            var top_4 = HotSpot.find("input[name='room_options_hot_spot_top_4']").val();
            if (left_4 && top_4) { attachment_block.append('<span class="point" style="top:'+top_4+'%;left:'+left_4+'%;">4</span>'); }

            // point 5
            var left_5 = HotSpot.find("input[name='room_options_hot_spot_left_5']").val();
            var top_5 = HotSpot.find("input[name='room_options_hot_spot_top_5']").val();
            if (left_5 && top_5) { attachment_block.append('<span class="point" style="top:'+top_5+'%;left:'+left_5+'%;">5</span>'); }
        }
    }
    
    
    
});

function room_show_media_manager(el) {
	"use strict";

	ROOM_STORAGE['media_link'] = jQuery(el);
	// If the media frame already exists, reopen it.
	if ( ROOM_STORAGE['media_frame'] ) {
		ROOM_STORAGE['media_frame'].open();
		return false;
	}

	// Create the media frame.
	ROOM_STORAGE['media_frame'] = wp.media({
		// Popup layout (if comment next row - hide filters and image sizes popups)
		frame: 'post',
		// Set the title of the modal.
		title: ROOM_STORAGE['media_link'].data('choose'),
		// Tell the modal to show only images.
		library: {
			type: 'image'
		},
		// Multiple choise
		multiple: ROOM_STORAGE['media_link'].data('multiple')===true ? 'add' : false,
		// Customize the submit button.
		button: {
			// Set the text of the button.
			text: ROOM_STORAGE['media_link'].data('update'),
			// Tell the button not to close the modal, since we're
			// going to refresh the page when the image is selected.
			close: true
		}
	});

	// When an image is selected, run a callback.
	ROOM_STORAGE['media_frame'].on( 'insert select', function(selection) {
		"use strict";
		// Grab the selected attachment.
		var field = jQuery("#"+ROOM_STORAGE['media_link'].data('linked-field')).eq(0);
		var attachment = null, attachment_url = '';
		if (ROOM_STORAGE['media_link'].data('multiple')===true) {
			ROOM_STORAGE['media_frame'].state().get('selection').map( function( att ) {
				attachment_url += (attachment_url ? "\n" : "") + att.toJSON().url;
			});
			var val = field.val();
			attachment_url = val + (val ? "\n" : '') + attachment_url;
		} else {
			attachment = ROOM_STORAGE['media_frame'].state().get('selection').first().toJSON();
			attachment_url = attachment.url;
			var sizes_selector = jQuery('.media-modal-content .attachment-display-settings select.size');
			if (sizes_selector.length > 0) {
				var size = room_get_listbox_selected_value(sizes_selector.get(0));
				if (size != '') attachment_url = attachment.sizes[size].url;
			}
		}
		field.val(attachment_url);
		if (field.siblings('img').length > 0) field.siblings('img').attr('src', attachment_url);
		field.trigger('change');
	});

	// Finally, open the modal.
	ROOM_STORAGE['media_frame'].open();
	return false;
}
