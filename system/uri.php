<?php
if(!function_exists("base_url")) {
	function base_url() {
		return get_config_value("base_url");
	}
}

if(!function_exists("base_api_url")) {
	function base_api_url() {
		return get_config_value("base_api_url");
	}
}

if(!function_exists("get_uri")) {
	function get_uri() {
		$requests = get_requests();

		$request_uri = '';
		if(!array_key_empty("REQUEST_URI", $_SERVER)) {
			$request_uri = $requests["_URI"];
		}

		return $request_uri;
	}
}

if(!function_exists("read_requests")) {
	function read_requests() {
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

if(!function_exists("get_requests")) {
	function get_requests() {
		global $requests;
		$requests = is_array($requests) ? $requests : read_requests();
		return $requests;
	}
}

if(!function_exists("redirect_uri")) {
	function redirect_uri($uri, $permanent=false) {
		header('Location: ' . $uri, true, $permanent ? 301 : 302);
		exit();
	}
}

if(!function_exists("get_requested_value")) {
	function get_requested_value($name, $scope="all", $escape_quotes=true, $escape_tags=false) {
		$requests = get_requests();

		$value = "";
		$method = "";

		switch($scope) {
			case "all":
				$method = "_ALL";
				break;
			case "post":
				$method = "_POST";
				break;
			case "get":
				$method = "_GET";
				break;
			default:
				$method = "";
		}

		// set validated value
		if(!empty($method)) {
			$value = array_key_empty($name, $requests[$method]) ? $value : $requests[$method][$name];

			// security: set escape quotes
			if($escape_quotes == true) {
				$value = addslashes($value);
			}

			// security: set escape tags
			if($escape_tags == true) {
				$value = htmlspecialchars($value);
			}
		}

		return $value;
	}
}

$requests = read_requests();
