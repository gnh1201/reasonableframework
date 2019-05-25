<?php
if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

loadHelper("webpagetool");

$method = get_requested_value("method");
$mime = get_requested_value("mime"); // example: text/html, image/jpeg
$url = get_requested_value("url");

$res_method = "get.cache";
$res_methods = explode(".", $method);
if(in_array("nocache", $res_methods)) {
    $res_method = "get";
}

if(!empty($url)) {
    $response = get_web_page($url, $res_method);
    if(!empty($mime)) {
        header(sprintf("Content-Type: %s", $mime));
    }
    print_r($response['content']);
}

write_common_log($url, "webproxy");
