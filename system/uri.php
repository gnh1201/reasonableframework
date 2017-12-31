<?php
$requests = array(
	"_ALL"  => $_REQUEST,
	"_POST" => $_POST,
	"_GET"  => $_GET,
	"_URI"  => !array_key_empty("REQUEST_URI", $_SERVER) ? $_SERVER["REQUEST_URI"] : ''
);

if(!function_exists("base_url")) {
	function base_url() {
		global $config;

		$base_url = '';
		if(!array_key_empty("base_url", $config)) {
			$base_url = $config["base_url"];
		}

		return $base_url;
	}
}

if(!function_exists("get_uri")) {
	function get_uri() {
		global $requests;
		
		$request_uri = '';
		if(!array_key_empty("REQUEST_URI", $_SERVER)) {
			$request_uri = $requests["_URI"];
		}

		return $request_uri;
	}
}
