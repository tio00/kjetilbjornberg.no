<?php
/**
 * Theme storage manipulations
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Get theme variable
if (!function_exists('room_storage_get')) {
	function room_storage_get($var_name, $default='') {
		global $ROOM_STORAGE;
		return isset($ROOM_STORAGE[$var_name]) ? $ROOM_STORAGE[$var_name] : $default;
	}
}

// Set theme variable
if (!function_exists('room_storage_set')) {
	function room_storage_set($var_name, $value) {
		global $ROOM_STORAGE;
		$ROOM_STORAGE[$var_name] = $value;
	}
}

// Check if theme variable is empty
if (!function_exists('room_storage_empty')) {
	function room_storage_empty($var_name, $key='', $key2='') {
		global $ROOM_STORAGE;
		if (!empty($key) && !empty($key2))
			return empty($ROOM_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return empty($ROOM_STORAGE[$var_name][$key]);
		else
			return empty($ROOM_STORAGE[$var_name]);
	}
}

// Check if theme variable is set
if (!function_exists('room_storage_isset')) {
	function room_storage_isset($var_name, $key='', $key2='') {
		global $ROOM_STORAGE;
		if (!empty($key) && !empty($key2))
			return isset($ROOM_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return isset($ROOM_STORAGE[$var_name][$key]);
		else
			return isset($ROOM_STORAGE[$var_name]);
	}
}

// Inc/Dec theme variable with specified value
if (!function_exists('room_storage_inc')) {
	function room_storage_inc($var_name, $value=1) {
		global $ROOM_STORAGE;
		if (empty($ROOM_STORAGE[$var_name])) $ROOM_STORAGE[$var_name] = 0;
		$ROOM_STORAGE[$var_name] += $value;
	}
}

// Concatenate theme variable with specified value
if (!function_exists('room_storage_concat')) {
	function room_storage_concat($var_name, $value) {
		global $ROOM_STORAGE;
		if (empty($ROOM_STORAGE[$var_name])) $ROOM_STORAGE[$var_name] = '';
		$ROOM_STORAGE[$var_name] .= $value;
	}
}

// Get array (one or two dim) element
if (!function_exists('room_storage_get_array')) {
	function room_storage_get_array($var_name, $key, $key2='', $default='') {
		global $ROOM_STORAGE;
		if (empty($key2))
			return !empty($var_name) && !empty($key) && isset($ROOM_STORAGE[$var_name][$key]) ? $ROOM_STORAGE[$var_name][$key] : $default;
		else
			return !empty($var_name) && !empty($key) && isset($ROOM_STORAGE[$var_name][$key][$key2]) ? $ROOM_STORAGE[$var_name][$key][$key2] : $default;
	}
}

// Set array element
if (!function_exists('room_storage_set_array')) {
	function room_storage_set_array($var_name, $key, $value) {
		global $ROOM_STORAGE;
		if (!isset($ROOM_STORAGE[$var_name])) $ROOM_STORAGE[$var_name] = array();
		if ($key==='')
			$ROOM_STORAGE[$var_name][] = $value;
		else
			$ROOM_STORAGE[$var_name][$key] = $value;
	}
}

// Set two-dim array element
if (!function_exists('room_storage_set_array2')) {
	function room_storage_set_array2($var_name, $key, $key2, $value) {
		global $ROOM_STORAGE;
		if (!isset($ROOM_STORAGE[$var_name])) $ROOM_STORAGE[$var_name] = array();
		if (!isset($ROOM_STORAGE[$var_name][$key])) $ROOM_STORAGE[$var_name][$key] = array();
		if ($key2==='')
			$ROOM_STORAGE[$var_name][$key][] = $value;
		else
			$ROOM_STORAGE[$var_name][$key][$key2] = $value;
	}
}

// Merge array elements
if (!function_exists('room_storage_merge_array')) {
	function room_storage_merge_array($var_name, $key, $value) {
		global $ROOM_STORAGE;
		if (!isset($ROOM_STORAGE[$var_name])) $ROOM_STORAGE[$var_name] = array();
		if ($key==='')
			$ROOM_STORAGE[$var_name] = array_merge($ROOM_STORAGE[$var_name], $value);
		else
			$ROOM_STORAGE[$var_name][$key] = array_merge($ROOM_STORAGE[$var_name][$key], $value);
	}
}

// Add array element after the key
if (!function_exists('room_storage_set_array_after')) {
	function room_storage_set_array_after($var_name, $after, $key, $value='') {
		global $ROOM_STORAGE;
		if (!isset($ROOM_STORAGE[$var_name])) $ROOM_STORAGE[$var_name] = array();
		if (is_array($key))
			room_array_insert_after($ROOM_STORAGE[$var_name], $after, $key);
		else
			room_array_insert_after($ROOM_STORAGE[$var_name], $after, array($key=>$value));
	}
}

// Add array element before the key
if (!function_exists('room_storage_set_array_before')) {
	function room_storage_set_array_before($var_name, $before, $key, $value='') {
		global $ROOM_STORAGE;
		if (!isset($ROOM_STORAGE[$var_name])) $ROOM_STORAGE[$var_name] = array();
		if (is_array($key))
			room_array_insert_before($ROOM_STORAGE[$var_name], $before, $key);
		else
			room_array_insert_before($ROOM_STORAGE[$var_name], $before, array($key=>$value));
	}
}

// Push element into array
if (!function_exists('room_storage_push_array')) {
	function room_storage_push_array($var_name, $key, $value) {
		global $ROOM_STORAGE;
		if (!isset($ROOM_STORAGE[$var_name])) $ROOM_STORAGE[$var_name] = array();
		if ($key==='')
			array_push($ROOM_STORAGE[$var_name], $value);
		else {
			if (!isset($ROOM_STORAGE[$var_name][$key])) $ROOM_STORAGE[$var_name][$key] = array();
			array_push($ROOM_STORAGE[$var_name][$key], $value);
		}
	}
}

// Pop element from array
if (!function_exists('room_storage_pop_array')) {
	function room_storage_pop_array($var_name, $key='', $defa='') {
		global $ROOM_STORAGE;
		$rez = $defa;
		if ($key==='') {
			if (isset($ROOM_STORAGE[$var_name]) && is_array($ROOM_STORAGE[$var_name]) && count($ROOM_STORAGE[$var_name]) > 0) 
				$rez = array_pop($ROOM_STORAGE[$var_name]);
		} else {
			if (isset($ROOM_STORAGE[$var_name][$key]) && is_array($ROOM_STORAGE[$var_name][$key]) && count($ROOM_STORAGE[$var_name][$key]) > 0) 
				$rez = array_pop($ROOM_STORAGE[$var_name][$key]);
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if (!function_exists('room_storage_inc_array')) {
	function room_storage_inc_array($var_name, $key, $value=1) {
		global $ROOM_STORAGE;
		if (!isset($ROOM_STORAGE[$var_name])) $ROOM_STORAGE[$var_name] = array();
		if (empty($ROOM_STORAGE[$var_name][$key])) $ROOM_STORAGE[$var_name][$key] = 0;
		$ROOM_STORAGE[$var_name][$key] += $value;
	}
}

// Concatenate array element with specified value
if (!function_exists('room_storage_concat_array')) {
	function room_storage_concat_array($var_name, $key, $value) {
		global $ROOM_STORAGE;
		if (!isset($ROOM_STORAGE[$var_name])) $ROOM_STORAGE[$var_name] = array();
		if (empty($ROOM_STORAGE[$var_name][$key])) $ROOM_STORAGE[$var_name][$key] = '';
		$ROOM_STORAGE[$var_name][$key] .= $value;
	}
}

// Call object's method
if (!function_exists('room_storage_call_obj_method')) {
	function room_storage_call_obj_method($var_name, $method, $param=null) {
		global $ROOM_STORAGE;
		if ($param===null)
			return !empty($var_name) && !empty($method) && isset($ROOM_STORAGE[$var_name]) ? $ROOM_STORAGE[$var_name]->$method(): '';
		else
			return !empty($var_name) && !empty($method) && isset($ROOM_STORAGE[$var_name]) ? $ROOM_STORAGE[$var_name]->$method($param): '';
	}
}

// Get object's property
if (!function_exists('room_storage_get_obj_property')) {
	function room_storage_get_obj_property($var_name, $prop, $default='') {
		global $ROOM_STORAGE;
		return !empty($var_name) && !empty($prop) && isset($ROOM_STORAGE[$var_name]->$prop) ? $ROOM_STORAGE[$var_name]->$prop : $default;
	}
}
?>