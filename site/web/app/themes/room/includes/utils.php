<?php
/**
 * Theme tags and utilities
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }


// Theme init
if (!function_exists('room_utils_theme_setup')) {
	add_action( 'after_setup_theme', 'room_utils_theme_setup' );
	function room_utils_theme_setup() {
		add_filter( 'wp_editor_set_quality',	'room_set_images_quality', 10, 2 );
	}
}



/* Custom fields
----------------------------------------------------------------------------------------------------- */

// Show theme specific fields in Post (and Page) options
function room_show_custom_field($id, $field, $value) {
	$output = '';
	switch ($field['type']) {
		/*
		case 'reviews':
			$output .= '<div class="reviews_block">' . trim(room_reviews_get_markup($field, $value, true)) . '</div>';
			break;
		*/
		
		case 'mediamanager':
			wp_enqueue_media( );
			$output .= '<a id="'.esc_attr($id).'" class="button mediamanager room_media_selector"
				data-param="' . esc_attr($id) . '"
				data-choose="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Choose Images', 'room') : esc_html__( 'Choose Image', 'room')).'"
				data-update="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Add to Gallery', 'room') : esc_html__( 'Choose Image', 'room')).'"
				data-multiple="'.esc_attr(isset($field['multiple']) && $field['multiple'] ? 'true' : 'false').'"
				data-linked-field="'.esc_attr($field['linked_field_id']).'"
				>' . (isset($field['multiple']) && $field['multiple'] ? esc_html__( 'Choose Images', 'room') : esc_html__( 'Choose Image', 'room')) . '</a>';
			break;
	}
	return apply_filters('room_filter_show_custom_field', $output, $id, $field, $value);
}





/* Arrays manipulations
----------------------------------------------------------------------------------------------------- */

// Merge arrays and lists (preserve number indexes)
// $a = array("one", "k2"=>"two", "three");
// $b = array("four", "k1"=>"five", "k2"=>"six", "seven");
// $c = array_merge($a, $b);			["one", "k2"=>"six", "three", "four", "k1"=>"five", "seven");
// $d = room_array_merge($a, $b);	["four", "k2"=>"six", "seven", "k1"=>"five");
if (!function_exists('room_array_merge')) {
	function room_array_merge($a1, $a2) {
		for ($i = 1; $i < func_num_args(); $i++){
			$arg = func_get_arg($i);
			if (is_array($arg) && count($arg)>0) {
				foreach($arg as $k=>$v) {
					$a1[$k] = $v;
				}
			}
		}
		return $a1;
	}
}

// Inserts any number of scalars or arrays at the point
// in the haystack immediately after the search key ($needle) was found,
// or at the end if the needle is not found or not supplied.
// Modifies $haystack in place.
// @param array &$haystack the associative array to search. This will be modified by the function
// @param string $needle the key to search for
// @param mixed $stuff one or more arrays or scalars to be inserted into $haystack
// @return int the index at which $needle was found
if (!function_exists('room_array_insert')) {
	function room_array_insert_after(&$haystack, $needle, $stuff){
		if (! is_array($haystack) ) return -1;

		$new_array = array();
		for ($i = 2; $i < func_num_args(); ++$i){
			$arg = func_get_arg($i);
			if (is_array($arg)) {
				if ($i==2)
					$new_array = $arg;
				else
					$new_array = room_array_merge($new_array, $arg);
			} else 
				$new_array[] = $arg;
		}

		$i = 0;
		if (is_array($haystack) && count($haystack) > 0) {
			foreach($haystack as $key => $value){
				$i++;
				if ($key == $needle) break;
			}
		}

		$haystack = room_array_merge(array_slice($haystack, 0, $i, true), $new_array, array_slice($haystack, $i, null, true));

		return $i;
    }
}

// Inserts any number of scalars or arrays at the point
// in the haystack immediately before the search key ($needle) was found,
// or at the end if the needle is not found or not supplied.
// Modifies $haystack in place.
// @param array &$haystack the associative array to search. This will be modified by the function
// @param string $needle the key to search for
// @param mixed $stuff one or more arrays or scalars to be inserted into $haystack
// @return int the index at which $needle was found
if (!function_exists('room_array_before')) {
	function room_array_insert_before(&$haystack, $needle, $stuff){
		if (! is_array($haystack) ) return -1;

		$new_array = array();
		for ($i = 2; $i < func_num_args(); ++$i){
			$arg = func_get_arg($i);
			if (is_array($arg)) {
				if ($i==2)
					$new_array = $arg;
				else
					$new_array = room_array_merge($new_array, $arg);
			} else 
				$new_array[] = $arg;
		}

		$i = 0;
		if (is_array($haystack) && count($haystack) > 0) {
			foreach($haystack as $key => $value){
				if ($key == $needle) break;
				$i++;
			}
		}

		$haystack = room_array_merge(array_slice($haystack, 0, $i, true), $new_array, array_slice($haystack, $i, null, true));

		return $i;
    }
}





/* Templates manipulations
----------------------------------------------------------------------------------------------------- */

// Set template arguments
if (!function_exists('room_template_set_args')) {
	function room_template_set_args($tpl, $args) {
		room_storage_push_array('call_args', $tpl, $args);
	}
}

// Get template arguments
if (!function_exists('room_template_get_args')) {
	function room_template_get_args($tpl) {
		return room_storage_pop_array('call_args', $tpl, array());
	}
}

// Look for last template arguments (without removing it from storage)
if (!function_exists('room_template_last_args')) {
	function room_template_last_args($tpl) {
		$args = room_storage_get_array('call_args', $tpl, array());
		return is_array($args) ? array_pop($args) : array();
	}
}

// Push call state into stack
if (!function_exists('room_push_call_state')) {
	function room_push_state($sc) {
		room_storage_push_array('call_stack', '', $sc);
	}
}

// Pop (restore) call state from stack
if (!function_exists('room_pop_call_state')) {
	function room_pop_state() {
		return room_storage_pop_array('call_stack');
	}
}

// Get last call state from stack
if (!function_exists('room_last_call_state')) {
	function room_last_state() {
		$stack = room_storage_get('call_stack');
		return count($stack) > 0 ? $stack[count($stack)-1] : '';
	}
}

// Check if name exists in call stack
if (!function_exists('room_check_call_state')) {
	function room_check_state($sc) {
		$stack = room_storage_get('call_stack');
		return in_array($sc, $stack);
	}
}





/* HTML & CSS
----------------------------------------------------------------------------------------------------- */

// Return first tag from text
if (!function_exists('room_get_tag')) {
	function room_get_tag($text, $tag_start, $tag_end) {
		$val = '';
		if (($pos_start = strpos($text, $tag_start))!==false) {
			$pos_end = strpos($text, $tag_end, $pos_start);
			if ($pos_end===false) {
				$tag_end = '>';
				$pos_end = strpos($text, $tag_end, $pos_start);
			}
			$val = substr($text, $pos_start, $pos_end+strlen($tag_end)-$pos_start);
		}
		return $val;
	}
}

// Decode html-entities in the shortcode parameters
if (!function_exists('room_html_decode')) {
	function room_html_decode($prm) {
		if (is_array($prm) && count($prm) > 0) {
			foreach ($prm as $k=>$v) {
				if (is_string($v))
					$prm[$k] = htmlspecialchars_decode($v, ENT_QUOTES);
			}
		}
		return $prm;
	}
}

// Return string with position rules for the style attr
if (!function_exists('room_get_css_position_from_values')) {
	function room_get_css_position_from_values($top='',$right='',$bottom='',$left='',$width='',$height='') {
		if (!is_array($top)) {
			$top = compact('top','right','bottom','left','width','height');
		}
		$output = '';
		foreach ($top as $k=>$v) {
			$imp = substr($v, 0, 1);
			if ($imp == '!') $v = substr($v, 1);
			if ($v != '') $output .= ($k=='width' ? 'width' : ($k=='height' ? 'height' : 'margin-'.esc_attr($k))) . ':' . esc_attr(room_prepare_css_value($v)) . ($imp=='!' ? ' !important' : '') . ';';
		}
		return $output;
	}
}

// Return value for the style attr
if (!function_exists('room_prepare_css_value')) {
	function room_prepare_css_value($val) {
		if ($val != '') {
			$ed = substr($val, -1);
			if ('0'<=$ed && $ed<='9') $val .= 'px';
		}
		return $val;
	}
}

// Minify CSS string
if (!function_exists('room_minify_css')) {
	function room_minify_css($css) {
		$css = preg_replace("/\r*\n*/", "", $css);
		$css = preg_replace("/\s{2,}/", " ", $css);
        //$css = str_ireplace('@CHARSET "UTF-8";', "", $css);
		$css = preg_replace("/\s*>\s*/", ">", $css);
		$css = preg_replace("/\s*:\s*/", ":", $css);
		$css = preg_replace("/\s*{\s*/", "{", $css);
		$css = preg_replace("/\s*;*\s*}\s*/", "}", $css);
        $css = str_replace(', ', ',', $css);
        $css = preg_replace("/(\/\*[\w\'\s\r\n\*\+\,\"\-\.]*\*\/)/", "", $css);
        return $css;
	}
}





/* GET, POST, COOKIE, SESSION manipulations
----------------------------------------------------------------------------------------------------- */

// Get GET, POST value
if (!function_exists('room_get_value_gp')) {
	function room_get_value_gp($name, $defa='') {
		global $_GET, $_POST;
		$rez = $defa;
		if (isset($_GET[$name])) {
			$rez = stripslashes(trim($_GET[$name]));
		} else if (isset($_POST[$name])) {
			$rez = stripslashes(trim($_POST[$name]));
		}
		return $rez;
	}
}


// Get GET, POST, SESSION value and save it (if need)
if (!function_exists('room_get_value_gps')) {
	function room_get_value_gps($name, $defa='', $page='') {
		global $_GET, $_POST, $_SESSION;
		$putToSession = $page!='';
		$rez = $defa;
		if (isset($_GET[$name])) {
			$rez = stripslashes(trim($_GET[$name]));
		} else if (isset($_POST[$name])) {
			$rez = stripslashes(trim($_POST[$name]));
		} else if (isset($_SESSION[$name.($page!='' ? '_'.($page) : '')])) {
			$rez = stripslashes(trim($_SESSION[$name.($page!='' ? '_'.($page) : '')]));
			$putToSession = false;
		}
		if ($putToSession)
			room_set_session_value($name, $rez, $page);
		return $rez;
	}
}

// Get GET, POST, COOKIE value and save it (if need)
if (!function_exists('room_get_value_gpc')) {
	function room_get_value_gpc($name, $defa='', $page='', $exp=0) {
		global $_GET, $_POST, $_COOKIE;
		$putToCookie = $page!='';
		$rez = $defa;
		if (isset($_GET[$name])) {
			$rez = stripslashes(trim($_GET[$name]));
		} else if (isset($_POST[$name])) {
			$rez = stripslashes(trim($_POST[$name]));
		} else if (isset($_COOKIE[$name.($page!='' ? '_'.($page) : '')])) {
			$rez = stripslashes(trim($_COOKIE[$name.($page!='' ? '_'.($page) : '')]));
			$putToCookie = false;
		}
		if ($putToCookie)
			setcookie($name.($page!='' ? '_'.($page) : ''), $rez, $exp, '/');
		return $rez;
	}
}

// Save value into session
if (!function_exists('room_set_session_value')) {
	function room_set_session_value($name, $value, $page='') {
		global $_SESSION;
		if (!session_id()) session_start();
		$_SESSION[$name.($page!='' ? '_'.($page) : '')] = $value;
	}
}

// Save value into session
if (!function_exists('room_del_session_value')) {
	function room_del_session_value($name, $page='') {
		global $_SESSION;
		if (!session_id()) session_start();
		unset($_SESSION[$name.($page!='' ? '_'.($page) : '')]);
	}
}





/* Colors manipulations
----------------------------------------------------------------------------------------------------- */

if (!function_exists('room_hex2rgb')) {
	function room_hex2rgb($hex) {
		$dec = hexdec(substr($hex, 0, 1)== '#' ? substr($hex, 1) : $hex);
		return array('r'=> $dec >> 16, 'g'=> ($dec & 0x00FF00) >> 8, 'b'=> $dec & 0x0000FF);
	}
}

if (!function_exists('room_hex2rgba')) {
	function room_hex2rgba($hex, $alpha) {
		$rgb = room_hex2rgb($hex);
		return 'rgba('.$rgb['r'].','.$rgb['g'].','.$rgb['b'].','.$alpha.')';
	}
}

if (!function_exists('room_hex2hsb')) {
	function room_hex2hsb ($hex) {
		return room_rgb2hsb(room_hex2rgb($hex));
	}
}

if (!function_exists('room_rgb2hsb')) {
	function room_rgb2hsb ($rgb) {
		$hsb = array();
		$hsb['b'] = max(max($rgb['r'], $rgb['g']), $rgb['b']);
		$hsb['s'] = ($hsb['b'] <= 0) ? 0 : round(100*($hsb['b'] - min(min($rgb['r'], $rgb['g']), $rgb['b'])) / $hsb['b']);
		$hsb['b'] = round(($hsb['b'] /255)*100);
		if (($rgb['r']==$rgb['g']) && ($rgb['g']==$rgb['b'])) $hsb['h'] = 0;
		else if($rgb['r']>=$rgb['g'] && $rgb['g']>=$rgb['b']) $hsb['h'] = 60*($rgb['g']-$rgb['b'])/($rgb['r']-$rgb['b']);
		else if($rgb['g']>=$rgb['r'] && $rgb['r']>=$rgb['b']) $hsb['h'] = 60  + 60*($rgb['g']-$rgb['r'])/($rgb['g']-$rgb['b']);
		else if($rgb['g']>=$rgb['b'] && $rgb['b']>=$rgb['r']) $hsb['h'] = 120 + 60*($rgb['b']-$rgb['r'])/($rgb['g']-$rgb['r']);
		else if($rgb['b']>=$rgb['g'] && $rgb['g']>=$rgb['r']) $hsb['h'] = 180 + 60*($rgb['b']-$rgb['g'])/($rgb['b']-$rgb['r']);
		else if($rgb['b']>=$rgb['r'] && $rgb['r']>=$rgb['g']) $hsb['h'] = 240 + 60*($rgb['r']-$rgb['g'])/($rgb['b']-$rgb['g']);
		else if($rgb['r']>=$rgb['b'] && $rgb['b']>=$rgb['g']) $hsb['h'] = 300 + 60*($rgb['r']-$rgb['b'])/($rgb['r']-$rgb['g']);
		else $hsb['h'] = 0;
		$hsb['h'] = round($hsb['h']);
		return $hsb;
	}
}

if (!function_exists('room_hsb2rgb')) {
	function room_hsb2rgb($hsb) {
		$rgb = array();
		$h = round($hsb['h']);
		$s = round($hsb['s']*255/100);
		$v = round($hsb['b']*255/100);
		if ($s == 0) {
			$rgb['r'] = $rgb['g'] = $rgb['b'] = $v;
		} else {
			$t1 = $v;
			$t2 = (255-$s)*$v/255;
			$t3 = ($t1-$t2)*($h%60)/60;
			if ($h==360) $h = 0;
			if ($h<60) { 		$rgb['r']=$t1; $rgb['b']=$t2; $rgb['g']=$t2+$t3; }
			else if ($h<120) {	$rgb['g']=$t1; $rgb['b']=$t2; $rgb['r']=$t1-$t3; }
			else if ($h<180) {	$rgb['g']=$t1; $rgb['r']=$t2; $rgb['b']=$t2+$t3; }
			else if ($h<240) {	$rgb['b']=$t1; $rgb['r']=$t2; $rgb['g']=$t1-$t3; }
			else if ($h<300) {	$rgb['b']=$t1; $rgb['g']=$t2; $rgb['r']=$t2+$t3; }
			else if ($h<360) {	$rgb['r']=$t1; $rgb['g']=$t2; $rgb['b']=$t1-$t3; }
			else {				$rgb['r']=0;   $rgb['g']=0;   $rgb['b']=0; }
		}
		return array('r'=>round($rgb['r']), 'g'=>round($rgb['g']), 'b'=>round($rgb['b']));
	}
}

if (!function_exists('room_rgb2hex')) {
	function room_rgb2hex($rgb) {
		$hex = array(
			dechex($rgb['r']),
			dechex($rgb['g']),
			dechex($rgb['b'])
		);
		return '#'.(strlen($hex[0])==1 ? '0' : '').($hex[0]).(strlen($hex[1])==1 ? '0' : '').($hex[1]).(strlen($hex[2])==1 ? '0' : '').($hex[2]);
	}
}

if (!function_exists('room_hsb2hex')) {
	function room_hsb2hex($hsb) {
		return room_rgb2hex(room_hsb2rgb($hsb));
	}
}






/* String manipulations
----------------------------------------------------------------------------------------------------- */

// Replace macros in the string
if (!function_exists('room_prepare_macros')) {
	function room_prepare_macros($str) {
		return str_replace(
			array("{{",  "}}",   "[[",  "]]",   "||"),
			array("<i>", "</i>", "<b>", "</b>", "<br>"),
			$str);
	}
}

// Remove macros from the string
if (!function_exists('room_remove_macros')) {
	function room_remove_macros($str) {
		return str_replace(
			array("{{", "}}", "[[", "]]", "||"),
			array("",   "",   "",   "",   ""),
			$str);
	}
}

// Check value for "on" | "off" | "inherit" values
if (!function_exists('room_param_is_on')) {
	function room_param_is_on($prm) {
		return $prm>0 || in_array(strtolower($prm), array('true', 'on', 'yes', 'show'));
	}
}
if (!function_exists('room_param_is_off')) {
	function room_param_is_off($prm) {
		return empty($prm) || $prm===0 || in_array(strtolower($prm), array('false', 'off', 'no', 'none', 'hide'));
	}
}
if (!function_exists('room_param_is_inherit')) {
	function room_param_is_inherit($prm) {
		return in_array(strtolower($prm), array('inherit', 'default'));
	}
}

// Return truncated string
if (!function_exists('room_strshort')) {
	function room_strshort($str, $maxlength, $add='...') {
	//	if ($add && substr($add, 0, 1) != ' ')
	//		$add .= ' ';
		if ($maxlength < 0) 
			return '';
		if ($maxlength < 1 || $maxlength >= strlen($str)) 
			return strip_tags($str);
		$str = substr(strip_tags($str), 0, $maxlength - strlen($add));
		$ch = substr($str, $maxlength - strlen($add), 1);
		if ($ch != ' ') {
			for ($i = strlen($str) - 1; $i > 0; $i--)
				if (substr($str, $i, 1) == ' ') break;
			$str = trim(substr($str, 0, $i));
		}
		if (!empty($str) && strpos(',.:;-', substr($str, -1))!==false) $str = substr($str, 0, -1);
		return ($str) . ($add);
	}
}

// Unserialize string (try replace \n with \r\n)
if (!function_exists('room_unserialize')) {
	function room_unserialize($str) {
		if ( is_serialized($str) ) {
			try {
				$data = unserialize($str);
			} catch (Exception $e) {
				dcl($e->getMessage());
				$data = false;
			}
			if ($data===false) {
				try {
					$data = @unserialize(str_replace("\n", "\r\n", $str));
				} catch (Exception $e) {
					dcl($e->getMessage());
					$data = false;
				}
			}
			//if ($data===false) $data = @unserialize(str_replace(array("\n", "\r"), array('\\n','\\r'), $str));
			return $data;
		} else
			return $str;
	}
}





/* Date & Time
----------------------------------------------------------------------------------------------------- */

// Prepare month names in date for translation
if (!function_exists('room_get_date_translations')) {
	function room_get_date_translations($dt) {
		return str_replace(
			array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',
				  'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'),
			array(
				esc_html__('January', 'room'),
				esc_html__('February', 'room'),
				esc_html__('March', 'room'),
				esc_html__('April', 'room'),
				esc_html__('May', 'room'),
				esc_html__('June', 'room'),
				esc_html__('July', 'room'),
				esc_html__('August', 'room'),
				esc_html__('September', 'room'),
				esc_html__('October', 'room'),
				esc_html__('November', 'room'),
				esc_html__('December', 'room'),
				esc_html__('Jan', 'room'),
				esc_html__('Feb', 'room'),
				esc_html__('Mar', 'room'),
				esc_html__('Apr', 'room'),
				esc_html__('May', 'room'),
				esc_html__('Jun', 'room'),
				esc_html__('Jul', 'room'),
				esc_html__('Aug', 'room'),
				esc_html__('Sep', 'room'),
				esc_html__('Oct', 'room'),
				esc_html__('Nov', 'room'),
				esc_html__('Dec', 'room'),
			),
			$dt);
	}
}






/* Media: images, galleries, audio, video
----------------------------------------------------------------------------------------------------- */

// Get image sizes from image url (if image in the uploads folder)
if (!function_exists('room_getimagesize')) {
	function room_getimagesize($url) {
	
		// Get upload path & dir
		$upload_info = wp_upload_dir();

		// Where check file
		$locations = array(
			'uploads' => array(
				'dir' => $upload_info['basedir'],
				'url' => $upload_info['baseurl']
				),
			'theme' => array(
				'dir' => get_template_directory(),
				'url' => get_template_directory_uri()
				),
			'child' => array(
				'dir' => get_stylesheet_directory(),
				'url' => get_stylesheet_directory_uri()
				)
			);
		
		$http_prefix = "http://";
		$https_prefix = "https://";
		
		$img_size = false;
		
		foreach ($locations as $key=>$loc) {
			// if the $url scheme differs from $upload_url scheme, make them match 
			// if the schemes differe, images don't show up.
			if (!strncmp($url, $https_prefix, strlen($https_prefix))) 		//if url begins with https:// make $upload_url begin with https:// as well
				$loc['url'] = str_replace($http_prefix, $https_prefix, $loc['url']);
			else if (!strncmp($url, $http_prefix, strlen($http_prefix))) 	//if url begins with http:// make $upload_url begin with http:// as well
				$loc['url'] = str_replace($https_prefix, $http_prefix, $loc['url']);		
			
			// Check if $img_url is local.
			if ( false === strpos($url, $loc['url']) ) continue;
			
			// Get path of image.
			$img_path = $loc['dir'] . str_replace($loc['url'], '', $url);
		
			// Check if img path exists, and is an image indeed.
			if ( !file_exists($img_path)) continue;
	
			// Get image size
			$img_size = getimagesize($img_path);
			break;
		}
		
		return $img_size;
	}
}

// Clear thumb sizes from image name
if (!function_exists('room_clear_thumb_sizes')) {
	function room_clear_thumb_sizes($url) {
		$pi = pathinfo($url);
		$parts = explode('-', $pi['filename']);
		$suff = explode('x', $parts[count($parts)-1]);
		if (count($suff)==2 && (int) $suff[0] > 0 && (int) $suff[1] > 0) {
			array_pop($parts);
			$url = $pi['dirname'] . '/' . join('-', $parts) . '.' . $pi['extension'];
		}
		return $url;
	}
}

// Add thumb sizes to image name
if (!function_exists('room_add_thumb_sizes')) {
	function room_add_thumb_sizes($url, $thumb_size, $check_exists=true) {
		$pi = pathinfo($url);
		$parts = explode('-', $pi['filename']);
		// Remove image sizes from filename
		$suff = explode('x', $parts[count($parts)-1]);
		if (count($suff)==2 && (int) $suff[0] > 0 && (int) $suff[1] > 0) {
			array_pop($parts);
		}
		// Add new image sizes
		global $_wp_additional_image_sizes;
		if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $thumb_size, array_keys( $_wp_additional_image_sizes ) ) )
			$parts[] = intval( $_wp_additional_image_sizes[$thumb_size]['width'] ) . 'x' . intval( $_wp_additional_image_sizes[$thumb_size]['height'] );
		$pi['filename'] = join('-', $parts);
		$new_url = $pi['dirname'] . '/' . $pi['filename'] . '.' . $pi['extension'];
		if ($check_exists) {
			$uploads_info = wp_upload_dir();
			$uploads_url = $uploads_info['baseurl'];
			$uploads_dir = $uploads_info['basedir'];
			if (strpos($new_url, $uploads_url)!==false) {
				if (!file_exists(str_replace($uploads_url, $uploads_dir, $new_url)))
					$new_url = $url;
			}
		}
		return $new_url;
	}
}

// Return image size multiplier
if (!function_exists('room_get_thumb_size')) {
	function room_get_thumb_size($ts) {
		static $retina = '-';
		if ($retina=='-') $retina = room_get_theme_option("retina_ready")=='retina' && (int) room_get_value_gpc('room_retina', 0) > 0 ? '-@retina' : '';
		return ($ts=='post-thumbnail' ? '' : 'room-thumb-') . $ts . $retina;
	}
}

// Return image size multiplier
if (!function_exists('room_get_retina_multiplier')) {
	function room_get_retina_multiplier() {
		static $mult = 0;
		if ($mult == 0 && room_get_theme_option("retina_ready")=='retina') {
			$mult = min(4, max(1, room_get_theme_setting("retina_multiplier")));
			if ($mult > 1 && (int) room_get_value_gpc('room_retina', 0) == 0)
				$mult = 1;
		}
		return $mult;
	}
}

// Set quality to save cropped images
if (!function_exists('room_set_images_quality')) {
	//add_filter( 'wp_editor_set_quality', 'room_set_images_quality', 10, 2 );
	function room_set_images_quality($defa=90, $mime='') {
		$q = (int) room_get_theme_option('images_quality');
		if ($q == 0) $q = 90;
		return max(1, min(100, $q));
	}
}


// Return url from first <img> tag inserted in post
if (!function_exists('room_get_post_image')) {
	function room_get_post_image($post_text='', $src=true) {
		global $post;
		$img = '';
		if (empty($post_text)) $post_text = $post->post_content;
		if (preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_text, $matches)) {
			$img = $matches[$src ? 1 : 0][0];
		}
		return $img;
	}
}


// Build slider layout
if (!function_exists('room_build_slider_layout')) {
	function room_build_slider_layout($args=array(), $images=array()) {
		global $post;
		$args = array_merge(array(
			'mode' => "gallery",			// gallery | posts - fromwhere get images for slider - from current post's gallery or from featured images
			'controls' => "yes",			// Show Prev/Next arrows
			'pagination' => "yes",			// Show pagination bullets
			'titles' => "no",				// no | center | bottom | lb | rb - where put post's title on slide
			'interval' => "",				// Slides change interval
			'per_view' => 1,				// Slides per view
			'height' => "",					// Slides height (if empty - auto)
			'thumb_size' => "",				// Size of images (if empty - big)
			'cat' => '',					// Category to get posts
			'ids' => '',					// Comma separated posts IDs
			'count' => room_get_theme_setting('slides_from_posts'),	// Posts number to show in slider
			'orderby' => "date",			// Posts order by
			'order' => "desc",				// Posts order
			'class' => ''					// Additional classes for slider container
			), $args);

		$args['per_view'] = empty($args['per_view']) ? 1 : max(1, min(8, (int) $args['per_view']));
		$args['interval'] = empty($args['interval']) ? mt_rand(5000, 10000) : max(1000, min(60000, $args['interval']));

		// Get images from first gallery in the current post
		if (empty($images)) {

			if ($args['mode'] == 'gallery') {						// Get images from first gallery in the current post

				$post_content = $post->post_content;
				if ( has_shortcode($post_content, 'gallery') ) {
					$gallery = get_post_gallery_images( $post );
					$num = 0;
					$images = array();
					if (count($gallery) > 0) {
						if (empty($args['thumb_size'])) {
							$thumb_size = 'small';
							if ($args['per_view'] < 2)		$thumb_size = 'big';
							else if ($args['per_view'] < 3)	$thumb_size = 'med';
							$args['thumb_size'] = room_get_thumb_size($thumb_size);
						}
						foreach ( $gallery as $image_url ) {
							$num++;
							$images[] = array(
								'url' => room_add_thumb_sizes($image_url, $args['thumb_size']),
								'title' => '',
								'link' => is_singular() ? '' : get_permalink()
								);
							if ($num >= $args['count']) break;
						}
					}
				}

			} else {										// Get featured images from posts in the specified category

				if (empty($args['thumb_size'])) {
					$thumb_size = 'med';
					if ($args['per_view'] < 2) {
						$thumb_size = 'full';
						$args['thumb_size'] = $thumb_size;
					}
					else if ($args['per_view'] < 6) {
						$thumb_size = 'big';
						$args['thumb_size'] = room_get_thumb_size($thumb_size);
					}
				}

				if (!empty($args['ids'])) {
					$posts = explode(',', $args['ids']);
					$args['count'] = count($posts);
				}
			
				$q_args = array(
					'post_type' => 'post',
					'post_status' => 'publish',
					'posts_per_page' => $args['count'],
					'ignore_sticky_posts' => true,
					'order' => $args['order'] == 'asc' ? 'asc' : 'desc',
				);
		
				$q_args = room_query_add_sort_order($q_args, $args['orderby'], $args['order']);
				$q_args = room_query_add_filters($q_args, 'thumbs');
				$q_args = room_query_add_posts_and_cats($q_args, $args['ids'], 'post', $args['cat']);
				$query = new WP_Query( $q_args );
	
				$num = 0;
				
				while ( $query->have_posts() ) { $query->the_post();
					$num++;
					$thumb_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $args['thumb_size']);
					if (is_array($thumb_src) && !empty($thumb_src[0]))
						$thumb_url = $thumb_src[0];
					else
						$thumb_url = room_clear_thumb_sizes(wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())));
					$images[] = array(
						'url' => $thumb_url,
						'title' => get_the_title(),
						'cats' => get_the_category_list(', '),
						'date' => get_the_date(),
						'link' => get_permalink()
						);
					if ($num >= $args['count']) break;
				}
				
				wp_reset_postdata();
			}
		}
		
		$num = 0;
		$output = '';
		if (is_array($images) && count($images) > 0) {
			$slides_type = room_get_theme_setting('slides_type');
			$output .= '<div class="slider_swiper swiper-slider-container' . (!empty($args['class']) ? ' '.esc_attr($args['class']) : '')
					. ' slider_' . esc_attr(room_param_is_on($args['controls']) ? 'controls' : 'nocontrols')
					. ' slider_' . esc_attr(room_param_is_on($args['pagination']) ? 'pagination' : 'nopagination')
					. ' slider_' . esc_attr(!room_param_is_off($args['titles']) ? 'titles_'.$args['titles'] : 'notitles')
					. ' slider_' . esc_attr($args['per_view']==1 ? 'one' : 'multi')
					.'"'
					.' data-interval="'.esc_attr($args['interval']).'"'
					.' data-slides-per-view="'.esc_attr($args['per_view']).'"'
					.((int)$args['height']>0 ? ' style="max-'.esc_attr(room_get_css_position_from_values('', '', '', '', '', $args['height'])).'"' : '')
					.'>
					<div class="swiper-wrapper">';
			foreach ($images as $image) {
				$num++;
				if ($slides_type == 'bg') {
					$output .= '<div class="swiper-slide"'
							. ' style="background-image:url(' . esc_url($image['url']) . ');"'
							. '>'
								. '<div class="slide_overlay">';
					if (!room_param_is_off($args['titles']) ) {
						$output .= '<div class="slide_info">';
						//if (!empty($image['cats']))
							//$output .= '<div class="slide_cats">' . trim($image['cats']) . '</div>';
						if (!empty($image['date']))
							$output .= '<div class="slide_date">' . trim($image['date']) . '</div>';
						if (!empty($image['title'])) 
							$output .= '<h3 class="slide_title">'
										. ($image['link'] ? '<a href="'.esc_url($image['link']).'">' : '')
										. trim($image['title'])
										. ($image['link'] ? '</a>' : '')
										. '</h3>';

						if (!empty($image['link']))
							$output .= '<div class="slide_link"><a class="link" href="'.esc_url($image['link']).'"><span>' . esc_html__('Read More','room') . '</span></a></div>';

						$output .= '</div>';
					} else
						$output .= ($image['link'] ? '<a href="'.esc_url($image['link']).'"></a>' : '');
					$output .= '</div></div>';
				} else {
					$output .= '<div class="swiper-slide">'
								. ($image['link'] ? '<a href="'.esc_url($image['link']).'">' : '')
								. '<img src="' . esc_url($image['url']) . '" alt="'.esc_attr($image['title']).'">'
								. '<div class="slide_overlay">';
					if (!room_param_is_off($args['titles']) ) {
						$output .= '<div class="slide_info">';
						if (!empty($image['cats']))
							$output .= '<div class="slide_cats">' . trim($image['cats']) . '</div>';
						if (!empty($image['title'])) 
							$output .= '<h3 class="slide_title">' . trim($image['title']) . '</h3>';
						$output .= '</div>';
					}
					$output .= ($image['link'] ? '</a>' : '')
								. '</div></div>';
				}
			}
			$output .= '</div>'
				. (room_param_is_on($args['controls']) 
						? '<div class="slider_controls_wrap"><a class="slider_prev swiper-button-prev" href="#"></a><a class="slider_next swiper-button-next" href="#"></a></div>' 
						: '')
				. (room_param_is_on($args['pagination']) 
						? '<div class="slider_pagination_wrap swiper-pagination"></div>' 
						: '')
				. '</div>';
		}
		return $output;
	}
}


// Return url from first <audio> tag inserted in post
if (!function_exists('room_get_post_audio')) {
	function room_get_post_audio($post_text='', $src=true) {
		global $post;
		$img = '';
		if (empty($post_text)) $post_text = $post->post_content;
		if ($src) {
			if (preg_match_all('/<audio.+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_text, $matches)) {
				$img = $matches[1][0];
			}
		} else {
			$img = room_get_tag($post_text, '<audio', '</audio>');
			if (empty($mg)) {
				$img = do_shortcode(room_get_tag($post_text, '[audio', '[/audio]'));
			}
		}
		return $img;
	}
}


// Return url from first <video> tag inserted in post
if (!function_exists('room_get_post_video')) {
	function room_get_post_video($post_text='', $src=true) {
		global $post;
		$img = '';
		if (empty($post_text)) $post_text = $post->post_content;
		if ($src) {
			if (preg_match_all('/<video.+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_text, $matches)) {
				$img = $matches[1][0];
			}
		} else {
			$img = room_get_tag($post_text, '<video', '</video>');
			if (empty($mg)) {
				$img = do_shortcode(room_get_tag($post_text, '[video', '[/video]'));
			}
		}
		return $img;
	}
}


// Return url from first <iframe> tag inserted in post
if (!function_exists('room_get_post_iframe')) {
	function room_get_post_iframe($post_text='', $src=true) {
		global $post;
		$img = '';
		if (empty($post_text)) $post_text = $post->post_content;
		if ($src) {
			if (preg_match_all('/<iframe.+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_text, $matches)) {
				$img = $matches[1][0];
			}
		} else
			$img = room_get_tag($post_text, '<iframe', '</iframe>');
		return $img;
	}
}







/* Socials and Share links
----------------------------------------------------------------------------------------------------- */

// Return (and show) share social links
if (!function_exists('room_get_share_links')) {
	function room_get_share_links($args) {

		$args = array_merge(array(
			'post_id' => 0,						// post ID
			'post_link' => '',					// post link
			'post_title' => '',					// post title
			'post_descr' => '',					// post descr
			'post_thumb' => '',					// post featured image
			'size' => 'tiny',					// icons size: tiny|small|medium|big
			'style' => room_get_theme_setting('socials_type')=='images' ? 'bg' : 'icons',	// style for show icons: icons|images|bg
			'type' => 'block',					// share block type: list|block|drop
			'popup' => true,					// open share url in new window or in popup window
			'counters' => true,					// show share counters
			'direction' => 'horizontal',		// share block direction
			'caption' => esc_html__('Share:', 'room'),			// share block caption
			'share' => room_get_theme_setting('share_list'),	// list of allowed socials for sharing
			'echo' => true						// if true - show on page, else - only return as string
			), $args);

		if (count($args['share'])==0 || implode('', $args['share'][0])=='') return '';
		if (empty($args['post_id']))	$args['post_id'] = get_the_ID();
		if (empty($args['post_link']))	$args['post_link'] = get_permalink();
		if (empty($args['post_title']))	$args['post_title'] = get_the_title();
		if (empty($args['post_descr']))	$args['post_descr'] = strip_tags(get_the_excerpt());
		if (empty($args['post_thumb']))	{
			$attach = wp_get_attachment_image_src( get_post_thumbnail_id( $args['post_id'] ), room_get_thumb_size('big') );
			$args['post_thumb'] = !empty($attach[0]) ? $attach[0] : '';
		}
		
		$output = '<div class="socials_wrap socials_share socials_size_'.esc_attr($args['size']).($args['type']=='drop' ? ' socials_drop' : ' socials_dir_' . esc_attr($args['direction'])) . '">'
			. ($args['caption']!='' 
				? ($args['type']=='drop' 
					? '<a href="#" class="socials_caption"><span class="socials_caption_label">'.($args['caption']).'</span></a>'
					: '<span class="socials_caption">'.($args['caption']).'</span>')
				: '');

		if (is_array($args['share']) && count($args['share']) > 0) {
			$output .= '<span class="social_items">';
			foreach ($args['share'] as $soc) {
				list($sn, $url, $icon) = room_get_social_data($soc, $args['style'], 'share');
				if (empty($url)) continue;
				$link = str_replace(
					array('{id}', '{link}', '{title}', '{descr}', '{image}'),
					array(
						urlencode($args['post_id']),
						urlencode($args['post_link']),
						urlencode(strip_tags($args['post_title'])),
						urlencode(strip_tags($args['post_descr'])),
						urlencode($args['post_thumb'])
						),
					$url);
				$output .= '<span class="social_item'.(!empty($args['popup']) ? ' social_item_popup' : '').'">'
							. '<a href="'.esc_url($link).'"'
							. ' class="social_icons social_'.esc_attr($sn).'"'
							. ($args['style']=='bg' ? ' style="background-image: url('.esc_url($icon).');"' : '')
							. ($args['popup'] ? ' data-link="' . esc_url($link) .'"' : ' target="_blank"')
							. ($args['counters'] ? ' data-count="'.esc_attr($sn).'"' : '') 
							. '>'
								. ($args['style']=='icons' 
									? '<span class="' . esc_attr($soc['icon']) . '"></span>' 
									: ($args['style']=='images' 
										? '<img src="'.esc_url($icon).'" alt="'.esc_attr($sn).'" />' 
										: '<span class="social_hover" style="background-image: url('.esc_url($icon).');"></span>'
										)
									)
								//. ($args['counters'] ? '<span class="share_counter">0</span>' : '') 
								. ($args['type']=='drop' ? '<i>' . trim($sn) . '</i>' : '')
							. '</a>'
						. '</span>';

			}
			$output .= '</span>';
		}
		$output .= '</div>';
		if ($args['echo']) echo trim($output);
		return $output;
	}
}


// Return social icons links
if (!function_exists('room_get_socials_links')) {
	function room_get_socials_links($list=array(), $style='') {
		if (empty($list))  $list  = room_get_theme_setting('socials_list');
		if (empty($style)) $style = room_get_theme_setting('socials_type')=='images' ? 'bg' : 'icons';
		$output = '';
		if (is_array($list) && count($list) > 0) {
			foreach ($list as $soc) {

				list($sn, $url, $icon) = room_get_social_data($soc, $style, 'socials');
				if (empty($url)) continue;

				$output .= '<span class="social_item">'
						. '<a href="'.esc_url($url).'" target="_blank" class="social_icons social_'.esc_attr($sn).'"'
						. ($style=='bg' ? ' style="background-image: url('.esc_url($icon).');"' : '')
						. '>'
						. ($style=='icons' 
							? '<span class="icon-' . esc_attr($sn) . '"></span>' 
							: ($style=='images' 
								? '<img src="'.esc_url($icon).'" alt="" />' 
								: '<span class="social_hover" style="background-image: url('.esc_url($icon).');"></span>'))
						. '</a>'
						. '</span>';
			}
		}
		return $output;
	}
}


// Return social name and icon (image)
if (!function_exists('room_get_social_data')) {
	function room_get_social_data($soc, $type, $prefix) {
		if ($type!='icons') {
			$upload_info = wp_upload_dir();
			$upload_url = $upload_info['baseurl'];
		}
		$icon = $type=='icons' || strpos($soc['icon'], $upload_url)!==false ? $soc['icon'] : room_get_socials_url(basename($soc['icon']));
		if ($type == 'icons') {
			$parts = explode('-', $soc['icon'], 2);
			$sn = isset($parts[1]) ? $parts[1] : $parts[0];
		} else {
			$sn = basename($soc['icon']);
			$sn = substr($sn, 0, strrpos($sn, '.'));
			if (($pos=strrpos($sn, '_'))!==false)
				$sn = substr($sn, 0, $pos);
		}
		$url = room_check_theme_option($prefix.'_'.$sn) ? room_get_theme_option($prefix.'_'.$sn) : $soc['url'];
		return array($sn, $url, $icon);
	}
}
	
	
// Twitter
if (!function_exists('room_get_twitter_data')) {
	function room_get_twitter_data($cfg) {
		return function_exists('trx_utils_twitter_acquire_data') 
				? trx_utils_twitter_acquire_data(array(
						'mode'            => 'user_timeline',
						'consumer_key'    => $cfg['consumer_key'],
						'consumer_secret' => $cfg['consumer_secret'],
						'token'           => $cfg['token'],
						'secret'          => $cfg['secret']
					))
				: '';
	}
}

if (!function_exists('room_prepare_twitter_text')) {
	function room_prepare_twitter_text($tweet) {
		$text = $tweet['text'];
		if (!empty($tweet['entities']['urls']) && count($tweet['entities']['urls']) > 0) {
			foreach ($tweet['entities']['urls'] as $url) {
				$text = str_replace($url['url'], '<a href="'.esc_url($url['expanded_url']).'" target="_blank">' . ($url['display_url']) . '</a>', $text);
			}
		}
		if (!empty($tweet['entities']['media']) && count($tweet['entities']['media']) > 0) {
			foreach ($tweet['entities']['media'] as $url) {
				$text = str_replace($url['url'], '<a href="'.esc_url($url['expanded_url']).'" target="_blank">' . ($url['display_url']) . '</a>', $text);
			}
		}
		return $text;
	}
}


// Return Twitter followers count
if (!function_exists('room_get_twitter_followers')) {
	function room_get_twitter_followers($cfg) {
		$data = room_get_twitter_data($cfg); 
		return $data && isset($data[0]['user']['followers_count']) ? $data[0]['user']['followers_count'] : 0;
	}
}


// Add facebook meta tags for post/page sharing
function room_facebook_og_tags() {
	if ( !is_singular() || !room_storage_empty('blog_streampage')) return;
	if (has_post_thumbnail( get_the_ID() )) {
		$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), room_get_thumb_size('full') );
		echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>' . "\n";
	}
	//echo '<meta property="og:title" content="' . esc_attr( strip_tags( get_the_title() ) ) . '" />' . "\n"
	//	.'<meta property="og:description" content="' . esc_attr( strip_tags( strip_shortcodes( get_the_excerpt()) ) ) . '" />' . "\n"
	//	.'<meta property="og:url" content="' . esc_attr( get_permalink() ) . '" />';
}







/* Enqueue files
----------------------------------------------------------------------------------------------------- */

// Enqueue .min.css (if exists and filetime .min.css > filetime .css) instead .css
if (!function_exists('room_enqueue_style')) {	
	function room_enqueue_style($handle, $src=false, $depts=array(), $ver=null, $media='all') {
		$load = true;
		if (!is_array($src) && $src !== false && $src !== '') {
			$theme_dir = get_template_directory();
			$theme_url = get_template_directory_uri();
			$child_dir = get_stylesheet_directory();
			$child_url = get_stylesheet_directory_uri();
			$dir = $url = '';
			if (strpos($src, $child_url)===0) {
				$dir = $child_dir;
				$url = $child_url;
			} else if (strpos($src, $theme_url)===0) {
				$dir = $theme_dir;
				$url = $theme_url;
			}
			if ($dir != '') {
				if (!DEBUG_MODE) {
					if (substr($src, -4)=='.css') {
						if (substr($src, -8)!='.min.css') {
							$src_min = substr($src, 0, strlen($src)-4).'.min.css';
							$file_src = $dir . substr($src, strlen($url));
							$file_min = $dir . substr($src_min, strlen($url));
							if (file_exists($file_min) && filemtime($file_src) <= filemtime($file_min)) $src = $src_min;
						}
					}
				}
				$file_src = $dir . substr($src, strlen($url));
				$load = file_exists($file_src) && filesize($file_src) > 0;
			}
		}
		if ($load) {
			if (is_array($src) || $src===false) {
				wp_enqueue_style( $handle, $depts, $ver, $media );
			} else if (!empty($src)) {
				wp_enqueue_style( $handle, $src.(DEBUG_MODE ? (strpos($src, '?')!==false ? '&' : '?').'rnd='.mt_rand() : ''), $depts, $ver, $media );
			}
		}
	}
}

// Enqueue .min.js (if exists and filetime .min.js > filetime .js) instead .js
if (!function_exists('room_enqueue_script')) {	
	function room_enqueue_script($handle, $src=false, $depts=array(), $ver=null, $in_footer=true) {
		$load = true;
		if (!is_array($src) && $src !== false && $src !== '') {
			$theme_dir = get_template_directory();
			$theme_url = get_template_directory_uri();
			$child_dir = get_stylesheet_directory();
			$child_url = get_stylesheet_directory_uri();
			$dir = $url = '';
			if (strpos($src, $child_url)===0) {
				$dir = $child_dir;
				$url = $child_url;
			} else if (strpos($src, $theme_url)===0) {
				$dir = $theme_dir;
				$url = $theme_url;
			}
			if ($dir != '') {
				if (!DEBUG_MODE) {
					if (substr($src, -3)=='.js') {
						if (substr($src, -7)!='.min.js') {
							$src_min  = substr($src, 0, strlen($src)-3).'.min.js';
							$file_src = $dir . substr($src, strlen($url));
							$file_min = $dir . substr($src_min, strlen($url));
							if (file_exists($file_min) && filemtime($file_src) <= filemtime($file_min)) $src = $src_min;
						}
					}
				}
				$file_src = $dir . substr($src, strlen($url));
				$load = file_exists($file_src) && filesize($file_src) > 0;
			}
		}
		if ($load) {
			if (is_array($src) || $src===false) {
				wp_enqueue_script( $handle, $depts, $ver, $in_footer );
			} else if (!empty($src)) {
				wp_enqueue_script( $handle, $src.(DEBUG_MODE ? (strpos($src, '?')!==false ? '&' : '?').'rnd='.mt_rand() : ''), $depts, $ver, $in_footer );
			}
		}
	}
}


// Check if file/folder present in the child theme and return path (url) to it. 
// Else - path (url) to file in the main theme dir
if (!function_exists('room_get_file_dir')) {	
	function room_get_file_dir($file, $return_url=false) {
		if ($file[0]=='/') $file = substr($file, 1);
		$theme_dir = get_template_directory();
		$theme_url = get_template_directory_uri();
		$child_dir = get_stylesheet_directory();
		$child_url = get_stylesheet_directory_uri();
		$dir = '';
		if (file_exists(($child_dir).'/'.($file)))
			$dir = ($return_url ? $child_url : $child_dir).'/'.($file);
		else if (file_exists(($theme_dir).'/'.($file)))
			$dir = ($return_url ? $theme_url : $theme_dir).'/'.($file);
		return $dir;
	}
}

if (!function_exists('room_get_file_url')) {	
	function room_get_file_url($file) {
		return room_get_file_dir($file, true);
	}
}

// Detect folder location with same algorithm as file (see above)
if (!function_exists('room_get_folder_dir')) {	
	function room_get_folder_dir($folder, $return_url=false) {
		if ($folder[0]=='/') $folder = substr($folder, 1);
		$theme_dir = get_template_directory();
		$theme_url = get_template_directory_uri();
		$child_dir = get_stylesheet_directory();
		$child_url = get_stylesheet_directory_uri();
		$dir = '';
		if (is_dir(($child_dir).'/'.($folder)))
			$dir = ($return_url ? $child_url : $child_dir).'/'.($folder);
		else if (file_exists(($theme_dir).'/'.($folder)))
			$dir = ($return_url ? $theme_url : $theme_dir).'/'.($folder);
		return $dir;
	}
}

if (!function_exists('room_get_folder_url')) {	
	function room_get_folder_url($folder) {
		return room_get_folder_dir($folder, true);
	}
}

// Replace uploads url to current site uploads url
if (!function_exists('room_replace_uploads_url')) {	
	function room_replace_uploads_url($str, $uploads_folder='uploads') {
		static $uploads_url = '', $uploads_len = 0;
		if (is_array($str) && count($str) > 0) {
			foreach ($str as $k=>$v) {
				$str[$k] = room_replace_uploads_url($v, $uploads_folder);
			}
		} else if (is_string($str)) {
			if (empty($uploads_url)) {
				$uploads_info = wp_upload_dir();
				$uploads_url = $uploads_info['baseurl'];
				$uploads_len = strlen($uploads_url);
			}
			$break = '\'" ';
			$pos = 0;
			while (($pos = strpos($str, "/{$uploads_folder}/", $pos))!==false) {
				$pos0 = $pos;
				$chg = true;
				while ($pos0) {
					if (strpos($break, substr($str, $pos0, 1))!==false) {
						$chg = false;
						break;
					}
					if (substr($str, $pos0, 5)=='http:' || substr($str, $pos0, 6)=='https:')
						break;
					$pos0--;
				}
				if ($chg) {
					$str = ($pos0 > 0 ? substr($str, 0, $pos0) : '') . ($uploads_url) . substr($str, $pos+strlen($uploads_folder)+1);
					$pos = $pos0 + $uploads_len;
				} else 
					$pos++;
			}
		}
		return $str;
	}
}

// Replace site url to current site url
if (!function_exists('room_replace_site_url')) {	
	function room_replace_site_url($str, $old_url) {
		static $site_url = '', $site_len = 0;
		if (is_array($str) && count($str) > 0) {
			foreach ($str as $k=>$v) {
				$str[$k] = room_replace_site_url($v, $old_url);
			}
		} else if (is_string($str)) {
			if (empty($site_url)) {
				$site_url = get_site_url();
				$site_len = strlen($site_url);
				if (substr($site_url, -1)=='/') {
					$site_len--;
					$site_url = substr($site_url, 0, $site_len);
				}
			}
			if (substr($old_url, -1)=='/') $old_url = substr($old_url, 0, strlen($old_url)-1);
			$break = '\'" ';
			$pos = 0;
			while (($pos = strpos($str, $old_url, $pos))!==false) {
				$str = room_unserialize($str);
				if (is_array($str) && count($str) > 0) {
					foreach ($str as $k=>$v) {
						$str[$k] = room_replace_site_url($v, $old_url);
					}
					$str = serialize($str);
					break;
				} else {
					$pos0 = $pos;
					$chg = true;
					while ($pos0 >= 0) {
						if (strpos($break, substr($str, $pos0, 1))!==false) {
							$chg = false;
							break;
						}
						if (substr($str, $pos0, 5)=='http:' || substr($str, $pos0, 6)=='https:')
							break;
						$pos0--;
					}
					if ($chg && $pos0>=0) {
						$str = ($pos0 > 0 ? substr($str, 0, $pos0) : '') . ($site_url) . substr($str, $pos+strlen($old_url));
						$pos = $pos0 + $site_len;
					} else 
						$pos++;
				}
			}
		}
		return $str;
	}
}


// Put text into specified file
if (!function_exists('room_fpc')) {	
	function room_fpc($file, $content, $flag=0) {
		$fn = join('_', array('file', 'put', 'contents'));
		return @$fn($file, $content, $flag);
	}
}

// Get text from specified file
if (!function_exists('room_fgc')) {	
	function room_fgc($file) {
		$fn = join('_', array('file', 'get', 'contents'));
		return @$fn($file);
	}
}

// Get array with rows from specified file
if (!function_exists('room_fga')) {	
	function room_fga($file) {
		return @file($file);
	}
}

// Remove unsafe characters from file/folder path
if (!function_exists('room_esc')) {	
	function room_esc($file) {
		return str_replace(array('\\'), array('/'), $file);
		//return str_replace(array('~', '>', '<', '|', '"', "'", '`', "\xFF", "\x0A", '#', '&', ';', '*', '?', '^', '(', ')', '[', ']', '{', '}', '$'), '', $file);
	}
}

// Create folder
if (!function_exists('room_mkdir')) {	
	function room_mkdir($folder, $addindex = true) {
		if (is_dir($folder) && $addindex == false) return true;
		$created = wp_mkdir_p(trailingslashit($folder));
		@chmod($folder, 0777);
		if ($addindex == false) return $created;
		$index_file = trailingslashit($folder) . 'index.php';
		if (file_exists($index_file)) return $created;
		room_fpc($index_file, "<?php\n// Silence is golden.\n");
		return $created;
	}
}



/* Debug
----------------------------------------------------------------------------------------------------- */
// Short analogs for debug functions
if (!function_exists('dcl')) {	function dcl($msg)	{ 				if (is_user_logged_in()) echo '<br>"' . esc_html($msg) . '"<br>'; } }		// Console log - output any message on the screen
if (!function_exists('dco')) {	function dco(&$var, $lvl=5)	{ 		if (is_user_logged_in()) room_debug_dump_screen($var, $lvl); } }		// Console obj - output object structure on the screen
if (!function_exists('dcs')) {	function dcs($lvl=-1){ 				if (is_user_logged_in()) room_debug_calls_stack_screen($lvl); } }		// Console stack - output calls stack on the screen
if (!function_exists('dcw')) {	function dcw()		{				if (is_user_logged_in()) room_debug_dump_wp(); } }						// Console WP - output WP is_... states on the screen
if (!function_exists('ddo')) {	function ddo(&$var, $lvl=5)	{ 		if (is_user_logged_in()) return room_debug_dump_var($var, 0, $lvl); } }	// Return obj - return object structure
if (!function_exists('dfl')) {	function dfl($var)	{				if (is_user_logged_in()) room_debug_trace_message($var); } }			// File log - output any message into file debug.log
if (!function_exists('dfo')) {	function dfo(&$var, $lvl=5)	{ 		if (is_user_logged_in()) room_debug_dump_file($var, $lvl); } }			// File obj - output object structure into file debug.log
if (!function_exists('dfs')) {	function dfs($lvl=-1){ 				if (is_user_logged_in()) room_debug_calls_stack_file($lvl); } }			// File stack - output calls stack into file debug.log

if (!function_exists('room_debug_die_message')) {
	function room_debug_die_message($msg) {
		room_debug_trace_message($msg);
		die($msg);
	}
}

if (!function_exists('room_debug_trace_message')) {
	function room_debug_trace_message($msg) {
		room_fpc(get_stylesheet_directory().'/debug.log', date('d.m.Y H:i:s')." $msg\n", FILE_APPEND);
	}
}

if (!function_exists('room_debug_calls_stack_screen')) {
	function room_debug_calls_stack_screen($level=-1) {
		$s = debug_backtrace();
		array_shift($s);
		room_debug_dump_screen($s, $level);
	}
}

if (!function_exists('room_debug_calls_stack_file')) {
	function room_debug_calls_stack_file($level=-1) {
		$s = debug_backtrace();
		array_shift($s);
		room_debug_dump_file($s, $level);
	}
}

if (!function_exists('room_debug_dump_screen')) {
	function room_debug_dump_screen(&$var, $level=-1) {
		if ((is_array($var) || is_object($var)) && count($var))
			echo "<pre>\n".nl2br(esc_html(room_debug_dump_var($var, 0, $level)))."</pre>\n";
		else
			echo "<tt>".nl2br(esc_html(room_debug_dump_var($var, 0, $level)))."</tt>\n";
	}
}

if (!function_exists('room_debug_dump_file')) {
	function room_debug_dump_file(&$var, $level=-1) {
		room_debug_trace_message("\n\n".room_debug_dump_var($var, 0, $level));
	}
}

if (!function_exists('room_debug_dump_var')) {
	function room_debug_dump_var(&$var, $level=0, $max_level=-1)  {
		if (is_array($var)) $type="Array[".count($var)."]";
		else if (is_object($var)) $type="Object";
		else $type="";
		if ($type) {
			$rez = "$type\n";
			if ($max_level<0 || $level < $max_level) {
				for (Reset($var), $level++; list($k, $v)=each($var); ) {
					if (is_array($v) && $k==="GLOBALS") continue;
					for ($i=0; $i<$level*3; $i++) $rez .= " ";
					$rez .= $k.' => '. room_debug_dump_var($v, $level, $max_level);
				}
			}
		} else if (is_bool($var))
			$rez = ($var ? 'true' : 'false')."\n";
		else if (is_long($var) || is_float($var) || intval($var) != 0)
			$rez = $var."\n";
		else
			$rez = '"'.($var).'"'."\n";
		return $rez;
	}
}

if (!function_exists('room_debug_dump_wp')) {
	function room_debug_dump_wp($query=null) {
		global $wp_query;
		if (!$query) $query = $wp_query;
		echo "<tt>"
			."<br>admin=".is_admin()
			."<br>mobile=".wp_is_mobile()
			."<br>customize preview=".is_customize_preview()
			."<br>main_query=".is_main_query()."  query=".esc_html($query->is_main_query())
			."<br>home=".is_home()."  query=".esc_html($query->is_home())
			."<br>fp=".is_front_page()."  query=".esc_html($query->is_front_page())
			."<br>query->is_posts_page=".esc_html($query->is_posts_page)
			."<br>search=".is_search()."  query=".esc_html($query->is_search())
			."<br>category=".is_category()."  query=".esc_html($query->is_category())
			."<br>tag=".is_tag()."  query=".esc_html($query->is_tag())
			."<br>archive=".is_archive()."  query=".esc_html($query->is_archive())
			."<br>day=".is_day()."  query=".esc_html($query->is_day())
			."<br>month=".is_month()."  query=".esc_html($query->is_month())
			."<br>year=".is_year()."  query=".esc_html($query->is_year())
			."<br>author=".is_author()."  query=".esc_html($query->is_author())
			."<br>page=".is_page()."  query=".esc_html($query->is_page())
			."<br>single=".is_single()."  query=".esc_html($query->is_single())
			."<br>singular=".is_singular()."  query=".esc_html($query->is_singular())
			."<br>attachment=".is_attachment()."  query=".esc_html($query->is_attachment())
			."<br><br />"
			."</tt>";
	}
}
?>