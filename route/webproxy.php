<?php
loadHelper("webpagetool");

$url = get_requested_value("url");

if(!empty($url)) {
	$response = get_web_page($url, "get.cache");
	echo $response['content'];
}
