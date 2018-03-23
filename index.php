<?php
/**
 * @file index.php
 * @date 2017-12-18
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief ReasonableFramework
 * @cvs http://github.com/gnh1201/reasonableframework
 */

define("_DEF_VSPF_", true);

// define system modules
$load_systems = array("base", "config", "database", "uri", "logger", "security");

// load system modules
foreach($load_systems as $system_name) {
	$system_inc_file = "./system/" . $system_name . ".php";
	if(file_exists($system_inc_file)) {
		include_once($system_inc_file);
	}
}

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
$route = get_value_in_array("route", $_REQUEST, "");

// load route
if(empty($route)) {
	$route = "welcome";
} else {
	$route_names = explode('/', $route);
	if(count($route) > 1) {
		$route = end($route_names);
	}
}

// including route file
$route_file_name = "./route/" . $route . ".php";
if(file_exists($route_file_name)) {
	include($route_file_name);
} else {
	include("./route/errors/404.php");
}
