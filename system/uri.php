<?php
/**
 * @file uri.php
 * @date 2018-04-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief URI module
 */

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

		$request_uri = "";
		if(!array_key_empty("REQUEST_URI", $_SERVER)) {
			$request_uri = $requests["_URI"];
		}

		return $request_uri;
	}
}

if(!function_exists("read_requests")) {
	function read_requests() {
		$requests = array(
			"_ALL"   => $_REQUEST,
			"_POST"  => $_POST,
			"_GET"   => $_GET,
			"_URI"   => !array_key_empty("REQUEST_URI", $_SERVER) ? $_SERVER["REQUEST_URI"] : '',
			"_FILES" => is_array($_FILES) ? $_FILES : array(),
		);

		// with security module
		if(function_exists("get_clean_xss")) {
			foreach($requests['_GET'] as $k=>$v) {
				if(is_string($v)) {
					$requests['_GET'][$k] = get_clean_xss($v);
				}
			}
		}

		// set alias
		$aliases = array(
			"all" => "_ALL",
			"post" => "_POST",
			"get" => "_GET",
			"uri" => "_URI",
			"files" => "_FILES"
		);
		foreach($aliases as $k=>$v) {
			$requests[$k] = $requests[$v];
		}

		return $requests;
	}
}

if(!function_exists("get_requests")) {
	function get_requests() {
		$requests = get_scope("requests");
		
		if(!is_array($requests)) {
			set_scope("requests", read_requests());
		}

		return get_scope("requests");
	}
}

if(!function_exists("get_final_link")) {
	function get_final_link($url, $data=array(), $entity=true) {
		$link = "";
		$url = urldecode($url);

		$params = array();
		$base_url = "";
		$query_str = "";

		$strings = explode("?", $url);
		$pos = (count($strings) > 1) ? strlen($strings[0]) : -1;	
		
		if($pos < 0) {
			$base_url = $url;
		} else {
			$base_url = substr($url, 0, $pos);
			$query_str = substr($url, ($pos + 1));
			parse_str($query_str, $params);
		}

		foreach($data as $k=>$v) {
			$params[$k] = $v;
		}
		
		if(count($params) > 0) {
			$link = $base_url . "?" . http_build_query($params);
		} else {
			$link = $base_url;
		}

		if($entity == true) {
			$link = str_replace("&", "&amp;", $link);
		}

		return $link;
	}
}

if(!function_exists("get_route_link")) {
	function get_route_link($route, $data=array(), $entity=true, $base_url="") {
		$data['route'] = $route;

		if(empty($base_url)) {
			$base_url = base_url();
		}

		return get_final_link($base_url, $data, $entity);
	}
}

if(!function_exists("redirect_uri")) {
	function redirect_uri($uri, $permanent=false) {
		header("Location: " . $uri, true, $permanent ? 301 : 302);
		exit();
	}
}

if(!function_exists("redirect_with_params")) {
	function redirect_with_params($uri, $data=array(), $permanent=false, $entity=false) {
		redirect_uri(get_final_link($uri, $data, $entity), $permanent);
	}
}

if(!function_exists("redirect_route")) {
	function redirect_route($route, $data=array()) {
		redirect_uri(get_route_link($route, $data, false));
	}
}

if(!function_exists("get_requested_value")) {
	function get_requested_value($name, $method="_ALL", $escape_quotes=true, $escape_tags=false) {
		$value = "";
		$requests = get_requests();

		// set validated value
		if(array_key_exists($method, $requests)) {
			$value = array_key_empty($name, $requests[$method]) ? $value : $requests[$method][$name];

			if(is_string($value)) {
				// security: set escape quotes
				if($escape_quotes == true) {
					$value = addslashes($value);
				}

				// security: set escape tags
				if($escape_tags == true) {
					$value = htmlspecialchars($value);
				}
			}
		}

		return $value;
	}
}

if(!function_exists("get_requested_values")) {
	function get_requested_values($names, $method="_ALL", $escape_quotes=true, $escape_tags=false) {
		$values = array();

		if(is_array($names)) {
			foreach($names as $name) {
				$values[$name] = get_requested_value($name);
			}
		}
		
		return $values;
	}
}

if(!function_exists("get_binded_requests")) {
	function get_binded_requests($rules, $method="_ALL") {
		$data = array();
		
		foreach($rules as $k=>$v) {
			if(!empty($v)) {
				$data[$v] = get_requested_value($k);
			}
		}

		return $data;
	}
}

if(!function_exists("get_array")) {
	function get_array($arr) {
		return is_array($arr) ? $arr : array();
	}
}

// set scope
set_scope("requests", read_requests());
