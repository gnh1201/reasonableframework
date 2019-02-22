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
	$is_static_file = false;
	$end_route = end($routes);
	$end_routes_attributes = explode(".", $end_route);
	$end_era = end($end_routes_attributes);

	if($end_era == "php" || file_exists($appfile_path)) {
		$appfile_path = str_replace(".php.php", ".php", $appfile_path);
		if(file_exists($appfile_path)) {
			include($appfile_path);
		} else {
			set_error("Webapp 404 Not Found");
			show_errors();
		}
	} else {
		if(file_exists($appfile . "index.php")) {
			$appfile .= "index.php";
			include($appfile);
		} elseif(file_exists($appfile . "index.html")) {
			$is_static_file = true;
			$appfile .= "index.html";
			$end_era = "html";
		} else {
			$is_static_file = true;
		}
	}

	if($is_static_file == true) {
		set_header_content_type($end_era);
		header("Cache-Control: max-age=86400");
		$fp = fopen($appfile, "r") or die("file does not exists");
		$buffer = fread($fp, filesize($appfile));
		echo $buffer;
		fclose($fp);
	}
}
