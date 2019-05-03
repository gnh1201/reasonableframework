<?php
/**
 * @file index.php
 * @date 2018-05-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief ReasonableFramework
 * @cvs http://github.com/gnh1201/reasonableframework
 */

define("_DEF_VSPF_", true); // compatible to VSPF
define("_DEF_RSF_", true); // compatible to RSF
define("APP_DEVELOPMENT", false); // set the status of development
define("DOC_EOL", "\r\n"); // set the 'end of line' commonly

// check if current status is development
if(APP_DEVELOPMENT == true) {
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
}

// set empty scope
$scope = array();

// define system modules
$load_systems = array("base", "storage", "config", "security", "database", "uri", "logger");

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
	} else {
                echo "ERROR: Dose not exists " . $system_inc_file;
                exit;
        }
}

// get config
$config = get_config();

// set max_execution_time
$max_execution_time = get_value_in_array("max_execution_time", $config, 0);
@ini_set("max_execution_time", $max_execution_time);

// set autoloader
if(!array_key_empty("enable_autoload", $config)) {
	set_autoloader();
}

// set timezone
$default_timezone = get_value_in_array("timezone", $config, "UTC");
date_default_timezone_set($default_timezone);

// write visit log
write_visit_log();

// get requested route
$route = read_route();

// load route file
if(!loadRoute($route, $scope)) {
	loadRoute("errors/404", $scope);
}
