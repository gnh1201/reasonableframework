<?php
/**
 * @file base.php
 * @date 2018-04-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Base module
 */

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
		$flag = true;
		$views = explode(';', $name);
		foreach($views as $name2) {
			$viewfile = './view/' . $name2 . '.php';
			if(file_exists($viewfile)) {
				$flag = $flag && !include_isolate($viewfile, $data);
				register_loaded("view", $viewfile);
			}
		}
		return !$flag;
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
				$flag = $flag && !include_isolate($systemfile); 
				register_loaded("system", $systemfile);
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
				$flag = $flag && !include_isolate($helperfile); 
				register_loaded("helper", $helperfile);
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
			$routefile = './route/' . $name . '.php';
			if(file_exists($routefile)) {
				$flag = $flag && !include_isolate($routefile, $data);
				register_loaded("route", $routefile);
			} else { 
				set_error("Route " . $name . "dose not exists");
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

// check function exists
if(!function_exists("check_function_exists")) {
	function check_function_exists($rules) {
		$flag = true;

		if(is_string($rules)) {
			$rules = explode(";", $rules);
		}

		foreach($rules as $k=>$v) {
			$exists = function_exists($k);
			$flag = $flag && !$exists;
			if($exists === false) {
				if(empty($v)) {
					set_error("Function " . $k . " dose not exists");
				} else {
					set_error($v);
				}
			}
		}

		return !$flag;
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


$scope = array();

set_scope("loaded", array(
	"module" => array(),
	"helper" => array(),
	"view" => array(),
	"route" => array(),
));

set_scope("errors", array());
