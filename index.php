<?php
/**
 * @file index.php
 * @date 2017-12-18
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief VerySimplePHPFramework
 * @cvs http://github.com/gnh1201/verysimplephpframework
 */

define("_DEF_VSPF_", true);
ini_set("max_execution_time", 0);

error_reporting(E_ALL);
ini_set("display_errors", 1);

// including vendor autoloader
include_once('./vendor/autoload.php');

// load system files
$load_systems = array('base', 'config', 'database', 'uri', 'logger', 'security');
foreach($load_systems as $system_name) {
	$system_inc_file = './system/' . $system_name . '.php';
	if(file_exists($system_inc_file)) {
		include_once($system_inc_file);
	}
}

// route controller
$route = '';
if(array_key_exists('route', $_REQUEST)) {
	$route = $_REQUEST['route'];
}

if(empty($route)) {
	$route = 'index';
} else {
	$route_names = explode('/', $route);
	if(count($route) > 1) {
		$route = end($route_names);
	}
}

// including route file
$route_file_name = './route/' . $route . '.php';
if(file_exists($route_file_name)) {
	include($route_file_name);
} else {
	include('./route/errors/404.php');
}
