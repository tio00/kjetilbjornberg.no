<?php
/* Revolution Slider support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if (!function_exists('room_revslider_theme_setup1')) {
	add_action( 'after_setup_theme', 'room_revslider_theme_setup1', 1 );
	function room_revslider_theme_setup1() {
		if (room_exists_revslider()) {
			add_filter( 'room_filter_list_sliders',					'room_revslider_list_sliders' );
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('room_revslider_theme_setup9')) {
	add_action( 'after_setup_theme', 'room_revslider_theme_setup9', 9 );
	function room_revslider_theme_setup9() {
		if (room_exists_revslider()) {
			add_filter( 'room_filter_show_slider',					'room_revslider_show_slider', 10, 3 );
			add_action( 'room_action_show_slider_params',			'room_revslider_show_slider_params', 10, 2 );
			add_filter( 'room_filter_save_slider_params',			'room_revslider_save_slider_params', 10, 2 );
			add_filter( 'room_filter_add_slider_params_in_vc',		'room_revslider_add_slider_params_in_vc' );
			if (is_admin()) {
				add_filter( 'room_filter_importer_options',			'room_revslider_importer_set_options' );
				add_action( 'room_action_importer_params',			'room_revslider_importer_show_params', 10, 1 );
				add_action( 'room_action_importer_clear_tables',	'room_revslider_importer_clear_tables', 10, 2 );
				add_action( 'room_action_importer_import',			'room_revslider_importer_import', 10, 2 );
				add_action( 'room_action_importer_import_fields',	'room_revslider_importer_import_fields', 10, 1 );
			}
		}
		if (is_admin()) {
			add_filter( 'room_filter_importer_required_plugins',	'room_revslider_importer_required_plugins', 10, 2 );
			add_filter( 'room_filter_required_plugins',				'room_revslider_required_plugins' );
		}
	}
}

// Check if RevSlider installed and activated
if ( !function_exists( 'room_exists_revslider' ) ) {
	function room_exists_revslider() {
		return function_exists('rev_slider_shortcode');
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'room_revslider_required_plugins' ) ) {
	//add_filter('room_filter_required_plugins',	'room_revslider_required_plugins');
	function room_revslider_required_plugins($list=array()) {
		if (in_array('revslider', room_storage_get('required_plugins'))) {
			$path = room_get_file_dir('plugins/install/revslider.zip');
			if (file_exists($path)) {
				$list[] = array(
						'name' 		=> 'Revolution Slider',
						'slug' 		=> 'revslider',
						'source'	=> $path,
						'required' 	=> false
					);
			}
		}
		return $list;
	}
}



// Add Revslider into lists and sc parameters
//------------------------------------------------------------------------

// Filter to show revslider
if ( !function_exists( 'room_revslider_show_slider' ) ) {
	//add_filter('room_filter_show_slider',	'room_revslider_show_slider', 10, 3);
	function room_revslider_show_slider($html='', $engine='', $alias='') {
		if ($engine=='revo' && !empty($alias))
			$html = do_shortcode('[rev_slider '.esc_attr($alias).']');
		return $html;
	}
}

// Action to show revslider params into widget's settings area
if ( !function_exists( 'room_revslider_show_slider_params' ) ) {
	//add_action('room_action_show_slider_params',	'room_revslider_show_slider_params', 10, 2);
	function room_revslider_show_slider_params($widget, $instance) {
		$revo_alias_list = room_get_list_revo_sliders();
		$alias = $instance['alias'];
		?>
		<p>
			<label for="<?php echo esc_attr($widget->get_field_id('alias')); ?>"><?php esc_html_e('Revolution Slider alias', 'room'); ?></label>
			<select id="<?php echo esc_attr($widget->get_field_id('alias')); ?>" class="widgets_param_fullwidth" name="<?php echo esc_attr($widget->get_field_name('alias')); ?>">
			<?php
				if (is_array($revo_alias_list) && count($revo_alias_list) > 0) {
					foreach ($revo_alias_list as $slug => $name) {
						echo '<option value="'.esc_attr($slug).'"'.($slug==$alias ? ' selected="selected"' : '').'>'.esc_html($name).'</option>';
					}
				}
			?>
			</select>
		</p>
		<?php
	}
}

// Filter to save revslider parameters
if ( !function_exists( 'room_revslider_save_slider_params' ) ) {
	//add_filter('room_filter_save_slider_params',	'room_revslider_save_slider_params', 10, 2);
	function room_revslider_save_slider_params($instance='', $new_instance='') {
		$instance['alias'] = strip_tags( $new_instance['alias'] );
		return $instance;
	}
}

// Filter to add revslider parameters in VC sc
if ( !function_exists( 'room_revslider_add_slider_params_in_vc' ) ) {
	//add_filter('room_filter_add_slider_params_in_vc',	'room_revslider_add_slider_params_in_vc');
	function room_revslider_add_slider_params_in_vc($params) {
		if (is_array($params) && count($params) > 0) {
			$rez = array();
			$i = 0;
			foreach ($params as $v) {
				// Add new params before 'category'
				if ($v['param_name']=='category') {
					$rez[$i++] = array(
						"param_name" => "alias",
						"heading" => esc_html__("RevSlider alias", 'room'),
						"description" => wp_kses_data( __("Select previously created Revolution slider", 'room') ),
						'dependency' => array(
							'element' => 'engine',
							'value' => 'revo'
						),
						"class" => "",
						"value" => array_flip(room_get_list_revo_sliders()),
						"type" => "dropdown"
					);
				}
				$rez[$i++] = $v;
			}
			return $rez;
		}
		return $params;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check RevSlider in the required plugins
if ( !function_exists( 'room_revslider_importer_required_plugins' ) ) {
	//add_filter( 'room_filter_importer_required_plugins',	'room_revslider_importer_required_plugins', 10, 2 );
	function room_revslider_importer_required_plugins($not_installed='', $list='') {
		//if (in_array('revslider', room_storage_get('required_plugins')) && !room_exists_revslider() )
		if (strpos($list, 'revslider')!==false && !room_exists_revslider() )
			$not_installed .= '<br>Revolution Slider';
		return $not_installed;
	}
}

// Set options for one-click importer
if ( !function_exists( 'room_revslider_importer_set_options' ) ) {
	//add_filter( 'room_filter_importer_options',	'room_revslider_importer_set_options', 10, 1 );
	function room_revslider_importer_set_options($options=array()) {
		if ( in_array('revslider', room_storage_get('required_plugins')) && room_exists_revslider() ) {
			$options['folder_with_revsliders'] = 'demo/revslider';			// Name of the folder with Revolution slider data
		}
		return $options;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'room_revslider_importer_show_params' ) ) {
	//add_action( 'room_action_importer_params',	'room_revslider_importer_show_params', 10, 1 );
	function room_revslider_importer_show_params($importer) {
		?>
		<input type="checkbox" <?php echo in_array('revslider', room_storage_get('required_plugins')) && $importer->options['plugins_initial_state'] 
											? 'checked="checked"' 
											: ''; ?> value="1" name="import_revslider" id="import_revslider" /> <label for="import_revslider"><?php esc_html_e('Import Revolution Sliders', 'room'); ?></label><br>
		<?php
	}
}

// Clear tables
if ( !function_exists( 'room_revslider_importer_clear_tables' ) ) {
	//add_action( 'room_action_importer_clear_tables',	'room_revslider_importer_clear_tables', 10, 2 );
	function room_revslider_importer_clear_tables($importer, $clear_tables) {
		if (strpos($clear_tables, 'revslider')!==false && $importer->last_slider==0) {
			if ($importer->options['debug']) dfl(esc_html__('Clear Revolution Slider tables', 'room'));
			global $wpdb;
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_sliders");
			if ( is_wp_error( $res ) ) dfl( esc_html__( 'Failed truncate table "revslider_sliders".', 'room' ) . ' ' . ($res->get_error_message()) );
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_slides");
			if ( is_wp_error( $res ) ) dfl( esc_html__( 'Failed truncate table "revslider_slides".', 'room' ) . ' ' . ($res->get_error_message()) );
			$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_static_slides");
			if ( is_wp_error( $res ) ) dfl( esc_html__( 'Failed truncate table "revslider_static_slides".', 'room' ) . ' ' . ($res->get_error_message()) );
		}
	}
}

// Import posts
if ( !function_exists( 'room_revslider_importer_import' ) ) {
	//add_action( 'room_action_importer_import',	'room_revslider_importer_import', 10, 2 );
	function room_revslider_importer_import($importer, $action) {
		if ( $action == 'import_revslider' ) {
			if (file_exists(WP_PLUGIN_DIR . '/revslider/revslider.php')) {
				require_once WP_PLUGIN_DIR . '/revslider/revslider.php';
				$dir = room_get_folder_dir($importer->options['folder_with_revsliders']);
				if ( is_dir($dir) ) {
					$hdir = @opendir( $dir );
					if ( $hdir ) {
						if ($importer->options['debug']) dfl( esc_html__('Import Revolution sliders', 'room') );
						// Collect files with sliders
						$sliders = array();
						while (($file = readdir( $hdir ) ) !== false ) {
							$pi = pathinfo( ($dir) . '/' . ($file) );
							if ( substr($file, 0, 1) == '.' || is_dir( ($dir) . '/' . ($file) ) || $pi['extension']!='zip' )
								continue;
							$sliders[] = array('name' => $file, 'path' => ($dir) . '/' . ($file));
						}
						@closedir( $hdir );
						// Process next slider
						$slider = new RevSlider();
						for ($i=0; $i<count($sliders); $i++) {
							if ($i+1 <= $importer->last_slider) continue;
							if ($importer->options['debug']) dfl( sprintf(esc_html__('Process slider "%s"', 'room'), $sliders[$i]['name']) );
							if (!is_array($_FILES)) $_FILES = array();
							$_FILES["import_file"] = array("tmp_name" => $sliders[$i]['path']);
							$response = $slider->importSliderFromPost();
							if ($response["success"] == false) {
								$msg = sprintf(esc_html__('Revolution Slider "%s" import error', 'room'), $sliders[$i]['name']);
								$importer->response['error'] = $msg;
								dfl( $msg );
								dfo( $response );
							} else {
								if ($importer->options['debug']) dfl( sprintf(esc_html__('Slider "%s" imported', 'room'), $sliders[$i]['name']) );
							}
							break;
						}
						// Write last slider into log
						room_fpc($importer->import_log, $i+1 < count($sliders) ? '0|100|'.($i+1) : '');
						$importer->response['result'] = min(100, round(($i+1) / count($sliders) * 100));
					}
				}
			} else {
				dfl( sprintf(esc_html__('Can not locate plugin Revolution Slider: %s', 'room'), WP_PLUGIN_DIR.'/revslider/revslider.php') );
			}
		}
	}
}

// Display import progress
if ( !function_exists( 'room_revslider_importer_import_fields' ) ) {
	//add_action( 'room_action_importer_import_fields',	'room_revslider_importer_import_fields', 10, 1 );
	function room_revslider_importer_import_fields($importer) {
		?>
		<tr class="import_revslider">
			<td class="import_progress_item"><?php esc_html_e('Revolution Slider', 'room'); ?></td>
			<td class="import_progress_status"></td>
		</tr>
		<?php
	}
}


// Lists
//------------------------------------------------------------------------

// Add RevSlider in the sliders list, prepended inherit (if need)
if ( !function_exists( 'room_revslider_list_sliders' ) ) {
	//add_filter( 'room_filter_list_sliders',					'room_revslider_list_sliders' );
	function room_revslider_list_sliders($list=array()) {
		$list["revo"] = esc_html__("Layer slider (Revolution)", 'room');
		return $list;
	}
}

// Return Revo Sliders list, prepended inherit (if need)
if ( !function_exists( 'room_get_list_revo_sliders' ) ) {
	function room_get_list_revo_sliders($prepend_inherit=false) {
		if (($list = room_storage_get('list_revo_sliders'))=='') {
			$list = array();
			if (room_exists_revslider()) {
				global $wpdb;
				$rows = $wpdb->get_results( "SELECT alias, title FROM " . esc_sql($wpdb->prefix) . "revslider_sliders" );
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $row) {
						$list[$row->alias] = $row->title;
					}
				}
			}
			room_storage_set('list_revo_sliders', $list);
		}
		return $prepend_inherit ? room_array_merge(array('inherit' => esc_html__("Inherit", 'room')), $list) : $list;
	}
}
?>