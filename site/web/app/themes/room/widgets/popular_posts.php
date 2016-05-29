<?php
/**
 * Theme Widget: Most popular and commented posts
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Load widget
if (!function_exists('room_widget_popular_posts_load')) {
	add_action( 'widgets_init', 'room_widget_popular_posts_load' );
	function room_widget_popular_posts_load() {
		register_widget('room_widget_popular_posts');
	}
}

// Widget Class
class room_widget_popular_posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_popular_posts', 'description' => esc_html__('The most popular and most commented blog posts', 'room'));
		parent::__construct( 'room_widget_popular_posts', esc_html__('ThemeREX - Most Popular & Commented Posts', 'room'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		global $post;

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
		$tabs = array(
			array(
				'title' => isset($instance['title_popular']) 	? $instance['title_popular']	: '',
				'content' => ''
				),
			array(
				'title' => isset($instance['title_commented'])	? $instance['title_commented']	: '',
				'content' => ''
				),
			array(
				'title' => isset($instance['title_liked'])		? $instance['title_liked']		: '',
				'content' => ''
				)
			);

		$number = isset($instance['number']) ? (int) $instance['number'] : '';

		$widget_counters = room_get_theme_setting('widget_counters');
		for ($i=0; $i<3; $i++) {
			if (empty($tabs[$i]['title'])) continue;
			room_set_theme_setting('widget_counters', $i==0 ? 'views' : ($i==1 ? 'comments' : 'likes'));
			$args = array(
				'post_type' => 'post',
				'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
				'post_password' => '',
				'posts_per_page' => $number,
				'ignore_sticky_posts' => true,
				'order' => 'DESC',
			);
			if ($i==0) {			// Most popular
				$args['meta_key'] = 'room_post_views_count';
				$args['orderby'] = 'meta_value_num';
			} else if ($i==2) {		// Most liked
				$args['meta_key'] = 'room_post_likes_count';
				$args['orderby'] = 'meta_value_num';
			} else {				// Most commented
				$args['orderby'] = 'comment_count';
			}
			
			$q = new WP_Query($args); 
			
			// Loop posts
			if ( $q->have_posts() ) {
				$post_number = 0;
				room_storage_set('output', '');
				while ($q->have_posts()) { $q->the_post();
					$post_number++;
					room_template_set_args('widgets-posts', array(
						'show_image' => isset($instance['show_image']) ? (int) $instance['show_image'] : 0,
						'show_date' => isset($instance['show_date']) ? (int) $instance['show_date'] : 0,
						'show_author' => isset($instance['show_author']) ? (int) $instance['show_author'] : 0,
						'show_counters'	=> isset($instance['show_counters']) ? (int) $instance['show_counters'] : 0,
			            'show_categories' => isset($instance['show_categories']) ? (int) $instance['show_categories'] : 0
            			)
					);
					get_template_part('templates/widgets-posts');
					if ($post_number >= $number) break;
				}
				$tabs[$i]['content'] .= room_storage_get('output');
			}
		}
		
		room_set_theme_setting('widget_counters', $widget_counters);

		wp_reset_postdata();

		if ( $tabs[0]['title'].$tabs[0]['content'].$tabs[1]['title'].$tabs[1]['content'].$tabs[2]['title'].$tabs[2]['content'] ) {

			// Before widget (defined by themes)
			echo trim($before_widget);
			
			// Display the widget title if one was input (before and after defined by themes)
			if ($title) echo trim($before_title . $title . $after_title);

			// Display widget body
			$id = 'sc_tabs_'.str_replace('.', '', mt_rand());
			?>
			<div id="<?php echo esc_attr($id); ?>" class="sc_tabs">
				<ul class="sc_tabs_titles"><?php
					foreach ($tabs as $k=>$tab) {
						if (empty($tab['title']) || empty($tab['content'])) continue;
						$id_tab = $id . '_' . $k;
						?><li class="sc_tabs_title<?php echo !$k ? ' ui-tabs-active' : '';?>"><a href="<?php echo is_customize_preview() ? esc_url(room_get_protocol().'://' . ($_SERVER["HTTP_HOST"]) . ($_SERVER["REQUEST_URI"])) : ''; ?>#<?php echo esc_attr($id_tab); ?>_content"><?php echo esc_html($tab['title']); ?></a></li><?php
					}
				?></ul>
				<?php
				foreach ($tabs as $k=>$tab) {
					if (empty($tab['title']) || empty($tab['content'])) continue;
					$id_tab = $id . '_' . $k;
					?>
					<div id="<?php echo esc_attr($id_tab); ?>_content" class="sc_tabs_content">
						<?php echo trim($tab['content']); ?>
					</div>
					<?php
				}
				?>
			</div>
			<?php
			
			// After widget (defined by themes)
			echo trim($after_widget);

			if (!is_customize_preview()) {
				room_enqueue_script('jquery-ui-tabs', false, array('jquery','jquery-ui-core'), null, true);
				room_enqueue_script('jquery-effects-fade', false, array('jquery','jquery-effects-core'), null, true);
			}
		}
	}

	// Update the widget settings
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['title_popular'] = strip_tags($new_instance['title_popular']);
		$instance['title_commented'] = strip_tags($new_instance['title_commented']);
		$instance['title_liked'] = strip_tags($new_instance['title_liked']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = (int) $new_instance['show_date'];
		$instance['show_image'] = (int) $new_instance['show_image'];
		$instance['show_author'] = (int) $new_instance['show_author'];
		$instance['show_counters'] = (int) $new_instance['show_counters'];
		$instance['show_categories'] = (int) $new_instance['show_categories'];

		return $instance;
	}

	// Displays the widget settings controls on the widget panel
	function form($instance) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '', 
			'title_popular' => '', 
			'title_commented' => '', 
			'title_liked' => '', 
			'number' => '4', 
			'show_date' => '1', 
			'show_image' => '1', 
			'show_author' => '1', 
			'show_counters' => '1',
			'show_categories' => '1'
			)
		);
		$title = $instance['title'];
		$title_popular = $instance['title_popular'];
		$title_commented = $instance['title_commented'];
		$title_liked = $instance['title_liked'];
		$number = (int) $instance['number'];
		$show_date = (int) $instance['show_date'];
		$show_image = (int) $instance['show_image'];
		$show_author = (int) $instance['show_author'];
		$show_counters = (int) $instance['show_counters'];
		$show_categories = (int) $instance['show_categories'];
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title_popular')); ?>"><?php esc_html_e('Most popular tab title:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title_popular')); ?>" name="<?php echo esc_attr($this->get_field_name('title_popular')); ?>" value="<?php echo esc_attr($title_popular); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title_commented')); ?>"><?php esc_html_e('Most commented tab title:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title_commented')); ?>" name="<?php echo esc_attr($this->get_field_name('title_commented')); ?>" value="<?php echo esc_attr($title_commented); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title_liked')); ?>"><?php esc_html_e('Most liked tab title:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id('title_liked')); ?>" name="<?php echo esc_attr($this->get_field_name('title_liked')); ?>" value="<?php echo esc_attr($title_liked); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number posts to show:', 'room'); ?></label>
			<input type="text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" value="<?php echo esc_attr($number); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1"><?php esc_html_e("Show post's image:", 'room'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_image')); ?>" value="1" <?php echo (1==$show_image ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_1"><?php esc_html_e('Show', 'room'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_image')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_image')); ?>" value="0" <?php echo (0==$show_image ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_image')); ?>_0"><?php esc_html_e('Hide', 'room'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1"><?php esc_html_e("Show post's author:", 'room'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_author')); ?>" value="1" <?php echo (1==$show_author ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_1"><?php esc_html_e('Show', 'room'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_author')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_author')); ?>" value="0" <?php echo (0==$show_author ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_author')); ?>_0"><?php esc_html_e('Hide', 'room'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1"><?php esc_html_e("Show post's date:", 'room'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" value="1" <?php echo (1==$show_date ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_1"><?php esc_html_e('Show', 'room'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_date')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" value="0" <?php echo (0==$show_date ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>_0"><?php esc_html_e('Hide', 'room'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_1"><?php esc_html_e("Show post's counters:", 'room'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_2" name="<?php echo esc_attr($this->get_field_name('show_counters')); ?>" value="1" <?php echo (1==$show_counters ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_1"><?php esc_html_e('Show', 'room'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_counters')); ?>" value="0" <?php echo (0==$show_counters ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_counters')); ?>_0"><?php esc_html_e('Hide', 'room'); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_1"><?php esc_html_e("Show post's categories:", 'room'); ?></label><br />
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_2" name="<?php echo esc_attr($this->get_field_name('show_categories')); ?>" value="1" <?php echo (1==$show_categories ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_1"><?php esc_html_e('Show', 'room'); ?></label>
			<input type="radio" id="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_0" name="<?php echo esc_attr($this->get_field_name('show_categories')); ?>" value="0" <?php echo (0==$show_categories ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('show_categories')); ?>_0"><?php esc_html_e('Hide', 'room'); ?></label>
		</p>

	<?php
	}
}



// trx_widget_popular_posts
//-------------------------------------------------------------
/*
[trx_widget_popular_posts id="unique_id" title="Widget title" title_popular="title for the tab 'most popular'" title_commented="title for the tab 'most commented'" title_liked="title for the tab 'most liked'" number="4"]
*/
if ( !function_exists( 'room_sc_widget_popular_posts' ) ) {
	function room_sc_widget_popular_posts($atts, $content=null){	
		$atts = room_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"title_popular" => "",
			"title_commented" => "",
			"title_liked" => "",
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
		$type = 'room_widget_popular_posts';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_popular_posts' 
								. (room_exists_visual_composer() ? ' vc_widget_popular_posts wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, room_prepare_widgets_args(room_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_popular_posts', 'widget_popular_posts') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('room_shortcode_output', $output, 'trx_widget_popular_posts', $atts, $content);
	}
	room_require_shortcode("trx_widget_popular_posts", "room_sc_widget_popular_posts");
}


// Add [trx_widget_popular_posts] in the VC shortcodes list
if (!function_exists('room_sc_widget_popular_posts_add_in_vc')) {
	function room_sc_widget_popular_posts_add_in_vc() {

		if (!room_exists_visual_composer()) return;
		
		vc_map( array(
				"base" => "trx_widget_popular_posts",
				"name" => esc_html__("Widget Popular Posts", 'room'),
				"description" => wp_kses_data( __("Insert popular posts list with thumbs, post's meta and category", 'room') ),
				"category" => esc_html__('Content', 'room'),
				"icon" => 'icon_trx_widget_popular_posts',
				"class" => "trx_widget_popular_posts",
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
						"param_name" => "title_popular",
						"heading" => esc_html__("Most popular tab title", 'room'),
						"description" => wp_kses_data( __("Most popular tab title", 'room') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "title_commented",
						"heading" => esc_html__("Most commented tab title", 'room'),
						"description" => wp_kses_data( __("Most commented tab title", 'room') ),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "title_liked",
						"heading" => esc_html__("Most liked tab title", 'room'),
						"description" => wp_kses_data( __("Most liked tab title", 'room') ),
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
			
		class WPBakeryShortCode_Trx_Widget_Popular_Posts extends WPBakeryShortCode {}

	}
	add_action('after_setup_theme', 'room_sc_widget_popular_posts_add_in_vc');
}
?>