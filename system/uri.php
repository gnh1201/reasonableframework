<?php
if(!function_exists("base_url")) {
	function base_url() {
		$config = get_config();

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

if(!function_exists("get_requests")) {
	function get_requests() {
		$requests = array(
			"_ALL"  => $_REQUEST,
			"_POST" => $_POST,
			"_GET"  => $_GET,
			"_URI"  => !array_key_empty("REQUEST_URI", $_SERVER) ? $_SERVER["REQUEST_URI"] : ''
		);

		// with security module
		if(function_exists("get_clean_xss")) {
			foreach($requests['_GET'] as $k=>$v) {
				if(is_string($v)) {
					$requests['_GET'][$k] = get_clean_xss($v);
				}
			}
		}

		return $requests;
	}
}

if(!function_exists("redirect_uri")) {
	function redirect_uri($uri, $permanent=false) {
		header('Location: ' . $uri, true, $permanent ? 301 : 302);
		exit();
	}
}

$requests = get_requests();
