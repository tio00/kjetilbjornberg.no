<?php
/* Woocommerce support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if (!function_exists('room_woocommerce_theme_setup1')) {
	add_action( 'after_setup_theme', 'room_woocommerce_theme_setup1', 1 );
	function room_woocommerce_theme_setup1() {
		add_filter( 'room_filter_list_sidebars', 'room_woocommerce_list_sidebars' );
	}
}
// 3 - add/remove Theme Options elements
if (!function_exists('room_woocommerce_theme_setup3')) {
	add_action( 'after_setup_theme', 'room_woocommerce_theme_setup3', 3 );
	function room_woocommerce_theme_setup3() {
		if (room_exists_woocommerce()) {
			room_storage_merge_array('options', '', array(
				// Section 'WooCommerce' - settings for show pages
				'shop' => array(
					"title" => esc_html__('Shop', 'room'),
					"desc" => wp_kses_data( __('Select parameters to display on the shop pages', 'room') ),
					"type" => "section"
					),
				'expand_content_shop' => array(
					"title" => esc_html__('Expand content', 'room'),
					"desc" => wp_kses_data( __('Expand the content width if the sidebar is hidden', 'room') ),
					"refresh" => false,
					"std" => 1,
					"type" => "checkbox"
					),
				'shop_mode' => array(
					"title" => esc_html__('Shop mode', 'room'),
					"desc" => wp_kses_data( __('Select style for the products list', 'room') ),
					"std" => 'thumbs',
					"options" => array(
						'thumbs' => __('Thumbnails', 'room'),
						'list' => __('List', 'room'),
					),
					"type" => "select"
					),
				'header_widgets_shop' => array(
					"title" => esc_html__('Header widgets', 'room'),
					"desc" => wp_kses_data( __('Select set of widgets to show in the header on the shop pages', 'room') ),
					"std" => 'hide',
					"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
					"type" => "select"
					),
				'sidebar_widgets_shop' => array(
					"title" => esc_html__('Sidebar widgets', 'room'),
					"desc" => wp_kses_data( __('Select sidebar to show on the shop pages', 'room') ),
					"std" => 'woocommerce_widgets',
					"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
					"type" => "select"
					),
				'sidebar_position_shop' => array(
					"title" => esc_html__('Sidebar position', 'room'),
					"desc" => wp_kses_data( __('Select position to show sidebar on the shop pages', 'room') ),
					"refresh" => false,
					"std" => 'left',
					"options" => room_get_list_sidebars_positions(),
					"type" => "select"
					),
				'widgets_above_page_shop' => array(
					"title" => esc_html__('Widgets above the page', 'room'),
					"desc" => wp_kses_data( __('Select widgets to show above page (content and sidebar)', 'room') ),
					"std" => 'hide',
					"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
					"type" => "select"
					),
				'widgets_above_content_shop' => array(
					"title" => esc_html__('Widgets above the content', 'room'),
					"desc" => wp_kses_data( __('Select widgets to show at the beginning of the content area', 'room') ),
					"std" => 'hide',
					"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
					"type" => "select"
					),
				'widgets_below_content_shop' => array(
					"title" => esc_html__('Widgets below the content', 'room'),
					"desc" => wp_kses_data( __('Select widgets to show at the ending of the content area', 'room') ),
					"std" => 'hide',
					"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
					"type" => "select"
					),
				'widgets_below_page_shop' => array(
					"title" => esc_html__('Widgets below the page', 'room'),
					"desc" => wp_kses_data( __('Select widgets to show below the page (content and sidebar)', 'room') ),
					"std" => 'hide',
					"options" => array_merge(array('hide'=>esc_html__('- Select widgets -', 'room')), room_get_list_sidebars()),
					"type" => "select"
					)
				)
			);
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('room_woocommerce_theme_setup9')) {
	add_action( 'after_setup_theme', 'room_woocommerce_theme_setup9', 9 );
	function room_woocommerce_theme_setup9() {

		// One-click importer support
		if (room_exists_woocommerce()) {
			add_action( 'wp_enqueue_scripts', 								'room_woocommerce_frontend_scripts' );
			add_filter( 'room_filter_get_css',						'room_woocommerce_get_css', 10, 3 );
			if (is_admin()) {
				add_filter( 'room_filter_importer_options',				'room_woocommerce_importer_set_options' );
				add_action( 'room_action_importer_after_import_posts',	'room_woocommerce_importer_after_import_posts', 10, 1 );
				add_action( 'room_action_importer_params',				'room_woocommerce_importer_show_params', 10, 1 );
				add_action( 'room_action_importer_import',				'room_woocommerce_importer_import', 10, 2 );
				add_action( 'room_action_importer_import_fields',		'room_woocommerce_importer_import_fields', 10, 1 );
				add_action( 'room_action_importer_export',				'room_woocommerce_importer_export', 10, 1 );
				add_action( 'room_action_importer_export_fields',		'room_woocommerce_importer_export_fields', 10, 1 );
			} else {
				add_filter( 'room_filter_detect_blog_mode',				'room_woocommerce_detect_blog_mode' );
				add_filter( 'room_filter_sidebar_present',				'room_woocommerce_sidebar_present' );
			}
			add_filter( 'room_filter_get_css',						'room_woocommerce_get_css', 10, 3 );
		}
		if (is_admin()) {
			add_filter( 'room_filter_importer_required_plugins',		'room_woocommerce_importer_required_plugins', 10, 2 );
			add_filter( 'room_filter_required_plugins',					'room_woocommerce_required_plugins' );
		}

		// Add wrappers and classes to the standard WooCommerce output
		if (room_exists_woocommerce()) {

			// Remove WOOC sidebar
			remove_action( 'woocommerce_sidebar', 						'woocommerce_get_sidebar', 10 );

			// Open main content wrapper - <article>
			remove_action( 'woocommerce_before_main_content',			'woocommerce_output_content_wrapper', 10);
			add_action(    'woocommerce_before_main_content',			'room_woocommerce_wrapper_start', 10);
			// Close main content wrapper - </article>
			remove_action( 'woocommerce_after_main_content',			'woocommerce_output_content_wrapper_end', 10);
			add_action(    'woocommerce_after_main_content',			'room_woocommerce_wrapper_end', 10);

			// Close header section
			add_action( 'woocommerce_archive_description',				'room_woocommerce_archive_description', 15 );

			// Add theme specific search form
			add_filter(    'get_product_search_form',					'room_woocommerce_get_product_search_form' );

			// Add list mode buttons
			add_action(    'woocommerce_before_shop_loop', 				'room_woocommerce_before_shop_loop', 10 );

			// Set columns number for the products loop
			add_filter(    'loop_shop_columns',							'room_woocommerce_loop_shop_columns' );
			add_filter(    'post_class',								'room_woocommerce_loop_shop_columns_class' );
			add_filter(    'product_cat_class',							'room_woocommerce_loop_shop_columns_class', 10, 3 );
			// Open product/category item wrapper
			add_action(    'woocommerce_before_subcategory_title',		'room_woocommerce_item_wrapper_start', 9 );
			add_action(    'woocommerce_before_shop_loop_item_title',	'room_woocommerce_item_wrapper_start', 9 );
			// Close featured image wrapper and open title wrapper
			add_action(    'woocommerce_before_subcategory_title',		'room_woocommerce_title_wrapper_start', 20 );
			add_action(    'woocommerce_before_shop_loop_item_title',	'room_woocommerce_title_wrapper_start', 20 );
			// Wrap product title into link
			add_action(    'the_title',									'room_woocommerce_the_title');
			// Close title wrapper and add description in the list mode
			add_action(    'woocommerce_after_shop_loop_item_title',	'room_woocommerce_title_wrapper_end', 7);
			add_action(    'woocommerce_after_subcategory_title',		'room_woocommerce_title_wrapper_end2', 10 );
			// Close product/category item wrapper
			add_action(    'woocommerce_after_subcategory',				'room_woocommerce_item_wrapper_end', 20 );
			add_action(    'woocommerce_after_shop_loop_item',			'room_woocommerce_item_wrapper_end', 20 );

			// Add product ID into product meta section (after categories and tags)
			add_action(    'woocommerce_product_meta_end',				'room_woocommerce_show_product_id', 10);
			// Set columns number for the product's thumbnails
			add_filter(    'woocommerce_product_thumbnails_columns',	'room_woocommerce_product_thumbnails_columns' );
			// Set columns number for the related products
			add_filter(    'woocommerce_output_related_products_args',	'room_woocommerce_output_related_products_args' );

			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
			add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 10);


			// Detect current shop mode
			if (!is_admin()) {
				$shop_mode = room_get_value_gpc('room_shop_mode');
				if (empty($shop_mode))
					$shop_mode = room_get_theme_option('shop_mode');
				if (empty($shop_mode))
					$shop_mode = 'thumbs';
				room_storage_set('shop_mode', $shop_mode);
			}

		}
	}
}



// Check if WooCommerce installed and activated
if ( !function_exists( 'room_exists_woocommerce' ) ) {
	function room_exists_woocommerce() {
		return class_exists('Woocommerce');
		//return function_exists('is_woocommerce');
	}
}

// Return true, if current page is any woocommerce page
if ( !function_exists( 'room_is_woocommerce_page' ) ) {
	function room_is_woocommerce_page() {
		$rez = false;
		if (room_exists_woocommerce())
			$rez = is_woocommerce() || is_shop() || is_product() || is_product_category() || is_product_tag() || is_product_taxonomy() || is_cart() || is_checkout() || is_account_page();
		return $rez;
	}
}

// Detect current blog mode
if ( !function_exists( 'room_woocommerce_detect_blog_mode' ) ) {
	//add_filter( 'room_filter_detect_blog_mode', 'room_woocommerce_detect_blog_mode' );
	function room_woocommerce_detect_blog_mode($mode='') {
		if (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy())
			$mode = 'shop';
		else if (is_product() || is_cart() || is_checkout() || is_account_page())
			$mode = 'shop';	//'shop_single';
		return $mode;
	}
}

// Return shop page ID
if ( !function_exists( 'room_woocommerce_get_shop_page_id' ) ) {
	function room_woocommerce_get_shop_page_id() {
		return get_option('woocommerce_shop_page_id');
	}
}

// Return shop page link
if ( !function_exists( 'room_woocommerce_get_shop_page_link' ) ) {
	function room_woocommerce_get_shop_page_link() {
		$url = '';
		$id = room_woocommerce_get_shop_page_id();
		if ($id) $url = get_permalink($id);
		return $url;
	}
}

// Enqueue WooCommerce custom styles
if ( !function_exists( 'room_woocommerce_frontend_scripts' ) ) {
	//add_action( 'wp_enqueue_scripts', 'room_woocommerce_frontend_scripts' );
	function room_woocommerce_frontend_scripts() {
		if (room_is_woocommerce_page())
			if (file_exists(room_get_file_dir('css/plugin.woocommerce.css')))
				room_enqueue_style( 'room-plugin.woocommerce-style',  room_get_file_url('css/plugin.woocommerce.css'), array(), null );
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'room_woocommerce_required_plugins' ) ) {
	//add_filter('room_filter_required_plugins',	'room_woocommerce_required_plugins');
	function room_woocommerce_required_plugins($list=array()) {
		if (in_array('woocommerce', room_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> 'WooCommerce',
					'slug' 		=> 'woocommerce',
					'required' 	=> false
				);

		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check WooC in the required plugins
if ( !function_exists( 'room_woocommerce_importer_required_plugins' ) ) {
	//add_filter( 'room_filter_importer_required_plugins',	'room_woocommerce_importer_required_plugins', 10, 2 );
	function room_woocommerce_importer_required_plugins($not_installed='', $list='') {
		//if (in_array('woocommerce', room_storage_get('required_plugins')) && !room_exists_woocommerce() )
		if (strpos($list, 'woocommerce')!==false && !room_exists_woocommerce() )
			$not_installed .= '<br>WooCommerce';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'room_woocommerce_importer_set_options' ) ) {
	//add_filter( 'room_filter_importer_options',	'room_woocommerce_importer_set_options' );
	function room_woocommerce_importer_set_options($options=array()) {
		if ( in_array('woocommerce', room_storage_get('required_plugins')) && room_exists_woocommerce() ) {
			$options['additional_options'][]	= 'shop_%';					// Add slugs to export options for this plugin
			$options['additional_options'][]	= 'woocommerce_%';
			$options['file_with_woocommerce']	= 'importer/demo/woocommerce.txt';	// Name of the file with WooCommerce data
		}
		return $options;
	}
}

// Setup WooC pages after import posts complete
if ( !function_exists( 'room_woocommerce_importer_after_import_posts' ) ) {
	//add_action( 'room_action_importer_after_import_posts',	'room_woocommerce_importer_after_import_posts', 10, 1 );
	function room_woocommerce_importer_after_import_posts($importer) {
		$wooc_pages = array(						// Options slugs and pages titles for WooCommerce pages
			'woocommerce_shop_page_id' 				=> 'Shop',
			'woocommerce_cart_page_id' 				=> 'Cart',
			'woocommerce_checkout_page_id' 			=> 'Checkout',
			'woocommerce_pay_page_id' 				=> 'Checkout &#8594; Pay',
			'woocommerce_thanks_page_id' 			=> 'Order Received',
			'woocommerce_myaccount_page_id' 		=> 'My Account',
			'woocommerce_edit_address_page_id'		=> 'Edit My Address',
			'woocommerce_view_order_page_id'		=> 'View Order',
			'woocommerce_change_password_page_id'	=> 'Change Password',
			'woocommerce_logout_page_id'			=> 'Logout',
			'woocommerce_lost_password_page_id'		=> 'Lost Password'
		);
		foreach ($wooc_pages as $woo_page_name => $woo_page_title) {
			$woopage = get_page_by_title( $woo_page_title );
			if ($woopage->ID) {
				update_option($woo_page_name, $woopage->ID);
			}
		}
		// We no longer need to install pages
		delete_option( '_wc_needs_pages' );
		delete_transient( '_wc_activation_redirect' );
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'room_woocommerce_importer_show_params' ) ) {
	//add_action( 'room_action_importer_params',	'room_woocommerce_importer_show_params', 10, 1 );
	function room_woocommerce_importer_show_params($importer) {
		?>
		<input type="checkbox" <?php echo in_array('woocommerce', room_storage_get('required_plugins'))
											? 'checked="checked"'
											: ''; ?> value="1" name="import_woocommerce" id="import_woocommerce" /> <label for="import_woocommerce"><?php esc_html_e('Import WooCommerce', 'room'); ?></label><br>
		<?php
	}
}

// Import posts
if ( !function_exists( 'room_woocommerce_importer_import' ) ) {
	//add_action( 'room_action_importer_import',	'room_woocommerce_importer_import', 10, 2 );
	function room_woocommerce_importer_import($importer, $action) {
		if ( $action == 'import_woocommerce' ) {
			$importer->import_dump('woocommerce', esc_html__('WooCommerce meta', 'room'));
		}
	}
}

// Display import progress
if ( !function_exists( 'room_woocommerce_importer_import_fields' ) ) {
	//add_action( 'room_action_importer_import_fields',	'room_woocommerce_importer_import_fields', 10, 1 );
	function room_woocommerce_importer_import_fields($importer) {
		?>
		<tr class="import_woocommerce">
			<td class="import_progress_item"><?php esc_html_e('WooCommerce meta', 'room'); ?></td>
			<td class="import_progress_status"></td>
		</tr>
		<?php
	}
}

// Export posts
if ( !function_exists( 'room_woocommerce_importer_export' ) ) {
	//add_action( 'room_action_importer_export',	'room_woocommerce_importer_export', 10, 1 );
	function room_woocommerce_importer_export($importer) {
		room_storage_set('export_woocommerce', serialize( array(
			"woocommerce_attribute_taxomonies"				=> $importer->export_dump("woocommerce_attribute_taxomonies"),
			"woocommerce_downloadable_product_permissions"	=> $importer->export_dump("woocommerce_downloadable_product_permissions"),
            "woocommerce_order_itemmeta"					=> $importer->export_dump("woocommerce_order_itemmeta"),
            "woocommerce_order_items"						=> $importer->export_dump("woocommerce_order_items"),
            "woocommerce_termmeta"							=> $importer->export_dump("woocommerce_termmeta")
            ) )
        );
	}
}

// Display exported data in the fields
if ( !function_exists( 'room_woocommerce_importer_export_fields' ) ) {
	//add_action( 'room_action_importer_export_fields',	'room_woocommerce_importer_export_fields', 10, 1 );
	function room_woocommerce_importer_export_fields($importer) {
		?>
		<tr>
			<th align="left"><?php esc_html_e('WooCommerce', 'room'); ?></th>
			<td><?php room_fpc(room_get_file_dir('importer/export/woocommerce.txt'), room_storage_get('export_woocommerce')); ?>
				<a download="woocommerce.txt" href="<?php echo esc_url(room_get_file_url('importer/export/woocommerce.txt')); ?>"><?php esc_html_e('Download', 'room'); ?></a>
			</td>
		</tr>
		<?php
	}
}



// Add WooCommerce specific items into lists
//------------------------------------------------------------------------

// Add sidebar
if ( !function_exists( 'room_woocommerce_list_sidebars' ) ) {
	//add_filter( 'room_filter_list_sidebars', 'room_woocommerce_list_sidebars' );
	function room_woocommerce_list_sidebars($list=array()) {
		$list['woocommerce_widgets'] = esc_html__('WooCommerce Widgets', 'room');
		return $list;
	}
}




// Decorate WooCommerce output: Loop
//------------------------------------------------------------------------

// Before main content
if ( !function_exists( 'room_woocommerce_wrapper_start' ) ) {
	//remove_action( 'woocommerce_before_main_content', 'room_woocommerce_wrapper_start', 10);
	//add_action('woocommerce_before_main_content', 'room_woocommerce_wrapper_start', 10);
	function room_woocommerce_wrapper_start() {
		if (is_product() || is_cart() || is_checkout() || is_account_page()) {
			?>
			<article class="post_item_single post_type_product">
			<?php
		} else {
			?>
			<div class="list_products shop_mode_<?php echo !room_storage_empty('shop_mode') ? room_storage_get('shop_mode') : 'thumbs'; ?>">
				<div class="list_products_header">
			<?php
		}
	}
}

// After main content
if ( !function_exists( 'room_woocommerce_wrapper_end' ) ) {
	//remove_action( 'woocommerce_after_main_content', 'room_woocommerce_wrapper_end', 10);
	//add_action('woocommerce_after_main_content', 'room_woocommerce_wrapper_end', 10);
	function room_woocommerce_wrapper_end() {
		if (is_product() || is_cart() || is_checkout() || is_account_page()) {
			?>
			</article><!-- /.post_item_single -->
			<?php
		} else {
			?>
			</div><!-- /.list_products -->
			<?php
		}
	}
}

// Close header section
if ( !function_exists( 'room_woocommerce_archive_description' ) ) {
	//add_action( 'woocommerce_archive_description', 'room_woocommerce_archive_description', 15 );
	function room_woocommerce_archive_description() {
		?>
		</div><!-- /.list_products_header -->
		<?php
	}
}

// Add list mode buttons
if ( !function_exists( 'room_woocommerce_before_shop_loop' ) ) {
	//add_action( 'woocommerce_before_shop_loop', 'room_woocommerce_before_shop_loop', 10 );
	function room_woocommerce_before_shop_loop() {
		?>
		<div class="room_shop_mode_buttons"><form action="<?php echo esc_url(room_get_protocol().'://' . ($_SERVER["HTTP_HOST"]) . ($_SERVER["REQUEST_URI"])); ?>" method="post"><input type="hidden" name="room_shop_mode" value="<?php echo esc_attr(room_storage_get('shop_mode')); ?>" /><a href="#" class="woocommerce_thumbs icon-th" title="<?php esc_attr_e('Show products as thumbs', 'room'); ?>"></a><a href="#" class="woocommerce_list icon-th-list" title="<?php esc_attr_e('Show products as list', 'room'); ?>"></a></form></div><!-- /.room_shop_mode_buttons -->
		<?php
	}
}

// Number of columns for the shop streampage
if ( !function_exists( 'room_woocommerce_loop_shop_columns' ) ) {
	//add_filter( 'loop_shop_columns', 'room_woocommerce_loop_shop_columns' );
	function room_woocommerce_loop_shop_columns($cols) {
		$ccc_add = in_array(room_get_theme_option('body_style'), array('fullwide', 'fullscreen')) ? 1 : 0;
		$ccc = room_sidebar_present() ? 3+$ccc_add : 4+$ccc_add;
		return $ccc;
	}
}

// Add column class into product item in shop streampage
if ( !function_exists( 'room_woocommerce_loop_shop_columns_class' ) ) {
	//add_filter( 'post_class', 'room_woocommerce_loop_shop_columns_class' );
	//add_filter( 'product_cat_class', 'room_woocommerce_loop_shop_columns_class', 10, 3 );
	function room_woocommerce_loop_shop_columns_class($classes, $class='', $cat='') {
		global $woocommerce_loop;
		if (is_product()) {
			if (!empty($woocommerce_loop['columns']))
				$classes[] = ' column-1_'.esc_attr($woocommerce_loop['columns']);
		} else if (is_shop() || is_product_category() && is_product_taxonomy()) {
			$ccc_add = in_array(room_get_theme_option('body_style'), array('fullwide', 'fullscreen')) ? 1 : 0;
			$ccc = room_sidebar_present() ? 3+$ccc_add : 4+$ccc_add;
			$classes[] = ' column-1_'.esc_attr($ccc);
		}
		return $classes;
	}
}


// Open item wrapper for categories and products
if ( !function_exists( 'room_woocommerce_item_wrapper_start' ) ) {
	//add_action( 'woocommerce_before_subcategory_title', 'room_woocommerce_item_wrapper_start', 9 );
	//add_action( 'woocommerce_before_shop_loop_item_title', 'room_woocommerce_item_wrapper_start', 9 );
	function room_woocommerce_item_wrapper_start($cat='') {
		room_storage_set('in_product_item', true);
		?>
		</a>
		<div class="post_item post_layout_<?php echo esc_attr(room_storage_get('shop_mode')); ?>">
			<div class="post_featured">
				<a href="<?php echo esc_url(is_object($cat) ? get_term_link($cat->slug, 'product_cat') : get_permalink()); ?>"></a>
		<?php
	}
}

// Open item wrapper for categories and products
if ( !function_exists( 'room_woocommerce_open_item_wrapper' ) ) {
	//add_action( 'woocommerce_before_subcategory_title', 'room_woocommerce_title_wrapper_start', 20 );
	//add_action( 'woocommerce_before_shop_loop_item_title', 'room_woocommerce_title_wrapper_start', 20 );
	function room_woocommerce_title_wrapper_start($cat='') {
		?>
			</div><!-- /.post_featured -->
			<div class="post_data">
				<div class="post_header entry-header">
		<?php
	}
}

// Wrap product title into link
if ( !function_exists( 'room_woocommerce_the_title' ) ) {
	//add_filter( 'the_title', 'room_woocommerce_the_title' );
	function room_woocommerce_the_title($title) {
		if (room_storage_get('in_product_item') && get_post_type()=='product') {
			$title = '<a href="'.get_permalink().'">'.esc_html($title).'</a>';
		}
		return $title;
	}
}

// Add excerpt in output for the product in the list mode
if ( !function_exists( 'room_woocommerce_title_wrapper_end' ) ) {
	//add_action( 'woocommerce_after_shop_loop_item_title', 'room_woocommerce_title_wrapper_end', 7);
	function room_woocommerce_title_wrapper_end() {
		?>
			</div><!-- /.post_header -->
		<?php
		if (room_storage_get('shop_mode') == 'list' && !is_product()) {
		    $excerpt = apply_filters('the_excerpt', get_the_excerpt());
			?>
			<div class="post_content entry-content"><?php echo trim($excerpt); ?></div>
			<?php
		}
		?>
<!--		<a href="#">-->
		<?php
	}
}

// Add excerpt in output for the product in the list mode
if ( !function_exists( 'room_woocommerce_title_wrapper_end2' ) ) {
	//add_action( 'woocommerce_after_subcategory_title', 'room_woocommerce_title_wrapper_end2', 10 );
	function room_woocommerce_title_wrapper_end2($category) {
		?>
			</div><!-- /.post_header -->
		<?php
		if (room_storage_get('shop_mode') == 'list' && !is_product()) {
			?>
			<div class="post_content entry-content"><?php echo trim($category->description); ?></div><!-- /.post_content -->
			<?php
		}
	}
}

// Close item wrapper for categories and products
if ( !function_exists( 'room_woocommerce_close_item_wrapper' ) ) {
	//add_action( 'woocommerce_after_subcategory', 'room_woocommerce_item_wrapper_end', 20 );
	//add_action( 'woocommerce_after_shop_loop_item', 'room_woocommerce_item_wrapper_end', 20 );
	function room_woocommerce_item_wrapper_end($cat='') {
		?>
			</div><!-- /.post_data -->
		</div><!-- /.post_item -->
		<?php
		room_storage_set('in_product_item', false);
	}
}



// Decorate WooCommerce output: Single product
//------------------------------------------------------------------------

// Hide sidebar on the single products and pages
if ( !function_exists( 'room_woocommerce_sidebar_present' ) ) {
	//add_action( 'woocommerce_product_sidebar_present', 'room_woocommerce_sidebar_present' );
	function room_woocommerce_sidebar_present($present) {
		return is_product() || is_cart() || is_checkout() || is_account_page() ? false : $present;
	}
}

// Add Product ID for the single product
if ( !function_exists( 'room_woocommerce_show_product_id' ) ) {
	//add_action( 'woocommerce_product_meta_end', 'room_woocommerce_show_product_id', 10);
	function room_woocommerce_show_product_id() {
		global $post;
		echo '<span class="product_id">'.esc_html__('Product ID: ', 'room') . '<span>' . ($post->ID) . '</span></span>';
	}
}

// Number columns for the product's thumbnails
if ( !function_exists( 'room_woocommerce_product_thumbnails_columns' ) ) {
	//add_filter( 'woocommerce_product_thumbnails_columns', 'room_woocommerce_product_thumbnails_columns' );
	function room_woocommerce_product_thumbnails_columns($cols) {
		return 4;
	}
}

// Set columns number for the related products
if ( !function_exists( 'room_woocommerce_output_related_products_args' ) ) {
	//add_filter( 'woocommerce_output_related_products_args', 'room_woocommerce_output_related_products_args' );
	function room_woocommerce_output_related_products_args($args) {
		$ccc_add = in_array(room_get_theme_option('body_style'), array('fullwide', 'fullscreen')) ? 1 : 0;
		$ccc = room_sidebar_present() ? 3+$ccc_add : 4+$ccc_add;
		$args['posts_per_page'] = $ccc;
		$args['columns'] = $ccc;
		return $args;
	}
}



// Decorate WooCommerce output: Widgets
//------------------------------------------------------------------------

// Search form
if ( !function_exists( 'room_woocommerce_get_product_search_form' ) ) {
	//add_filter( 'get_product_search_form', 'room_woocommerce_get_product_search_form' );
	function room_woocommerce_get_product_search_form($form) {
		return '
		<form role="search" method="get" class="search_form" action="' . esc_url(home_url('/')) . '">
			<input type="text" class="search_field" placeholder="' . esc_attr__('Search for products &hellip;', 'room') . '" value="' . get_search_query() . '" name="s" /><button class="search_button" type="submit">' . esc_html__('Search', 'room') . '</button>
			<input type="hidden" name="post_type" value="product" />
		</form>
		';
	}
}



// Add WooCommerce specific styles into color scheme
//------------------------------------------------------------------------

// Add styles into CSS
if ( !function_exists( 'room_woocommerce_get_css' ) ) {
	//add_filter( 'room_filter_get_css', 'room_woocommerce_get_css', 10, 3 );
	function room_woocommerce_get_css($css, $colors, $fonts) {
		$css .= <<<CSS

/* Page header */
.woocommerce .woocommerce-breadcrumb {
	color: {$colors['text']};
}
.woocommerce .woocommerce-breadcrumb a {
	color: {$colors['text_link']};
}
.woocommerce .woocommerce-breadcrumb a:hover {
	color: {$colors['text_hover']};
}
.woocommerce .list_products_header, .woocommerce-page .list_products_header {
	background-color: {$colors['alter_bg_color']};
	color: {$colors['alter_text']};
}
.woocommerce .list_products_header a, .woocommerce-page .list_products_header a {
}
.woocommerce .list_products_header a:hover, .woocommerce-page .list_products_header a:hover {
	color: {$colors['alter_hover']};
}

.woocommerce .star-rating:before {
	color: {$colors['alter_link_alpha']};
}
.woocommerce .star-rating span:before {
	color: {$colors['alter_link']};
}

/* List and Single product */
.woocommerce ul.products li.product .post_header a:hover, .woocommerce-page ul.products li.product .post_header a:hover {
	color: {$colors['alter_dark']};
}
.woocommerce span.amount, .woocommerce-page span.amount {
	color: {$colors['text_dark']};
}
.woocommerce .widget_shopping_cart_content li span.amount, .woocommerce-page .widget_shopping_cart_content li span.amount {
	color: #a9abaf;
}
aside.woocommerce del,
.woocommerce del > span.amount, .woocommerce-page del > span.amount {
	color: {$colors['alter_light']};
}
.single-product div.product .trx-stretch-width .woocommerce-tabs {
	border-color: {$colors['bd_color']};
}
.woocommerce div.product form.cart div.quantity span:hover, .woocommerce-page div.product form.cart div.quantity span:hover {
	background-color: {$colors['text_hover']};
}

.single-product div.product .trx-stretch-width .woocommerce-tabs .wc-tabs li.active a {
	color: {$colors['text_dark']};
}
.single-product div.product .trx-stretch-width .woocommerce-tabs .wc-tabs li a:hover {
	color: {$colors['text_dark']};
	border-color: {$colors['alter_link']};
}
.single-product div.product .woocommerce-tabs .wc-tabs li a,
.woocommerce div.product p.price ins,
 .woocommerce div.product p.price del {
	font-family: {$fonts['h4']['family']};
}
.woocommerce div.product .woocommerce-tabs ul.tabs li.active a {
	border-color: {$colors['alter_link']};
}
.single-product div.product .trx-stretch-width .woocommerce-tabs #review_form_wrapper {
	border-color: {$colors['bd_color']};
}


/* Pagination */
.woocommerce nav.woocommerce-pagination {
	border-color: {$colors['bd_color']};
}
.woocommerce nav.woocommerce-pagination ul li,
.woocommerce nav.woocommerce-pagination ul li span,
.woocommerce nav.woocommerce-pagination ul li a {
	color: {$colors['alter_light']};
	font-family: {$fonts['h4']['family']};
}
.woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current {
	color: {$colors['text_dark']};
	border-color: {$colors['alter_link']};
}

/* Widgets */
.woocommerce.widget_product_search form:before {
	color: {$colors['text_light']};
}
.woocommerce.widget_product_search .search_button {
	font-family: {$fonts['h4']['family']};
}
.woocommerce ul.products li.product .post_featured:after {
	background-color: {$colors['alter_link']};
}
.woocommerce ul.products li.product .post_featured, .woocommerce-page ul.products li.product .post_featured {
	border-color: {$colors['bd_color']};
}
.woocommerce ul.products li.product .post_featured:hover {
	border-color: {$colors['alter_link_alpha']};
}
.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button {
	font-family: {$fonts['h4']['family']};
}
.woocommerce span.onsale {
	background-color: {$colors['alter_link']};
	font-family: {$fonts['h4']['family']};
}

CSS;
		return $css;
	}
}
?>