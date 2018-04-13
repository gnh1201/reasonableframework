<?php
/**
 * @file base.php
 * @date 2018-04-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Base module
 */

// set scope
if(!function_exists("set_scope")) {
	function set_scope($k, $v) {
		global $scope;
		$scope[$k] = $v;
	}
}

// get scope
if(!function_exists("get_scope")) {
	function get_scope($k) {
		global $scope;
		return array_key_exists($k, $scope) ? $scope[$k] : null;
	}
}

// register loaded resources
if(!function_exists("register_loaded")) {
	function register_loaded($k, $v) {
		global $scope;
		if(array_key_exists($k, $scope['loaded'])) {
			array_push($scope['loaded'][$k], $v);
		}
	}
}

// sandbox for include function
if(!function_exists("include_isolate")) {
	function include_isolate($file, $data=array()) {
		if(count($data) > 0) {
			extract($data);
		}
		return include($file);
	}
}

// view render
if(!function_exists('renderView')) {
	function renderView($name, $data=array()) {
		if(count($data) > 0) {
			extract($data);
		}

		// load view file
		$views = explode(';', $name);
		foreach($views as $name2) {
			$viewfile = './view/' . $name2 . '.php';
			if(file_exists($viewfile)) {
				include_isolate($viewfile, $data);
				register_loaded("view", $viewfile);
			}
		}
	}
}

// load system module
if(!function_exists('loadModule')) {
	function loadModule($name) {
		// load system file
		$modules = explode(';', $name);
		foreach($modules as $name2) {
			$systemfile = './system/' . $name2 . '.php';
			if(file_exists($systemfile)) {
				include_isolate($systemfile); 
				register_loaded("view", $systemfile);
			}
		}
	}
}

// load helper
if(!function_exists('loadHelper')) {
	function loadHelper($name) {
		// load helper file
		$helpers = explode(';', $name);
		foreach($helpers as $name2) {
			$helperfile = './helper/' . $name2 . '.php';
			if(file_exists($helperfile)) {
				include_isolate($helperfile); 
				register_loaded("helper", $helperfile);
			}
		}
	}
}

// re-route
if(!function_exists('loadRoute')) {
	function loadRoute($name, $data=array()) {
		// load route file
		$routes = explode(";", $name);
		foreach($routes as $name2) {
			$routefile = './route/' . $name . '.php';
			if(file_exists($routefile)) {
				include_isolate($routefile, $data); 
				register_loaded("route", $routefile);
			}
		}
	}
}

if(!function_exists('array_key_empty')) {
	function array_key_empty($key, $array) {
		$empty = true;
		
		if(is_array($array)) {
			if(array_key_exists($key, $array)) {
				if(!empty($array[$key])) {
					$empty = false;
				}
			}
		}
		
		return $empty;
	}
}

if(!function_exists('array_multikey_empty')) {
	function array_multikey_empty($keys, $array) {
		$empty = true;
		foreach($keys as $key) {
			$empty = ($empty && array_key_empty($key, $array));
		}
		return $empty;
	}
}

if(!function_exists("get_value_in_array")) {
	function get_value_in_array($name, $arr=array(), $default=0) {
		$output = 0;

		if(is_array($arr)) {
			$output = array_key_empty($name, $arr) ? $default : $arr[$name];
		} else {
			$output = $default;
		}

		return $output;
	}
}

if(!function_exists("cut_str")) {
	function cut_str($str, $start, $len=0) {
		$cutted_str = "";
		if(function_exists("iconv_substr")) {
			$cutted_str = iconv_substr($str, $start, $len, "utf-8");
		} elseif(function_exists("mb_substr")) {
			$cutted_str = mb_substr($str, $start, $len);
		} else {
			$cutted_str = substr($start, $len);
		}
		
		return $cutted_str;
	}
}

if(!function_exists("read_file_by_line")) {
	function read_file_by_line($filename) {
		$lines = array();
		$handle = fopen($filename, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$lines[] .= $line;
			}

			fclose($handle);
		}
		
		return $lines;
	}
}

if(!function_exists("nl2p")) {
	function nl2p($string) {
		$paragraphs = '';
		foreach (explode("\n", $string) as $line) {
			if (trim($line)) {
				$paragraphs .= '<p>' . $line . '</p>';
			}
		}
		return $paragraphs;
	}
}

$scope = array();

set_scope("loaded", array(
	"module" => array(),
	"helper" => array(),
	"view" => array(),
	"route" => array(),
));
