#!/bin/php -q
<?php
/**
 * @file cli.php
 * @created_on 2018-07-22
 * @created_on 2020-01-28
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief ReasonableFramework CLI mode
 * @cvs http://github.com/gnh1201/reasonableframework
 */

define("_DEF_VSPF_", true); // compatible to VSPF
define("_DEF_RSF_", true); // compatible to RSF
define("APP_DEVELOPMENT", false); // set the status of development
define("DOC_EOL", "\r\n"); // set the 'end of line'

// development mode
if(APP_DEVELOPMENT == true) {
    error_reporting(E_ALL);
    @ini_set("log_errors", 1);
    @ini_set("error_log", sprintf("%s/storage/sandbox/logs/error.log", getcwd()));
} else {
    error_reporting(E_ERROR | E_PARSE);
}
@ini_set("display_errors", 1);

// set shared vars
$shared_vars = array();

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
//@set_time_limit($max_execution_time);

// set memory limit
$memory_limit = get_value_in_array("memory_limit", $config, "");
if(!empty($memory_limit)) {
    @ini_set("memory_limit", $memory_limit);
    @ini_set("suhosin.memory_limit", $memory_limit);
}


// autoload module
if(!array_key_empty("enable_autoload", $config)) {
    set_autoloader();
}

// set timezone
$default_timezone = get_value_in_array("timezone", $config, "UTC");
date_default_timezone_set($default_timezone);

// set default route
$route = "welcome";

// set arguments of command line
$opts = getopt("r::h::", array("route::", "host::")); 
if(!empty($opts['route'])) {
    $route = $opts['route'];
}

// set global variables
set_shared_var("route", $route);
set_shared_var("host", $opts['host']);

// load route file
if(!loadRoute($route, $shared_vars)) {
    loadRoute("errors/404", $shared_vars);
}
