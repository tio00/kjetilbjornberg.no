<?php
/**
 * Default Theme Options and Internal Theme Settings
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options
// 9 - register other filters (for installer, etc.)
if (!function_exists('room_options_theme_setup2')) {
	add_action( 'after_setup_theme', 'room_options_theme_setup2', 2 );
	function room_options_theme_setup2() {
		room_options_create();
	}
}

// Step 1: Load default settings and previously saved mods
if (!function_exists('room_options_theme_setup5')) {
	add_action( 'after_setup_theme', 'room_options_theme_setup5', 5 );
	function room_options_theme_setup5() {
		room_load_theme_options();
	}
}

// Step 2: Load current theme customization mods
if (is_customize_preview()) {
	if (!function_exists('room_load_custom_options')) {
		add_action( 'wp_loaded', 'room_load_custom_options' );
		function room_load_custom_options() {
			room_load_theme_options();
		}
	}
}

// Load current values for each customizable option
if ( !function_exists('room_load_theme_options') ) {
	function room_load_theme_options() {
		$options = room_storage_get('options');
		foreach ($options as $k=>$v) {
			if (isset($v['std'])) {
				if (strpos($v['std'], '$room_')!==false) {
					$func = substr($v['std'], 1);
					if (function_exists($func)) {
						$v['std'] = $func($k);
					}
				}
				room_storage_set_array2('options', $k, 'val', isset($_GET[$k]) 
					? $_GET[$k] 
					: get_theme_mod($k, $v['std'])
					);
			}
		}
	}
}

// Override options with stored page/post meta
if ( !function_exists('room_override_theme_options') ) {
	add_action( 'wp', 'room_override_theme_options', 1 );
	function room_override_theme_options($query=null) {
		room_storage_set('blog_mode', room_detect_blog_mode());
		if (is_page())
			room_storage_set('options_meta', get_post_meta(get_the_ID(), 'room_options', true));
	}
}


// Return customizable option value
if (!function_exists('room_get_theme_option')) {
	function room_get_theme_option($name, $defa='') {
		$rez = $defa;
		if (room_storage_isset('options')) {
			if ( !room_storage_isset('options', $name) ) {
				$s = debug_backtrace();
				//array_shift($s);
				$s = array_shift($s);
				die("<pre>Undefined option '{$name}' called from:\n" . ddo($s).'</pre>');
			}
			$blog_mode = room_storage_get('blog_mode');
			// Override option from GET
			if (isset($_GET[$name])) {
				$rez = $_GET[$name];
			// Override option from GET for current blog mode
			} else if (!empty($blog_mode) && isset($_GET[$name.'_'.$blog_mode])) {
				$rez = $_GET[$name.'_'.$blog_mode];
			// Override option from current page settings (if exists)
			} else if (room_storage_isset('options_meta', $name)) {
				$rez = room_storage_get_array('options_meta', $name);
			// Override option from current blog mode settings: 'home', 'search', 'page', 'post', 'blog', etc. (if exists)
			} else if (!empty($blog_mode) && room_storage_isset('options', $name.'_'.$blog_mode, 'val')) {
				$rez = room_storage_get_array('options', $name.'_'.$blog_mode, 'val');
			// Get saved option value
			} else if (room_storage_isset('options', $name, 'val')) {
				$rez = room_storage_get_array('options', $name, 'val');
			}
			
		}
		return $rez;
	}
}


// Check if customizable option exists
if (!function_exists('room_check_theme_option')) {
	function room_check_theme_option($name) {
		return room_storage_isset('options', $name);
	}
}

// Get dependencies list from the Theme Options
if ( !function_exists('room_get_theme_dependencies') ) {
	function room_get_theme_dependencies() {
		$options = room_storage_get('options');
		$depends = array();
		foreach ($options as $k=>$v) {
			if (isset($v['dependency'])) 
				$depends[$k] = $v['dependency'];
		}
		return $depends;
	}
}

// Return internal theme setting value
if (!function_exists('room_get_theme_setting')) {
	function room_get_theme_setting($name) {
		return room_storage_isset('settings', $name) ? room_storage_get_array('settings', $name) : false;
	}
}


// Set theme setting
if ( !function_exists( 'room_set_theme_setting' ) ) {
	function room_set_theme_setting($option_name, $value) {
		if (room_storage_isset('settings', $option_name))
			room_storage_set_array('settings', $option_name, $value);
	}
}


// -----------------------------------------------------------------
// -- ONLY FOR PROGRAMMERS, NOT FOR CUSTOMER
// -- Internal theme settings
// -----------------------------------------------------------------
room_storage_set('settings', array(
	
	'custom_sidebars'	=> 8,									// How many custom sidebars will be registered (in addition to theme preset sidebars): 0 - 10

	'ajax_views_counter'=> true,								// Use AJAX for increment posts counter (if cache plugins used) 
																// or increment posts counter then loading page (without cache plugin)
	'post_counters'		=> 'comments,views,likes',				// comments | views | likes - Comma separated (without spaces) counters to display on each post meta block
	'widget_counters'	=> 'comments',							// comments | views | likes - Single counter to display on each post in the widgets and search results

	'retina_multiplier'			=> 2,							// Multiplier for images dimensions when Retina Ready is on

	'breadcrumbs_max_level' 	=> 3,							// Max number of the nested categories in the breadcrumbs (0 - unlimited)

	'ajax_search_types'			=> 'post,page',					// Comma separated (without spaces) post types which can be searched
	'ajax_search_posts_count'	=> 4,							// How many posts showed in the search results

	'socials_type'		=> 'icons',								// images|icons - Use this kind of pictograms for all socials: site social profiles, team members socials, etc.
	'socials_list'		=> array(								// Allowed socials list
		array( 'icon' => 'icon-twitter',	'url' => ''),
		array( 'icon' => 'icon-facebook',	'url' => ''),
		array( 'icon' => 'icon-gplus',		'url' => ''),
		array( 'icon' => 'icon-tumblr',		'url' => '')
	),
	'share_list'		=> array(								// Allowed share list. If 'url' is empty - use internal url (see in includes/lists.php)
		array( 'icon' => 'icon-twitter',	'url' => room_get_list_share('twitter') ),
		array( 'icon' => 'icon-facebook',	'url' => room_get_list_share('facebook') ),
		array( 'icon' => 'icon-gplus',		'url' => room_get_list_share('gplus') ),
		array( 'icon' => 'icon-tumblr',		'url' => room_get_list_share('tumblr') ),
		array( 'icon' => 'icon-mail',		'url' => room_get_list_share('mail') )
	),
	'pinit_images'		=> false,								// Add pinterest label on images

	'popup_engine'		=> 'magnific',							// Popup engine to enlarge images

	'slides_type'		=> 'bg',								// images|bg - Use image as slide's content or as slide's background
	'slides_from_posts'	=> 5,									// How many images used to build posts slider in the blog

	'use_mediaelements'	=> true,								// Load script "Media Elements" to play video and audio

	'max_excerpt_length'=> 60,									// Max words count for the excerpt in the blog style 'Excerpt'. For style 'Grid' - get half from this value
	'message_maxlength'	=> 1000,								// Max length of the message from contact form
	'mail_function'		=> 'wp_mail',							// wp_mail | mail - Function to send contact form message
	
	'admin_dummy_timeout' => 1200,								// Timeframe for PHP scripts when import dummy data
	'admin_dummy_style' => 2									// 1 | 2 - Progress bar style when import dummy data
));



// -----------------------------------------------------------------
// -- Theme fonts (Google and/or custom fonts)
// -----------------------------------------------------------------
// 'theme_fonts' => array(
//		'p' => array(
//			'family' => '"Advent Pro", sans-serif',																			// (required) font family
//			'link'   => 'Advent+Pro:100,100italic,300,300italic,400,400italic,700,700italic&subset=latin,latin-ext'			// (optional) if you use Google font repository
//			),
//		'logo' => array(
//			'family' => 'Amadeus, serif',																					// (required) font family
//			'css' => '/css/font-face/Amadeus/stylesheet.css'																// (optional) if you use custom font-face
//		)
// );
//
// Allowed keys:
//		h1 ... h6	- headers
//		p			- plain text
//		menu		- menu elements
//		logo		- logo text
room_storage_set('theme_fonts', array(
	'p' => array(					// Text
		'family'=> 'Roboto, sans-serif',
		'link'	=> 'Roboto:100,300,400,400italic,500,700'
		),
	'h1' => array(
		'family'=> 'Roboto, sans-serif'
		),
	'h2' => array(
		'family'=> 'Roboto, sans-serif'
		),
	'h3' => array(
		'family'=> 'Roboto, sans-serif'
		),
	'h4' => array(
		'family' => 'bebas_neuebold, serif',
		'css' => 'css/font-face/bebas/stylesheet.css'
		),
	'h6' => array(
		'family'=> 'Roboto, sans-serif'
		),
	'logo' => array(
		'family'=> 'Satisfy, cursive',
		'link'	=> 'Satisfy'
		),
	'menu' => array(
		'family' => 'bebas_neuebold, serif',
		'css' => 'css/font-face/bebas/stylesheet.css'
	)
));


// -----------------------------------------------------------------
// -- Theme colors for customizer
// -----------------------------------------------------------------
room_storage_set('schemes', array(

	// Color scheme: 'default'
	'default' => array(
		'title'	 => esc_html__('Default', 'room'),
		'colors' => array(
			
			// Whole block border and background
			'bg_color'				=> '#ffffff',
			'bd_color'				=> '#f9f9f9', //ok

			// Text and links colors
			'text'					=> '#65686b', //ok
			'text_light'			=> '#b5b8b9',
			'text_dark'				=> '#1a1a1a', // ok
			'text_link'				=> '#1a1a1a', // ok old- #85b7d5
			'text_hover'			=> '#64676a', // oki old #91bed9

			// Alternative blocks (submenu items, form's fields, etc.)
			'alter_bg_color'		=> '#f9f9f9', // ok - sinhron
			'alter_bg_hover'		=> '#f0f0f0',
			'alter_bd_color'		=> '#e0e0e0',
			'alter_bd_hover'		=> '#cccccc',
			'alter_text'			=> '#64676a', // ok - sinhron
			'alter_light'			=> '#aaabaf', // ok
			'alter_dark'			=> '#1a1a1a', // ok old - #171a1b - sinhron
			'alter_link'			=> '#f6cc4c', // ok - sinhron
			'alter_hover'			=> '#64676a', // ok

			// Additional accented colors (if used in the current theme)
//			'accent2'				=> '',
//			'accent2_hover'			=> '',
//			'accent3'				=> '',
//			'accent3_hover'			=> '',
			
			// Inverse blocks (text and links on accented bg)
			'inverse_text'			=> '#ffffff',
			'inverse_light'			=> '#ffffff',
			'inverse_dark'			=> '#ffffff',
			'inverse_link'			=> '#171a1b',
			'inverse_hover'			=> '#505b5e'
			)
		),

	// Color scheme: 'dark'
	'dark' => array(
		'title'  => esc_html__('Dark', 'room'),
		'colors' => array(
			
			// Whole block border and background
			'bg_color'				=> '#171a1b',
			'bd_color'				=> '#262a2c',
			
			// Text and links colors
			'text'					=> '#858585',
			'text_light'			=> '#585a5a',
			'text_dark'				=> '#ffffff', // ok
			'text_link'				=> '#ffffff', // ok
			'text_hover'			=> '#64676a', // ok
		
			// Alternative blocks (submenu items, form's fields, etc.)
			'alter_bg_color'		=> '#222222', // ok - sinhron
			'alter_bg_hover'		=> '#303030',
			'alter_bd_color'		=> '#585a5a',
			'alter_bd_hover'		=> '#707070',
			'alter_text'			=> '#64676a', // ok - sinhron
			'alter_light'			=> '#aaabaf', // ok
			'alter_dark'			=> '#ffffff', // ok - sinhron
			'alter_link'			=> '#f6cc4c', // ok - sinhron
			'alter_hover'			=> '#64676a', // ok - sinhron

			// Additional accented colors
//			'accent2'				=> '',
//			'accent2_hover'			=> '',
//			'accent3'				=> '',
//			'accent3_hover'			=> '',

			// Inverse blocks (text and links on accented bg)
			'inverse_text'			=> '#ffffff',
			'inverse_light'			=> '#ffffff',
			'inverse_dark'			=> '#ffffff',
			'inverse_link'			=> '#191102',
			'inverse_hover'			=> '#303030'
		)
	)
));



// -----------------------------------------------------------------
// -- Theme options for customizer
// -----------------------------------------------------------------
if (!function_exists('room_options_create')) {

	function room_options_create() {

		room_storage_set('options', array(
		
			// Section 'General' - main theme options
			'general' => array(
				"title" => esc_html__('General', 'room'),
				"desc" => wp_kses_data( __('General theme options', 'room') ),
				"type" => "section",
				"priority" => 10
				),
			'responsive_layouts' => array(
				"title" => esc_html__('Responsive layouts', 'room'),
				"desc" => wp_kses_data( __('Do you want use responsive layouts on small screen or still use main layout?', 'room') ),
				"std" => 'yes',
				"options" => room_get_list_yesno(),
				"type" => "switch"
				),
			'responsive_menu_width' => array(
				"title" => esc_html__('Responsive menu', 'room'),
				"desc" => wp_kses_data( __('Window width to show responsive menu', 'room') ),
				"std" => 960,
				"type" => "text"
				),
			'seo_ready' => array(
				"title" => esc_html__('Add SEO meta', 'room'),
				"desc" => wp_kses_data( __('Do you want add metadata in the single post (Google reach snippets, Facebook meta tags, etc.)? Leave "No" if you use any SEO plugin.', 'room') ),
				"std" => 'no',
				"options" => room_get_list_yesno(),
				"type" => "switch"
				),
			'retina_ready' => array(
				"title" => esc_html__('Image dimensions', 'room'),
				"desc" => wp_kses_data( __('What dimensions use for uploaded image: Original or "Retina ready" (twice enlarged)', 'room') ),
				"std" => "none",
				"size" => "medium",
				"options" => array(
					"none" => esc_html__("Original", 'room'), 
					"retina" => esc_html__("Retina", 'room')
				),
				"type" => "switch"),
			'images_quality' => array(
				"title" => esc_html__('Quality for cropped images', 'room'),
				"desc" => wp_kses_data( __('Quality (1-100) to save cropped images', 'room') ),
				"std" => 60,
				"type" => "text"
				),
			'page_preloader' => array(
				"title" => esc_html__('Show page preloader',  'room'),
				"desc" => wp_kses_data( __('Select or upload page preloader image for your site. If empty - site not using preloader',  'room') ),
				"std" => "",
				"type" => "image"
				),
		
		
			// Section 'Title & Tagline' - add theme options in the standard WP section
			'title_tagline' => array(
				"title" => esc_html__('Title, Tagline & Site icon', 'room'),
				"desc" => wp_kses_data( __('Select or upload favicon, specify site title and tagline (if need)', 'room') ),
				"type" => "section"
				),
			'favicon' => array(
				"title" => esc_html__('Favicon', 'room'),
				"desc" => wp_kses_data( __('Select or upload site favicon', 'room') ),
				"std" => '',
				"type" => "image"
				),
		
		
			// Section 'Title & Tagline' - add theme options in the standard WP section
			'header_image' => array(
				"title" => esc_html__('Header', 'room'),
				"desc" => wp_kses_data( __('Select or upload logo images, select header type and widgets set for the header', 'room') ),
				"type" => "section"
				),
			'header_style' => array(
				"title" => esc_html__('Header style', 'room'),
				"desc" => wp_kses_data( __('Select style to display the site header', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'room')
				),
				"std" => 'header-1',
				"options" => room_get_list_header_styles(),
				"type" => "select"
				),
			'header_widgets' => array(
				"title" => esc_html__('Header widgets', 'room'),
				"desc" => wp_kses_data( __('Select set of widgets to show in the header on each page', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'room'),
					"desc" => wp_kses_data( __('Select set of widgets to show in the header on this page', 'room') ),
				),
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'header_columns' => array(
				"title" => esc_html__('Header columns', 'room'),
				"desc" => wp_kses_data( __('Select number columns to show widgets in the Header. If 0 - autodetect by the widgets count', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'room')
				),
				"dependency" => array(
					'header_widgets' => array('^hide')
				),
				"std" => 0,
				"options" => room_get_list_range(0,6),
				"type" => "select"
				),
			'header_wide' => array(
				"title" => esc_html__('Header fullwide', 'room'),
				"desc" => wp_kses_data( __('Do you want to stretch the header widgets area to the entire window width?', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'room')
				),
				"std" => 1,
				"type" => "checkbox"
				),
			'logo' => array(
				"title" => esc_html__('Logo', 'room'),
				"desc" => wp_kses_data( __('Select or upload site logo', 'room') ),
				"std" => '',
				"type" => "image"
				),
			'logo_retina' => array(
				"title" => esc_html__('Logo for Retina', 'room'),
				"desc" => wp_kses_data( __('Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'room') ),
				"std" => '',
				"type" => "image"
				),
			'logo_2' => array(
				"title" => esc_html__('Logo 2', 'room'),
				"desc" => wp_kses_data( __('Select or upload site logo (header 2)', 'room') ),
				"std" => '',
				"type" => "image"
			),

			'logo_height' => array(
				"title" => esc_html__('Logo Height', 'room'),
				"desc" => wp_kses_data( __('Logo max height (px)', 'room') ),
				"std" => 'auto',
				"type" => "textarea"
			),

			

			// Section 'Content'
			'content' => array(
				"title" => esc_html__('Content', 'room'),
				"desc" => wp_kses_data( __('Options for the content area', 'room') ),
				"type" => "section",
				),
			'body_style' => array(
				"title" => esc_html__('Body style', 'room'),
				"desc" => wp_kses_data( __('Select width of the body content', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'room')
				),
				"refresh" => false,
				"std" => 'wide',
				"options" => room_get_list_body_styles(),
				"type" => "select"
				),
			'left_menu' => array(
				"title" => esc_html__('Left menu item', 'room'),
				"desc" => wp_kses_data( __('Title menu item', 'room') ),
				"std" => '',
				"refresh" => false,
				"type" => "textarea"
			),
			'left_menu_link' => array(
				"title" => esc_html__('Left menu item link', 'room'),
				"desc" => wp_kses_data( __('Left menu item link', 'room') ),
				"std" => '',
				"refresh" => false,
				"type" => "textarea"
			),
			'right_menu' => array(
				"title" => esc_html__('Right menu item', 'room'),
				"desc" => wp_kses_data( __('Title menu item', 'room') ),
				"std" => '',
				"refresh" => false,
				"type" => "textarea"
			),
			'right_menu_link' => array(
				"title" => esc_html__('Right menu item link', 'room'),
				"desc" => wp_kses_data( __('Right menu item link', 'room') ),
				"std" => '',
				"refresh" => false,
				"type" => "textarea"
			),
			'expand_content' => array(
				"title" => esc_html__('Expand content', 'room'),
				"desc" => wp_kses_data( __('Expand the content width if the sidebar is hidden', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Content', 'room')
				),
				"refresh" => false,
				"std" => 0,
				"type" => "checkbox"
				),
			'sidebar_widgets' => array(
				"title" => esc_html__('Sidebar widgets', 'room'),
				"desc" => wp_kses_data( __('Select default widgets to show in the sidebar', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Widgets', 'room')
				),
				"hidden" => true,
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'sidebar_position' => array(
				"title" => esc_html__('Sidebar position', 'room'),
				"desc" => wp_kses_data( __('Select position to show sidebar', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Widgets', 'room')
				),
				"hidden" => true,
				"refresh" => false,
				"std" => 'right',
				"options" => room_get_list_sidebars_positions(),
				"type" => "select"
				),
			'widgets_above_page' => array(
				"title" => esc_html__('Widgets above the page', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show above page (content and sidebar)', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Widgets', 'room')
				),
				"hidden" => true,
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'widgets_above_content' => array(
				"title" => esc_html__('Widgets above the content', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show at the beginning of the content area', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Widgets', 'room')
				),
				"hidden" => true,
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'widgets_below_content' => array(
				"title" => esc_html__('Widgets below the content', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show at the ending of the content area', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Widgets', 'room')
				),
				"hidden" => true,
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'widgets_below_page' => array(
				"title" => esc_html__('Widgets below the page', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show below the page (content and sidebar)', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Widgets', 'room')
				),
				"hidden" => true,
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
		
		
		
			// Section 'Footer'
			'footer' => array(
				"title" => esc_html__('Footer', 'room'),
				"desc" => wp_kses_data( __('Select set of widgets and columns number for the site footer', 'room') ),
				"type" => "section"
				),
			'footer_widgets' => array(
				"title" => esc_html__('Footer widgets', 'room'),
				"desc" => wp_kses_data( __('Select set of widgets to show in the footer', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Footer', 'room')
				),
				"std" => 'footer_widgets',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'footer_columns' => array(
				"title" => esc_html__('Footer columns', 'room'),
				"desc" => wp_kses_data( __('Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Footer', 'room')
				),
				"dependency" => array(
					'footer_widgets' => array('^hide')
				),
				"std" => 0,
				"options" => room_get_list_range(0,6),
				"type" => "select"
				),
			'footer_wide' => array(
				"title" => esc_html__('Footer fullwide', 'room'),
				"desc" => wp_kses_data( __('Do you want to stretch the footer to the entire window width?', 'room') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Footer', 'room')
				),
				"std" => 0,
				"type" => "checkbox"
				),
			'logo_footer' => array(
				"title" => esc_html__('Logo for footer', 'room'),
				"desc" => wp_kses_data( __('Select or upload site logo to display it in the footer', 'room') ),
				"std" => '',
				"type" => "image"
				),
			'logo_footer_retina' => array(
				"title" => esc_html__('Logo for footer (Retina)', 'room'),
				"desc" => wp_kses_data( __('Select or upload logo for the footer area used on Retina displays (if empty - use default logo from the field above)', 'room') ),
				"std" => '',
				"type" => "image"
				),
			'copyright' => array(
				"title" => esc_html__('Copyright', 'room'),
				"desc" => wp_kses_data( __('Copyright text in the footer', 'room') ),
				"std" => esc_html__('Themerex &copy; 2015. All rights reserved.', 'room') . "\n" . esc_html__('Terms of use and Privacy Policy', 'room'),
				"refresh" => false,
				"type" => "textarea"
				),
		
		
		
			// Section 'Homepage' - settings for home page
			'homepage' => array(
				"title" => esc_html__('Homepage', 'room'),
				"desc" => wp_kses_data( __('Select blog style and widgets to display on the homepage', 'room') ),
				"type" => "section"
				),
			'blog_style_home' => array(
				"title" => esc_html__('Blog style', 'room'),
				"desc" => wp_kses_data( __('Select posts style for the homepage', 'room') ),
				"std" => 'excerpt',
				"options" => room_get_list_blog_styles(),
				"type" => "select"
				),
			'header_widgets_home' => array(
				"title" => esc_html__('Header widgets', 'room'),
				"desc" => wp_kses_data( __('Select set of widgets to show in the header on the homepage', 'room') ),
				"std" => 'header_widgets',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'sidebar_widgets_home' => array(
				"title" => esc_html__('Sidebar widgets', 'room'),
				"desc" => wp_kses_data( __('Select sidebar to show on the homepage', 'room') ),
				"std" => 'sidebar_widgets',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'sidebar_position_home' => array(
				"title" => esc_html__('Sidebar position', 'room'),
				"desc" => wp_kses_data( __('Select position to show sidebar on the homepage', 'room') ),
				"refresh" => false,
				"std" => 'right',
				"options" => room_get_list_sidebars_positions(),
				"type" => "select"
				),
			'widgets_above_page_home' => array(
				"title" => esc_html__('Widgets above the page', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show above page (content and sidebar)', 'room') ),
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'widgets_above_content_home' => array(
				"title" => esc_html__('Widgets above the content', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show at the beginning of the content area', 'room') ),
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'widgets_below_content_home' => array(
				"title" => esc_html__('Widgets below the content', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show at the ending of the content area', 'room') ),
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'widgets_below_page_home' => array(
				"title" => esc_html__('Widgets below the page', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show below the page (content and sidebar)', 'room') ),
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			
		
		
			// Section 'Blog archive'
			'blog' => array(
				"title" => esc_html__('Blog archive', 'room'),
				"desc" => wp_kses_data( __('Options for the blog archive', 'room') ),
				"type" => "section",
				),
			'blog_style' => array(
				"title" => esc_html__('Blog style', 'room'),
				"desc" => wp_kses_data( __('Select posts style for blog archives', 'room') ),
				"std" => 'excerpt',
				"options" => room_get_list_blog_styles(),
				"type" => "select"
				),
			"blog_content" => array( 
				"title" => esc_html__('Posts content', 'room'),
				"desc" => wp_kses_data( __("Show full post's content in the blog or only post's excerpt", 'room') ),
				"std" => "excerpt",
				"options" => room_get_list_blog_content(),
				"type" => "select"
				),
			"blog_animation" => array( 
				"title" => esc_html__('Animation for posts', 'room'),
				"desc" => wp_kses_data( __('Select animation to show posts in the blog', 'room') ),
				"std" => "none",
				"options" => room_get_list_animations_in(),
				"type" => "select"
				),
			"animation_on_mobile" => array( 
				"title" => esc_html__('Allow animation on mobile', 'room'),
				"desc" => wp_kses_data( __('Allow extended animation effects on mobile devices', 'room') ),
				"std" => 'yes',
				"dependency" => array(
					'blog_animation' => array('^none')
				),
				"options" => room_get_list_yesno(),
				"type" => "switch"
				),
			"blog_pagination" => array( 
				"title" => esc_html__('Pagination style', 'room'),
				"desc" => wp_kses_data( __('Show Older/Newest posts or Page numbers below the posts list', 'room') ),
				"std" => "links",
				"options" => room_get_list_paginations(),
				"type" => "select"
				),
			'header_widgets_blog' => array(
				"title" => esc_html__('Header widgets', 'room'),
				"desc" => wp_kses_data( __('Select set of widgets to show in the header on the blog archive', 'room') ),
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'sidebar_widgets_blog' => array(
				"title" => esc_html__('Sidebar widgets', 'room'),
				"desc" => wp_kses_data( __('Select sidebar to show on the blog archive', 'room') ),
				"std" => 'sidebar_widgets',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'sidebar_position_blog' => array(
				"title" => esc_html__('Sidebar position', 'room'),
				"desc" => wp_kses_data( __('Select position to show sidebar on the blog archive', 'room') ),
				"refresh" => false,
				"std" => 'right',
				"options" => room_get_list_sidebars_positions(),
				"type" => "select"
				),
			'widgets_above_page_blog' => array(
				"title" => esc_html__('Widgets above the page', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show above page (content and sidebar)', 'room') ),
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'widgets_above_content_blog' => array(
				"title" => esc_html__('Widgets above the content', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show at the beginning of the content area', 'room') ),
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'widgets_below_content_blog' => array(
				"title" => esc_html__('Widgets below the content', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show at the ending of the content area', 'room') ),
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			'widgets_below_page_blog' => array(
				"title" => esc_html__('Widgets below the page', 'room'),
				"desc" => wp_kses_data( __('Select widgets to show below the page (content and sidebar)', 'room') ),
				"std" => 'hide',
				"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
				"type" => "select"
				),
			
		
		
		
			// Section 'Colors' - choose color scheme and customize separate colors from it
			'scheme' => array(
				"title" => esc_html__('Color scheme', 'room'),
				"desc" => wp_kses_data( __("<b>Simple settings</b> - you can change only accented color, used for links, buttons and some accented areas.", 'room') )
						. '<br>'
						. wp_kses_data( __("<b>Advanced settings</b> - change all scheme's colors and get full control over the appearance of your site!", 'room') ),
				"priority" => 70,
				"type" => "section"
				),
		
			'color_settings' => array(
				"title" => esc_html__('Color settings', 'room'),
				"desc" => '',
				"std" => 'simple',
				"options" => room_get_list_user_skills(),
				"refresh" => false,
				"type" => "switch"
				),
		
			'color_scheme' => array(
				"title" => esc_html__('Color Scheme', 'room'),
				"desc" => wp_kses_data( __('Select color scheme to decorate whole site at once', 'room') ),
				"std" => 'default',
				"options" => room_get_list_schemes(),
				"refresh" => false,
				"type" => "select"
				),
		
			'scheme_info_single' => array(
				"title" => esc_html__('Colors for single post/page', 'room'),
				"desc" => wp_kses_data( __('Specify colors for single post/page (not for alter blocks)', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"type" => "info"
				),
				
			'bg_color' => array(
				"title" => esc_html__('Background color', 'room'),
				"desc" => wp_kses_data( __('Background color of the whole page', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'bd_color' => array(
				"title" => esc_html__('Border color', 'room'),
				"desc" => wp_kses_data( __('Color of the bordered elements, separators, etc.', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
		
			'text' => array(
				"title" => esc_html__('Text', 'room'),
				"desc" => wp_kses_data( __('Plain text color on single page/post', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'text_light' => array(
				"title" => esc_html__('Light text', 'room'),
				"desc" => wp_kses_data( __('Color of the post meta: post date and author, comments number, etc.', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'text_dark' => array(
				"title" => esc_html__('Dark text', 'room'),
				"desc" => wp_kses_data( __('Color of the headers, strong text, etc.', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'text_link' => array(
				"title" => esc_html__('Links', 'room'),
				"desc" => wp_kses_data( __('Color of links and accented areas', 'room') ),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'text_hover' => array(
				"title" => esc_html__('Links hover', 'room'),
				"desc" => wp_kses_data( __('Hover color for links and accented areas', 'room') ),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
		
			'scheme_info_alter' => array(
				"title" => esc_html__('Colors for alter blocks', 'room'),
				"desc" => wp_kses_data( __('Specify colors for alter blocks, rectangular blocks with its own background color (posts in homepage, blog archive, search results, widgets on sidebar, footer, etc.)', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"type" => "info"
				),
		
			'alter_bg_color' => array(
				"title" => esc_html__('Alter background color', 'room'),
				"desc" => wp_kses_data( __('Alternative background color (form fields, alter blocks)', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'alter_bg_hover' => array(
				"title" => esc_html__('Alter hovered background color', 'room'),
				"desc" => wp_kses_data( __('Alternative background color for the focused state of the form fields, alter blocks, etc.', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'alter_bd_color' => array(
				"title" => esc_html__('Alternative border color', 'room'),
				"desc" => wp_kses_data( __('Alternative color of the bordered elements (form fields, alter blocks)', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'alter_bd_hover' => array(
				"title" => esc_html__('Alternative hovered border color', 'room'),
				"desc" => wp_kses_data( __('Alternative color for the focused state of the form fields, alter blocks, etc.', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'alter_text' => array(
				"title" => esc_html__('Alter text', 'room'),
				"desc" => wp_kses_data( __('Color of the text inside block with alternative background', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'alter_light' => array(
				"title" => esc_html__('Alter light', 'room'),
				"desc" => wp_kses_data( __('Color of the info blocks inside block with alternative background', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'alter_dark' => array(
				"title" => esc_html__('Alter dark', 'room'),
				"desc" => wp_kses_data( __('Color of the headers inside block with alternative background', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'alter_link' => array(
				"title" => esc_html__('Alter link', 'room'),
				"desc" => wp_kses_data( __('Color of the links inside block with alternative background', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'alter_hover' => array(
				"title" => esc_html__('Alter hover', 'room'),
				"desc" => wp_kses_data( __('Color of the hovered links inside block with alternative background', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
		
			'scheme_info_inverse' => array(
				"title" => esc_html__('Colors for inverse blocks', 'room'),
				"desc" => wp_kses_data( __('Specify colors for inverse blocks, rectangular blocks with background color equal to the links color or one of accented colors (if used in the current theme)', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"type" => "info"
				),
		
			'inverse_text' => array(
				"title" => esc_html__('Inverse text', 'room'),
				"desc" => wp_kses_data( __('Color of the text inside block with accented background', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'inverse_light' => array(
				"title" => esc_html__('Inverse light', 'room'),
				"desc" => wp_kses_data( __('Color of the info blocks inside block with accented background', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'inverse_dark' => array(
				"title" => esc_html__('Inverse dark', 'room'),
				"desc" => wp_kses_data( __('Color of the headers inside block with accented background', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'inverse_link' => array(
				"title" => esc_html__('Inverse link', 'room'),
				"desc" => wp_kses_data( __('Color of the links inside block with accented background', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				),
			'inverse_hover' => array(
				"title" => esc_html__('Inverse hover', 'room'),
				"desc" => wp_kses_data( __('Color of the hovered links inside block with accented background', 'room') ),
				"dependency" => array(
					'color_settings' => array('^simple')
				),
				"std" => '$room_get_scheme_color',
				"refresh" => false,
				"type" => "color"
				)
		));
		
		// Social profiles
		$room_socials_list = room_get_theme_setting('socials_list');
		$room_share_list = room_get_theme_setting('share_list');
		if (count($room_socials_list) > 0 || count($room_share_list) > 0) {
			room_storage_set_array('options', 'socials', array(
				"title" => esc_html__('Socials', 'room'),
				"desc" => '',
				"priority" => 20,
				"type" => "section"
				)
			);
			if (count($room_socials_list) > 0) {
				room_storage_set_array('options', 'socials_info', array(
					"title" => esc_html__('Socials', 'room'),
					"desc" => wp_kses_data( __('Specify URLs to your profile in some social networks (if empty - social icon not showed)', 'room') ),
					"type" => "info"
					)
				);
				foreach ($room_socials_list as $room_soc) {
					list($room_sn, $room_url, $room_icon) = room_get_social_data($room_soc, room_get_theme_setting('socials_type'), 'socials');
					room_storage_set_array('options', 'socials_'.$room_sn, array(
						"title" => strtoupper($room_sn),
						"desc" => '',
						"std" => $room_url,
						"type" => "text"
						)
					);
				}
			}
		
			if (count($room_share_list) > 0) {
				room_storage_set_array('options', 'share_info', array(
					"title" => esc_html__('Share', 'room'),
					"desc" => wp_kses_data( __('Specify URLs to share posts in some social networks (if empty - no sharing)', 'room') ),
					"type" => "info"
					)
				);
				foreach ($room_share_list as $room_soc) {
					list($room_sn, $room_url, $room_icon) = room_get_social_data($room_soc, room_get_theme_setting('socials_type'), 'share');
					room_storage_set_array('options', 'share_'.$room_sn, array(
						"title" => strtoupper($room_sn),
						"desc" => '',
						"std" => $room_url,
						"type" => "text"
						)
					);
				}
			}
		}
	}
}
?>