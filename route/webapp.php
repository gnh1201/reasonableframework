<?php
if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

$routes = read_route_all();

if($routes[0] == "") {
	renderView("view_welcome");
} else {
	$webapp_root = $_SERVER["DOCUMENT_ROOT"] . "webapp";
	$webapp_url = base_url() . "webapp";

	// set DOCUMENT_ROOT forcely
	$_SERVER["DOCUMENT_ROOT"] = $webapp_root;

	// set file path
	$appfile = $webapp_root . "/" . implode("/", $routes);
	$appfile_path = $appfile . ".php";

	// get end of routes
	$end_route = end($routes);
	$end_routes_attributes = explode(".", $end_route);
	if(end($end_routes_attributes) == "php" || count($end_routes_attributes) == 1) {
		$appfile_path = str_replace(".php.php", ".php", $appfile_path);
		if(file_exists($appfile_path)) {
			include($appfile_path);
		} else {
			set_error("Webapp 404 Not Found");
			show_errors();
		}
	} else {
		set_header_content_type(end($end_routes_attributes));
		$fp = fopen($appfile, "r") or die("file does not exists");
		$buffer = fread($fp, filesize($appfile));
		echo $buffer;
		fclose($fp);
	}
}
