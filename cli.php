#!/bin/php -q
<?php
/**
 * @file cli.php
 * @date 2018-07-22
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief ReasonableFramework CLI mode
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
$load_systems = array("base", "storage", "config", "security", "database", "uri");

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

// get configurations
$config = get_config();

// set max_execution_time
$max_execution_time = get_value_in_array("max_execution_time", $config, 0);
@ini_set("max_execution_time", $max_execution_time);
//@set_time_limit($max_execution_time);

// autoload module
if(!array_key_empty("enable_autoload", $config)) {
    set_autoloader();
}

// set timezone
$default_timezone = get_value_in_array("timezone", $config, "UTC");
date_default_timezone_set($default_timezone);

// default route
$route = "welcome";

// parse arguments
$num_of_args = count($argv);
if($num_of_args > 1) {
    foreach($argv as $k=>$v) {
        switch($v) {
            case "--route":
                if($k < ($num_of_args - 1)) {
                    $route = $argv[$k + 1];
                } else {
                    set_error("invaild argument");
                    show_errors();
                }
                break;
            case "--static-ip":
                if($k < ($num_of_args - 1)) {
                    $host = $argv[$k + 1];
                    set_scope("static_ip", $host);
                } else {
                    set_error("invaild argument");
                    show_errors();
                }
                break;
        }
    }
} else {
    set_error("not enough arguments");
    show_errors();
}

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
