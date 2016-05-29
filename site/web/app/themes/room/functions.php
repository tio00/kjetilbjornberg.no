<?php
/**
 * Theme functions: init, enqueue scripts and styles, include required files and widgets
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Theme content width
if ( !isset( $content_width ) )
	$content_width = 1170;

// Set multibyte rule for the strings
//if (function_exists('mb_strlen')) @ini_set("mbstring.internal_encoding", "UTF-8");

// Theme storage
$ROOM_STORAGE = array(
	'required_plugins' => array(					// Theme required plugins
		'trx_utils',
		'instagram_feed',
		'mailchimp',
		'visual_composer',
		'woocommerce'
	),
	'widgets_args' => array(						// Arguments to register widgets
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h5 class="widget_title">',
		'after_title'   => '</h5>',
	),
	'call_stack' => array(),						// Calls stack - used to determine inner shortcodes state
	'call_args'	=> array()							// Arguments to send to the calling template
);

// Theme constants
define('DEBUG_MODE', false);


//-------------------------------------------------------
//-- Theme init
//-------------------------------------------------------

if ( !function_exists('room_theme_setup') ) {
	
	add_action( 'after_setup_theme', 'room_theme_setup' );

	function room_theme_setup() {

		// Add default posts and comments RSS feed links to head 
		add_theme_support( 'automatic-feed-links' );
		
		// Enable support for Post Thumbnails
		$mult = room_get_theme_option('retina_ready')=='retina' 
				? max(1, min(4, room_get_theme_setting('retina_multiplier'))) 
				: 1;
		add_theme_support( 'post-thumbnails' );
//		set_post_thumbnail_size(                770, 433, true );
		add_image_size( 'room-thumb-full', 1170, 658, true );
		add_image_size( 'room-thumb-big',   770, 433, true );
		add_image_size( 'room-thumb-med',   370, 208, true );
		add_image_size( 'room-thumb-medium',370, 104, true );		// Need only for widget Recent News
		add_image_size( 'room-thumb-small', 270, 152, true );
		add_image_size( 'room-thumb-tiny',  103, 103, true );
		add_image_size( 'room-thumb-avatar',270, 270, true );
		if ($mult > 1) {
			global $content_width;
			$content_width = 1170*$mult;
			add_image_size( 'room-thumb-full-@retina', 1170*$mult, 658*$mult, true );
			add_image_size( 'room-thumb-big-@retina',   770*$mult, 433*$mult, true );
			add_image_size( 'room-thumb-med-@retina',   370*$mult, 208*$mult, true );
			add_image_size( 'room-thumb-medium-@retina',370*$mult, 104*$mult, true );		// Need only for widget Recent News
			add_image_size( 'room-thumb-small-@retina', 270*$mult, 152*$mult, true );
			add_image_size( 'room-thumb-tiny-@retina',  103*$mult, 103*$mult, true );
			add_image_size( 'room-thumb-avatar-@retina',270*$mult, 270*$mult, true );
		}
		
		// Custom header setup
		add_theme_support( 'custom-header', array(
			// 'height' => 190,
			'header-text'=>false
			)
		);
		
		// Custom backgrounds setup
		add_theme_support( 'custom-background', array(
			// 'default-color'      => $default_color,
			// 'default-attachment' => 'fixed',
			)
		);
		
		// Supported posts formats
		add_theme_support( 'post-formats', array('gallery', 'video', 'audio', 'link', 'quote', 'image', 'status', 'aside', 'chat') ); 
 
 		// Autogenerate title tag
		add_theme_support('title-tag');
 		
		// Add theme menus
		add_theme_support('nav-menus');
		
		// Switch default markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support( 'html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption') );
		
		// WooCommerce Support
		add_theme_support( 'woocommerce' );
		
		// Editor custom stylesheet - for user
		add_editor_style( array_merge(
			array(
				'/css/editor-style.css'
			),
			array_values( room_theme_fonts_links() )
			)
		);	
		
		// Make theme available for translation
		// Translations can be filed in the /languages/ directory
		load_theme_textdomain( 'room', room_get_folder_dir('languages') );
	
		// Register navigation menu
		register_nav_menus(array(
			'menu_main'		=> esc_html__('Main Menu', 'room'),
			'menu_footer'	=> esc_html__('Footer Menu', 'room')
			)
		);

		// Register widgetized areas
		$sidebars = room_get_list_sidebars();
		if (is_array($sidebars) && count($sidebars) > 0) {
			foreach ($sidebars as $id=>$name) {
				register_sidebar( array_merge( array(
													'name'          => $name,
													'id'            => $id
												),
												room_storage_get('widgets_args')
									)
				);
			}
		}

		// Excerpt filters
		add_filter( 'excerpt_length',						'room_excerpt_length' );
		add_filter( 'excerpt_more',							'room_excerpt_more' );
		
		// Enqueue scripts and styles for frontend
		add_action('wp_enqueue_scripts', 					'room_frontend_scripts');
		add_action('wp_footer',		 						'room_frontend_scripts_inline');

		// Enqueue scripts and styles for admin
		add_action("admin_enqueue_scripts",					'room_admin_scripts');
		add_action("admin_head",							'room_admin_scripts_inline');

		// AJAX: Send contact form data
		add_action('wp_ajax_send_contact_form',				'room_send_contact_form');
		add_action('wp_ajax_nopriv_send_contact_form',		'room_send_contact_form');
		
		// TGM Activation plugin
		add_action('tgmpa_register',						'room_register_plugins');

		// Set options for importer
		add_filter( 'room_filter_importer_options',		'room_importer_set_options' );

		// Frontend actions
		if ( !is_admin() ) {
			// Add og:image meta tag for facebook
			if ( room_param_is_on(room_get_theme_option('seo_ready')) ) 
				add_action( 'wp_head', 'room_facebook_og_tags', 5 );
		}
	}

}


//-------------------------------------------------------
//-- Theme scripts and styles
//-------------------------------------------------------

// Load frontend scripts
if ( !function_exists( 'room_frontend_scripts' ) ) {
	//add_action('wp_enqueue_scripts', 'room_frontend_scripts');
	function room_frontend_scripts() {
		
		// Enqueue styles
		//------------------------
		
		// Links to selected fonts
		$links = room_theme_fonts_links();
		if (count($links) > 0) {
			foreach ($links as $slug => $link)
				room_enqueue_style( 'font-style-'.$slug, $link );
		}
		
		// Fontello styles must be loaded before main stylesheet
		room_enqueue_style( 'fontello-style',  room_get_file_url('css/fontello/fontello-embedded.css') );
		//room_enqueue_style( 'fontello-animation-style', room_get_file_url('css/fontello/animation.css'), array(), null);

		// Main stylesheet
		room_enqueue_style( 'room-main-style', get_stylesheet_uri(), array(), null );
		
		// Animations
		if (room_get_theme_option('blog_animation')!='none' && (room_get_theme_option('animation_on_mobile')=='yes' || !wp_is_mobile()) && !room_vc_is_frontend())
			room_enqueue_style( 'animation-style',	room_get_file_url('css/animation.css') );

		// Responsive
		if (room_get_theme_option('responsive_layouts') == 'yes')
			room_enqueue_style( 'room-responsive-style', room_get_file_url('css/responsive.css') );

		// Custom stylesheet
		$custom_css_file = room_get_file_url('css/custom.css');
		if ( !is_customize_preview() && !isset($_GET['color_scheme']) && !DEBUG_MODE )
			room_enqueue_style( 'room-custom-style', room_get_file_url('css/custom.css') );
		else
			wp_add_inline_style( 'room-main-style', room_customizer_get_css() );

		// Add post nav background
		room_add_bg_in_post_nav();

		// Disable loading JQuery UI CSS
		wp_deregister_style('jquery_ui');
		wp_deregister_style('date-picker-css');


		// Enqueue scripts	
		//------------------------

		// Superfish Menu
		room_enqueue_script( 'superfish-script', room_get_file_url('js/superfish.min.js'), array('jquery') );

		room_enqueue_script( 'room-utils-script', room_get_file_url('js/_utils.js'), array('jquery') );
		room_enqueue_script( 'room-init-script', room_get_file_url('js/_init.js'), array('jquery') );	

		// Comments
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Media elements library	
		if (room_get_theme_setting('use_mediaelements')) {
			wp_enqueue_style ( 'mediaelement' );
			wp_enqueue_style ( 'wp-mediaelement' );
			wp_enqueue_script( 'mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		} else {
			//$wp_styles->done[]	= 'mediaelement';
			//$wp_styles->done[]	= 'wp-mediaelement';
			//$wp_scripts->done[]	= 'mediaelement';
			//$wp_scripts->done[]	= 'wp-mediaelement';
			wp_deregister_style('mediaelement');
			wp_deregister_style('wp-mediaelement');
			wp_deregister_script('mediaelement');
			wp_deregister_script('wp-mediaelement');
		}

		// Skip link focus
		room_enqueue_script( 'skip-link-focus-fix-script', room_get_file_url('js/skip-link-focus-fix.js') );
		
		// Load popup engine to enlarge images
		if ( is_singular() )
			room_enqueue_popup();
	}
}

//  Enqueue Swiper Slider scripts and styles
if ( !function_exists( 'room_enqueue_slider' ) ) {
	function room_enqueue_slider($engine='all') {
		if ($engine=='all' || $engine=='swiper') {
			room_enqueue_style(  'swiperslider-style',	room_get_file_url('js/swiper/swiper.css'), array(), null );
			room_enqueue_script( 'swiperslider-script',room_get_file_url('js/swiper/swiper.jquery.js'), array('jquery'), null, true );
		}
	}
}

// Enqueue Theme Popup scripts and styles
// Link must have attribute: data-rel="popup" or data-rel="popup[gallery]"
if ( !function_exists( 'room_enqueue_popup' ) ) {
	function room_enqueue_popup($engine='') {
		if ($engine=='pretty' || (empty($engine) && room_get_theme_setting('popup_engine')=='pretty')) {
			room_enqueue_style(  'prettyphoto-style',	room_get_file_url('js/prettyphoto/css/prettyPhoto.css'), array(), null );
			room_enqueue_script( 'prettyphoto-script',	room_get_file_url('js/prettyphoto/jquery.prettyPhoto.min.js'), array('jquery'), 'no-compose', true );
		} else if ($engine=='magnific' || (empty($engine) && room_get_theme_setting('popup_engine')=='magnific')) {
			room_enqueue_style(  'magnific-style',	room_get_file_url('js/magnific/magnific-popup.css'), array(), null );
			room_enqueue_script( 'magnific-script',room_get_file_url('js/magnific/jquery.magnific-popup.min.js'), array('jquery'), '', true );
		}
	}
}

//  Add inline scripts in the footer for frontend
if (!function_exists('room_frontend_scripts_inline')) {
	function room_frontend_scripts_inline() {

		echo "<script type=\"text/javascript\">"
			
			. "if (typeof ROOM_STORAGE == 'undefined') var ROOM_STORAGE = {};"
			
			. "jQuery(document).ready(function() {"
			
			// AJAX parameters
			. "ROOM_STORAGE['ajax_url']				= '" . esc_url(admin_url('admin-ajax.php')) . "';"
			. "ROOM_STORAGE['ajax_nonce']			= '" . esc_attr(wp_create_nonce(admin_url('admin-ajax.php'))) . "';"
			
			// Site base url
			. "ROOM_STORAGE['site_url']				= '" . get_site_url() . "';"
			
			// User logged in
			. "ROOM_STORAGE['user_logged_in']		= " . (is_user_logged_in() ? 'true' : 'false') . ";"
			
			// Menu width for responsive mode and relayout mode
			. "ROOM_STORAGE['menu_mode_relayout_width']		= 0;"
			. "ROOM_STORAGE['menu_mode_responsive_width']	= " . max(480, room_get_theme_option('responsive_menu_width')) . ";"

			// Menu animation
			. "ROOM_STORAGE['menu_animation_in']			= 'none';"
			. "ROOM_STORAGE['menu_animation_out']			= 'none';"
			
			// Popup engine to zoom images (pretty|magnific)
			. "ROOM_STORAGE['popup_engine']			= '" . room_get_theme_setting('popup_engine') . "';"

			// Video and Audio tag wrapper
			. "ROOM_STORAGE['use_mediaelements']	= " . (room_get_theme_setting('use_mediaelements') ? 'true' : 'false') . ";"

			// Messages max length
			. "ROOM_STORAGE['message_maxlength']	= " . intval(room_get_theme_setting('message_maxlength')) . ";"

			
			// Internal vars - do not change it!
			
			// Flag for review mechanism
			. "ROOM_STORAGE['admin_mode']			= false;"

			// E-mail mask
			. "ROOM_STORAGE['email_mask']			= '^([a-zA-Z0-9_\\-]+\\.)*[a-zA-Z0-9_\\-]+@[a-z0-9_\\-]+(\\.[a-z0-9_\\-]+)*\\.[a-z]{2,6}$';"
			
			// Strings for translation
			. "ROOM_STORAGE['strings']				= {
									'ajax_error':			'" . addslashes(esc_html__('Invalid server answer!', 'room')) . "',
									'magnific_loading':		'" . addslashes(esc_html__('Loading image', 'room')) . "',
									'magnific_error':		'" . addslashes(esc_html__('Error loading image', 'room')) . "',
									'error_like':			'" . addslashes(esc_html__('Error saving your like! Please, try again later.', 'room')) . "',
									'error_global':			'" . addslashes(esc_html__('Error data validation!', 'room')) . "',
									'name_empty':			'" . addslashes(esc_html__("The name can't be empty", 'room')) . "',
									'name_long':			'" . addslashes(esc_html__('Too long name', 'room')) . "',
									'email_empty':			'" . addslashes(esc_html__('Too short (or empty) email address', 'room')) . "',
									'email_long':			'" . addslashes(esc_html__('Too long email address', 'room')) . "',
									'email_not_valid':		'" . addslashes(esc_html__('Invalid email address', 'room')) . "',
									'text_empty':			'" . addslashes(esc_html__("The message text can't be empty", 'room')) . "',
									'text_long':			'" . addslashes(esc_html__('Too long message text', 'room')) . "',
									'search_error':			'" . addslashes(esc_html__('Search error! Try again later.', 'room')) . "',
									'send_complete':		'" . addslashes(esc_html__("Send message complete!", 'room')) . "',
									'send_error':			'" . addslashes(esc_html__('Transmit failed!', 'room')) . "'
			};"

			. "});"
			. "</script>";
	}
}
	
// Load required styles and scripts for admin mode
if ( !function_exists( 'room_admin_scripts' ) ) {
	//add_action("admin_enqueue_scripts", 'room_admin_scripts');
	function room_admin_scripts() {
		room_enqueue_style(  'room-admin-style',  room_get_file_url('css/admin.css') );
		room_enqueue_script( 'room-utils-script', room_get_file_url('js/_utils.js'), array('jquery') );
		room_enqueue_script( 'room-admin-script', room_get_file_url('js/_admin.js'), array('jquery') );
	}
}

// Prepare required styles and scripts for admin mode
if ( !function_exists( 'room_admin_scripts_inline' ) ) {
	//add_action("admin_head", 'room_admin_scripts_inline');
	function room_admin_scripts_inline() {
		?>
		<script>
			if (typeof ROOM_STORAGE == 'undefined') var ROOM_STORAGE = {};
			jQuery(document).ready(function() {
				ROOM_STORAGE['admin_mode']	= true;
				ROOM_STORAGE['ajax_url']	= "<?php echo esc_url(admin_url('admin-ajax.php')); ?>";
				ROOM_STORAGE['ajax_nonce'] 	= "<?php echo esc_attr(wp_create_nonce(admin_url('admin-ajax.php'))); ?>";
				ROOM_STORAGE['user_logged_in'] = true;
			});
		</script>
		<?php
	}
}


//-------------------------------------------------------
//-- Theme fonts
//-------------------------------------------------------
if ( !function_exists('room_theme_fonts_links') ) {
	function room_theme_fonts_links() {
		$links = array();
		if (!room_storage_empty('theme_fonts')) {
			$theme_fonts = room_storage_get('theme_fonts');
			if (count($theme_fonts) > 0) {
				$google_fonts = '';
				foreach ($theme_fonts as $tag => $font) {
					if (empty($font['family']) || room_param_is_inherit($font['family']) || (empty($font['link']) && empty($font['css']))) continue;
					$font_name = str_replace('"', '', ($pos=strpos($font['family'], ','))!==false ? substr($font['family'], 0, $pos) : $font['family']);
					if (!empty($font['css'])) {
						$css = $font['css'];
						$links[str_replace(' ', '-', $font_name)] = room_get_file_url($css);
					} else {
						$google_fonts .= ($google_fonts ? '|' : '') 
							. (!empty($font['link']) ? $font['link'] : str_replace(' ', '+', $font_name).':400,400italic,700,700italic');
					}
				}
				if ($google_fonts) {
					//$links['google_fonts'] = room_get_protocol() . '://fonts.googleapis.com/css?family=' . $google_fonts . '&subset=latin,latin-ext';
					$links['google_fonts'] = add_query_arg( 'family', urlencode( $google_fonts.'&subset=latin,latin-ext' ), room_get_protocol() ."://fonts.googleapis.com/css" );
				}
			}





		}
		return $links;
	}
}



//-------------------------------------------------------
//-- The Excerpt
//-------------------------------------------------------
if ( !function_exists('room_excerpt_length') ) {
	function room_excerpt_length( $length ) {
		return max(1, room_get_theme_setting('max_excerpt_length'));
	}
}

if ( !function_exists('room_excerpt_more') ) {
	function room_excerpt_more( $more ) {
		return '&hellip;';
	}
}



//-------------------------------------------------------
//-- Featured images
//-------------------------------------------------------
if ( !function_exists('room_image_sizes') ) {
	add_filter( 'image_size_names_choose', 'room_image_sizes' );
	function room_image_sizes( $sizes ) {
		$theme_sizes = array(
			'room-thumb-full'	=> esc_html__( 'Fullsize image', 'room' ),
			'room-thumb-big'	=> esc_html__( 'Large image', 'room' ),
			'room-thumb-med'	=> esc_html__( 'Medium image', 'room' ),
			'room-thumb-small'	=> esc_html__( 'Small image', 'room' ),
			'room-thumb-avatar'	=> esc_html__( 'Big square avatar', 'room' ),
			'room-thumb-tiny'	=> esc_html__( 'Small square avatar', 'room' )
		);
		if (room_get_theme_option('retina_ready')=='retina') {
			$theme_sizes['room-thumb-full-@retina'] 	= esc_html__( 'Fullsize image @2x', 'room' );
			$theme_sizes['room-thumb-big-@retina']		= esc_html__( 'Large image @2x', 'room' );
			$theme_sizes['room-thumb-med-@retina']		= esc_html__( 'Medium image @2x', 'room' );
			$theme_sizes['room-thumb-small-@retina']	= esc_html__( 'Small image @2x', 'room' );
			$theme_sizes['room-thumb-avatar-@retina']	= esc_html__( 'Big square avatar @2x', 'room' );
			$theme_sizes['room-thumb-tiny-@retina']		= esc_html__( 'Small square avatar @2x', 'room' );
		}
		return array_merge( $sizes, $theme_sizes );
	}
}

// Add featured image as background image to post navigation elements.
if ( !function_exists('room_add_bg_in_post_nav') ) {
	function room_add_bg_in_post_nav() {
		if ( ! is_single() ) return;
	
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );
		$css      = '';
	
		if ( is_attachment() &&  $previous->post_type == 'attachment' ) return;
	
		if ( $previous && has_post_thumbnail( $previous->ID ) ) {
			$prevthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $previous->ID ), 'thumb_med' );
			$css .= '
				.post-navigation .nav-previous a { background-image: url(' . esc_url( $prevthumb[0] ) . '); }
				.post-navigation .nav-previous .post-title, .post-navigation .nav-previous a:hover .post-title, .post-navigation .nav-previous .meta-nav, .post-navigation .nav-previous a:after { color: #fff; }
				.post-navigation .nav-previous a:before { background-color: rgba(0, 0, 0, 0.4); }
				.post-navigation .nav-previous a:after { border-color: #fff; }
			';
		}
	
		if ( $next && has_post_thumbnail( $next->ID ) ) {
			$nextthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $next->ID ), 'thumb_med' );
			$css .= '
				.post-navigation .nav-next a { background-image: url(' . esc_url( $nextthumb[0] ) . '); }
				.post-navigation .nav-next .post-title, .post-navigation .nav-next a:hover .post-title, .post-navigation .nav-next .meta-nav, .post-navigation .nav-next a:after { color: #fff; }
				.post-navigation .nav-next a:before { background-color: rgba(0, 0, 0, 0.4); }
				.post-navigation .nav-next a:after { border-color: #fff; }
			';
		}
	
		wp_add_inline_style( 'room-main-style', $css );
	}
}


//-------------------------------------------------------
//-- Contact form
//-------------------------------------------------------
if ( !function_exists('room_show_contact_form') ) {
	function room_show_contact_form($args=array()) {
		$args = array_merge(array(
			'title' => '',
			'description' => ''
		), $args);
		?>
		<div id="contact_form" class="sc_contact_form">
			<?php if (!empty($args['title'])) { ?>
			<h3 class="sc_contact_form_title"><?php echo trim(room_prepare_macros($args['title'])); ?></h3>
			<?php } ?>
			<?php if (!empty($args['description'])) { ?>
			<div class="sc_contact_form_descr"><?php echo trim(room_prepare_macros($args['description'])); ?></div>'
			<?php } ?>
			<form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
				<div class="sc_contact_form_info columns_wrap"><div class="sc_contact_form_item column-1_2 sc_contact_form_field label_over"><input id="sc_contact_form_username" type="text" name="username" placeholder="<?php esc_attr_e('Name *', 'room'); ?>"></div><div class="sc_contact_form_item column-1_2 sc_contact_form_field label_over"><input id="sc_contact_form_email" type="text" name="email" placeholder="<?php esc_attr_e('E-mail *', 'room'); ?>"></div></div>
				<div class="sc_contact_form_item sc_contact_form_message label_over"><textarea id="sc_contact_form_message" name="message" placeholder="<?php esc_attr_e('Message', 'room'); ?>"></textarea></div>
				<div class="sc_contact_form_item sc_contact_form_button"><button><?php esc_html_e('Send Message', 'room'); ?></button></div>
				<div class="result sc_infobox"></div>
			</form>
		</div>
		<?php
	}
}

// AJAX Callback: Send contact form data
if ( !function_exists( 'room_send_contact_form' ) ) {
	function room_send_contact_form() {

		if ( !wp_verify_nonce( room_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
	
		$response = array('error'=>'');
		if (!($contact_email = get_option('admin_email'))) 
			$response['error'] = esc_html__('Unknown admin email!', 'room');
		else {
			parse_str($_POST['data'], $post_data);
			$user_name	= room_strshort($post_data['username'],	100);
			$user_email	= room_strshort($post_data['email'],	100);
			$user_msg	= room_strshort($post_data['message'],	room_get_theme_setting('message_maxlength'));
		
			$subj = sprintf(esc_html__('Site %s - Contact form message from %s', 'room'), get_bloginfo('site_name'), $user_name);
			$msg = "\n".esc_html__('Name:', 'room')   .' '.esc_html($user_name)
				.  "\n".esc_html__('E-mail:', 'room') .' '.esc_html($user_email)
				.  "\n".esc_html__('Message:', 'room').' '.esc_html($user_msg)
				.  "\n\n............. " . get_bloginfo('site_name') . " (" . esc_url(home_url('/')) . ") ............";

			$mail = room_get_theme_setting('mail_function');
			if (!@$mail($contact_email, $subj, $msg)) {
				$response['error'] = esc_html__('Error send message!', 'room');
			}
		
			echo json_encode($response);
			die();
		}
	}
}



//-------------------------------------------------------
//-- Third party plugins
//-------------------------------------------------------

// Register optional plugins
if ( !function_exists( 'room_register_plugins' ) ) {
	function room_register_plugins() {

		$plugins = apply_filters('room_filter_required_plugins', array(
			array(
				'name' 		=> 'Room Utilities',
				'version'	=> '2.2',				// Minimal required version
				'slug' 		=> 'trx_utils',
				'source'	=> room_get_file_dir('plugins/install/trx_utils.zip'),
				'force_activation'   => true,		// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				'force_deactivation' => true,		// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
				'required' 	=> true
			),
		));
		$config = array(
			'id'           => 'tgmpa',                 // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'themes.php',            // Parent menu slug.
			'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => true,                    // Automatically activate plugins after installation or not.
			'message'      => ''                       // Message to output right before the plugins table.
		);

		tgmpa( $plugins, $config );
	}
}


// Set theme specific importer options
if ( !function_exists( 'room_importer_set_options' ) ) {
	//add_filter( 'room_filter_importer_options',	'room_importer_set_options' );
	function room_importer_set_options($options=array()) {
		if (is_array($options)) {
			// Please, note! The following text strings should not be translated, 
			// since these are article titles, menu locations, etc. used by us in the demo-website. 
			// They are required when setting some of the WP parameters during demo data installation 
			// and cannot be changed/translated into other languages.
			$options['debug'] = DEBUG_MODE;
			$options['domain_dev'] = 'room.my';
			$options['domain_demo'] = 'room.axiomthemes.com';
			$options['menus'] = array(								// Menus locations and names
				'menu-main'	  => esc_html__('Main menu', 'room'),
				'menu-footer' => esc_html__('Footer menu', 'room')
			);
			$options['file_with_attachments'] = array(				// Array with names of the attachments or it parts
				'http://room.axiomthemes.com/wp-content/imports/uploads.001',
				'http://room.axiomthemes.com/wp-content/imports/uploads.002',
				'http://room.axiomthemes.com/wp-content/imports/uploads.003',
				'http://room.axiomthemes.com/wp-content/imports/uploads.004',
				'http://room.axiomthemes.com/wp-content/imports/uploads.005'
			);
			$options['attachments_by_parts'] = true;	// Files above are parts of single file - large media archive. They are must be concatenated in one file before unpacking
		}
		return $options;
	}
}



//-------------------------------------------------------
//-- Include files
//-------------------------------------------------------

get_template_part('includes/utils');
get_template_part('includes/storage');
get_template_part('includes/lists');
get_template_part('includes/wp');

if (is_admin()) {
	get_template_part('includes/tgm/class-tgm-plugin-activation');
	get_template_part('importer/importer');
}

get_template_part('theme-options/theme.customizer');

get_template_part('plugins/plugin.instagram-feed');
get_template_part('plugins/plugin.mailchimp');
get_template_part('plugins/plugin.visual-composer');
get_template_part('plugins/plugin.woocommerce');
get_template_part('plugins/plugin.revslider');

get_template_part('widgets/about_me');
get_template_part('widgets/advert');

get_template_part('widgets/calendar');
get_template_part('widgets/categories_list');
get_template_part('widgets/popular_posts');
get_template_part('widgets/recent_posts');
get_template_part('widgets/recent_news');
get_template_part('widgets/slider');
get_template_part('widgets/socials');
get_template_part('widgets/twitter');
?>