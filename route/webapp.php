<?php
/**
 * @file webapp.php
 * @date 2019-02-23
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Isolator(Compatibility mode) for legacy application
 */

loadHelper("isemail.lnk");

if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

// Protect GET method
foreach($_GET as $k=>$v) {
    $_GET[$k] = get_requested_value($k, "_GET");
}

// Protect POST method
foreach($_POST as $k=>$v) {
    $_POST[$k] = get_requested_value($k, "_POST");
}

// Protect REQUEST(ALL) method
foreach($_REQUEST as $k=>$v) {
    $_REQUEST[$k] = get_requested_value($k, "_ALL");
}

// get self filename
$self_filename = "";

// get routes
$routes = read_route_all();

// set path and URL
$webapp_root = $_SERVER["DOCUMENT_ROOT"] . "/webapp";
$webapp_url = base_url() . "webapp";

// set DOCUMENT_ROOT forcely
$_SERVER["DOCUMENT_ROOT"] = $webapp_root;

// set file path
$appfile = $webapp_root . "/" . implode("/", $routes);

$appfile_path = $appfile . ".php";

// get end of routes
$is_static_file = false;
$is_redirect_to_index = false;
$end_route = end($routes);
$end_routes_attributes = explode(".", $end_route);
$end_fra = $end_routes_attributes[0];
$end_era = end($end_routes_attributes);

if($end_era == "php" || file_exists($appfile_path)) {
    $appfile_path = str_replace(".php.php", ".php", $appfile_path);
    if(file_exists($appfile_path)) {
        include($appfile_path);
        $self_filename = $end_fra . ".php";
    } else {
        set_error("Webapp 404 Not Found");
        show_errors();
    }
} else {
    if(file_exists($appfile . "/index.php")) {
        $appfile .= "/index.php";
        if(empty($end_era)) {
            include($appfile);
        } else {
            $is_redirect_to_index = true;
        }
    } elseif(file_exists($appfile . "/index.html")) {
        $is_static_file = true;
        $appfile .= "/index.html";
        if(empty($end_era)) {
            $end_era = "html";
        } else {
            $is_redirect_to_index = true;
        }
    } else {
        $is_static_file = true;
    }
}

if($is_redirect_to_index == true) {
    redirect_uri(base_url() . implode("/", $routes) . "/");
    exit;
}

if($is_static_file == true) {
    if(file_exists($appfile)) {
        set_header_content_type($end_era);
        header("Cache-Control: max-age=86400");

        $fp = fopen($appfile, "r") or die("404 Not Found");
        while(!feof($fp)) {
            echo fread($fp, 8192);
        }
        fclose($fp);
    } else {
        set_error("404 Not Found");
        show_errors();
    }
}
