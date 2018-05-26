<?php
/**
 * @file index.php
 * @date 2018-05-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief ReasonableFramework
 * @cvs http://github.com/gnh1201/reasonableframework
 */

define("_DEF_VSPF_", true);

// define system modules
$load_systems = array("base", "storage", "config", "database", "uri", "security", "logger");

// load system modules
foreach($load_systems as $system_name) {
	$system_inc_file = "./system/" . $system_name . ".php";
	if(file_exists($system_inc_file)) {
		if($system_name == "base") {
			include($system_inc_file);
			register_loaded("system", $system_inc_file);
		} else {
			loadModule($system_name);
		}
	}
}
 
$config = get_config();
$requests = get_requests();

// set max_execution_time
$max_execution_time = get_value_in_array("max_execution_time", $config, 0);
@ini_set("max_execution_time", $max_execution_time);

// autoload module
if(!array_key_empty("enable_autoload", $config)) {
	loadModule("autoload");
}

// set timezone
$default_timezone = get_value_in_array("timezone", $config, "UTC");
date_default_timezone_set($default_timezone);

// route controller
$route = get_value_in_array("route", $requests['all'], "");

// load route
if(empty($route)) {
	$route = get_value_in_array("default_route", $config, "welcome");
} else {
	$route_names = explode('/', $route);
	if(count($route_names) > 1) {
		$route = $route_names[0];
	}
}

// load route file
if(!loadRoute($route, $scope)) {
	loadRoute("errors/404", $scope);
}
