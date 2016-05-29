<?php
/**
 * Theme Widget: Categories list
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Load widget
if (!function_exists('room_widget_categories_list_load')) {
	add_action( 'widgets_init', 'room_widget_categories_list_load' );
	function room_widget_categories_list_load() {
		register_widget('room_widget_categories_list');
	}
}

// Widget Class
class room_widget_categories_list extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_categories_list', 'description' => esc_html__('Display categories list with icons or images', 'room'));
		parent::__construct( 'room_widget_categories_list', esc_html__('ThemeREX - Categories list', 'room'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		global $post;

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');

		$style = isset($instance['style']) ? max(1, (int) $instance['style']) : 1;
		$number = isset($instance['number']) ? (int) $instance['number'] : '';
		$columns = isset($instance['columns']) ? (int) $instance['columns'] : '';
		$show_posts = isset($instance['show_posts']) ? (int) $instance['show_posts'] : 0;
		$show_children = isset($instance['show_children']) ? (int) $instance['show_children'] : 0;
		$cat_list = isset($instance['cat_list']) ? $instance['cat_list'] : '';

		$categories = get_categories(array(
			'type'                     => 'post',
			'taxonomy'                 => 'category',
			'include'                  => $cat_list,
			'number'                   => $number > 0 && empty($cat_list) ? $number : '',
			'parent'                   => $show_children && is_category() ? (int) get_query_var( 'cat' ) : '',
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 0,
			'hierarchical'             => 0,
			'pad_counts'               => $show_posts > 0 
		
		));

		// If result is empty - exit without output
		if (count($categories)==0) return;
		
		// Before widget (defined by themes)
		echo trim($before_widget);
			
		// Display the widget title if one was input (before and after defined by themes)
		if ($title) echo trim($before_title . $title . $after_title);
	
		// Display widget body
		?>
		<div class="categories_list categories_list_style_<?php echo esc_attr($style); ?>">
			<?php 
			if ($columns > 1) echo '<div class="columns_wrap">';
			foreach ($categories as $cat) {
				$image = $style==1 ? room_get_category_icon($cat->term_id) : room_get_category_image($cat->term_id);
				room_template_set_args('categories_list', array(
					'style' => $style,
					'columns' => $columns,
					'image' => $image,
					'show_posts' => $show_posts,
					'cat' => $cat
				));
				get_template_part( 'templates/categories-list-'.trim($style) );
			}
			if ($columns > 1) echo '</div>';
			?>
		</div>
		<?php			

		// After widget (defined by themes)
		echo trim($after_widget);
	}

	// Update the widget settings
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['style'] = (int) $new_instance['style'];
		$instance['number'] = (int) $new_instance['number'];
		$instance['columns'] = (int) $new_instance['columns'];
		$instance['show_posts'] = !empty($new_instance['show_posts']) ? 1 : 0;
		$instance['show_children'] = !empty($new_instance['show_children']) ? 1 : 0;
		$instance['cat_list'] = join(',', $new_instance['cat_list']);
		return $instance;
	}

	// Displays the widget settings controls on the widget panel
	function form($instance) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'style' => '1',
			'number' => '5',
			'columns' => '5',
			'show_posts' => '1',
			'show_children' => '0',
			'cat_list' => ''
			)
		);
		$title = $instance['title'];
		$style = (int) $instance['style'];
		$number = (int) $instance['number'];
		$columns = (int) $instance['columns'];
		$show_posts = (int) $instance['show_posts'];
		$show_children = (int) $instance['show_children'];
		$cat_list = $instance['cat_list'];
		// Prepare categories list
		$categories = get_categories(array(
			'type'                     => 'post',
			'taxonomy'                 => 'category',
			'orderby'                  => 'id',
			'order'                    => 'ASC',
			'hide_empty'               => 1,
			'hierarchical'             => 0,
			'pad_counts'               => true 
		
		));
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('style')); ?>_1"><?php esc_html_e('Output style:', 'room'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('style')); ?>_1" name="<?php echo esc_attr($this->get_field_name('style')); ?>" value="1" <?php echo (1==$style ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('style')); ?>_1"><?php esc_html_e('Style 1', 'room'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('style')); ?>_2" name="<?php echo esc_attr($this->get_field_name('style')); ?>" value="2" <?php echo (2==$style ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('style')); ?>_2"><?php esc_html_e('Style 2', 'room'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('cat_list')); ?>"><?php esc_html_e('Categories to show:', 'room'); ?></label>
			<span class="widgets_param_catlist">
				<?php 
				foreach ($categories as $cat) {
					?><input type="checkbox"
								value="<?php echo esc_attr($cat->term_id); ?>" 
								id="<?php echo esc_attr($this->get_field_id('cat_list')); ?>_<?php echo esc_attr($cat->term_id); ?>" 
								name="<?php echo esc_attr($this->get_field_name('cat_list')); ?>[]"
								<?php if (strpos(','.$cat_list.',', ','.$cat->term_id.',')!==false) echo ' checked="checked"'; ?>>
					<label for="<?php echo esc_attr($this->get_field_id('cat_list')); ?>_<?php echo esc_attr($cat->term_id); ?>"><?php echo esc_html($cat->name); ?></label><br><?php
				}
				?>
			</span>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number categories to show (if field above is empty):', 'room'); ?></label>
			<input type="text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" value="<?php echo esc_attr($number); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('columns')); ?>"><?php esc_html_e('Columns number:', 'room'); ?></label>
			<input type="text" id="<?php echo esc_attr($this->get_field_id('columns')); ?>" name="<?php echo esc_attr($this->get_field_name('columns')); ?>" value="<?php echo esc_attr($columns); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_posts')); ?>_1"><?php esc_html_e('Show posts count:', 'room'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_posts')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_posts')); ?>" value="1" <?php echo (1==$show_posts ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_posts')); ?>_1"><?php esc_html_e('Show', 'room'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_posts')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_posts')); ?>" value="0" <?php echo (0==$show_posts ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_posts')); ?>_0"><?php esc_html_e('Hide', 'room'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_children')); ?>_1"><?php esc_html_e('Only children of the current category:', 'room'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_children')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_children')); ?>" value="1" <?php echo (1==$show_children ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_children')); ?>_1"><?php esc_html_e('Children', 'room'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_children')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_children')); ?>" value="0" <?php echo (0==$show_children ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_children')); ?>_0"><?php esc_html_e('From root', 'room'); ?></label>
		</p>
	<?php
	}
}



// trx_widget_categories_list
//-------------------------------------------------------------
/*
[trx_widget_categories_list id="unique_id" title="Widget title" style="1" number="4" columns="4" show_posts="0|1" show_children="0|1" cat_list="id1,id2,id3,..."]
*/
if ( !function_exists( 'room_sc_widget_categories_list' ) ) {
	function room_sc_widget_categories_list($atts, $content=null){	
		$atts = room_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"number" => 4,
			"show_date" => 1,
			"show_image" => 1,
			"show_author" => 1,
			"show_counters" => 1,
			"show_categories" => 1,
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts));
		if ($atts['show_date']=='') $atts['show_date'] = 0;
		if ($atts['show_image']=='') $atts['show_image'] = 0;
		if ($atts['show_author']=='') $atts['show_author'] = 0;
		if ($atts['show_counters']=='') $atts['show_counters'] = 0;
		if ($atts['show_categories']=='') $atts['show_categories'] = 0;
		extract($atts);
		$type = 'room_widget_recent_posts';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_recent_posts' 
								. (room_exists_visual_composer() ? ' vc_widget_recent_posts wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, room_prepare_widgets_args(room_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_recent_posts', 'widget_recent_posts') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('room_shortcode_output', $output, 'trx_widget_recent_posts', $atts, $content);
	}
	room_require_shortcode("trx_widget_recent_posts", "room_sc_widget_recent_posts");
}


// Add [trx_widget_categories_list] in the VC shortcodes list
if (!function_exists('room_sc_widget_categories_list_add_in_vc')) {
	function room_sc_widget_categories_list_add_in_vc() {

		if (!room_exists_visual_composer()) return;
		
		vc_map( array(
				"base" => "trx_widget_recent_posts",
				"name" => esc_html__("Widget Recent Posts", 'room'),
				"description" => wp_kses_data( __("Insert recent posts list with thumbs, post's meta and category", 'room') ),
				"category" => esc_html__('Content', 'room'),
				"icon" => 'icon_trx_widget_recent_posts',
				"class" => "trx_widget_recent_posts",
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
						"param_name" => "number",
						"heading" => esc_html__("Number posts to show", 'room'),
						"description" => wp_kses_data( __("How many posts display in widget?", 'room') ),
						"admin_label" => true,
						"class" => "",
						"value" => "4",
						"type" => "textfield"
					),
					array(
						"param_name" => "show_image",
						"heading" => esc_html__("Show post's image", 'room'),
						"description" => wp_kses_data( __("Do you want display post's featured image?", 'room') ),
						"group" => esc_html__('Details', 'room'),
						"class" => "",
						"std" => 1,
						"value" => array("Show image" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "show_author",
						"heading" => esc_html__("Show post's author", 'room'),
						"description" => wp_kses_data( __("Do you want display post's author?", 'room') ),
						"group" => esc_html__('Details', 'room'),
						"class" => "",
						"std" => 1,
						"value" => array("Show author" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "show_date",
						"heading" => esc_html__("Show post's date", 'room'),
						"description" => wp_kses_data( __("Do you want display post's publish date?", 'room') ),
						"group" => esc_html__('Details', 'room'),
						"class" => "",
						"std" => 1,
						"value" => array("Show date" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "show_counters",
						"heading" => esc_html__("Show post's counters", 'room'),
						"description" => wp_kses_data( __("Do you want display post's counters?", 'room') ),
						"group" => esc_html__('Details', 'room'),
						"class" => "",
						"std" => 1,
						"value" => array("Show counters" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "show_categories",
						"heading" => esc_html__("Show post's categories", 'room'),
						"description" => wp_kses_data( __("Do you want display post's categories?", 'room') ),
						"group" => esc_html__('Details', 'room'),
						"class" => "",
						"std" => 1,
						"value" => array("Show categories" => "1" ),
						"type" => "checkbox"
					),
					room_get_vc_param('id'),
					room_get_vc_param('class'),
					room_get_vc_param('css')
				)
			) );
			
		class WPBakeryShortCode_Trx_Widget_Categories_List extends WPBakeryShortCode {}

	}
	add_action('after_setup_theme', 'room_sc_widget_categories_list_add_in_vc');
}
?>