<?php
/**
 * @file webpagetool.php
 * @date 2018-06-01
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief WebPageTool helper
 */

/****** START EXAMPLES *****/
/* // REQUEST GET: $response = get_web_page($url, "get", $data); */
/* // REQUEST POST: $response = get_web_page($url, "post", $data); */
/* // REQUEST GET with CACHE: $response = get_web_page($url, "get.cache", $data); */
/* // REQUEST POST with CACHE: $response = get_web_page($url, "post.cache", $data); */
/* // REQUEST GET by CMD with CACHE: $response = get_web_page($url, "get.cmd.cache"); */
/* // REQUEST GET by SOCK with CACHE: $response = get_web_page($url, "get.sock.cache"); */
/* // REQUEST GET by FGC: $response = get_web_page($url, "get.fgc"); */
/* // REQUEST GET by WGET: $response = get_web_page($url, "get.wget"); */
/* // PRINT CONTENT: echo $response['content']; */
/****** END EXAMPLES *****/

if(!function_exists("get_web_fgc")) {
	function get_web_fgc($url) {
		return (ini_get("allow_url_fopen") ? file_get_contents($url) : false);
	}
}

if(!function_exists("get_web_build_qs")) {
	function get_web_build_qs($url="", $data) {
		$qs = "";
		if(empty($url)) {
			$qs = http_build_query($data);
		} else {
			$pos = strpos($url, '?');
			if ($pos === false) {
				$qs = $url . '?' . http_build_query($data);
			} else {
				$qs = $url . '&' . http_build_query($data);
			}
		}
		return $qs;
	}
}

if(!function_exists("get_web_cmd")) {
	function get_web_cmd($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$output = "";
		$cmd_fin = "";
		$cmd = "";

		if($method == "get") {
			$cmd = "curl -A '%s' -k '%s'";
			$cmd_fin = sprintf($cmd, make_safe_argument($ua), make_safe_argument(get_web_build_qs($url, $data)));
			$output = shell_exec($cmd_fin);
		}

		if($method == "post") {
			$cmd = "curl -X POST -A '%s' -k '%s' %s";
			$params_cmd = "";
			foreach($data as $k=>$v) {
				if(substr($v, 0, 1) == "@") { // if file
					$params_cmd .= sprintf("-F '%s=%s' ", make_safe_argument($k), make_safe_argument($v));
				} else {
					$params_cmd .= sprintf("-d '%s=%s' ", make_safe_argument($k), make_safe_argument($v));
				}
			}
			$cmd_fin = sprintf($cmd, make_safe_argument($ua), make_safe_argument($url), $params_cmd);
			$output = shell_exec($cmd_fin);
		}

		return $output;
	}
}

// http://dev.epiloum.net/109
if(!function_exists("get_web_sock")) {
	function get_web_sock($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$output     = "";

		$info       = parse_url($url);
		$req        = '';
		$line       = '';
		$agent      = $ua;
		$linebreak  = "\r\n";
		$headPassed = false;
		
		if(!array_key_empty("scheme", $info)) {		
			switch($info['scheme'] = strtolower($info['scheme'])) {
				case "http":
					$info['port'] = 80;
					break;
				case "https":
					$info['ssl'] = "ssl://";
					$info['port'] = 443;
					break;
				default:
					set_error("ambiguous protocol, HTTP or HTTPS");
					show_errors();
					return false;
			}
		} else {
			set_error("ambiguous protocol, HTTP or HTTPS");
			show_errors();
			return false;
		}

		// Setting Path
		if(array_key_empty("path", $info)) {
			$info['path'] = "/";
		}

		// Setting Request Header
		switch($method) {
			case 'get':
				if(array_key_empty("query", $info)) {
					$info['path'] .= '?' . $info['query'];
				}

				$req .= 'GET ' . $info['path'] . ' HTTP/1.1' . $linebreak;
				$req .= 'Host: ' . $info['host'] . $linebreak;
				$req .= 'User-Agent: ' . $agent . $linebreak;
				$req .= 'Referer: ' . $url . $linebreak;
				$req .= 'Connection: Close' . $linebreak . $linebreak;
				break;

			case 'post':
				$req .= 'POST ' . $info['path'] . ' HTTP/1.1' . $linebreak;
				$req .= 'Host: ' . $info['host'] . $linebreak;
				$req .= 'User-Agent: ' . $agent . $linebreak; 
				$req .= 'Referer: ' . $url . $linebreak;
				$req .= 'Content-Type: application/x-www-form-urlencoded'.$linebreak; 
				$req .= 'Content-Length: '. strlen($info['query']) . $linebreak;
				$req .= 'Connection: Close' . $linebreak . $linebreak;
				$req .= $info['query']; 
				break;
		}

		// Socket Open
		$fsock = @fsockopen($info['ssl'] . $info['host'], $info['port']);
		if ($fsock)
		{
			fwrite($fsock, $req);
			while(!feof($fsock))
			{
				$line = fgets($fsock, 128);
				if($line == "\r\n" && !$headPassed)
				{
					$headPassed = true;
					continue;
				}
				if($headPassed)
				{
					$output .= $line;
				}
			}
			fclose($fsock);
		}

		return $output;
	}
}

if(!function_exists("get_web_wget")) {
	function get_web_wget($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$content = "";
		
		$filename = make_random_id(32);
		$filepath = write_storage_file("", array(
			"filename" => $filename,
			"mode" => "fake"
		));

		$cmd = sprintf("wget '%s' -O %s", $url, $filepath);
		shell_exec($cmd);

		$content = read_storage_file($filename);

		return $content;
	}
}

if(!function_exists("get_web_page")) {
	function get_web_page($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$status = "-1";
		$resno = "-1";
		$errno = "-1";
		$req_method = $method;

		$method = strtolower($method);
		$res_methods = explode(".", $method);

		if(in_array("cache", $res_methods)) {
			$content = get_web_cache($url, $method, $data, $proxy, $ua, $ct_out, $t_out);
		} elseif(in_array("cmd", $res_methods)) {
			$content = get_web_cmd($url, $res_methods[0], $data, $proxy, $ua, $ct_out, $t_out);
		} elseif(in_array("fgc", $res_methods)) {
			$content = get_web_fgc($url);
		} elseif(in_array("sock", $res_methods)) {
			$content = get_web_sock($url, $res_methods[0], $data, $proxy, $ua, $ct_out, $t_out);
		} elseif(in_array("wget", $res_methods)) {
			$content = get_web_wget($url, $res_methods[0], $data, $proxy, $ua, $ct_out, $t_out);
		} else {
			if(!in_array("curl", get_loaded_extensions())) {
				$error_msg = "cURL extension needs to be installed.";
				set_error($error_msg);
				return $error_msg;
			}

			$options = array(
				CURLOPT_URL            => $url,     // set remote url
				CURLOPT_PROXY          => $proxy,   // set proxy server
				CURLOPT_RETURNTRANSFER => true,     // return web page
				CURLOPT_HEADER         => false,    // don't return headers
				CURLOPT_FOLLOWLOCATION => true,     // follow redirects
				CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
				CURLOPT_ENCODING       => "",       // handle compressed
				CURLOPT_USERAGENT      => $ua,      // name of client
				CURLOPT_AUTOREFERER    => true,     // set referrer on redirect
				CURLOPT_CONNECTTIMEOUT => $ct_out,  // time-out on connect
				CURLOPT_TIMEOUT        => $t_out,   // time-out on response
				CURLOPT_FAILONERROR    => true,     // get error code
				CURLOPT_SSL_VERIFYHOST => false,    // ignore ssl host verification
				CURLOPT_SSL_VERIFYPEER => false,    // ignore ssl peer verification
			);

			if(empty($options[CURLOPT_USERAGENT])) {
				$ua = "2018 ReasonableFramework: https://github.com/gnh1201/reasonableframework";
				$options[CURLOPT_USERAGENT] = $ua;
			}

			if($method == "post" && count($data) > 0) {
				$options[CURLOPT_POST] = 1;
				$options[CURLOPT_POSTFIELDS] = get_web_build_qs("", $data);
			}

			if($method == "get" && count($data) > 0) {
				$options[CURLOPT_URL] = get_web_build_qs($url, $data);
			}

			$ch = curl_init();
			curl_setopt_array($ch, $options);

			$content = curl_exec($ch);

			if(!is_string($content)) {
				$res = get_web_page($url, $method . ".cmd", $data, $proxy, $ua, $ct_out, $t_out);
				$content = $res['content'];
				$req_method = $res['method'];
			} else {
				$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$resno = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
				$errno = curl_errno($ch);
			}

			curl_close($ch);
		}

		$content_size = strlen($content);

		$gz_content = gzdeflate($content);
		$gz_content_size = strlen($gz_content);
		$gz_ratio = ($content_size > 0) ? (floatval($gz_content_size) / floatval($content_size)) : 1.0;

		$response = array(
			"content"    => $content,
			"size"       => $content_size,
			"status"     => $status,
			"resno"      => $resno,
			"errno"      => $errno,
			"id"         => get_web_identifier($url, $method, $data),
			"md5"        => get_hashed_text($content, "md5"),
			"sha1"       => get_hashed_text($content, "sha1"),
			"gz_content" => $gz_content,
			"gz_size"    => $gz_content_size,
			"gz_md5"     => get_hashed_text($gz_content, "md5"),
			"gz_sha1"    => get_hashed_text($gz_content, "sha1"),
			"gz_ratio"   => $gz_ratio,
			"method"     => $req_method,
			"params"     => $data,
		);

		return $response;
	}
}

if(!function_exists("get_web_identifier")) {
	function get_web_identifier($url, $method="get", $data=array()) {
		$hash_data = (count($data) > 0) ? get_hashed_text(serialize($data)) : "*";
		return get_hashed_text(sprintf("%s.%s.%s", get_hashed_text($method), get_hashed_text($url), $hash_data));
	}
}

if(!function_exists("get_web_cache")) {
	function get_web_cache($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$content = false;

		$identifier = get_web_identifier($url, $method, $data);
		$gz_content = read_storage_file($identifier, array(
			"storage_type" => "cache"
		));

		if($gz_content === false) {
			$no_cache_method = str_replace(".cache", "", $method);
			$response = get_web_page($url, $no_cache_method, $data, $proxy, $ua, $ct_out, $t_out);
			$content = $response['content'];
			$gz_content = gzdeflate($content);

			// save web page cache
			write_storage_file($gz_content, array(
				"storage_type" => "cache",
				"filename" => $identifier
			));
		} else {
			$content = gzinflate($gz_content);
		}

		return $content;
	}
}

if(!function_exists("get_web_json")) {
	function get_web_json($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$result = false;

		$response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);
		if($response['size'] > 0) {
			$result = json_decode($response['content']);
		}

		return $result;
	}
}

if(!function_exists("get_web_dom")) {
	function get_web_dom($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$result = new stdClass();
		$response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);

		// load simple_html_dom
		if($response['size'] > 0) {
			loadHelper("simple_html_dom");
			$result = function_exists("str_get_html") ? str_get_html($response['content']) : $result;
		}

		return $result;
	}
}

if(!function_exists("get_web_meta")) {
	function get_web_meta($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$result = array();
		$response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);

		// load PHP-Metaparser
		if($response['size'] > 0) {
			loadHelper("metaparser.lnk");
			$parser = new MetaParser($response['content'], $url);
			$result = $parser->getDetails();
		}

		return $result;
	}
}

if(!function_exists("get_web_xml")) {
	function get_web_xml($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
		$result = new stdClass();

		if(function_exists("simplexml_load_string")) {
			$response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);

			if($response['size'] > 0) {
				$result = simplexml_load_string($response['content'], null, LIBXML_NOCDATA);
			}
		}

		return $result;
	}
}

// 2016-06-01: Adaptive JSON is always quotes without escape non-ascii characters
if(!function_exists("get_adaptive_json")) {
	function get_adaptive_json($data) {
		$result = "";
		$lines = array();
		foreach($data as $k=>$v) {
			if(is_array($v)) {
				$lines[] = sprintf("\"%s\":%s", addslashes($k), get_adaptive_json($v));
			} else {
				$lines[] = sprintf("\"%s\":\"%s\"", addslashes($k), addslashes($v));
			}
		}
		$result = "{" . implode(",", $lines) . "}";

		return $result;
	}
}
