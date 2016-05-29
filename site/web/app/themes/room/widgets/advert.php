<?php
/**
 * Theme Widget: Advertisement
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Load widget
if (!function_exists('room_widget_advert_load')) {
	add_action( 'widgets_init', 'room_widget_advert_load' );
	function room_widget_advert_load() {
		register_widget( 'room_widget_advert' );
	}
}

// Widget Class
class room_widget_advert extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_advert', 'description' => esc_html__('Advertisement block', 'room') );
		parent::__construct( 'room_widget_advert', esc_html__('ThemeREX - Advertisement block', 'room'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$fullwidth = isset($instance['fullwidth']) ? $instance['fullwidth'] : '';
		$advert_image = isset($instance['advert_image']) ? $instance['advert_image'] : '';
		$advert_link = isset($instance['advert_link']) ? $instance['advert_link'] : '';
		$advert_code = isset($instance['advert_code']) ? $instance['advert_code'] : '';

		// Before widget (defined by themes)
		if ( room_param_is_on($fullwidth) ) $before_widget = str_replace('class="', 'class="widget_fullwidth ', $before_widget);

		echo trim($before_widget);

		// Display the widget title if one was input (before and after defined by themes)
		if ($title)	echo trim($before_title . $title . $after_title);

		// Widget body
		if ($advert_image!='') {
			if ($advert_image > 0) {
				$attach = wp_get_attachment_image_src( $advert_image, room_get_thumb_size('med') );
				if (isset($attach[0]) && $attach[0]!='')
					$advert_image = $attach[0];
			}
			$attr = room_getimagesize($advert_image);
			echo (!empty($advert_link) ? '<a href="' . esc_url($advert_link) . '"' : '<span') . ' class="image_wrap"><img src="' . esc_url($advert_image) . '" alt="' . esc_attr($title) . '"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>' . ($advert_link!='' ? '</a>': '</span>');
		}
		if ($advert_code!='') {
			echo force_balance_tags(do_shortcode($advert_code));
		}

		// After widget (defined by themes)
		echo trim($after_widget);
	}

	// Update the widget settings.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['fullwidth'] = strip_tags( $new_instance['fullwidth'] );
		$instance['advert_image'] = strip_tags( $new_instance['advert_image'] );
		$instance['advert_link'] = strip_tags( $new_instance['advert_link'] );
		$instance['advert_code'] = $new_instance['advert_code'];

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'fullwidth' => '1',
			'advert_image' => '',
			'advert_link' => '',
			'advert_code' => ''
			)
		);
		$title = $instance['title'];
		$fullwidth = $instance['fullwidth'];
		$advert_image = $instance['advert_image'];
		$advert_link = $instance['advert_link'];
		$advert_code = $instance['advert_code'];
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('fullwidth')); ?>_1"><?php esc_html_e('Widget size:', 'room'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('fullwidth')); ?>_1" name="<?php echo esc_attr($this->get_field_name('fullwidth')); ?>" value="1" <?php echo (1==$fullwidth ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('fullwidth')); ?>_1"><?php esc_html_e('Fullwidth', 'room'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('fullwidth')); ?>_0" name="<?php echo esc_attr($this->get_field_name('fullwidth')); ?>" value="0" <?php echo (0==$fullwidth ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('fullwidth')); ?>_0"><?php esc_html_e('Boxed', 'room'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'advert_image' )); ?>"><?php echo wp_kses_data( __('Image source URL:<br />(leave empty if you paste advert code)', 'room') ); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'advert_image' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'advert_image' )); ?>" value="<?php echo esc_attr($advert_image); ?>" class="widgets_param_fullwidth widgets_param_img_selector" />
            <?php
			echo trim(room_show_custom_field($this->get_field_id( 'advert_media' ), array('type'=>'mediamanager', 'linked_field_id'=>$this->get_field_id( 'advert_image' )), null));
			if ($advert_image) {
			?>
	            <br /><br /><img src="<?php echo esc_url($advert_image); ?>" class="widgets_param_maxwidth" alt="" />
			<?php
			}
			?>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'advert_link' )); ?>"><?php esc_html_e('Image link URL:<br />(leave empty if you paste advert code)', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'advert_link' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'advert_link' )); ?>" value="<?php echo esc_attr($advert_link); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'advert_code' )); ?>"><?php esc_html_e('or paste Advert Widget HTML Code:', 'room'); ?></label>
			<textarea id="<?php echo esc_attr($this->get_field_id( 'advert_code' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'advert_code' )); ?>" rows="5" class="widgets_param_fullwidth"><?php echo htmlspecialchars($advert_code); ?></textarea>
		</p>
	<?php
	}
}



// trx_widget_advert
//-------------------------------------------------------------
/*
[trx_widget_advert id="unique_id" title="Widget title" fullwidth="0|1" image="image_url" link="Image_link_url" code="html & js code"]
*/
if ( !function_exists( 'room_sc_widget_advert' ) ) {
	function room_sc_widget_advert($atts, $content=null){	
		$atts = room_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"image" => "",
			"link" => "",
			"code" => "",
			"fullwidth" => "off",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts));
		extract($atts);
		$type = 'room_widget_advert';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$atts['advert_image'] = $image;
			$atts['advert_link'] = $link;
			$atts['advert_code'] = $code;
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_advert' 
								. (room_exists_visual_composer() ? ' vc_widget_advert wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, room_prepare_widgets_args(room_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_advert', 'widget_advert') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('room_shortcode_output', $output, 'trx_widget_advert', $atts, $content);
	}
	room_require_shortcode("trx_widget_advert", "room_sc_widget_advert");
}


// Add [trx_widget_advert] in the VC shortcodes list
if (!function_exists('room_sc_widget_advert_add_in_vc')) {
	function room_sc_widget_advert_add_in_vc() {

		if (!room_exists_visual_composer()) return;
		
		vc_map( array(
				"base" => "trx_widget_advert",
				"name" => esc_html__("Widget Advertisement", 'room'),
				"description" => wp_kses_data( __("Insert widget with advertisement banner or any HTML and/or Javascript code", 'room') ),
				"category" => esc_html__('Content', 'room'),
				"icon" => 'icon_trx_widget_advert',
				"class" => "trx_widget_advert",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => esc_html__("Widget title", 'room'),
						"description" => wp_kses_data( __("Title of the widget", 'room') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "image",
						"heading" => esc_html__("Image", 'room'),
						"description" => wp_kses_data( __("Select or upload image or write URL from other site for the banner (leave empty if you paste advert code)", 'room') ),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "link",
						"heading" => esc_html__("Banner's link", 'room'),
						"description" => wp_kses_data( __("Link URL for the banner (leave empty if you paste advert code)", 'room') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "code",
						"heading" => esc_html__("or paste Advert Widget HTML Code", 'room'),
						"description" => wp_kses_data( __("Widget's HTML and/or JS code", 'room') ),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					room_get_vc_param('id'),
					room_get_vc_param('class'),
					room_get_vc_param('css')
				)
			) );
			
		class WPBakeryShortCode_Trx_Widget_Advert extends WPBakeryShortCode {}

	}
	add_action('after_setup_theme', 'room_sc_widget_advert_add_in_vc');
}
?>