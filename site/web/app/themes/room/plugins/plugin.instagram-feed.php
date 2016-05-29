<?php
/* Instagram Feed support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('room_instagram_feed_theme_setup9')) {
	add_action( 'after_setup_theme', 'room_instagram_feed_theme_setup9', 9 );
	function room_instagram_feed_theme_setup9() {
		if (room_exists_instagram_feed()) {
			if (is_admin()) {
				add_filter( 'room_filter_importer_options',				'room_instagram_feed_importer_set_options' );
			}
		}
		if (is_admin()) {
			add_filter( 'room_filter_importer_required_plugins',		'room_instagram_feed_importer_required_plugins', 10, 2 );
			add_filter( 'room_filter_required_plugins',					'room_instagram_feed_required_plugins' );
		}
	}
}

// Check if Instagram Feed installed and activated
if ( !function_exists( 'room_exists_instagram_feed' ) ) {
	function room_exists_instagram_feed() {
		return defined('SBIVER');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'room_instagram_feed_required_plugins' ) ) {
	//add_filter('room_filter_required_plugins',	'room_instagram_feed_required_plugins');
	function room_instagram_feed_required_plugins($list=array()) {
		if (in_array('instagram_feed', room_storage_get('required_plugins')))
			$list[] = array(
					'name' 		=> 'Instagram Feed',
					'slug' 		=> 'instagram-feed',
					'required' 	=> false
				);
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Instagram Feed in the required plugins
if ( !function_exists( 'room_instagram_feed_importer_required_plugins' ) ) {
	//add_filter( 'room_filter_importer_required_plugins',	'room_instagram_feed_importer_required_plugins', 10, 2 );
	function room_instagram_feed_importer_required_plugins($not_installed='', $list='') {
		//if (in_array('instagram_feed', room_storage_get('required_plugins')) && !room_exists_instagram_feed() )
		if (strpos($list, 'instagram_feed')!==false && !room_exists_instagram_feed() )
			$not_installed .= '<br>Instagram Feed';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'room_instagram_feed_importer_set_options' ) ) {
	//add_filter( 'room_filter_importer_options',	'room_instagram_feed_importer_set_options' );
	function room_instagram_feed_importer_set_options($options=array()) {
		if (is_array($options)) {
			$options['additional_options'][] = 'sb_instagram_settings';		// Add slugs to export options for this plugin
		}
		return $options;
	}
}
?>