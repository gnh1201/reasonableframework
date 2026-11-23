<?php
if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

loadHelper("webpagetool");

$url = get_requested_value("url");
if(!is_string($url) || !is_webproxy_url_allowed($url)) {
    header("HTTP/1.0 403 Forbidden");
    write_common_log("rejected", "webproxy");
    return;
}

$response = get_webproxy_page($url);
if(!is_array($response) || !isset($response["content"]) || !is_string($response["content"])) {
    header("HTTP/1.0 502 Bad Gateway");
    write_common_log($url, "webproxy");
    return;
}

$type_url = $url;
if(isset($response["url"]) && is_string($response["url"])) {
    $type_url = $response["url"];
}
header("Content-Type: " . webproxy_content_type($type_url));
header("X-Content-Type-Options: nosniff");
echo $response["content"];
write_common_log($url, "webproxy");
