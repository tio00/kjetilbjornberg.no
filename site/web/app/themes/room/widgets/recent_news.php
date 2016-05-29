<?php
/**
 * Theme Widget: Resent News list
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.5
 */

// Load widget
if (!function_exists('room_widget_recent_news_load')) {
	add_action( 'widgets_init', 'room_widget_recent_news_load' );
	function room_widget_recent_news_load() {
		register_widget('room_widget_recent_news');
	}
}


// Widget Class
//------------------------------------------------------
class room_widget_recent_news extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_recent_news', 'description' => esc_html__('Show recent news in many styles', 'room'));
		parent::__construct( 'room_widget_recent_news', esc_html__('ThemeREX - Recent News', 'room'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		$widget_title = apply_filters('widget_title', isset($instance['widget_title']) ? $instance['widget_title'] : '');

		$output = room_sc_recent_news( array(
			'title' 			=> isset($instance['title']) ? $instance['title'] : '',
			'subtitle'			=> isset($instance['subtitle']) ? $instance['subtitle'] : '',
			'style'				=> isset($instance['style']) ? $instance['style'] : 'news-1',
			'count'				=> isset($instance['count']) ? (int) $instance['count'] : 3,
			'category'			=> isset($instance['category']) ? (int) $instance['category'] : 0
			)
		);

		if (!empty($output)) {
	
			// Before widget (defined by themes)
			echo trim($before_widget);
			
			// Display the widget title if one was input (before and after defined by themes)
			if ($widget_title) echo trim($before_title . $widget_title . $after_title);
	
			// Display widget body
			echo trim($output);
			
			// After widget (defined by themes)
			echo trim($after_widget);
		}
	}

	// Update the widget settings
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['widget_title']	= strip_tags($new_instance['widget_title']);
		$instance['title']			= strip_tags($new_instance['title']);
		$instance['subtitle']		= strip_tags($new_instance['subtitle']);
		$instance['style']			= strip_tags($new_instance['style']);
		$instance['count']			= max(1, (int) $new_instance['count']);
		$instance['category']		= max(0, (int) $new_instance['category']);
		return $instance;
	}

	// Displays the widget settings controls on the widget panel
	function form($instance) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'widget_title' => '',
			'title' => '',
			'subtitle' => '',
			'style' => '',
			'count' => 3,
			'category' => 0
			)
		);
		$widget_title = $instance['widget_title'];
		$title = $instance['title'];
		$subtitle = $instance['subtitle'];
		$style = $instance['style'];
		$count = (int) $instance['count'];
		$category = (int) $instance['category'];

		$list_styles = room_get_list_news_styles();
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('widget_title')); ?>"><?php esc_html_e('Widget title:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('widget_title')); ?>" name="<?php echo esc_attr($this->get_field_name('widget_title')); ?>" value="<?php echo esc_attr($widget_title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Block title:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('subtitle')); ?>"><?php esc_html_e('Block subtitle:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('subtitle')); ?>" name="<?php echo esc_attr($this->get_field_name('subtitle')); ?>" value="<?php echo esc_attr($subtitle); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('style')); ?>"><?php esc_html_e('Style:', 'room'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>" class="widgets_param_fullwidth">
			<?php
				if (is_array($list_styles) && count($list_styles) > 0) {
					foreach ($list_styles as $slug => $name) {
						echo '<option value="'.esc_attr($slug).'"'.($slug==$style ? ' selected="selected"' : '').'>'.esc_html($name).'</option>';
					}
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php esc_html_e('Number of posts to be displayed:', 'room'); ?></label>
			<input type="text" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" value="<?php echo esc_attr($count); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e('Parent category:', 'room'); ?></label>
			<?php
			wp_dropdown_categories(array(
				'hide_empty' => 0,
				'name' => $this->get_field_name('category'),
				'orderby' => 'name',
				'selected' => $category,
				'hierarchical' => true,
				'show_option_none' => esc_html__('-- All categories --', 'room')
				)
			);
			?>
		</p>
	<?php
	}
}



// trx_recent_news
//-------------------------------------------------------------
/*
[trx_recent_news id="unique_id" count="5" style="news-1" title="Block title" subtitle="xxx" category="id|slug" show_categories="yes|no" show_counters="yes|no"]
*/
if ( !function_exists( 'room_sc_recent_news' ) ) {
	function room_sc_recent_news($atts, $content=null){	
		extract(room_html_decode(shortcode_atts(array(
			// Individual params
			"style" => "news-magazine",
			"count" => 3,
			"ids" => "",
			"category" => 0,
			"offset" => 0,
			"orderby" => "date",
			"order" => "desc",
			"widget_title" => "",
			"title" => "",
			"subtitle" => "",
			"show_categories" => "no",
			"show_counters" => "no",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts)));

		room_push_state('trx_recent_news');
		
		if (!empty($ids)) {
			$posts = explode(',', $ids);
			$count = count($posts);
		}
		$count = max(1, (int) $count);
		$category = max(0, (int) $category);

		// Get categories list
		if (room_param_is_on($show_categories)) {
			if ( !room_storage_isset('categories_'.$category) ) {
				room_storage_set('categories_'.$category, get_categories( array(
					'orderby' => 'name',
					'parent' => $category
					)
				));
			}
			$cats = room_storage_get('categories_'.$category);
		}

		$output = '';
		
		// If insert with VC as widget
		if (!empty($widget_title)) {
			$widget_args = room_prepare_widgets_args(room_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_recent_news', 'widget_recent_news');
			$output .= '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_recent_news_wrap' 
								. (room_exists_visual_composer() ? ' vc_recent_news wpb_content_element' : '') 
						. '">'
							. $widget_args['before_widget']
							. $widget_args['before_title'] .esc_html($widget_title). $widget_args['after_title'];
		}
		
		// Wrapper
		$output .= '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_recent_news'
							. ' sc_recent_news_style_'.esc_attr($style)
							. ' sc_recent_news_with_accented'
							. (!empty($class) ? ' '.esc_attr($class) : '')
							. '"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. '>';

		// Header
		if ( !empty($title) || !empty($subtitle) || (room_param_is_on($show_categories) && !empty($cats)) ) {
			$output	.= '<div class="sc_recent_news_header'.(room_param_is_on($show_categories) && !empty($cats) ? ' sc_recent_news_header_split' : '').'">'
							. ( !empty($title) || !empty($subtitle)
								? '<div class="sc_recent_news_header_captions">'
										. (!empty($title) ? '<h4 class="sc_recent_news_title">' . esc_html($title) . '</h4>' : '')
										. (!empty($subtitle) ? '<h6 class="sc_recent_news_subtitle">' . esc_html($subtitle) . '</h6>' : '')
									. '</div>'
								: '');

			// Categories list
			if (room_param_is_on($show_categories) && !empty($cats)) {
				$output .= '<div class="sc_recent_news_header_categories">';
				if (is_array($cats) && count($cats) > 0) {
					$output .= '<a href="' . esc_url( $category == 0 
						? ( get_option('show_on_front')=='page' 
							? get_permalink(get_option('page_for_posts')) 
							: home_url('/')
							)
						: get_category_link($category) ) . '" class="sc_recent_news_header_category_item">'.esc_html__('All News', 'room').'</span>';
					$number = 0;
					$number_max = 3;
					foreach ($cats as $cat) {
						$number++;
						if ($number == $number_max)
							$output .= '<span class="sc_recent_news_header_category_item sc_recent_news_header_category_item_more">'.esc_html__('More', 'room')
										. '<span class="sc_recent_news_header_more_categories">';
						$output .= '<a href="'.esc_url(get_category_link( $cat->term_id )).'" class="sc_recent_news_header_category_item">'.esc_html($cat->name).'</a>';
					}
					if ($number >= $number_max)
						$output .= '</span></span>';
				}
				$output .= '</div>';
			}
	
			$output .= '</div><!-- /.sc_recent_news_header -->';
		}

		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => $count,
			'ignore_sticky_posts' => true,
			'order' => $order=='asc' ? 'asc' : 'desc'
		);
		
		if ($offset > 0 && empty($ids)) {
			$args['offset'] = $offset;
		}
		
		$args = room_query_add_sort_order($args, $orderby, $order);
		$args = room_query_add_posts_and_cats($args, $ids, 'post', $category, 'category');
		$query = new WP_Query( $args );
	
		$count = min($count, $query->found_posts);
		
		$post_number = 0;
				
		while ( $query->have_posts() ) { $query->the_post();
			$post_number++;
			room_template_set_args( 'recent_news', array(
				'style' => $style,
				'number' => $post_number,
				'count' => $count
				)
			);
			ob_start();
			get_template_part( 'templates/'.$style );
			$output .= ob_get_contents();
			ob_end_clean();
		}
		wp_reset_postdata();

		$output .=  '</div><!-- /.sc_recent_news -->';

		if (!empty($widget_title)) $output .=  $widget_args['after_widget'] . '</div><!-- /.sc_recent_news_wrap -->';
	
		// Add template specific scripts and styles
		do_action('room_action_blog_scripts', $style);
	
		room_pop_state();

		return apply_filters('room_shortcode_output', $output, 'trx_recent_news', $atts, $content);
	}
	room_require_shortcode("trx_recent_news", "room_sc_recent_news");
}


// Add [trx_recent_news] in the VC shortcodes list
if (!function_exists('room_sc_recent_news_add_in_vc')) {
	function room_sc_recent_news_add_in_vc() {

		if (!room_exists_visual_composer()) return;
		
		vc_map( array(
				"base" => "trx_recent_news",
				"name" => esc_html__("Recent News", 'room'),
				"description" => wp_kses_data( __("Insert recent news list", 'room') ),
				"category" => esc_html__('Content', 'room'),
				"icon" => 'icon_trx_recent_news',
				"class" => "trx_recent_news",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => esc_html__("List style", 'room'),
						"description" => wp_kses_data( __("Select style to display news list", 'room') ),
						"class" => "",
						"admin_label" => true,
						"std" => 'news-magazine',
						"value" => array_flip(room_get_list_news_styles()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "widget_title",
						"heading" => esc_html__("Widget Title", 'room'),
						"description" => wp_kses_data( __("Title for the widget (fill this field only if you want to use shortcode as widget)", 'room') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", 'room'),
						"description" => wp_kses_data( __("Title for the block", 'room') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", 'room'),
						"description" => wp_kses_data( __("Subtitle for the block", 'room') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "category",
						"heading" => esc_html__("Category", 'room'),
						"description" => wp_kses_data( __("Select category to show news. If empty - select news from any category or from IDs list", 'room') ),
						"group" => esc_html__('Query', 'room'),
						"class" => "",
						"std" => 0,
						"value" => array_flip(room_array_merge(array(0 => esc_html__('- Select category -', 'room')), room_get_list_categories())),
						"type" => "dropdown"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Total posts", 'room'),
						"description" => wp_kses_data( __("How many posts will be displayed? If used IDs - this parameter ignored.", 'room') ),
						"admin_label" => true,
						"group" => esc_html__('Query', 'room'),
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => esc_html__("Offset before select posts", 'room'),
						"description" => wp_kses_data( __("Skip posts before select next part.", 'room') ),
						"group" => esc_html__('Query', 'room'),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Post sorting", 'room'),
						"description" => wp_kses_data( __("Select desired posts sorting method", 'room') ),
						"group" => esc_html__('Query', 'room'),
						"class" => "",
						"value" => array_flip(room_get_list_sortings()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Post order", 'room'),
						"description" => wp_kses_data( __("Select desired posts order", 'room') ),
						"group" => esc_html__('Query', 'room'),
						"class" => "",
						"value" => array_flip(room_get_list_orderings()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => esc_html__("List IDs", 'room'),
						"description" => wp_kses_data( __("Comma separated list of post's ID. If set - parameters above (category, count, order, etc.) are ignored!", 'room') ),
						"group" => esc_html__('Query', 'room'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					room_get_vc_param('id'),
					room_get_vc_param('class'),
					room_get_vc_param('css')
				)
			) );
			
		class WPBakeryShortCode_Trx_Recent_News extends WPBakeryShortCode {}

	}
	add_action('after_setup_theme', 'room_sc_recent_news_add_in_vc');
}
?>