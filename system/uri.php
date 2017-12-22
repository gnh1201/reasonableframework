<?php
if(!function_exists("base_url")) {
	function base_url() {
		global $config;

		$base_url = '';
		if(array_key_exists("base_url", $config)) {
			$base_url = $config["base_url"];
		}

		return $base_url;
	}
}

if(!function_exists("get_uri")) {
	function get_uri() {
		$request_uri = '';
		if(array_key_exists("REQUEST_URI", $_SERVER)) {
			$request_uri = $_SERVER["REQUEST_URI"];
		}

		return $request_uri;
	}
}
