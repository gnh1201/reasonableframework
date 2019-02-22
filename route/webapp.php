<?php
$routes = read_route_all();
$webapp_root = $_SERVER["DOCUMENT_ROOT"] . "webapp";

// set DOCUMENT_ROOT forcely
$_SERVER["DOCUMENT_ROOT"] = $webapp_root;

// set file path
$appfile_path = $webapp_root . "/" . implode("/", array_slice($routes, 1)) . ".php";

// load file
if(file_exists($appfile_path)) {
    include($appfile_path);
} else {
    set_error("404 Not Found");
    show_errors();
}
