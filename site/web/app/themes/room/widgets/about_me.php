<?php
/**
 * Theme Widget: About Me
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Load widget
if (!function_exists('room_widget_aboutme_load')) {
	add_action( 'widgets_init', 'room_widget_aboutme_load' );
	function room_widget_aboutme_load() {
		register_widget('room_widget_aboutme');
	}
}

// Widget Class
class room_widget_aboutme extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_aboutme', 'description' => esc_html__('About me - photo and short description', 'room'));
		parent::__construct( 'room_widget_aboutme', esc_html__('ThemeREX - About Me', 'room'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {
		extract($args);

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
		
		$blogusers = get_users( 'role=administrator' );
		
		$username = isset($instance['username']) ? $instance['username'] : '';
		if (empty($username) && count($blogusers) > 0 )
			$username = $blogusers[0]->display_name;

		$title_website = isset($instance['title_website']) ? $instance['title_website'] : '';

		$description = isset($instance['description']) ? $instance['description'] : '';
		if (empty($description) && count($description) > 0 )
			$description = $blogusers[0]->description;
		
		$avatar = isset($instance['avatar']) ? $instance['avatar'] : '';
		if (empty($avatar)) {
			if ( count($blogusers) > 0 ) {
				$mult = room_get_retina_multiplier();
				$avatar = get_avatar( $blogusers[0]->user_email, 220*$mult );
			}
		} else {
			if ($avatar > 0) {
				$attach = wp_get_attachment_image_src( $avatar, room_get_thumb_size('avatar') );
				if (isset($attach[0]) && $attach[0]!='')
					$avatar = $attach[0];
			}
			$attr = room_getimagesize($avatar);
			$avatar = '<img src="'.esc_url($avatar).'" alt="'.esc_attr($username).'"'.(!empty($attr[3]) ? ' '.trim($attr[3]) : '').'>';
		}
	
		// Before widget (defined by themes)
		echo trim($before_widget);

		// Display the widget title if one was input (before and after defined by themes)
		if ($title) echo trim($before_title . $title . $after_title);
	
		// Display widget body
		if (!empty($avatar)) {
			?><div class="aboutme_avatar"><?php echo trim($avatar); ?></div><?php
		}
		?>
		<h5 class="aboutme_username"><?php echo esc_html($username); ?></h5>
		<div class="aboutme_description"><?php echo wpautop(room_strshort($description, 150)); ?></div>
		<div class="aboutme_title_website"><?php echo (esc_attr($blogusers[0]->user_url) ? '<a href="'.esc_url($blogusers[0]->user_url).'"><span>' : ''); echo esc_html($title_website); ?><?php echo (esc_attr($blogusers[0]->user_url) ? '</span></a>' : '');?></div>
		<?php
		// After widget (defined by themes)
		echo trim($after_widget);
	}

	// Update the widget settings.
	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		// Strip tags for title and comments count to remove HTML (important for text inputs)
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['avatar'] = strip_tags($new_instance['avatar']);
		$instance['username'] = strip_tags($new_instance['username']);
		$instance['title_website'] = strip_tags($new_instance['title_website']);
		$instance['description'] = strip_tags($new_instance['description']);

		return $instance;
	}

	// Displays the widget settings controls on the widget panel.
	function form($instance) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'avatar' => '',
			'username' => '',
			'title_website' => '',
			'description' => ''
			)
		);
		$title = $instance['title'];
		$avatar = $instance['avatar'];
		$username = $instance['username'];
		$title_website = $instance['title_website'];
		$description = $instance['description'];
		?>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Widget title:', 'room'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>" class="widgets_param_fullwidth">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'avatar' )); ?>"><?php echo wp_kses_data( __('Avatar:<br />(if empty - get gravatar by admin email)', 'room') ); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'avatar' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'avatar' )); ?>" value="<?php echo esc_attr($avatar); ?>" class="widgets_param_fullwidth widgets_param_img_selector">
            <?php
			echo trim(room_show_custom_field($this->get_field_id( 'avatar_button' ), array('type'=>'mediamanager', 'linked_field_id'=>$this->get_field_id( 'avatar' )), null));
			if ($avatar) {
			?>
	            <br><br><img src="<?php echo esc_url($avatar); ?>" class="widgets_param_maxwidth" alt="">
			<?php
			}
			?>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('username')); ?>"><?php esc_html_e('User name:', 'room'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('username')); ?>" name="<?php echo esc_attr($this->get_field_name('username')); ?>" value="<?php echo esc_attr($username); ?>" class="widgets_param_fullwidth">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title_website')); ?>"><?php esc_html_e('Title Website:', 'room'); ?></label><br>
			<input id="<?php echo esc_attr($this->get_field_id('title_website')); ?>" name="<?php echo esc_attr($this->get_field_name('title_website')); ?>" value="<?php echo esc_attr($title_website); ?>" class="widgets_param_fullwidth">
		</p>
	<?php
	}
}



// trx_widget_aboutme
//-------------------------------------------------------------
/*
[trx_widget_aboutme id="unique_id" title="Widget title" avatar="image_url" username="User display name" description="short description"]
*/
if ( !function_exists( 'room_sc_widget_aboutme' ) ) {
	function room_sc_widget_aboutme($atts, $content=null){	
		$atts = room_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"avatar" => "",
			"username" => "",
			"title_website" => "",
			"description" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
		), $atts));
		extract($atts);
		$type = 'room_widget_aboutme';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_aboutme' 
								. (room_exists_visual_composer() ? ' vc_widget_aboutme wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
						. '">';
			ob_start();
			the_widget( $type, $atts, room_prepare_widgets_args(room_storage_get('widgets_args'), $id ? $id.'_widget' : 'widget_aboutme', 'widget_aboutme') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('room_shortcode_output', $output, 'trx_widget_aboutme', $atts, $content);
	}
	room_require_shortcode("trx_widget_aboutme", "room_sc_widget_aboutme");
}


// Add [trx_widget_aboutme] in the VC shortcodes list
if (!function_exists('room_sc_widget_aboutme_add_in_vc')) {
	function room_sc_widget_aboutme_add_in_vc() {

		if (!room_exists_visual_composer()) return;
		
		vc_map( array(
				"base" => "trx_widget_aboutme",
				"name" => esc_html__("Widget About Me", 'room'),
				"description" => wp_kses_data( __("Insert widget with blog owner's name, avatar and short description", 'room') ),
				"category" => esc_html__('Content', 'room'),
				"icon" => 'icon_trx_widget_aboutme',
				"class" => "trx_widget_aboutme",
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
						"param_name" => "avatar",
						"heading" => esc_html__("Avatar", 'room'),
						"description" => wp_kses_data( __("Select or upload image or write URL from other site for user's avatar. If empty - get gravatar from user's e-mail", 'room') ),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "username",
						"heading" => esc_html__("User name", 'room'),
						"description" => wp_kses_data( __("User display name. If empty - get display name of the first registered blog user", 'room') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "title_website",
						"heading" => esc_html__("Title Website", 'room'),
						"description" => wp_kses_data( __("User Website name", 'room') ),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", 'room'),
						"description" => wp_kses_data( __("Short description about user. If empty - get description of the first registered blog user", 'room') ),
						"class" => "",
						"value" => "",
						"type" => "textarea"
					),
					room_get_vc_param('id'),
					room_get_vc_param('class'),
					room_get_vc_param('css')
				)
			) );
			
		class WPBakeryShortCode_Trx_Widget_Aboutme extends WPBakeryShortCode {}

	}
	add_action('after_setup_theme', 'room_sc_widget_aboutme_add_in_vc');
}
?>