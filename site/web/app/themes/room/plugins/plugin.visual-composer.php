<?php
/* Visual Composer support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('room_vc_theme_setup9')) {
	add_action( 'after_setup_theme', 'room_vc_theme_setup9', 9 );
	function room_vc_theme_setup9() {
		if (room_exists_visual_composer()) {
			if (is_admin()) {
				add_filter( 'room_filter_importer_options',				'room_vc_importer_set_options' );
			}
		}
		if (is_admin()) {
			add_filter( 'room_filter_importer_required_plugins',		'room_vc_importer_required_plugins', 10, 2 );
			add_filter( 'room_filter_required_plugins',					'room_vc_required_plugins' );
		}
		room_init_vc_params();
	}
}

// Check if Visual Composer installed and activated
if ( !function_exists( 'room_exists_visual_composer' ) ) {
	function room_exists_visual_composer() {
		return class_exists('Vc_Manager');
	}
}

// Check if Visual Composer in frontend editor mode
if ( !function_exists( 'room_vc_is_frontend' ) ) {
	function room_vc_is_frontend() {
		return (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true')
			|| (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline');
		//return function_exists('vc_is_frontend_editor') && vc_is_frontend_editor();
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'room_vc_required_plugins' ) ) {
	//add_filter('room_filter_required_plugins',	'room_vc_required_plugins');
	function room_vc_required_plugins($list=array()) {
		if (in_array('visual_composer', room_storage_get('required_plugins'))) {
			$path = room_get_file_dir('plugins/install/js_composer.zip');
			if (file_exists($path)) {
				$list[] = array(
					'name' 		=> 'Visual Composer',
					'slug' 		=> 'js_composer',
					'source'	=> $path,
					'required' 	=> true
				);
			}
		}
		return $list;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check VC in the required plugins
if ( !function_exists( 'room_vc_importer_required_plugins' ) ) {
	//add_filter( 'room_filter_importer_required_plugins',	'room_vc_importer_required_plugins', 10, 2 );
	function room_vc_importer_required_plugins($not_installed='', $list='') {
		if (!room_exists_visual_composer())
			$not_installed .= '<br>Visual Composer';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'room_vc_importer_set_options' ) ) {
	//add_filter( 'room_filter_importer_options',	'room_vc_importer_set_options' );
	function room_vc_importer_set_options($options=array()) {
		if (in_array('visual_composer', room_storage_get('required_plugins')) && room_exists_visual_composer()) {
			$options['additional_options'][] = 'wpb_js_templates';		// Add slugs to export options for this plugin
		}
		return $options;
	}
}



// Common VC parameters for sc declaration
//------------------------------------------------------------------------

// Init common parameters for vc_map()
if ( !function_exists( 'room_init_vc_params' ) ) {
	function room_init_vc_params() {
		room_storage_set('vc_params', array(				
			// Current element id
			'id' => array(
				"param_name" => "id",
				"heading" => esc_html__("Element ID", 'room'),
				"description" => wp_kses_data( __("ID for current element", 'room') ),
				"group" => esc_html__('ID &amp; Class', 'room'),
				"value" => "",
				"type" => "textfield"
			),
			// Current element class
			'class' => array(
				"param_name" => "class",
				"heading" => esc_html__("Element CSS class", 'room'),
				"description" => wp_kses_data( __("CSS class for current element", 'room') ),
				"group" => esc_html__('ID &amp; Class', 'room'),
				"value" => "",
				"type" => "textfield"
			),
			// Current element style
			'css' => array(
				"param_name" => "css",
				"heading" => esc_html__("CSS styles", 'room'),
				"description" => wp_kses_data( __("Any additional CSS rules (if need)", 'room') ),
				"group" => esc_html__('ID &amp; Class', 'room'),
				"class" => "",
				"value" => "",
				"type" => "textfield"
			)
		));
	}
}
	
// Return vc_param value
if ( !function_exists( 'room_get_vc_param' ) ) {
	function room_get_vc_param($prm) {
		return room_storage_get_array('vc_params', $prm);
	}
}

// Set vc_param value
if ( !function_exists( 'room_set_vc_param' ) ) {
	function room_set_vc_param($prm, $val) {
		room_storage_set_array('vc_params', $prm, $val);
	}
}
?>