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

// load view file
if(!function_exists("renderView")) {
	function renderView($name, $data=array()) {
		if(count($data) > 0) {
			extract($data);
		}

		$flag = true;
		$views = explode(';', $name);
		foreach($views as $name2) {
			$viewfile = './view/' . $name2 . '.php';
			if(file_exists($viewfile)) {
				$flag = $flag && include_isolate($viewfile, $data);
				register_loaded("view", $viewfile);
			}
		}
		return $flag;
	}
}

// load system module
if(!function_exists("loadModule")) {
	function loadModule($name) {
		$flag = true;
		$modules = explode(';', $name);
		foreach($modules as $name2) {
			$systemfile = './system/' . $name2 . '.php';
			if(file_exists($systemfile)) {
				$flag = $flag && include_isolate($systemfile); 
				register_loaded("view", $systemfile);
			}
		}
		return $flag;
	}
}

// load helper file
if(!function_exists("loadHelper")) {
	function loadHelper($name) {
		$flag = true;
		$helpers = explode(';', $name);
		foreach($helpers as $name2) {
			$helperfile = './helper/' . $name2 . '.php';
			if(file_exists($helperfile)) {
				$flag = $flag && include_isolate($helperfile); 
				register_loaded("helper", $helperfile);
			}
		}
	}
}

// load route file
if(!function_exists("loadRoute")) {
	function loadRoute($name, $data=array()) {
		$flag = true;
		$routes = explode(";", $name);
		foreach($routes as $name2) {
			$routefile = './route/' . $name . '.php';
			if(file_exists($routefile)) {
				$flag = $flag && include_isolate($routefile, $data);
				register_loaded("route", $routefile);
			}
		}
		return $flag;
	}
}

if(!function_exists("array_key_empty")) {
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

if(!function_exists("array_multikey_empty")) {
	function array_multikey_empty($keys, $array) {
		$flag = true;
		foreach($keys as $key) {
			$flag = $flag && array_key_empty($key, $array);
		}
		return $flag;
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

if(!function_exists("get_value_in_object")) {
	function get_value_in_object($name, $obj, $default="") {
		$output = $obj->$name;
		return $output;
	}
}

$scope = array();

set_scope("loaded", array(
	"module" => array(),
	"helper" => array(),
	"view" => array(),
	"route" => array(),
));
