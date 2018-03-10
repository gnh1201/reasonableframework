<?php
/**
 * @file webpagetool.php
 * @date 2018-02-26
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief WebPageTool helper
 */

if(!function_exists("get_web_page")) {
	function get_web_page($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$options = array(
			CURLOPT_PROXY          => "",       // set proxy server
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER         => false,    // don't return headers
			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			CURLOPT_ENCODING       => "",       // handle compressed
			CURLOPT_USERAGENT      => "",       // name of client
			CURLOPT_AUTOREFERER    => true,     // set referrer on redirect
			CURLOPT_CONNECTTIMEOUT => $ct_out,  // time-out on connect
			CURLOPT_TIMEOUT        => $c_out,   // time-out on response
		);

		if(!empty($ua)) {
			$options[CURLOPT_USERAGENT] = $ua;
		} else {
			$options[CURLOPT_USERAGENT] = "2018 ReasonableFramework;https://github.com/gnh1201/reasonableframework";
		}
		
		if(!empty($proxy)) {
			$options[CURLOPT_PROXY] = $proxy;
		}

		if($method == "post" && count($data) > 0) {
			$options[CURLOPT_POST] = 1;
			$options[CURLOPT_POSTFIELDS] = $data;
		}

		if($method == "get" && count($data) > 0) {
			$pos = strpos($url, '?');
			if ($pos === false) {
				$url = $url . '?' . http_build_query($data);
			} else {
				$url = $url . '&' . http_build_query($data);
			}
		}

		$ch = curl_init($url);
		curl_setopt_array($ch, $options);

		$content = curl_exec($ch);
		curl_close($ch);

		$content_size = strlen($content);

		$response = array(
			"content" => $content,
			"size"    => $size
		);
		
		return $response;
	}
}

if(!function_exists("get_web_json")) {
	function get_web_json($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$doc = array();

		$response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);
		if($response['size'] > 0) {
			$doc = json_decode($response['content']);
		}

		return $doc;
	}
}

if(!function_exists("get_web_dom")) {
	function get_web_dom($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$html = new stdClass();
		$response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);

		// load simple_html_dom
		if($response['size'] > 0) {
			loadHelper("simple_html_dom");
			$html = function_exists("str_get_html") ? str_get_html($response['content']) : $html;
		}

		return $html;
	}
}
