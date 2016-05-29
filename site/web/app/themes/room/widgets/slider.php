<?php
/**
 * Theme Widget: Slider
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Load widget
if (!function_exists('room_widget_slider_load')) {
	add_action( 'widgets_init', 'room_widget_slider_load' );
	function room_widget_slider_load() {
		register_widget( 'room_widget_slider' );
	}
}

// Widget Class
class room_widget_slider extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_slider', 'description' => esc_html__('Display theme slider', 'room') );
		parent::__construct( 'room_widget_slider', esc_html__('ThemeREX - Slider', 'room'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$engine = isset($instance['engine']) ? $instance['engine'] : 'swiper';
		$slides_per_view = isset($instance['slides_per_view']) ? $instance['slides_per_view'] : 1;
		$height = isset($instance['height']) ? max(50, (int) $instance['height']) : 345;
		$alias = isset($instance['alias']) ? $instance['alias'] : '';
		$category = isset($instance['category']) ? (int) $instance['category'] : 0;
		$posts = isset($instance['posts']) ? $instance['posts'] : 5;
		$interval = isset($instance['interval']) ? max(1000, (int) $instance['interval']) : 7000;

		// Before widget (defined by themes)
		echo trim($before_widget);

		// Display the widget title if one was input (before and after defined by themes)
		if ($title)	echo trim($before_title . $title . $after_title);

		// Widget body
		$html = '';
		if ($engine == 'swiper') {
			$count = $ids = $posts;
			if (strpos($ids, ',')!==false) {
				$category = '';
				$count = 0;
			} else {
				$ids = '';
				if (empty($count)) $count = 3;
			}
			if ($count > 0 || !empty($ids)) {
				$html = room_build_slider_layout(array(
					'mode' => "posts",
					'controls' => "yes",
					'pagination' => "no",
					'titles' => "center",
					'interval' => $interval,
					'height' => $height,
					'per_view' => $slides_per_view,
					'cat' => $category,
					'ids' => $ids,
					'count' => $count,
					'orderby' => "date",
					'order' => "desc",
					'class' => "slider_height_auto"
					)
				);
				if (!empty($html)) room_enqueue_slider();
			}
		} else
			$html = apply_filters('room_filter_show_slider', '', $engine, $alias);
		if (!empty($html)) {
			?>
			<div class="slider_wrap slider_engine_<?php echo esc_attr($engine); ?>">
				<?php echo trim($html); ?>
			</div>
			<?php 
		}

		// After widget (defined by themes)
		echo trim($after_widget);
	}

	// Update the widget settings.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['engine'] = strip_tags( $new_instance['engine'] );
		$instance['slides_per_view'] = intval( $new_instance['slides_per_view'] );
		$instance['height'] = intval( $new_instance['height'] );
		$instance['category'] = intval( $new_instance['category'] );
		$instance['posts'] = strip_tags( $new_instance['posts'] );
		$instance['interval'] = intval( $new_instance['interval'] );

		return apply_filters('room_filter_save_slider_params', $instance, $new_instance);
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'engine' => 'swiper',
			'slides_per_view' => '1',
			'height' => '345',
			'alias' => '',
			'category' => '0',
			'posts' => '5',
			'interval' => '7000'
			)
		);
		$title = $instance['title'];
		$engine = $instance['engine'];
		$slides_per_view = $instance['slides_per_view'];
		$height = $instance['height'];
		$alias = $instance['alias'];
		$category = $instance['category'];
		$posts = $instance['posts'];
		$interval = $instance['interval'];
		
		$sliders_list = room_get_list_sliders();
		$categories_list = room_array_merge(array(0=>esc_html__('- Select category -', 'room')), room_get_list_categories());
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('engine')); ?>"><?php esc_html_e('Slider engine:', 'room'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('engine')); ?>" name="<?php echo esc_attr($this->get_field_name('engine')); ?>" class="widgets_param_fullwidth">
			<?php
				if (is_array($sliders_list) && count($sliders_list) > 0) {
					foreach ($sliders_list as $slug => $name) {
						echo '<option value="'.esc_attr($slug).'"'.($slug==$engine ? ' selected="selected"' : '').'>'.esc_html($name).'</option>';
					}
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'height' )); ?>"><?php esc_html_e('Slider height', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'height' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'height' )); ?>" value="<?php echo esc_attr($height); ?>" class="widgets_param_fullwidth" />
		</p>

		<?php do_action('room_action_show_slider_params', $this, $instance); ?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e('Category for the Swiper', 'room'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>" class="widgets_param_fullwidth">
			<?php
				if (is_array($categories_list) && count($categories_list) > 0) {
					foreach ($categories_list as $slug => $name) {
						echo '<option value="'.esc_attr($slug).'"'.($slug==$category ? ' selected="selected"' : '').'>'.esc_html($name).'</option>';
					}
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'slides_per_view' )); ?>"><?php esc_html_e('Slides per view in the Swiper', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'slides_per_view' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'slides_per_view' )); ?>" value="<?php echo esc_attr($slides_per_view); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'posts' )); ?>"><?php esc_html_e('Swiper posts:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'posts' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'posts' )); ?>" value="<?php echo esc_attr($posts); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'interval' )); ?>"><?php esc_html_e('Swiper interval (in msec.)', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'interval' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'interval' )); ?>" value="<?php echo esc_attr($interval); ?>" class="widgets_param_fullwidth" />
		</p>
		
	<?php
	}
}



// trx_widget_slider
//-------------------------------------------------------------
/*
[trx_widget_slider id="unique_id" title="Widget title" engine="revo" alias="home_slider_1"]
*/
if ( !function_exists( 'room_sc_widget_slider' ) ) {
	function room_sc_widget_slider($atts, $content=null){	
		$atts = room_html_decode(shortcode_atts(array(
			// Individual params
			'title' => '',
			'engine' => 'swiper',
			'slides_per_view' => '1',
			'height' => '345',
			'alias' => '',
			'category' => '0',
			'posts' => '5',
			'interval' => '7000',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts));
		extract($atts);
		$type = 'room_widget_slider';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_slider' 
								. (room_exists_visual_composer() ? ' vc_widget_slider wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, room_prepare_widgets_args(room_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_slider', 'widget_slider') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('room_shortcode_output', $output, 'trx_widget_slider', $atts, $content);
	}
	room_require_shortcode("trx_widget_slider", "room_sc_widget_slider");
}


// Add [trx_widget_slider] in the VC shortcodes list
if (!function_exists('room_sc_widget_slider_add_in_vc')) {
	function room_sc_widget_slider_add_in_vc() {

		if (!room_exists_visual_composer()) return;
		
		vc_map( array(
				"base" => "trx_widget_slider",
				"name" => esc_html__("Widget Slider", 'room'),
				"description" => wp_kses_data( __("Insert widget with slider", 'room') ),
				"category" => esc_html__('Content', 'room'),
				"icon" => 'icon_trx_widget_slider',
				"class" => "trx_widget_slider",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => apply_filters('room_filter_add_slider_params_in_vc', array(
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
						"param_name" => "engine",
						"heading" => esc_html__("Slider engine", 'room'),
						"description" => wp_kses_data( __("Select engine to show slider", 'room') ),
						"class" => "",
						"value" => array_flip(room_get_list_sliders()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "height",
						"heading" => esc_html__("Slider height", 'room'),
						"description" => wp_kses_data( __("Slider height", 'room') ),
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"class" => "",
						"value" => "345",
						"type" => "textfield"
					),
					array(
						"param_name" => "category",
						"heading" => esc_html__("Category for the Swiper", 'room'),
						"description" => wp_kses_data( __("Select category to get posts for the slider", 'room') ),
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"class" => "",
						"std" => 0,
						"value" => array_flip(room_array_merge(array(0=>esc_html__('- Select category -', 'room')), room_get_list_categories())),
						"type" => "dropdown"
					),
					array(
						"param_name" => "posts",
						"heading" => esc_html__("Posts for the Swiper", 'room'),
						"description" => wp_kses_data( __("Number posts or comma separated post IDs to show in the Swiper", 'room') ),
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"class" => "",
						"value" => "5",
						"type" => "textfield"
					),
					array(
						"param_name" => "slides_per_view",
						"heading" => esc_html__("Slides per view in the Swiper", 'room'),
						"description" => wp_kses_data( __("Specify slides per view in the Swiper", 'room') ),
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					array(
						"param_name" => "interval",
						"heading" => esc_html__("Interval between slides in the Swiper", 'room'),
						"description" => wp_kses_data( __("Specify interval between slides change in the Swiper", 'room') ),
						'dependency' => array(
							'element' => 'engine',
							'value' => 'swiper'
						),
						"class" => "",
						"value" => "1",
						"type" => "textfield"
					),
					room_get_vc_param('id'),
					room_get_vc_param('class'),
					room_get_vc_param('css')
				) )
			) );
			
		class WPBakeryShortCode_Trx_Widget_Slider extends WPBakeryShortCode {}

	}
	add_action('after_setup_theme', 'room_sc_widget_slider_add_in_vc');
}
?>