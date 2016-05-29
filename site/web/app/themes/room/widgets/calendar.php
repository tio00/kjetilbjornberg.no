<?php
/**
 * Theme Widget: Calendar
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Load widget
if (!function_exists('room_widget_calendar_load')) {
	add_action( 'widgets_init', 'room_widget_calendar_load' );
	function room_widget_calendar_load() {
		register_widget('room_widget_calendar');
	}
}

// Widget Class
class room_widget_calendar extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_calendar', 'description' => esc_html__('Standard WP Calendar with short week days', 'room'));
		parent::__construct( 'room_widget_calendar', esc_html__('ThemeREX - Calendar', 'room'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
		$weekdays = isset($instance['weekdays']) ? $instance['weekdays'] : 'short';
		
		$output = get_calendar($weekdays=='initial', false);

		if (!empty($output)) {
	
			// Before widget (defined by themes)
			echo trim($before_widget);
			
			// Display the widget title if one was input (before and after defined by themes)
			if ($title) echo trim($before_title . $title . $after_title);
	
			// Display widget body
			echo trim($output);
			
			// After widget (defined by themes)
			echo trim($after_widget);
		}
	}

	// Update the widget settings.
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['weekdays'] = !empty($new_instance['weekdays']) && $new_instance['weekdays']=='short' ? 'short' : 'initial';

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'weekdays' => 'short'
			)
		);
		$title = $instance['title'];
		$weekdays = $instance['weekdays'];
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'room'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('weekdays')); ?>_short"><?php esc_html_e('Week days:', 'room'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('weekdays')); ?>_short" name="<?php echo esc_attr($this->get_field_name('weekdays')); ?>" value="short" type="radio" <?php if ($weekdays=='short') echo ' checked="checked"'; ?> />
			<label for="<?php echo esc_attr($this->get_field_id('weekdays')); ?>_short"><?php esc_html_e('3 letters (Sun Mon Tue Wed Thu Fri Sat)', 'room'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('weekdays')); ?>_init" name="<?php echo esc_attr($this->get_field_name('weekdays')); ?>" value="initial" type="radio" <?php if ($weekdays=='initital') echo ' checked="checked"'; ?> />
			<label for="<?php echo esc_attr($this->get_field_id('weekdays')); ?>_init"><?php esc_html_e('First letter (S M T W T F S)', 'room'); ?></label>
		</p>
	<?php
	}
}



// trx_widget_calendar
//-------------------------------------------------------------
/*
[trx_widget_calendar id="unique_id" title="Widget title" weekdays="short|initial"]
*/
if ( !function_exists( 'room_sc_widget_calendar' ) ) {
	function room_sc_widget_calendar($atts, $content=null){	
		$atts = room_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"weekdays" => "short",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts));
		if ($atts['weekdays']=='') $atts['weekdays'] = 'short';
		extract($atts);
		$type = 'room_widget_calendar';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_calendar' 
								. (room_exists_visual_composer() ? ' vc_widget_calendar wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, room_prepare_widgets_args(room_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_calendar', 'widget_calendar') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('room_shortcode_output', $output, 'trx_widget_calendar', $atts, $content);
	}
	room_require_shortcode("trx_widget_calendar", "room_sc_widget_calendar");
}


// Add [trx_widget_calendar] in the VC shortcodes list
if (!function_exists('room_sc_widget_calendar_add_in_vc')) {
	function room_sc_widget_calendar_add_in_vc() {

		if (!room_exists_visual_composer()) return;
		
		vc_map( array(
				"base" => "trx_widget_calendar",
				"name" => esc_html__("Widget Calendar", 'room'),
				"description" => wp_kses_data( __("Insert standard WP Calendar, but allow user select week day's captions", 'room') ),
				"category" => esc_html__('Content', 'room'),
				"icon" => 'icon_trx_widget_calendar',
				"class" => "trx_widget_calendar",
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
						"param_name" => "weekdays",
						"heading" => esc_html__("Week days", 'room'),
						"description" => wp_kses_data( __("Show captions for the week days as three letters (Sun, Mon, etc.) or as one initial letter (S, M, etc.)", 'room') ),
						"class" => "",
						"value" => array("Initial letter" => "initial" ),
						"type" => "checkbox"
					),
					room_get_vc_param('id'),
					room_get_vc_param('class'),
					room_get_vc_param('css')
				)
			) );
			
		class WPBakeryShortCode_Trx_Widget_Calendar extends WPBakeryShortCode {}

	}
	add_action('after_setup_theme', 'room_sc_widget_calendar_add_in_vc');
}
?>