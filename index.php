<?php
/**
 * @file index.php
 * @date 2018-05-27
 * @updated 2019-11-15
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief ReasonableFramework
 * @cvs https://github.com/gnh1201/reasonableframework
 * @sponsor https://patreon.com/catswords (with advanced security)
 */

define("_DEF_VSPF_", true); // compatible to VSPF
define("_DEF_RSF_", true); // compatible to RSF
define("APP_DEVELOPMENT", false); // set the status of development
define("DOC_EOL", "\r\n"); // set the 'end of line' commonly
define("CORS_DOMAINS", false); // common security: allow origin domains (e.g. example.org,*.example.org)
define("PHP_FIREWALL_REQUEST_URI", strip_tags($_SERVER['REQUEST_URI'])); // advanced security
define("PHP_FIREWALL_ACTIVATION", false); // advanced security
define("PHP_DDOS_PROTECTION", false); // advanced security

// development mode
if(APP_DEVELOPMENT == true) {
    error_reporting(E_ALL);
    ini_set("log_errors", 1);
    ini_set("error_log", sprintf("%s/storage/sandbox/logs/error.log", getcwd()));
} else {
    error_reporting(E_ERROR | E_PARSE);
}
ini_set("display_errors", 1);

// CORS Security (https or http)
if(CORS_DOMAINS !== false) {
    $domains = explode(",", CORS_DOMAINS);
    $_origin = array_key_exists("HTTP_ORIGIN", $_SERVER) ? $_SERVER['HTTP_ORIGIN'] : "";
    $origins = array();
    if(!in_array("*", $domains)) {
        foreach($domains as $domain) {
            if(!empty($domain)) {
                if(substr($domain, 0, 2) == "*.") { // support wildcard
                    $needle = substr($domain, 1);
                    $length = strlen($needle);
                    if(substr($_origin, -$length) === $needle) {
                        $origins[] = $_origin;
                    }
                } else {
                    $origins[] = sprintf("https://%s", $domain);
                    $origins[] = sprintf("http://%s", $domain);
                }
            }
        }
        if(count($origins) > 0) {
            if(in_array($_origin, $origins)) {
                header(sprintf("Access-Control-Allow-Origin: %s", $_origin));
            } else {
                header(sprintf("Access-Control-Allow-Origin: %s", $origins[0])); 
            }
        }
    } else {
        header("Access-Control-Allow-Origin: *");
    }
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

// set scopes (1.6 - Change calling convention for lagacy PHP)
set_scope("dbc", get_db_connect());
set_scope("requests", read_requests());

// set max_execution_time
$max_execution_time = get_value_in_array("max_execution_time", $config, 0);
@ini_set("max_execution_time", $max_execution_time);

// start session
start_isolated_session();

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

// advanced security: set PHP firewall
if(PHP_FIREWALL_ACTIVATION !== false) {
    loadHelper("php-firewall.lnk");
}

// advanced security: set DDOS protection
IF(PHP_DDOS_PROTECTION !== false) {
    loadHelper("php-ddos.lnk");
}

// load route file
if(!loadRoute($route, $scope)) {
    loadRoute("errors/404", $scope);
}

// flush cache
if(array_key_equals("cache_enabled", $config, 1)) {
    remove_storage_files("cache", array(
        "max_age" => get_value_in_array("cache_max_age", $config, 0)
    ));
}

// EOF
