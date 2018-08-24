<?php
if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

loadHelper("webpagetool");

$path = str_replace("../", "", get_requested_value("path"));
$server_path = "./" . $path;
$client_path = base_url() . $path;

if(file_exists($server_path)) {
	$response = get_web_page($client_path, "get");
	echo $response['content'];
}
