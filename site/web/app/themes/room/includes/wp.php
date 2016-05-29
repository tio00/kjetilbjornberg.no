<?php
/**
 * WP tags and utils
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Theme init
if (!function_exists('room_wp_theme_setup')) {
	add_action( 'after_setup_theme', 'room_wp_theme_setup' );
	function room_wp_theme_setup() {
		
		// Init post counters
		add_action('save_post',						'room_init_post_counters');

		// Add image property to the "category" taxonomy
		add_action('category_edit_form_fields',		'room_category_show_custom_fields', 10, 1 );
		add_action('category_add_form_fields',		'room_category_show_custom_fields', 10, 1 );
		add_action('edited_category',				'room_category_save_custom_fields', 10, 1 );
		add_action('created_category',				'room_category_save_custom_fields', 10, 1 );
		add_filter('manage_edit-category_columns',	'room_category_add_custom_column', 9);
		add_filter('manage_category_custom_column',	'room_category_fill_custom_column', 9, 3);

		// Increment post views counter
		add_action('wp_ajax_post_counter', 			'room_callback_post_counter');
		add_action('wp_ajax_nopriv_post_counter',	'room_callback_post_counter');

		// AJAX: Incremental search
		add_action('wp_ajax_ajax_search',			'room_callback_ajax_search');
		add_action('wp_ajax_nopriv_ajax_search',	'room_callback_ajax_search');

		// Filters wp_title to print a neat <title> tag based on what is being viewed
		add_filter('wp_title',						'room_wp_title', 10, 2);
	}
}


/* Post views and likes
-------------------------------------------------------------------------------- */

//Return Post Views Count
if (!function_exists('room_get_post_views')) {
	function room_get_post_views($id=0){
		global $wp_query;
		if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
		$count_key = 'room_post_views_count';
		$count = get_post_meta($id, $count_key, true);
		if ($count===''){
			delete_post_meta($id, $count_key);
			add_post_meta($id, $count_key, '0');
			$count = 0;
		}
		return $count;
	}
}

//Set Post Views Count
if (!function_exists('room_set_post_views')) {
	function room_set_post_views($id=0, $counter=-1) {
		global $wp_query;
		if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
		$count_key = 'room_post_views_count';
		$count = get_post_meta($id, $count_key, true);
		if ($count===''){
			delete_post_meta($id, $count_key);
			add_post_meta($id, $count_key, 1);
		} else {
			$count = $counter >= 0 ? $counter : $count+1;
			update_post_meta($id, $count_key, $count);
		}
	}
}

// Increment Post Views Count
if (!function_exists('room_inc_post_views')) {
	function room_inc_post_views($id=0, $inc=0) {
		global $wp_query;
		if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
		$count_key = 'room_post_views_count';
		$count = get_post_meta($id, $count_key, true);
		if ($count===''){
			$count = max(0, $inc);
			delete_post_meta($id, $count_key);
			add_post_meta($id, $count_key, $count);
		} else {
			$count += $inc;
			update_post_meta($id, $count_key, $count);
		}
		return $count;
	}
}

//Return Post Likes Count
if (!function_exists('room_get_post_likes')) {
	function room_get_post_likes($id=0){
		global $wp_query;
		if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
		$count_key = 'room_post_likes_count';
		$count = get_post_meta($id, $count_key, true);
		if ($count===''){
			delete_post_meta($id, $count_key);
			add_post_meta($id, $count_key, '0');
			$count = 0;
		}
		return $count;
	}
}

//Set Post Likes Count
if (!function_exists('room_set_post_likes')) {
	function room_set_post_likes($id=0, $counter=-1) {
		global $wp_query;
		if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
		$count_key = 'room_post_likes_count';
		$count = get_post_meta($id, $count_key, true);
		if ($count===''){
			delete_post_meta($id, $count_key);
			add_post_meta($id, $count_key, 1);
		} else {
			$count = $counter >= 0 ? $counter : $count+1;
			update_post_meta($id, $count_key, $count);
		}
	}
}

// Increment Post Likes Count
if (!function_exists('room_inc_post_likes')) {
	function room_inc_post_likes($id=0, $inc=0) {
		global $wp_query;
		if (!$id) $id = $wp_query->current_post>=0 ? get_the_ID() : $wp_query->post->ID;
		$count_key = 'room_post_likes_count';
		$count = get_post_meta($id, $count_key, true);
		if ($count===''){
			$count = max(0, $inc);
			delete_post_meta($id, $count_key);
			add_post_meta($id, $count_key, $count);
		} else {
			$count += $inc;
			update_post_meta($id, $count_key, $count);
		}
		return $count;
	}
}


// Set post likes/views counters when save/publish post
if ( !function_exists( 'room_init_post_counters' ) ) {
	// add_action('save_post',		'room_init_post_counters');
	function room_init_post_counters($id) {
		global $post_type, $post;
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $id;
		}
		// check permissions
		if (empty($post_type) || !current_user_can('edit_'.$post_type, $id)) {
			return $id;
		}
		if ( !empty($post->ID) && $id==$post->ID ) {
			room_get_post_views($id);
			room_get_post_likes($id);
		}
	}
}


// AJAX: Set post likes/views count
if ( !function_exists( 'room_callback_post_counter' ) ) {
	// add_action('wp_ajax_post_counter', 			'room_callback_post_counter');
	// add_action('wp_ajax_nopriv_post_counter',	'room_callback_post_counter');
	function room_callback_post_counter() {
		
		if ( !wp_verify_nonce( room_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
	
		$response = array('error'=>'', 'counter' => 0);
		
		$id = (int) $_REQUEST['post_id'];
		if (isset($_REQUEST['likes'])) {
			$response['counter'] = room_inc_post_likes($id, (int) $_REQUEST['likes']);
		} else if (isset($_REQUEST['views'])) {
			$response['counter'] = room_inc_post_views($id, (int) $_REQUEST['views']);
		}
		echo json_encode($response);
		die();
	}
}





/* Blog utilities
-------------------------------------------------------------------------------- */

// Return current site protocol
if (!function_exists('room_get_protocol')) {
	function room_get_protocol() {
		return is_ssl() ? 'https' : 'http';
	}
}

// Detect current blog mode to get correspond options (post | page | search | blog | home)
if (!function_exists('room_detect_blog_mode')) {
	function room_detect_blog_mode() {
		if (is_front_page())
			$mode = 'home';
		else if (is_single())
			$mode = 'post';
		else if (is_page())
			$mode = 'page';
		//else if (is_search())
		//	$mode = 'search';
		else
			$mode = 'blog';
		return apply_filters('room_filter_detect_blog_mode', $mode);
	}
}


// Filters wp_title to print a neat <title> tag based on what is being viewed.
if ( !function_exists( 'room_wp_title' ) ) {
	// add_filter( 'wp_title', 'room_wp_title', 10, 2 );
	function room_wp_title( $title, $sep ) {
		if ( is_feed() ) return $title;
		if (floatval(get_bloginfo('version')) < "4.1") {
			global $page, $paged;
			// Add the blog name
			$title .= get_bloginfo( 'name' );
			// Add the blog description for the home/front page.
			if ( is_home() || is_front_page() ) {
				if ( ($site_description = get_bloginfo( 'description', 'display' )) != '' )
					$title .= " $sep $site_description";
			}
			// Add a page number if necessary:
			if ( $paged >= 2 || $page >= 2 )
				$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'room' ), max( $paged, $page ) );
		}
		return room_remove_macros($title);
	}
}

// Show blog title
if (!function_exists('room_show_blog_title')) {
	function room_show_blog_title($echo=true) {

		if (is_front_page())
			$title = esc_html__( 'Home', 'room' );
		else if ( is_home() )
			$title = esc_html__( 'All Posts', 'room' );
		else if ( is_author() ) {
			$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
			$title = sprintf(esc_html__('Author page: %s', 'room'), $curauth->display_name);
		} else if ( is_404() )
			$title = esc_html__('URL not found', 'room');
		else if ( is_search() )
			$title = sprintf( esc_html__( 'Search: %s', 'room' ), get_search_query() );
		else if ( is_day() )
			$title = sprintf( esc_html__( 'Daily Archives: %s', 'room' ), room_get_date_translations(get_the_date()) );
		else if ( is_month() )
			$title = sprintf( esc_html__( 'Monthly Archives: %s', 'room' ), room_get_date_translations(get_the_date( 'F Y' )) );
		else if ( is_year() )
			$title = sprintf( esc_html__( 'Yearly Archives: %s', 'room' ), get_the_date( 'Y' ) );
		 else if ( is_category() )
			$title = sprintf( esc_html__( '%s', 'room' ), single_cat_title( '', false ) );
		else if ( is_tag() )
			$title = sprintf( esc_html__( 'Tag: %s', 'room' ), single_tag_title( '', false ) );
		else if ( is_attachment() )
			$title = sprintf( esc_html__( 'Attachment: %s', 'room' ), room_get_post_title());
		else if ( is_single() || is_page() )
			$title = get_the_title();
		else
			$title = get_bloginfo('name', 'raw');		// Unknown pages - as homepage

		if ($echo) echo trim($title);
		return $title;
	}
}

// Show breadcrumbs path
if (!function_exists('room_show_breadcrumbs')) {
	function room_show_breadcrumbs($args=array()) {
		global $wp_query, $post;
		
		$args = array_merge( array(
			'home' => esc_html__('Home', 'room'),		// Home page title (if empty - not showed)
			'home_url' => '',						// Home page url
			'truncate_title' => 50,					// Truncate all titles to this length (if 0 - no truncate)
			'truncate_add' => '...',				// Append truncated title with this string
			'delimiter' => '<span class="breadcrumbs_delimiter"></span>',			// Delimiter between breadcrumbs items
			'max_levels' => room_get_theme_setting('breadcrumbs_max_level'),		// Max categories in the path (0 - unlimited)
			'echo' => true							// If true - show on page, else - only return value
			), is_array($args) ? $args : array( 'home' => $args )
		);

		if ( is_home() || is_front_page() ) return '';

		if ( $args['max_levels']<=0 ) $args['max_levels'] = 999;

		$need_reset = true;
		$rez = $rez_parent = $rez_all = $rez_level = $rez_period = '';
		$cat = $parent_tax = '';
		$level = $parent = $post_id = 0;

		// Get current post ID and path to current post/page/attachment ( if it have parent posts/pages )
		if (is_page() || is_attachment() || is_single()) {
			$page_parent_id = isset($wp_query->post->post_parent) ? $wp_query->post->post_parent : 0;
			$post_id = (is_attachment() ? $page_parent_id : (isset($wp_query->post->ID) ? $wp_query->post->ID : 0));
			while ($page_parent_id > 0) {
				$page_parent = get_post($page_parent_id);
				$level++;
				if ($level > $args['max_levels'])
					$rez_level = '...';
				else
					$rez_parent = '<a class="breadcrumbs_item cat_post" href="' . get_permalink($page_parent->ID) . '">' 
									. trim(room_strshort($page_parent->post_title, $args['truncate_title'], $args['truncate_add']))
									. '</a>' 
									. (!empty($rez_parent) ? $args['delimiter'] : '') 
									. ($rez_parent);
				if (($page_parent_id = $page_parent->post_parent) > 0) $post_id = $page_parent_id;
			}
		}
		
		$depth = 0;

		do {
			if ($depth++ == 0) {
				if (is_single() || is_attachment()) {
					$cats = get_the_category();
					$cat = !empty($cats[0]) ? $cats[0] : false;
					if ($cat) {
						$level++;
						if ($level > $args['max_levels'])
							$rez_level = '...';
						else
							$rez_parent = '<a class="breadcrumbs_item cat_post" href="'.esc_url(get_category_link($cat->term_id)).'">' 
											. trim(room_strshort($cat->name, $args['truncate_title'], $args['truncate_add']))
											. '</a>' 
											. (!empty($rez_parent) ? $args['delimiter'] : '') 
											. ($rez_parent);
					}
				} else if ( is_category() ) {
					$cat_id = (int) get_query_var( 'cat' );
					if (empty($cat_id)) $cat_id = get_query_var( 'category_name' );
					$cat = get_term_by( (int) $cat_id > 0 ? 'id' : 'slug', $cat_id, 'category', OBJECT);
				} else if ( is_tag() ) {
					$cat = get_term_by( 'slug', get_query_var( 'post_tag' ), 'post_tag', OBJECT);
				}
				if ($cat) {
					$parent = $cat->parent;
					$parent_tax = $cat->taxonomy;
				}
			}
			if ($parent) {
				$cat = get_term_by( 'id', $parent, $parent_tax, OBJECT);
				if ($cat) {
					$cat_link = get_term_link($cat->slug, $cat->taxonomy);
					$level++;
					if ($level > $args['max_levels'])
						$rez_level = '...';
					else
						$rez_parent = '<a class="breadcrumbs_item cat_parent" href="'.esc_url($cat_link).'">' 
										. trim(room_strshort($cat->name, $args['truncate_title'], $args['truncate_add']))
										. '</a>' 
										. (!empty($rez_parent) ? $args['delimiter'] : '') 
										. ($rez_parent);
					$parent = $cat->parent;
				}
			}
		} while ($parent);


		$rez_period = '';
		if ((is_day() || is_month()) && is_object($post)) {
			$year  = get_the_time('Y'); 
			$month = get_the_time('m'); 
			$rez_period = '<a class="breadcrumbs_item cat_parent" href="' . get_year_link( $year ) . '">' . ($year) . '</a>';
			if (is_day())
				$rez_period .= (!empty($rez_period) ? $args['delimiter'] : '') . '<a class="breadcrumbs_item cat_parent" href="' . esc_url(get_month_link( $year, $month )) . '">' . trim(get_the_date('F')) . '</a>';
		}
		

		if (!is_front_page()) {	// && !is_home()

			$title = room_strshort(room_show_blog_title(false), $args['truncate_title'], $args['truncate_add']);

			$rez .= (isset($args['home']) && $args['home']!='' 
					? '<a class="breadcrumbs_item home" href="' . esc_url($args['home_url'] ? $args['home_url'] : home_url('/')) . '">' . ($args['home']) . '</a>' . ($args['delimiter']) 
					: '') 
				. (!empty($rez_all)    ? ($rez_all)    . ($args['delimiter']) : '')
				. (!empty($rez_level)  ? ($rez_level)  . ($args['delimiter']) : '')
				. (!empty($rez_parent) ? ($rez_parent) . ($args['delimiter']) : '')
				. (!empty($rez_period) ? ($rez_period) . ($args['delimiter']) : '')
				. ($title ? '<span class="breadcrumbs_item current">' . ($title) . '</span>' : '');
		}

		if ($args['echo'] && !empty($rez)) echo trim($rez);

		return $rez;
	}
}

// Return nav menu html
if ( !function_exists( 'room_get_nav_menu' ) ) {
	function room_get_nav_menu($slug='', $depth=11, $custom_walker=false) {
		$menu = '';	//!empty($slug) ? room_get_custom_option($slug) : '';
		$args = array(
			'menu'				=> empty($menu) || $menu=='default' || room_param_is_inherit($menu) ? '' : $menu,
			'container'			=> '',
			'container_class'	=> '',
			'container_id'		=> '',
			'items_wrap'		=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
			'menu_class'		=> (!empty($slug) ? $slug : 'menu_main') . '_nav',
			'menu_id'			=> (!empty($slug) ? $slug : 'menu_main'),
			'echo'				=> false,
			'fallback_cb'		=> '',
			'before'			=> '',
			'after'				=> '',
			'link_before'       => '',
			'link_after'        => '',
			'depth'             => $depth
		);
		if (!empty($slug))
			$args['theme_location'] = $slug;
		if ($custom_walker && class_exists('room_custom_menu_walker'))
			$args['walker'] = new room_custom_menu_walker;
		return wp_nav_menu($args);
	}
}

// AJAX incremental search
if ( !function_exists( 'room_callback_ajax_search' ) ) {
	function room_callback_ajax_search() {
		if ( !wp_verify_nonce( room_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
	
		$response = array('error'=>'', 'data' => '');
		
		$s = $_REQUEST['text'];
	
		if (!empty($s)) {
			$args = array(
				'post_status' => 'publish',
				'orderby' => 'date',
				'order' => 'desc', 
				'posts_per_page' => max(1, min(10, room_get_theme_setting('ajax_search_posts_count'))),
				's' => esc_html($s),
				);
			// Filter post types
			$show_types = room_get_theme_setting('ajax_search_types');
			if (!empty($show_types)) $args['post_type'] = explode(',', $show_types);

			$args = apply_filters( 'room_ajax_search_query', $args);	

			$post_number = 0;
			room_storage_set('output', '');
			$query = new WP_Query( $args );
			while ( $query->have_posts() ) { $query->the_post();
				$post_number++;
				room_template_set_args('widgets-posts', array(
					'show_image' => 1,
					'show_date' => 1,
					'show_author' => 1,
					'show_counters' => 1,
	                'show_categories' => 0
    	            )
        	    );
				get_template_part('templates/widgets-posts');
			}
			$response['data'] = room_storage_get('output');
			if (empty($response['data'])) {
				$response['data'] .= '<article class="post_item">' . esc_html__('Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'room') . '</article>';
			} else {
				$response['data'] .= '<article class="post_item"><a href="#" class="post_more search_more">' . __('More results &hellip;', 'room') . '</a></article>';
			}
			room_storage_set('output', '');
		} else {
			$response['error'] = '<article class="post_item">' . esc_html__('The query string is empty!', 'room') . '</article>';
		}
		
		echo json_encode($response);
		die();
	}
}

// Return string with categories links
if (!function_exists('room_get_post_categories')) {
	function room_get_post_categories($delimiter=', ', $id=false) {
		$output = '';
		$categories = get_the_category($id);
		if ( !empty( $categories ) ) {
			foreach( $categories as $category )
				$output .= ($output ? $delimiter : '') . '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . sprintf( esc_attr__( 'View all posts in %s', 'room' ), $category->name ) . '">' . esc_html( $category->name ) . '</a>';
		}
		return $output;
	}
}

// Return image from the category
if (!function_exists('room_get_category_image')) {
	function room_get_category_image($term_id=0) {
		$url = '';
		if ($term_id == 0 && is_category())
			$term_id = (int) get_query_var('cat');
		if ($term_id > 0)
			$url = get_option('room_category_image_' . $term_id);
		return $url;
	}
}

// Return small image (icon) from the category
if (!function_exists('room_get_category_icon')) {
	function room_get_category_icon($term_id=0) {
		$url = '';
		if ($term_id == 0 && is_category())
			$term_id = (int) get_query_var('cat');
		if ($term_id > 0)
			$url = get_option('room_category_icon_' . $term_id);
		return $url;
	}
}

// Add the fields to the "category" taxonomy, using our callback function
if (!function_exists('room_category_show_custom_fields')) {
	function room_category_show_custom_fields($cat) {
		$cat_id = !empty($cat->term_id) ? $cat->term_id : 0;
		// Category's image
		echo ((int) $cat_id > 0 ? '<tr' : '<div') . ' class="form-field">'
			. ((int) $cat_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label for="room_category_image"><?php esc_html_e('Large image URL:', 'room'); ?></label><?php
		echo ((int) $cat_id > 0 ? '</th>' : '</div>')
			. ((int) $cat_id > 0 ? '<td valign="top">' : '<div>');
		$cat_img = $cat_id > 0 ? get_option('room_category_image_' . $cat_id) : ''; 
		?><input id="room_category_image" name="room_category_image" value="<?php echo esc_url($cat_img); ?>"><?php
		echo trim(room_show_custom_field('room_category_image_button', array('type' => 'mediamanager', 'linked_field_id' => 'room_category_image'), null));
		if (empty($cat_img)) $cat_img = room_get_file_url('images/no_img.jpg');
		?><img src="<?php echo esc_url($cat_img); ?>" class="room_category_image_preview"><?php
		echo (int) $cat_id > 0 ? '</td></tr>' : '</div></div>';

		// Category's icon
		echo ((int) $cat_id > 0 ? '<tr' : '<div') . ' class="form-field">'
			. ((int) $cat_id > 0 ? '<th valign="top" scope="row">' : '<div>');
		?><label for="room_category_icon"><?php esc_html_e('Small image (icon) URL:', 'room'); ?></label><?php
		echo ((int) $cat_id > 0 ? '</th>' : '</div>')
			. ((int) $cat_id > 0 ? '<td valign="top">' : '<div>');
		$cat_img = $cat_id > 0 ? get_option('room_category_icon_' . $cat_id) : ''; 
		?><input id="room_category_icon" name="room_category_icon" value="<?php echo esc_url($cat_img); ?>"><?php
		echo trim(room_show_custom_field('room_category_icon_button', array('type' => 'mediamanager', 'linked_field_id' => 'room_category_icon'), null));
		if (empty($cat_img)) $cat_img = room_get_file_url('images/no_img.jpg');
		?><img src="<?php echo esc_url($cat_img); ?>" class="room_category_icon_preview"><?php
		echo (int) $cat_id > 0 ? '</td></tr>' : '</div></div>';
	}
}

// Save the fields to the "category" taxonomy, using our callback function
if (!function_exists('room_category_save_custom_fields')) {
	function room_category_save_custom_fields($term_id) {
		if (isset($_POST['room_category_image']))
			update_option('room_category_image_' . intval($term_id), $_POST['room_category_image']);
		if (isset($_POST['room_category_icon']))
			update_option('room_category_icon_' . intval($term_id), $_POST['room_category_icon']);

	}
}

// Create additional column in the categories list
if (!function_exists('room_category_add_custom_column')) {
	//add_filter('manage_edit-category_columns',	'room_category_add_custom_column', 9);
	function room_category_add_custom_column( $columns ){
		$columns['category_image'] = esc_html__('Image', 'room');
		$columns['category_icon'] = esc_html__('Icon', 'room');
		return $columns;
	}
}

// Fill image column in the categories list
if (!function_exists('room_category_fill_custom_column')) {
	//add_filter('manage_category_custom_column',	'room_category_fill_custom_column', 9, 3);
	function room_category_fill_custom_column($output='', $column_name='', $tax_id=0) {
		if ($column_name == 'category_image' && ($cat_img = room_get_category_image($tax_id))) {
			?><img class="room_category_image_preview" src="<?php echo esc_url(room_add_thumb_sizes($cat_img, room_get_thumb_size('tiny'))); ?>" alt=""><?php
		}
		if ($column_name == 'category_icon' && ($cat_img = room_get_category_icon($tax_id))) {
			?><img class="room_category_icon_preview" src="<?php echo esc_url(room_add_thumb_sizes($cat_img, room_get_thumb_size('tiny'))); ?>" alt=""><?php
		}
	}
}


/* Query manipulations
-------------------------------------------------------------------------------- */

// Add sorting parameter in query arguments
if (!function_exists('room_query_add_sort_order')) {
	function room_query_add_sort_order($args, $orderby='date', $order='desc') {
		$q = array();
		$q['order'] = $order=='asc' ? 'asc' : 'desc';
		if ($orderby == 'views') {
			$q['orderby'] = 'meta_value_num';
			$q['meta_key'] = 'room_post_views_count';
		} else if ($orderby == 'likes') {
			$q['orderby'] = 'meta_value_num';
			$q['meta_key'] = 'room_post_likes_count';
		} else if ($orderby == 'comments') {
			$q['orderby'] = 'comment_count';
		} else if ($orderby == 'title' || $orderby == 'alpha') {
			$q['orderby'] = 'title';
		} else if ($orderby == 'rand' || $orderby == 'random')  {
			$q['orderby'] = 'rand';
		} else {
			$q['orderby'] = 'post_date';
		}
		foreach ($q as $mk=>$mv) {
			if (is_array($args))
				$args[$mk] = $mv;
			else
				$args->set($mk, $mv);
		}
		return $args;
	}
}

// Add post type and posts list or categories list in query arguments
if (!function_exists('room_query_add_posts_and_cats')) {
	function room_query_add_posts_and_cats($args, $ids='', $post_type='', $cat='', $taxonomy='category') {
		if (!empty($ids)) {
			$args['post_type'] = empty($args['post_type']) 
									? (empty($post_type) ? array('post', 'page') : $post_type)
									: $args['post_type'];
			$args['post__in'] = explode(',', str_replace(' ', '', $ids));
		} else {
			$args['post_type'] = empty($args['post_type']) 
									? (empty($post_type) ? 'post' : $post_type)
									: $args['post_type'];
			$post_type = is_array($args['post_type']) ? $args['post_type'][0] : $args['post_type'];
			if (!empty($cat)) {
				$cats = !is_array($cat) ? explode(',', $cat) : $cat;
				if ($taxonomy == 'category') {				// Add standard categories
					if (is_array($cats) && count($cats) > 1) {
						$cats_ids = array();
						foreach($cats as $c) {
							$c = trim(chop($c));
							if (empty($c)) continue;
							if ((int) $c == 0) {
								$cat_term = get_term_by( 'slug', $c, $taxonomy, OBJECT);
								if ($cat_term) $c = $cat_term->term_id;
							}
							if ($c==0) continue;
							$cats_ids[] = (int) $c;
							$children = get_categories( array(
								'type'                     => $post_type,
								'child_of'                 => $c,
								'hide_empty'               => 0,
								'hierarchical'             => 0,
								'taxonomy'                 => $taxonomy,
								'pad_counts'               => false
							));
							if (is_array($children) && count($children) > 0) {
								foreach($children as $c) {
									if (!in_array((int) $c->term_id, $cats_ids)) $cats_ids[] = (int) $c->term_id;
								}
							}
						}
						if (count($cats_ids) > 0) {
							$args['category__in'] = $cats_ids;
						}
					} else {
						if ((int) $cat > 0) 
							$args['cat'] = (int) $cat;
						else
							$args['category_name'] = $cat;
					}
				} else {									// Add custom taxonomies
					if (!isset($args['tax_query']))
						$args['tax_query'] = array();
					$args['tax_query']['relation'] = 'AND';
					$args['tax_query'][] = array(
						'taxonomy' => $taxonomy,
						'include_children' => true,
						'field'    => (int) $cats[0] > 0 ? 'id' : 'slug',
						'terms'    => $cats
					);
				}
			}
		}
		return $args;
	}
}

// Add filters (meta parameters) in query arguments
if (!function_exists('room_query_add_filters')) {
	function room_query_add_filters($args, $filters=false) {
		if (!empty($filters)) {
			if (!is_array($filters)) $filters = array($filters);
			foreach ($filters as $v) {
				$found = false;
				if ($v=='thumbs') {							// Filter with meta_query
					if (!isset($args['meta_query']))
						$args['meta_query'] = array();
					else {
						for ($i=0; $i<count($args['meta_query']); $i++) {
							if ($args['meta_query'][$i]['meta_filter'] == $v) {
								$found = true;
								break;
							}
						}
					}
					if (!$found) {
						$args['meta_query']['relation'] = 'AND';
						if ($v == 'thumbs') {
							$args['meta_query'][] = array(
								'meta_filter' => $v,
								'key' => '_thumbnail_id',
								'value' => false,
								'compare' => '!='
							);
						}
					}
				} else if (in_array($v, array('video', 'audio', 'gallery'))) {			// Filter with tax_query
					if (!isset($args['tax_query']))
						$args['tax_query'] = array();
					else {
						for ($i=0; $i<count($args['tax_query']); $i++) {
							if ($args['tax_query'][$i]['tax_filter'] == $v) {
								$found = true;
								break;
							}
						}
					}
					if (!$found) {
						$args['tax_query']['relation'] = 'AND';
						if ($v == 'video') {
							$args['tax_query'][] = array(
								'tax_filter' => $v,
								'taxonomy' => 'post_format',
								'field' => 'slug',
								'terms' => array( 'post-format-video' )
							);
						} else if ($v == 'audio') {
							$args['tax_query'] = array(
								'tax_filter' => $v,
								'taxonomy' => 'post_format',
								'field' => 'slug',
								'terms' => array( 'post-format-audio' )
							);
						} else if ($v == 'gallery') {
							$args['tax_query'] = array(
								'tax_filter' => $v,
								'taxonomy' => 'post_format',
								'field' => 'slug',
								'terms' => array( 'post-format-gallery' )
							);
						}
					}
				}
			}
		}
		return $args;
	}
}


	
/* WP cache
------------------------------------------------------------------------------------- */

// Clear WP cache (all, options or categories)
if (!function_exists('room_clear_cache')) {
	function room_clear_cache($cc) {
		if ($cc == 'categories' || $cc == 'all') {
			wp_cache_delete('category_children', 'options');
			$taxes = get_taxonomies();
			if (is_array($taxes) && count($taxes) > 0) {
				foreach ($taxes  as $tax ) {
					delete_option( "{$tax}_children" );
					_get_term_hierarchy( $tax );
				}
			}
		} else if ($cc == 'options' || $cc == 'all')
			wp_cache_delete('alloptions', 'options');
		if ($cc == 'all')
			wp_cache_flush();
	}
}



	
/* Other utils
------------------------------------------------------------------------------------- */

// Add theme required post type and taxonomies
if (!function_exists('room_require_data')) {
	function room_require_data($type, $value, $params) {
		if (function_exists('trx_utils_require_data'))
			trx_utils_require_data( $type, $value, $params);
	}
}
// Add theme required shortcode
if (!function_exists('room_require_shortcode')) {
	function room_require_shortcode($name, $cb) {
		if (function_exists('trx_utils_require_shortcode'))
			trx_utils_require_shortcode( $name, $cb);
	}
}

// Prepare widgets args - substitute id and class in parameter 'before_widget'
if (!function_exists('room_prepare_widgets_args')) {
	function room_prepare_widgets_args($args, $id, $class) {
		if (!empty($args['before_widget'])) $args['before_widget'] = str_replace(array('%1$s', '%2$s'), array($id, $class), $args['before_widget']);
		return $args;
	}
}

// Create widgets area
if (!function_exists('room_create_widgets_area')) {
	function room_create_widgets_area($name, $add_classes='') {
		$widgets_name = room_get_theme_option($name);
		if (!room_param_is_off($widgets_name) && is_active_sidebar($widgets_name)) { 
			room_storage_set('current_sidebar', $name);
			ob_start();
			do_action( 'before_sidebar' );
			if ( !dynamic_sidebar($widgets_name) ) {
				// Put here html if user no set widgets in sidebar
			}
			do_action( 'after_sidebar' );
			$out = ob_get_contents();
			ob_end_clean();
			$out = preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $out);
			$need_columns = strpos($out, 'columns_wrap')===false;
			if ($need_columns) {
				$columns = min(3, max(1, substr_count($out, '<aside ')));
				if($columns>=2 && $name == 'widgets_above_page'){
					$out = preg_replace("/class=\"widget /", "class=\"widget above_page ", $out);
					$need_columns = false;
				}
				else {
					$out = preg_replace("/class=\"widget /", "class=\"column-1_" . esc_attr($columns) . ' widget ', $out);
				}
			}
			?>
			<div class="<?php echo esc_attr($name); ?> <?php echo esc_attr($name); ?>_wrap widget_area">
				<div class="<?php echo esc_attr($name); ?>_inner <?php echo esc_attr($name); ?>_wrap_inner widget_area_inner">
					<?php
					echo (true==$need_columns ? '<div class="columns_wrap">' : '')
						. trim(chop($out))
						. (true==$need_columns ? '</div>' : '');
					?>
				</div> <!-- /.widget_area_inner -->
			</div> <!-- /.widget_area -->
			<?php
		}
	}
}

// Check if sidebar present
if (!function_exists('room_sidebar_present')) {
	function room_sidebar_present() {
		$sidebar_name = room_get_theme_option('sidebar_widgets');
		return apply_filters('room_filter_sidebar_present', !is_404() && !room_param_is_off($sidebar_name) && is_active_sidebar($sidebar_name));
	}
}
?>