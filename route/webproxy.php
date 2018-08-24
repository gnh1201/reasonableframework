<?php
if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

loadHelper("webpagetool");

$url = get_requested_value("url");

if(!empty($url)) {
	$response = get_web_page($url, "get.cache");
	echo $response['content'];
}
