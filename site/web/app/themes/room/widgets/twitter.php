<?php
/**
 * Theme Widget: Most popular and commented posts
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Load widget
if (!function_exists('room_widget_twitter_load')) {
	add_action( 'widgets_init', 'room_widget_twitter_load' );
	function room_widget_twitter_load() {
		register_widget('room_widget_twitter');
	}
}

// Widget Class
class room_widget_twitter extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_twitter', 'description' => esc_html__('Last Twitter Updates. Version for new Twitter API 1.1', 'room') );
		parent::__construct( 'room_widget_twitter', esc_html__('ThemeREX - Twitter', 'room'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$bg_image = isset($instance['bg_image']) ? $instance['bg_image'] : '';
		$twitter_username = isset($instance['twitter_username']) ? $instance['twitter_username'] : '';
		$twitter_consumer_key = isset($instance['twitter_consumer_key']) ? $instance['twitter_consumer_key'] : '';
		$twitter_consumer_secret = isset($instance['twitter_consumer_secret']) ? $instance['twitter_consumer_secret'] : '';
		$twitter_token_key = isset($instance['twitter_token_key']) ? $instance['twitter_token_key'] : '';
		$twitter_token_secret = isset($instance['twitter_token_secret']) ? $instance['twitter_token_secret'] : '';
		$twitter_count = isset($instance['twitter_count']) ? $instance['twitter_count'] : '';	
		$follow = isset($instance['follow']) ? (int) $instance['follow'] : 0;

		if (empty($twitter_consumer_key) || empty($twitter_consumer_secret) || empty($twitter_token_key) || empty($twitter_token_secret)) return;
		
		$data = room_get_twitter_data(array(
			'mode'            => 'user_timeline',
			'consumer_key'    => $twitter_consumer_key,
			'consumer_secret' => $twitter_consumer_secret,
			'token'           => $twitter_token_key,
			'secret'          => $twitter_token_secret
			)
		);
		
		if (!$data || !isset($data[0]['text'])) return;
		
		$output = '<div class="widget_content"><ul>';
		$cnt = 0;
		if (is_array($data) && count($data) > 0) {
			foreach ($data as $tweet) {
				if (substr($tweet['text'], 0, 1)=='@') continue;
				$output .= '<li' . ($cnt==$twitter_count-1 ? ' class="last"' : '') . '><a href="' . esc_url('https://twitter.com/'.($twitter_username)) . '" class="username" target="_blank">@' . ($tweet['user']['screen_name']) . '</a> ' . force_balance_tags(room_prepare_twitter_text($tweet)) . '</li>';
				if (++$cnt >= $twitter_count) break;
			}
		}
		$output .= '</ul>';
		
		if ($follow) 
			$output .= '<a href="http://twitter.com/'.esc_attr($twitter_username).'" class="twitter_follow">' . esc_html__('Follow us', 'room') . '</a>';

		$output .= '</div>';

		// Before widget (defined by themes)
		if (!empty($bg_image)) {
			if ($bg_image > 0) {
				$attach = wp_get_attachment_image_src( $bg_image, room_get_thumb_size('med') );
				if (isset($attach[0]) && $attach[0]!='')
					$bg_image = $attach[0];
			}
			$before_widget = str_replace(
				'class="',
				'style="background-image:url('.esc_url($bg_image).');"'
				.' class="widget_bg_image ',
				$before_widget
			);
		}

		// Before widget (defined by themes)
		echo trim($before_widget);
			
		// Display the widget title if one was input (before and after defined by themes)
		if ($title) echo trim($before_title . $title . $after_title);

		echo trim($output);
			
		// After widget (defined by themes). */
		echo trim($after_widget);
	}

	// Update the widget settings.
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['bg_image'] = strip_tags( $new_instance['bg_image'] );
		$instance['twitter_username'] = strip_tags( $new_instance['twitter_username'] );
		$instance['twitter_consumer_key'] = strip_tags( $new_instance['twitter_consumer_key'] );
		$instance['twitter_consumer_secret'] = strip_tags( $new_instance['twitter_consumer_secret'] );
		$instance['twitter_token_key'] = strip_tags( $new_instance['twitter_token_key'] );
		$instance['twitter_token_secret'] = strip_tags( $new_instance['twitter_token_secret'] );
		$instance['twitter_count'] = strip_tags( $new_instance['twitter_count'] );
		$instance['follow'] = isset( $new_instance['follow'] ) ? 1 : 0;

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'bg_image' => '',
			'twitter_username' => '',
			'twitter_consumer_key' => '',
			'twitter_consumer_secret' => '',
			'twitter_token_key' => '',
			'twitter_token_secret' => '',
			'twitter_count' => 2,
			'follow' => 1
			)
		);
		$title = $instance['title'];
		$bg_image = $instance['bg_image'];
		$twitter_username = $instance['twitter_username'];
		$twitter_consumer_key = $instance['twitter_consumer_key'];
		$twitter_consumer_secret = $instance['twitter_consumer_secret'];
		$twitter_token_key = $instance['twitter_token_key'];
		$twitter_token_secret = $instance['twitter_token_secret'];
		$twitter_count = max(1, (int) $instance['twitter_count']);
		$follow = (int) $instance['follow'] ? 1 : 0;
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_count' )); ?>"><?php esc_html_e('Tweets number:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_count' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_count' )); ?>" value="<?php echo esc_attr($twitter_count); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_username' )); ?>"><?php esc_html_e('Twitter Username:', 'room'); ?><br />(<?php esc_html_e('leave empty if you paste widget code', 'room'); ?>)</label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_username' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_username' )); ?>" value="<?php echo esc_attr($twitter_username); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_consumer_key' )); ?>"><?php esc_html_e('Consumer Key:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_consumer_key' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_consumer_key' )); ?>" value="<?php echo esc_attr($twitter_consumer_key); ?>" class="widgets_param_fullwidth" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_consumer_secret' )); ?>"><?php esc_html_e('Consumer Secret:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_consumer_secret' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_consumer_secret' )); ?>" value="<?php echo esc_attr($twitter_consumer_secret); ?>" class="widgets_param_fullwidth" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_token_key' )); ?>"><?php esc_html_e('Token Key:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_token_key' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_token_key' )); ?>" value="<?php echo esc_attr($twitter_token_key); ?>" class="widgets_param_fullwidth" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter_token_secret' )); ?>"><?php esc_html_e('Token Secret:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter_token_secret' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter_token_secret' )); ?>" value="<?php echo esc_attr($twitter_token_secret); ?>" class="widgets_param_fullwidth" />
		</p>

		<p>
			<input type="checkbox" id="<?php echo esc_attr($this->get_field_id('follow')); ?>" name="<?php echo esc_attr($this->get_field_name('follow')); ?>" value="1" <?php echo (1==$follow ? ' checked="checked"' : ''); ?> />
			<label for="<?php echo esc_attr($this->get_field_id('follow')); ?>"><?php esc_html_e('Show "Follow us"', 'room'); ?></label><br />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'bg_image' )); ?>"><?php esc_html_e('Background image:', 'room'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'bg_image' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'bg_image' )); ?>" value="<?php echo esc_attr($bg_image); ?>" class="widgets_param_fullwidth widgets_param_img_selector" />
            <?php
			echo trim(room_show_custom_field($this->get_field_id( 'advert_media' ), array('type'=>'mediamanager', 'linked_field_id'=>$this->get_field_id( 'bg_image' )), null));
			if ($bg_image) {
			?>
	            <br /><br /><img src="<?php echo esc_url($bg_image); ?>" class="widgets_param_maxwidth" alt="" />
			<?php
			}
			?>
		</p>

	<?php
	}
}



// trx_widget_twitter
//-------------------------------------------------------------
/*
[trx_widget_twitter id="unique_id" title="Widget title" bg_image="image_url" number="3" follow="0|1"]
*/
if ( !function_exists( 'room_sc_widget_twitter' ) ) {
	function room_sc_widget_twitter($atts, $content=null){	
		$atts = room_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"username" => "",
			"bg_image" => "",
			"count" => 2,
			"follow" => 1,
			"consumer_key" => "",
			"consumer_secret" => "",
			"token_key" => "",
			"token_secret" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts));
		if ($atts['follow']=='') $atts['follow'] = 0;
		extract($atts);
		$type = 'room_widget_twitter';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$atts['twitter_username'] = $username;
			$atts['twitter_consumer_key'] = $consumer_key;
			$atts['twitter_consumer_secret'] = $consumer_secret;
			$atts['twitter_token_key'] = $token_key;
			$atts['twitter_token_secret'] = $token_secret;
			$atts['twitter_count'] = max(1, (int) $count);
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_twitter' 
								. (room_exists_visual_composer() ? ' vc_widget_twitter wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, room_prepare_widgets_args(room_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_twitter', 'widget_twitter') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('room_shortcode_output', $output, 'trx_widget_twitter', $atts, $content);
	}
	room_require_shortcode("trx_widget_twitter", "room_sc_widget_twitter");
}


// Add [trx_widget_twitter] in the VC shortcodes list
if (!function_exists('room_sc_widget_twitter_add_in_vc')) {
	function room_sc_widget_twitter_add_in_vc() {

		if (!room_exists_visual_composer()) return;
		
		vc_map( array(
				"base" => "trx_widget_twitter",
				"name" => esc_html__("Widget Twitter Feed", 'room'),
				"description" => wp_kses_data( __("Insert widget with Twitter feed", 'room') ),
				"category" => esc_html__('Content', 'room'),
				"icon" => 'icon_trx_widget_twitter',
				"class" => "trx_widget_twitter",
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
						"param_name" => "count",
						"heading" => esc_html__("Tweets number", 'room'),
						"description" => wp_kses_data( __("Tweets number to show in the feed", 'room') ),
						"class" => "",
						"value" => "2",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_image",
						"heading" => esc_html__("Widget background", 'room'),
						"description" => wp_kses_data( __("Select or upload image or write URL from other site for use it as widget background", 'room') ),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "follow",
						"heading" => esc_html__("Show Follow Us", 'room'),
						"description" => wp_kses_data( __("Do you want display Follow Us link below the feed?", 'room') ),
						"class" => "",
						"std" => 1,
						"value" => array("Show Follow Us" => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "username",
						"heading" => esc_html__("Twitter Username", 'room'),
						"description" => wp_kses_data( __("Twitter Username", 'room') ),
						"group" => esc_html__('Twitter account', 'room'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "consumer_key",
						"heading" => esc_html__("Consumer Key", 'room'),
						"description" => wp_kses_data( __("Specify Consumer Key from Twitter application", 'room') ),
						"group" => esc_html__('Twitter account', 'room'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "consumer_secret",
						"heading" => esc_html__("Consumer Secret", 'room'),
						"description" => wp_kses_data( __("Specify Consumer Secret from Twitter application", 'room') ),
						"group" => esc_html__('Twitter account', 'room'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "token_key",
						"heading" => esc_html__("Token Key", 'room'),
						"description" => wp_kses_data( __("Specify Token Key from Twitter application", 'room') ),
						"group" => esc_html__('Twitter account', 'room'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "token_secret",
						"heading" => esc_html__("Token Secret", 'room'),
						"description" => wp_kses_data( __("Specify Token Secret from Twitter application", 'room') ),
						"group" => esc_html__('Twitter account', 'room'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					room_get_vc_param('id'),
					room_get_vc_param('class'),
					room_get_vc_param('css')
				)
			) );
			
		class WPBakeryShortCode_Trx_Widget_Twitter extends WPBakeryShortCode {}

	}
	add_action('after_setup_theme', 'room_sc_widget_twitter_add_in_vc');
}
?>