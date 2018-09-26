<?php
/**
 * @file networktool.php
 * @date 2018-04-11
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Network tool helper
 */

if(!function_exists("get_network_event")) {
    function get_network_event() {
        $config = get_config();

        return array(
            "datetime"   => get_current_datetime(),
            "server"     => get_network_server_addr(),
            "hostname"   => get_network_hostname(),
            "client"     => get_network_client_addr(),
            "agent"      => getenv("HTTP_USER_AGENT"),
            "referrer"   => getenv("HTTP_REFERER"),
            "query"      => getenv("QUERY_STRING"),
            "self"       => get_value_in_array("PHP_SELF", $_SERVER, ""),
            "method"     => get_value_in_array("REQUEST_METHOD", $_SERVER, ""),
        );
    }
}

if(!function_exists("get_network_client_addr")) {
    function get_network_client_addr() {
        $addr = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $addr = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $addr = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            $addr = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $addr = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $addr = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $addr = $_SERVER['REMOTE_ADDR'];
        else
            $addr = 'UNKNOWN';
        return $addr;
    }
}

if(!function_exists("get_network_server_addr")) {
    function get_network_server_addr() {
        $addr = '';
        if(isset($_SERVER['SERVER_ADDR']) && isset($_SERVER['SERVER_PORT'])) {
            $addr = $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'];
        } else if(isset($_SERVER['SERVER_ADDR'])) {
            $addr = $_SERVER['SERVER_ADDR'];
        } else if(isset($_SERVER['LOCAL_ADDR'])) {
            $addr = $_SERVER['LOCAL_ADDR'];
        } else if(function_exists('gethostname') && function_exists('gethostbyname')) {
            $host = gethostname();
            $addr = gethostbyname($host);
        } else {
            $addr = 'UNKNOWN';
        }
        return $addr;
    }
}

if(!function_exists("get_network_hostname")) {
    function get_network_hostname() {
        $host = '';
        if(isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else if(isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'];
        } else if(function_exists('gethostname')) {
            $host = gethostname();
        } else {
            $host = 'UNKNOWN';
        }

        return $host;
    }
}

if(!function_exists("check_secure_protocol")) {
	function check_secure_protocol() {
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
	}
}

if(!function_exists("get_os_platform")) {
	function get_os_platform() {
		$os = "";

		if(defined("PHP_OS")) {
			$os = PHP_OS;
		} else {
			$os = php_uname(s);
		}

		return $os;
	}
}

if(!function_exists("get_network_outbound_addr")) {
	function get_network_outbound_addr($protocol="") {
		$addr = false;

		if(loadHelper("webpagetool")) {
			$remote_host = "http://" . ($protocol == "ipv6" ? "ipv6." : "") . "icanhazip.com";
			$response = get_web_json($remote_host, "get.cache");
			$addr = get_value_in_array("content", $response, $addr);
		}

		return $addr;
	}
}
