<?php
/* Mail Chimp support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('room_mailchimp_theme_setup9')) {
	add_action( 'after_setup_theme', 'room_mailchimp_theme_setup9', 9 );
	function room_mailchimp_theme_setup9() {
		if (room_exists_mailchimp()) {
			if (is_admin()) {
				add_filter( 'room_filter_importer_options',				'room_mailchimp_importer_set_options' );
			}
		}
		if (is_admin()) {
			add_filter( 'room_filter_importer_required_plugins',		'room_mailchimp_importer_required_plugins', 10, 2 );
			add_filter( 'room_filter_required_plugins',					'room_mailchimp_required_plugins' );
		}
	}
}

// Check if Instagram Feed installed and activated
if ( !function_exists( 'room_exists_mailchimp' ) ) {
	function room_exists_mailchimp() {
		return function_exists('__mc4wp_load_plugin') || defined('MC4WP_VERSION');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'room_mailchimp_required_plugins' ) ) {
	//add_filter('room_filter_required_plugins',	'room_mailchimp_required_plugins');
	function room_mailchimp_required_plugins($list=array()) {
		if (in_array('mailchimp', room_storage_get('required_plugins')))
			$list[] = array(
				'name' 		=> 'MailChimp for WP',
				'slug' 		=> 'mailchimp-for-wp',
				'required' 	=> false
			);
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check Mail Chimp in the required plugins
if ( !function_exists( 'room_mailchimp_importer_required_plugins' ) ) {
	//add_filter( 'room_filter_importer_required_plugins',	'room_mailchimp_importer_required_plugins', 10, 2 );
	function room_mailchimp_importer_required_plugins($not_installed='', $list='') {
		//if (in_array('mailchimp', room_storage_get('required_plugins')) && !room_exists_mailchimp() )
		if (strpos($list, 'mailchimp')!==false && !room_exists_mailchimp() )
			$not_installed .= '<br>Mail Chimp';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'room_mailchimp_importer_set_options' ) ) {
	//add_filter( 'room_filter_importer_options',	'room_mailchimp_importer_set_options' );
	function room_mailchimp_importer_set_options($options=array()) {
		if (is_array($options)) {
			$options['additional_options'][] = 'mc4wp_default_form_id';		// Add slugs to export options for this plugin
			$options['additional_options'][] = 'mc4wp_form_stylesheets';
			$options['additional_options'][] = 'mc4wp_flash_messages';
			$options['additional_options'][] = 'mc4wp_integrations';
		}
		return $options;
	}
}
?>