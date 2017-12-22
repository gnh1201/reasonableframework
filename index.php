<?php
/*
 * VerySimplePHPFramework
 * Go Namhyeon <gnh1201@gmail.com>
 * Date: 2017-12-18
 */

// including vendor autoloader
include_once('./vendor/autoload.php');

// load system files
$load_systems = array('config', 'database', 'uri');
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
	$route = 'welcome';
} else {
	$route_names = explode('/', $route);
	if(count($route) > 1) {
		$route = end($route_names);
	}
}

// view render
function renderView($name) {
	$viewfile = './view/' . $name . '.php';
	if(file_exists($viewfile)) {
		include($viewfile);
	}
}

// including route file
include('./route/' . $route . '.php');
