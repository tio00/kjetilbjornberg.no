<?php
/**
 * Theme lists
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



// Return numbers range
if ( !function_exists( 'room_get_list_range' ) ) {
	function room_get_list_range($from=1, $to=2, $prepend_inherit=false) {
		$list = array();
		for ($i=$from; $i<=$to; $i++)
			$list[$i] = $i;
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}



// Return styles list
if ( !function_exists( 'room_get_list_styles' ) ) {
	function room_get_list_styles($from=1, $to=2, $prepend_inherit=false) {
		$list = array();
		for ($i=$from; $i<=$to; $i++)
			$list[$i] = sprintf(esc_html__('Style %d', 'room'), $i);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list of the news styles
if ( !function_exists( 'room_get_list_news_styles' ) ) {
	function room_get_list_news_styles($prepend_inherit=false) {
		$list = array(
			'news-magazine'	=> esc_html__('Magazine',	'room'),
			'news-portfolio'=> esc_html__('Portfolio',	'room'),
			'news-extra'    => esc_html__('Extra',	    'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list of the headers
if ( !function_exists( 'room_get_list_header_styles' ) ) {
	function room_get_list_header_styles($prepend_inherit=false) {
		$list = array(
			'header-1'	=> esc_html__('Header 1',	'room'),
			'header-2'	=> esc_html__('Header 2',	'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list of the animations
if ( !function_exists( 'room_get_list_animations' ) ) {
	function room_get_list_animations($prepend_inherit=false) {
		$list = array(
			'none'			=> esc_html__('- None -',	'room'),
			'bounced'		=> esc_html__('Bounced',	'room'),
			'flash'			=> esc_html__('Flash',		'room'),
			'flip'			=> esc_html__('Flip',		'room'),
			'pulse'			=> esc_html__('Pulse',		'room'),
			'rubberBand'	=> esc_html__('Rubber Band','room'),
			'shake'			=> esc_html__('Shake',		'room'),
			'swing'			=> esc_html__('Swing',		'room'),
			'tada'			=> esc_html__('Tada',		'room'),
			'wobble'		=> esc_html__('Wobble',		'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list of the enter animations
if ( !function_exists( 'room_get_list_animations_in' ) ) {
	function room_get_list_animations_in($prepend_inherit=false) {
		$list = array(
			'none'				=> esc_html__('- None -',			'room'),
			'bounceIn'			=> esc_html__('Bounce In',			'room'),
			'bounceInUp'		=> esc_html__('Bounce In Up',		'room'),
			'bounceInDown'		=> esc_html__('Bounce In Down',		'room'),
			'bounceInLeft'		=> esc_html__('Bounce In Left',		'room'),
			'bounceInRight'		=> esc_html__('Bounce In Right',	'room'),
			'fadeIn'			=> esc_html__('Fade In',			'room'),
			'fadeInUp'			=> esc_html__('Fade In Up',			'room'),
			'fadeInDown'		=> esc_html__('Fade In Down',		'room'),
			'fadeInLeft'		=> esc_html__('Fade In Left',		'room'),
			'fadeInRight'		=> esc_html__('Fade In Right',		'room'),
			'fadeInUpBig'		=> esc_html__('Fade In Up Big',		'room'),
			'fadeInDownBig'		=> esc_html__('Fade In Down Big',	'room'),
			'fadeInLeftBig'		=> esc_html__('Fade In Left Big',	'room'),
			'fadeInRightBig'	=> esc_html__('Fade In Right Big',	'room'),
			'flipInX'			=> esc_html__('Flip In X',			'room'),
			'flipInY'			=> esc_html__('Flip In Y',			'room'),
			'lightSpeedIn'		=> esc_html__('Light Speed In',		'room'),
			'rotateIn'			=> esc_html__('Rotate In',			'room'),
			'rotateInUpLeft'	=> esc_html__('Rotate In Down Left','room'),
			'rotateInUpRight'	=> esc_html__('Rotate In Up Right',	'room'),
			'rotateInDownLeft'	=> esc_html__('Rotate In Up Left',	'room'),
			'rotateInDownRight'	=> esc_html__('Rotate In Down Right','room'),
			'rollIn'			=> esc_html__('Roll In',			'room'),
			'slideInUp'			=> esc_html__('Slide In Up',		'room'),
			'slideInDown'		=> esc_html__('Slide In Down',		'room'),
			'slideInLeft'		=> esc_html__('Slide In Left',		'room'),
			'slideInRight'		=> esc_html__('Slide In Right',		'room'),
			'zoomIn'			=> esc_html__('Zoom In',			'room'),
			'zoomInUp'			=> esc_html__('Zoom In Up',			'room'),
			'zoomInDown'		=> esc_html__('Zoom In Down',		'room'),
			'zoomInLeft'		=> esc_html__('Zoom In Left',		'room'),
			'zoomInRight'		=> esc_html__('Zoom In Right',		'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list of the out animations
if ( !function_exists( 'room_get_list_animations_out' ) ) {
	function room_get_list_animations_out($prepend_inherit=false) {
		$list = array(
			'none'			=> esc_html__('- None -',			'room'),
			'bounceOut'		=> esc_html__('Bounce Out',			'room'),
			'bounceOutUp'	=> esc_html__('Bounce Out Up',		'room'),
			'bounceOutDown'	=> esc_html__('Bounce Out Down',	'room'),
			'bounceOutLeft'	=> esc_html__('Bounce Out Left',	'room'),
			'bounceOutRight'=> esc_html__('Bounce Out Right',	'room'),
			'fadeOut'		=> esc_html__('Fade Out',			'room'),
			'fadeOutUp'		=> esc_html__('Fade Out Up',		'room'),
			'fadeOutDown'	=> esc_html__('Fade Out Down',		'room'),
			'fadeOutLeft'	=> esc_html__('Fade Out Left',		'room'),
			'fadeOutRight'	=> esc_html__('Fade Out Right',		'room'),
			'fadeOutUpBig'	=> esc_html__('Fade Out Up Big',	'room'),
			'fadeOutDownBig'=> esc_html__('Fade Out Down Big',	'room'),
			'fadeOutLeftBig'=> esc_html__('Fade Out Left Big',	'room'),
			'fadeOutRightBig'=> esc_html__('Fade Out Right Big','room'),
			'flipOutX'		=> esc_html__('Flip Out X',			'room'),
			'flipOutY'		=> esc_html__('Flip Out Y',			'room'),
			'hinge'			=> esc_html__('Hinge Out',			'room'),
			'lightSpeedOut'	=> esc_html__('Light Speed Out',	'room'),
			'rotateOut'		=> esc_html__('Rotate Out',			'room'),
			'rotateOutUpLeft'	=> esc_html__('Rotate Out Down Left',	'room'),
			'rotateOutUpRight'	=> esc_html__('Rotate Out Up Right',	'room'),
			'rotateOutDownLeft'	=> esc_html__('Rotate Out Up Left',		'room'),
			'rotateOutDownRight'=> esc_html__('Rotate Out Down Right',	'room'),
			'rollOut'			=> esc_html__('Roll Out',		'room'),
			'slideOutUp'		=> esc_html__('Slide Out Up',	'room'),
			'slideOutDown'		=> esc_html__('Slide Out Down',	'room'),
			'slideOutLeft'		=> esc_html__('Slide Out Left',	'room'),
			'slideOutRight'		=> esc_html__('Slide Out Right','room'),
			'zoomOut'			=> esc_html__('Zoom Out',		'room'),
			'zoomOutUp'			=> esc_html__('Zoom Out Up',	'room'),
			'zoomOutDown'		=> esc_html__('Zoom Out Down',	'room'),
			'zoomOutLeft'		=> esc_html__('Zoom Out Left',	'room'),
			'zoomOutRight'		=> esc_html__('Zoom Out Right',	'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return classes list for the specified animation
if (!function_exists('room_get_animation_classes')) {
	function room_get_animation_classes($animation, $speed='normal', $loop='none') {
		// speed:	fast=0.5s | normal=1s | slow=2s
		// loop:	none | infinite
		return room_param_is_off($animation) ? '' : 'animated '.esc_attr($animation).' '.esc_attr($speed).(!room_param_is_off($loop) ? ' '.esc_attr($loop) : '');
	}
}


// Return list of categories
if ( !function_exists( 'room_get_list_categories' ) ) {
	function room_get_list_categories($prepend_inherit=false) {
		if (($list = room_storage_get('list_categories'))=='') {
			$list = array();
			$args = array(
				'type'                     => 'post',
				'child_of'                 => 0,
				'parent'                   => '',
				'orderby'                  => 'name',
				'order'                    => 'ASC',
				'hide_empty'               => 0,
				'hierarchical'             => 1,
				'exclude'                  => '',
				'include'                  => '',
				'number'                   => '',
				'taxonomy'                 => 'category',
				'pad_counts'               => false );
			$taxonomies = get_categories( $args );
			if (is_array($taxonomies) && count($taxonomies) > 0) {
				foreach ($taxonomies as $cat) {
					$list[$cat->term_id] = $cat->name;
				}
			}
			room_storage_set('list_categories', $list);
		}
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list of taxonomies
if ( !function_exists( 'room_get_list_terms' ) ) {
	function room_get_list_terms($prepend_inherit=false, $taxonomy='category') {
		if (($list = room_storage_get('list_taxonomies_'.($taxonomy)))=='') {
			$list = array();
			$args = array(
				'child_of'                 => 0,
				'parent'                   => '',
				'orderby'                  => 'name',
				'order'                    => 'ASC',
				'hide_empty'               => 0,
				'hierarchical'             => 1,
				'exclude'                  => '',
				'include'                  => '',
				'number'                   => '',
				'taxonomy'                 => $taxonomy,
				'pad_counts'               => false );
			$taxonomies = get_terms( $taxonomy, $args );
			if (is_array($taxonomies) && count($taxonomies) > 0) {
				foreach ($taxonomies as $cat) {
					$list[$cat->term_id] = $cat->name;	// . ($taxonomy!='category' ? ' /'.($cat->taxonomy).'/' : '');
				}
			}
			room_storage_set('list_taxonomies_'.($taxonomy), $list);
		}
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list of post's types
if ( !function_exists( 'room_get_list_posts_types' ) ) {
	function room_get_list_posts_types($prepend_inherit=false) {
		if (($list = room_storage_get('list_posts_types'))=='') {
			$list = get_post_types();
			room_storage_set('list_posts_types', $list);
		}
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list post items from any post type and taxonomy
if ( !function_exists( 'room_get_list_posts' ) ) {
	function room_get_list_posts($prepend_inherit=false, $opt=array()) {
		$opt = array_merge(array(
			'post_type'			=> 'post',
			'post_status'		=> 'publish',
			'taxonomy'			=> 'category',
			'taxonomy_value'	=> '',
			'posts_per_page'	=> -1,
			'orderby'			=> 'post_date',
			'order'				=> 'desc',
			'return'			=> 'id'
			), is_array($opt) ? $opt : array('post_type'=>$opt));

		$hash = 'list_posts_'.($opt['post_type']).'_'.($opt['taxonomy']).'_'.($opt['taxonomy_value']).'_'.($opt['orderby']).'_'.($opt['order']).'_'.($opt['return']).'_'.($opt['posts_per_page']);
		if (($list = room_storage_get($hash))=='') {
			$list = array();
			$list['none'] = esc_html__("- Not selected -", 'room');
			$args = array(
				'post_type' => $opt['post_type'],
				'post_status' => $opt['post_status'],
				'posts_per_page' => $opt['posts_per_page'],
				'ignore_sticky_posts' => true,
				'orderby'	=> $opt['orderby'],
				'order'		=> $opt['order']
			);
			if (!empty($opt['taxonomy_value'])) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $opt['taxonomy'],
						'field' => (int) $opt['taxonomy_value'] > 0 ? 'id' : 'slug',
						'terms' => $opt['taxonomy_value']
					)
				);
			}
			$posts = get_posts( $args );
			if (is_array($posts) && count($posts) > 0) {
				foreach ($posts as $post) {
					$list[$opt['return']=='id' ? $post->ID : $post->post_title] = $post->post_title;
				}
			}
			room_storage_set($hash, $list);
		}
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list of registered users
if ( !function_exists( 'room_get_list_users' ) ) {
	function room_get_list_users($prepend_inherit=false, $roles=array('administrator', 'editor', 'author', 'contributor', 'shop_manager')) {
		if (($list = room_storage_get('list_users'))=='') {
			$list = array();
			$list['none'] = esc_html__("- Not selected -", 'room');
			$args = array(
				'orderby'	=> 'display_name',
				'order'		=> 'ASC' );
			$users = get_users( $args );
			if (is_array($users) && count($users) > 0) {
				foreach ($users as $user) {
					$accept = true;
					if (is_array($user->roles)) {
						if (is_array($user->roles) && count($user->roles) > 0) {
							$accept = false;
							foreach ($user->roles as $role) {
								if (in_array($role, $roles)) {
									$accept = true;
									break;
								}
							}
						}
					}
					if ($accept) $list[$user->user_login] = $user->display_name;
				}
			}
			room_storage_set('list_users', $list);
		}
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return slider engines list, prepended inherit (if need)
if ( !function_exists( 'room_get_list_sliders' ) ) {
	function room_get_list_sliders($prepend_inherit=false) {
		if (($list = room_storage_get('list_sliders'))=='') {
			$list = array();
			$list["swiper"] = esc_html__("Posts slider (Swiper)", 'room');
			$list = apply_filters('room_filter_list_sliders', $list);
			room_storage_set('list_sliders', $list);
		}
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with popup engines
if ( !function_exists( 'room_get_list_popup_engines' ) ) {
	function room_get_list_popup_engines($prepend_inherit=false) {
		$list = array(
			'pretty' 	=> esc_html__("Pretty photo", 'room'),
			'magnific' 	=> esc_html__("Magnific popup", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return menus list, prepended inherit
if ( !function_exists( 'room_get_list_menus' ) ) {
	function room_get_list_menus($prepend_inherit=false) {
		if (($list = room_storage_get('list_menus'))=='') {
			$list = array();
			$list['default'] = esc_html__("Default", 'room');
			$menus = wp_get_nav_menus();
			if (is_array($menus) && count($menus) > 0) {
				foreach ($menus as $menu) {
					$list[$menu->slug] = $menu->name;
				}
			}
			room_storage_set('list_menus', $list);
		}
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return custom sidebars list, prepended inherit and main sidebars item (if need)
if ( !function_exists( 'room_get_list_sidebars' ) ) {
	function room_get_list_sidebars($prepend_inherit=false) {
		if (($list = room_storage_get('list_sidebars'))=='') {
			$list = apply_filters('room_filter_list_sidebars', array(
				'sidebar_widgets'		=> esc_html__('Sidebar Widgets', 'room'),
				'header_widgets'		=> esc_html__('Header Widgets', 'room'),
				'above_page_widgets'	=> esc_html__('Above Page Widgets', 'room'),
				'above_content_widgets' => esc_html__('Above Content Widgets', 'room'),
				'below_content_widgets' => esc_html__('Below Content Widgets', 'room'),
				'below_page_widgets' 	=> esc_html__('Below Page Widgets', 'room'),
				'footer_widgets'		=> esc_html__('Footer Widgets', 'room')
				)
			);
			$custom_sidebars_number = max(0, min(10, room_get_theme_setting('custom_sidebars')));
			if (count($custom_sidebars_number) > 0) {
				for ($i=1; $i <= $custom_sidebars_number; $i++) {
					$list['custom_widgets_'.intval($i)] = sprintf(esc_html__('Custom Widgets %d', 'room'), $i);
				}
			}
			room_storage_set('list_sidebars', $list);
		}
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return sidebars positions
if ( !function_exists( 'room_get_list_sidebars_positions' ) ) {
	function room_get_list_sidebars_positions($prepend_inherit=false) {
		$list = array(
			'left'  => esc_html__('Left',  'room'),
			'right' => esc_html__('Right', 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return body styles list, prepended inherit
if ( !function_exists( 'room_get_list_body_styles' ) ) {
	function room_get_list_body_styles($prepend_inherit=false) {
		$list = array(
			'boxed'		=> esc_html__('Boxed',		'room'),
			'wide'		=> esc_html__('Wide',		'room'),
			'fullwide'	=> esc_html__('Fullwide',	'room'),
			//'fullscreen'=> esc_html__('Fullscreen',	'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return blog styles list, prepended inherit
if ( !function_exists( 'room_get_list_blog_styles' ) ) {
	function room_get_list_blog_styles($prepend_inherit=false) {
		$list = array(
			'excerpt'	=> esc_html__('Excerpt','room'),
			'grid_2'	=> esc_html__('Grid /2 columns/',	'room')
			//'grid_3'	=> esc_html__('Grid /3 columns/',	'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return blog contents list, prepended inherit
if ( !function_exists( 'room_get_list_blog_content' ) ) {
	function room_get_list_blog_content($prepend_inherit=false) {
		$list = array(
			'excerpt'	=> esc_html__('Excerpt',	'room'),
			'fullpost'	=> esc_html__('Full post',	'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with pagination styles
if ( !function_exists( 'room_get_list_paginations' ) ) {
	function room_get_list_paginations($prepend_inherit=false) {
		$list = array(
			'pages'	=> esc_html__("Page numbers", 'room'),
			'links'	=> esc_html__("Older/Newset", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list of the bg image positions
if ( !function_exists( 'room_get_list_bg_image_positions' ) ) {
	function room_get_list_bg_image_positions($prepend_inherit=false) {
		$list = array(
			'left top'		=> esc_html__('Left Top', 'room'),
			'center top'	=> esc_html__("Center Top", 'room'),
			'right top'		=> esc_html__("Right Top", 'room'),
			'left center'	=> esc_html__("Left Center", 'room'),
			'center center'	=> esc_html__("Center Center", 'room'),
			'right center'	=> esc_html__("Right Center", 'room'),
			'left bottom'	=> esc_html__("Left Bottom", 'room'),
			'center bottom'	=> esc_html__("Center Bottom", 'room'),
			'right bottom'	=> esc_html__("Right Bottom", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list of the bg image repeat
if ( !function_exists( 'room_get_list_bg_image_repeats' ) ) {
	function room_get_list_bg_image_repeats($prepend_inherit=false) {
		$list = array(
			'repeat'	=> esc_html__('Repeat', 'room'),
			'repeat-x'	=> esc_html__('Repeat X', 'room'),
			'repeat-y'	=> esc_html__('Repeat Y', 'room'),
			'no-repeat'	=> esc_html__('No Repeat', 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list of the bg image attachment
if ( !function_exists( 'room_get_list_bg_image_attachments' ) ) {
	function room_get_list_bg_image_attachments($prepend_inherit=false) {
		$list = array(
			'scroll'	=> esc_html__('Scroll', 'room'),
			'fixed'		=> esc_html__('Fixed', 'room'),
			'local'		=> esc_html__('Local', 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with 'Yes' and 'No' items
if ( !function_exists( 'room_get_list_yesno' ) ) {
	function room_get_list_yesno($prepend_inherit=false) {
		$list = array(
			"yes"	=> esc_html__("Yes", 'room'),
			"no"	=> esc_html__("No", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with 'On' and 'Of' items
if ( !function_exists( 'room_get_list_onoff' ) ) {
	function room_get_list_onoff($prepend_inherit=false) {
		$list = array(
			"on"	=> esc_html__("On", 'room'),
			"off"	=> esc_html__("Off", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with 'Show' and 'Hide' items
if ( !function_exists( 'room_get_list_showhide' ) ) {
	function room_get_list_showhide($prepend_inherit=false) {
		$list = array(
			"show" => esc_html__("Show", 'room'),
			"hide" => esc_html__("Hide", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with 'Horizontal' and 'Vertical' items
if ( !function_exists( 'room_get_list_directions' ) ) {
	function room_get_list_directions($prepend_inherit=false) {
		$list = array(
			"horizontal" => esc_html__("Horizontal", 'room'),
			"vertical"   => esc_html__("Vertical", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with 'Simple' and 'Advanced' items
if ( !function_exists( 'room_get_list_user_skills' ) ) {
	function room_get_list_user_skills($prepend_inherit=false) {
		$list = array(
			"simple"  => esc_html__("Simple", 'room'),
			"advanced" => esc_html__("Advanced", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with item's shapes
if ( !function_exists( 'room_get_list_shapes' ) ) {
	function room_get_list_shapes($prepend_inherit=false) {
		$list = array(
			"round"  => esc_html__("Round", 'room'),
			"square" => esc_html__("Square", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with item's sizes
if ( !function_exists( 'room_get_list_sizes' ) ) {
	function room_get_list_sizes($prepend_inherit=false) {
		$list = array(
			"tiny"   => esc_html__("Tiny", 'room'),
			"small"  => esc_html__("Small", 'room'),
			"medium" => esc_html__("Medium", 'room'),
			"large"  => esc_html__("Large", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with float items
if ( !function_exists( 'room_get_list_floats' ) ) {
	function room_get_list_floats($prepend_inherit=false) {
		$list = array(
			"none"  => esc_html__("None", 'room'),
			"left"  => esc_html__("Float Left", 'room'),
			"right" => esc_html__("Float Right", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with alignment items
if ( !function_exists( 'room_get_list_alignments' ) ) {
	function room_get_list_alignments($justify=false, $prepend_inherit=false) {
		$list = array(
			"none"	=> esc_html__("None", 'room'),
			"left"	=> esc_html__("Left", 'room'),
			"center"=> esc_html__("Center", 'room'),
			"right"	=> esc_html__("Right", 'room')
		);
		if ($justify) $list["justify"] = esc_html__("Justify", 'room');
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return sorting list items
if ( !function_exists( 'room_get_list_sortings' ) ) {
	function room_get_list_sortings($prepend_inherit=false) {
		$list = array(
			"date"		=> esc_html__("Date", 'room'),
			"title"		=> esc_html__("Alphabetically", 'room'),
			"views"		=> esc_html__("Popular (views count)", 'room'),
			"comments"	=> esc_html__("Most commented (comments count)", 'room'),
			"random"	=> esc_html__("Random", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with 'Ascending' and 'Descending' items
if ( !function_exists( 'room_get_list_orderings' ) ) {
	function room_get_list_orderings($prepend_inherit=false) {
		$list = array(
			"asc"  => esc_html__("Ascending", 'room'),
			"desc" => esc_html__("Descending", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list with columns widths
if ( !function_exists( 'room_get_list_columns' ) ) {
	function room_get_list_columns($prepend_inherit=false) {
		$list = array(
			"none" => esc_html__("None", 'room'),
			"1_1" => esc_html__("100%", 'room'),
			"1_2" => esc_html__("1/2", 'room'),
			"1_3" => esc_html__("1/3", 'room'),
			"2_3" => esc_html__("2/3", 'room'),
			"1_4" => esc_html__("1/4", 'room'),
			"3_4" => esc_html__("3/4", 'room'),
			"1_5" => esc_html__("1/5", 'room'),
			"2_5" => esc_html__("2/5", 'room'),
			"3_5" => esc_html__("3/5", 'room'),
			"4_5" => esc_html__("4/5", 'room'),
			"1_6" => esc_html__("1/6", 'room'),
			"5_6" => esc_html__("5/6", 'room'),
			"1_7" => esc_html__("1/7", 'room'),
			"2_7" => esc_html__("2/7", 'room'),
			"3_7" => esc_html__("3/7", 'room'),
			"4_7" => esc_html__("4/7", 'room'),
			"5_7" => esc_html__("5/7", 'room'),
			"6_7" => esc_html__("6/7", 'room'),
			"1_8" => esc_html__("1/8", 'room'),
			"3_8" => esc_html__("3/8", 'room'),
			"5_8" => esc_html__("5/8", 'room'),
			"7_8" => esc_html__("7/8", 'room'),
			"1_9" => esc_html__("1/9", 'room'),
			"2_9" => esc_html__("2/9", 'room'),
			"4_9" => esc_html__("4/9", 'room'),
			"5_9" => esc_html__("5/9", 'room'),
			"7_9" => esc_html__("7/9", 'room'),
			"8_9" => esc_html__("8/9", 'room'),
			"1_10"=> esc_html__("1/10", 'room'),
			"3_10"=> esc_html__("3/10", 'room'),
			"7_10"=> esc_html__("7/10", 'room'),
			"9_10"=> esc_html__("9/10", 'room'),
			"1_11"=> esc_html__("1/11", 'room'),
			"2_11"=> esc_html__("2/11", 'room'),
			"3_11"=> esc_html__("3/11", 'room'),
			"4_11"=> esc_html__("4/11", 'room'),
			"5_11"=> esc_html__("5/11", 'room'),
			"6_11"=> esc_html__("6/11", 'room'),
			"7_11"=> esc_html__("7/11", 'room'),
			"8_11"=> esc_html__("8/11", 'room'),
			"9_11"=> esc_html__("9/11", 'room'),
			"10_11"=> esc_html__("10/11", 'room'),
			"1_12"=> esc_html__("1/12", 'room'),
			"5_12"=> esc_html__("5/12", 'room'),
			"7_12"=> esc_html__("7/12", 'room'),
			"10_12"=> esc_html__("10/12", 'room'),
			"11_12"=> esc_html__("11/12", 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return list of locations for the featured content
if ( !function_exists( 'room_get_list_featured_locations' ) ) {
	function room_get_list_featured_locations($prepend_inherit=false) {
		$list = array(
			"default" => esc_html__('As in the post defined', 'room'),
			"center"  => esc_html__('Above the text of the post', 'room'),
			"left"    => esc_html__('To the left the text of the post', 'room'),
			"right"   => esc_html__('To the right the text of the post', 'room'),
			"alter"   => esc_html__('Alternates for each post', 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return post-format name
if ( !function_exists( 'room_get_post_format_name' ) ) {
	function room_get_post_format_name($format, $single=true) {
		$name = '';
		if ($format=='gallery')		$name = $single ? esc_html__('gallery', 'room') : esc_html__('galleries', 'room');
		else if ($format=='video')	$name = $single ? esc_html__('video', 'room') : esc_html__('videos', 'room');
		else if ($format=='audio')	$name = $single ? esc_html__('audio', 'room') : esc_html__('audios', 'room');
		else if ($format=='image')	$name = $single ? esc_html__('image', 'room') : esc_html__('images', 'room');
		else if ($format=='quote')	$name = $single ? esc_html__('quote', 'room') : esc_html__('quotes', 'room');
		else if ($format=='link')	$name = $single ? esc_html__('link', 'room') : esc_html__('links', 'room');
		else if ($format=='status')	$name = $single ? esc_html__('status', 'room') : esc_html__('statuses', 'room');
		else if ($format=='aside')	$name = $single ? esc_html__('aside', 'room') : esc_html__('asides', 'room');
		else if ($format=='chat')	$name = $single ? esc_html__('chat', 'room') : esc_html__('chats', 'room');
		else						$name = $single ? esc_html__('standard', 'room') : esc_html__('standards', 'room');
		return $name;
	}
}

// Return post-format icon name (from Fontello library)
if ( !function_exists( 'room_get_post_format_icon' ) ) {
	function room_get_post_format_icon($format) {
		$icon = 'icon-';
		if ($format=='gallery')		$icon .= 'pictures';
		else if ($format=='video')	$icon .= 'video';
		else if ($format=='audio')	$icon .= 'note';
		else if ($format=='image')	$icon .= 'picture';
		else if ($format=='quote')	$icon .= 'quote';
		else if ($format=='link')	$icon .= 'link';
		else if ($format=='status')	$icon .= 'comment';
		else if ($format=='aside')	$icon .= 'doc-text';
		else if ($format=='chat')	$icon .= 'chat';
		else						$icon .= 'book-open';
		return $icon;
	}
}

// Return fonts styles list, prepended inherit
if ( !function_exists( 'room_get_list_fonts_styles' ) ) {
	function room_get_list_fonts_styles($prepend_inherit=false) {
		$list = array(
			'i' => esc_html__('I','room'),
			'u' => esc_html__('U', 'room')
		);
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}


// Return list of the social networks share URLs
if ( !function_exists( 'room_get_list_share' ) ) {
	function room_get_list_share($soc='') {
		$list = array(
			'blogger' =>		'http://www.blogger.com/blog_this.pyra?t&u={link}&n={title}',
			'bobrdobr' =>		'http://bobrdobr.ru/add.html?url={link}&title={title}&desc={descr}',
			'delicious' =>		'http://delicious.com/save?url={link}&title={title}&note={descr}',
			'designbump' =>		'http://designbump.com/node/add/drigg/?url={link}&title={title}',
			'designfloat' =>	'http://www.designfloat.com/submit.php?url={link}',
			'digg' =>			'http://digg.com/submit?url={link}',
			'evernote' =>		'https://www.evernote.com/clip.action?url={link}&title={title}',
			'email' =>			'mailto:'.get_bloginfo('admin_email'),
//			'facebook' =>		'http://www.facebook.com/sharer.php?s=100&p[url]={link}&p[title]={title}&p[summary]={descr}&p[images][0]={image}',
			'facebook' =>		'http://www.facebook.com/sharer.php?u={link}',
			'friendfeed' =>		'http://www.friendfeed.com/share?title={title} - {link}',
			'google' =>			'http://www.google.com/bookmarks/mark?op=edit&output=popup&bkmk={link}&title={title}&annotation={descr}',
			'gplus' => 			'https://plus.google.com/share?url={link}', 
			'identi' => 		'http://identi.ca/notice/new?status_textarea={title} - {link}', 
			'juick' => 			'http://www.juick.com/post?body={title} - {link}',
			'linkedin' => 		'http://www.linkedin.com/shareArticle?mini=true&url={link}&title={title}', 
			'liveinternet' =>	'http://www.liveinternet.ru/journal_post.php?action=n_add&cnurl={link}&cntitle={title}',
			'livejournal' =>	'http://www.livejournal.com/update.bml?event={link}&subject={title}',
			'mail' =>			'http://connect.mail.ru/share?url={link}&title={title}&description={descr}&imageurl={image}',
			'memori' =>			'http://memori.ru/link/?sm=1&u_data[url]={link}&u_data[name]={title}', 
			'mister-wong' =>	'http://www.mister-wong.ru/index.php?action=addurl&bm_url={link}&bm_description={title}', 
			'mixx' =>			'http://chime.in/chimebutton/compose/?utm_source=bookmarklet&utm_medium=compose&utm_campaign=chime&chime[url]={link}&chime[title]={title}&chime[body]={descr}', 
			'moykrug' =>		'http://share.yandex.ru/go.xml?service=moikrug&url={link}&title={title}&description={descr}',
			'myspace' =>		'http://www.myspace.com/Modules/PostTo/Pages/?u={link}&t={title}&c={descr}', 
			'newsvine' =>		'http://www.newsvine.com/_tools/seed&save?u={link}&h={title}',
			'odnoklassniki' =>	'http://www.odnoklassniki.ru/dk?st.cmd=addShare&st._surl={link}&title={title}', 
			'pikabu' =>			'http://pikabu.ru/add_story.php?story_url={link}',
			'pinterest' =>		'http://pinterest.com/pin/create/button/?url={link}&media={image}&description={title}',
			'posterous' =>		'http://posterous.com/share?linkto={link}&title={title}',
			'postila' =>		'http://postila.ru/publish/?url={link}&agregator=room',
			'reddit' =>			'http://reddit.com/submit?url={link}&title={title}', 
			'rutvit' =>			'http://rutvit.ru/tools/widgets/share/popup?url={link}&title={title}', 
			'stumbleupon' =>	'http://www.stumbleupon.com/submit?url={link}&title={title}', 
			'surfingbird' =>	'http://surfingbird.ru/share?url={link}', 
			'technorati' =>		'http://technorati.com/faves?add={link}&title={title}', 
			'tumblr' =>			'http://www.tumblr.com/share?v=3&u={link}&t={title}&s={descr}', 
			'twitter' =>		'https://twitter.com/intent/tweet?text={title}&url={link}',
			'vk' =>				'http://vk.com/share.php?url={link}&title={title}&description={descr}',
			'vk2' =>			'http://vk.com/share.php?url={link}&title={title}&description={descr}',
			'vkontakte' =>		'http://vk.com/share.php?url={link}&title={title}&description={descr}',
			'webdiscover' =>	'http://webdiscover.ru/share.php?url={link}',
			'yahoo' =>			'http://bookmarks.yahoo.com/toolbar/savebm?u={link}&t={title}&d={descr}',
			'yandex' =>			'http://zakladki.yandex.ru/newlink.xml?url={link}&name={title}&descr={descr}',
			'ya' =>				'http://my.ya.ru/posts_add_link.xml?URL={link}&title={title}&body={descr}',
			'yosmi' =>			'http://yosmi.ru/index.php?do=share&url={link}'
		);
		return $soc 
					? (isset($list[$soc]) 
						? $list[$soc] 
						: '') 
					: $list;
	}
}

// Return Google fonts list
if ( !function_exists( 'room_get_list_fonts' ) ) {
	function room_get_list_fonts($prepend_inherit=false) {
		if (($list = room_storage_get('list_fonts'))=='') {
			// Google and custom fonts list:
			//$list['Advent Pro'] = array(
			//		'family'=>'sans-serif',																						// (required) font family
			//		'link'=>'Advent+Pro:100,100italic,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic',	// (optional) if you use Google font repository
			//		'css'=>room_get_file_url('/css/font-face/Advent-Pro/stylesheet.css')									// (optional) if you use custom font-face
			//		);
			$list = array(
				'Advent Pro' => array('family'=>'sans-serif'),
				'Alegreya Sans' => array('family'=>'sans-serif'),
				'Arimo' => array('family'=>'sans-serif'),
				'Asap' => array('family'=>'sans-serif'),
				'Averia Sans Libre' => array('family'=>'cursive'),
				'Averia Serif Libre' => array('family'=>'cursive'),
				'Bree Serif' => array('family'=>'serif',),
				'Cabin' => array('family'=>'sans-serif'),
				'Cabin Condensed' => array('family'=>'sans-serif'),
				'Cardo' => array('family'=>'serif'),
				'Caudex' => array('family'=>'serif'),
				'Comfortaa' => array('family'=>'cursive'),
				'Cousine' => array('family'=>'sans-serif'),
				'Crimson Text' => array('family'=>'serif'),
				'Cuprum' => array('family'=>'sans-serif'),
				'Dosis' => array('family'=>'sans-serif'),
				'Economica' => array('family'=>'sans-serif'),
				'Exo' => array('family'=>'sans-serif'),
				'Expletus Sans' => array('family'=>'cursive'),
				'Karla' => array('family'=>'sans-serif'),
				'Lato' => array('family'=>'sans-serif'),
				'Lekton' => array('family'=>'sans-serif'),
				'Lobster Two' => array('family'=>'cursive'),
				'Maven Pro' => array('family'=>'sans-serif'),
				'Merriweather' => array('family'=>'serif'),
				'Montserrat' => array('family'=>'sans-serif'),
				'Neuton' => array('family'=>'serif'),
				'Noticia Text' => array('family'=>'serif'),
				'Old Standard TT' => array('family'=>'serif'),
				'Open Sans' => array('family'=>'sans-serif'),
				'Orbitron' => array('family'=>'sans-serif'),
				'Oswald' => array('family'=>'sans-serif'),
				'Overlock' => array('family'=>'cursive'),
				'Oxygen' => array('family'=>'sans-serif'),
				'PT Serif' => array('family'=>'serif'),
				'Puritan' => array('family'=>'sans-serif'),
				'Raleway' => array('family'=>'sans-serif'),
				'Roboto' => array('family'=>'sans-serif'),
				'Roboto Slab' => array('family'=>'sans-serif'),
				'Roboto Condensed' => array('family'=>'sans-serif'),
				'Rosario' => array('family'=>'sans-serif'),
				'Share' => array('family'=>'cursive'),
				'Signika' => array('family'=>'sans-serif'),
				'Signika Negative' => array('family'=>'sans-serif'),
				'Source Sans Pro' => array('family'=>'sans-serif'),
				'Tinos' => array('family'=>'serif'),
				'Ubuntu' => array('family'=>'sans-serif'),
				'Vollkorn' => array('family'=>'serif')
			);
			$list = room_array_merge($list, room_get_list_font_faces());
			room_storage_get('list_fonts', $list);
		}
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}

// Return Custom font-face list
if ( !function_exists( 'room_get_list_font_faces' ) ) {
	function room_get_list_font_faces($prepend_inherit=false) {
		static $list = false;
		if (is_array($list)) return $list;
		$list = array();
		$dir = room_get_folder_dir("css/font-face");
		if ( is_dir($dir) ) {
			$hdir = @ opendir( $dir );
			if ( $hdir ) {
				while (($file = readdir( $hdir ) ) !== false ) {
					$pi = pathinfo( ($dir) . '/' . ($file) );
					if ( substr($file, 0, 1) == '.' || ! is_dir( ($dir) . '/' . ($file) ) )
						continue;
					$css = file_exists( ($dir) . '/' . ($file) . '/' . ($file) . '.css' ) 
						? room_get_folder_url("css/font-face/".($file).'/'.($file).'.css')
						: (file_exists( ($dir) . '/' . ($file) . '/stylesheet.css' ) 
							? room_get_folder_url("css/font-face/".($file).'/stylesheet.css')
							: '');
					if ($css != '')
						$list[$file.' ('.esc_html__('uploaded font', 'room').')'] = array('css' => $css);
				}
				@closedir( $hdir );
			}
		}
		return $list;
	}
}
?>