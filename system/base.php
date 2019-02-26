<?php
/**
 * @file base.php
 * @date 2018-04-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Base module
 */

// check invaild function (mixed)
if(!function_exists("check_invaild_function")) {
	function check_invaild_function($fn) {
		$status = -1;

		if(is_array($fn)) {
			foreach($fn as $k=>$v) {
				if(!function_exists($v)) {
					$status = $k;
					break;
				}
			} 
		} else {
			if(!function_exists($fn)) {
				$status = 0;
			}
		}
	}
	
	return $status;   
}

// check vaild function (bool)
if(!function_exists("check_vaild_function")) {
	function check_vaild_function($fn) {
		return (check_invaild_function($fn) == -1);
	}
}

// get all scope
if(!function_exists("get_scope_all")) {
	function get_scope_all() {
		global $scope;
		return $scope;
	}
}

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
		$scope['loaded'][$k][] = $v;
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

// set autoloader
if(!function_exists("set_autoloader")) {
	function set_autoloader() {
		return include('./vendor/autoload.php');
	}
}

// load view file
if(!function_exists("renderView")) {
	function renderView($name, $data=array()) {
		$flag = true;
		$views = explode(';', $name);
		foreach($views as $name2) {
			$viewfile = './view/' . $name2 . '.php';
			if(file_exists($viewfile)) {
				register_loaded("view", $name2);
				$flag = $flag && !include_isolate($viewfile, $data);
			}
		}
		return !$flag;
	}
}

// load view by rules
if(!function_exists("renderViewByRules")) {
	function renderViewByRules($rules, $data=array()) {
		foreach($rules as $k=>$v) {
			if(in_array($k, get_routes())) {
				renderView($v, $data);
			}
		}
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
				register_loaded("system", $name2);
				$flag = $flag && !include_isolate($systemfile); 
			} else {
				set_error("Module " . $name . "dose not exists");
			}
		}
		return !$flag;
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
				register_loaded("helper", $name2);
				$flag = $flag && !include_isolate($helperfile); 
			} else {
				set_error("Helper " . $name . "dose not exists");
			}
		}
		return !$flag;
	}
}

// load route file
if(!function_exists("loadRoute")) {
	function loadRoute($name, $data=array()) {
		$flag = true;
		$routes = explode(";", $name);
		foreach($routes as $name2) {
			$routefile = './route/' . $name2 . '.php';
			if(file_exists($routefile)) {
				register_loaded("route", $name2);
				$flag = $flag && !include_isolate($routefile, $data);
			} else { 
				set_error("Route " . $name . "dose not exists");
			}
		}
		return !$flag;
	}
}

// load vendor file
if(!function_exists("loadVendor")) {
	function loadVendor($uses, $data=array()) {
		$flag = true;
		$usenames = array();

		if(is_string($uses) && !empty($uses)) {
			$usenames[] = $uses;
		} elseif(is_array($uses)) {
			$usenames = array_merge($usenames, $uses);
		} else {
			return !$flag;
		}

		foreach($usenames as $name) {
			$vendorfile = './vendor/' . $name . '.php';
			if(file_exists($vendorfile)) {
				register_loaded("vendor", $name);
				$flag = $flag && !include_isolate($vendorfile, $data);
			} else {
				set_error("Vendor " . $name . "dose not exists");
			}
		}
		return !$flag;
	}
}

if(!function_exists("array_key_empty")) {
	function array_key_empty($key, $array) {
		$empty = true;
		
		if(is_array($array)) {
			if(array_key_exists($key, $array)) {
				$empty = $empty && empty($array[$key]);
			}
		}

		return $empty;
	}
}

if(!function_exists("array_key_equals")) {
	function array_key_equals($key, $array, $value) {
		$equals = false;

		if(is_array($array)) {
			if(array_key_exists($key, $array)) {
				$equals = ($array[$key] == $value);
			}
		}

		return $equals;
	}
}

if(!function_exists("array_key_is_array")) {
	function array_key_is_array($key, $array) {
		$flag = false;

		if(is_array($array)) {
			if(array_key_exists($key, $array)) {
				$flag = is_array($array[$key]);
			}
		}

		return $flag;
	}
}

if(!function_exists("array_multikey_empty")) {
	function array_multikey_empty($keys, $array) {
		$flag = false;
		foreach($keys as $key) {
			if(array_key_empty($key, $array)) {
				$flag = $key;
			}
		}
		return $flag;
	}
}

if(!function_exists("get_value_in_array")) {
	function get_value_in_array($name, $arr=array(), $default=false) {
		$output = false;

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

// error handler
if(!function_exists("set_error")) {
	function set_error($msg, $code="ERROR") {
		global $scope;
		$scope['errors'][] = $code . ": " . $msg;
	}
}

if(!function_exists("get_errors")) {
	function get_errors($d=false, $e=false) { // d: display, e: exit
		global $scope;
		$errors = $scope['errors'];
		if($d === true) {
			foreach($errors as $err) {
				echo $err . PHP_EOL;
			}
		}

		if($e === true) {
			exit;
		}

		return $errors;
	}
}

if(!function_exists("show_errors")) {
	function show_errors($exit=true) {
		return get_errors(true, $exit);
	}
}

if(!function_exists("set_error_exit")) {
	function set_error_exit($msg, $code="ERROR") {
		set_error($msg, $code);
		show_errors();
	}
}


if(!function_exists("get_property_value")) {
	function get_property_value($prop, $obj, $ac=false) {
		$result = false;
		if(is_object($obj) && property_exists($obj, $prop)) {
			if($ac) {
				$reflection = new ReflectionClass($obj);
				$property = $reflection->getProperty($prop);
				$property->setAccessible($ac);
				$result = $property->getValue($obj);
			} else {
				$result = $obj->{$prop};
			}
		}
		return $result;
	}
}

if(!function_exists("get_routes")) {
	function get_routes() {
		$loaded = get_scope("loaded");
		return $loaded['route'];
	}
}


$scope = array();

set_scope(
	"loaded",
	array(
		"module" => array(),
		"helper" => array(),
		"view" => array(),
		"route" => array(),
		"vendor" => array()
	)
);

set_scope("errors", array());
