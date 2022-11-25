<?php
/**
 * @file networktool.php
 * @created_on 2018-04-11
 * @updated_on 2022-09-16
 * @author Go Namhyeon <abuse@catswords.net>
 * @brief Network tool helper
 */

if(!is_fn("get_network_event")) {
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

if(!is_fn("get_network_client_addr")) {
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

if(!is_fn("get_network_server_addr")) {
    function get_network_server_addr() {
        $addr = '';
        if(isset($_SERVER['SERVER_ADDR']) && isset($_SERVER['SERVER_PORT'])) {
            $addr = $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'];
        } else if(isset($_SERVER['SERVER_ADDR'])) {
            $addr = $_SERVER['SERVER_ADDR'];
        } else if(isset($_SERVER['LOCAL_ADDR'])) {
            $addr = $_SERVER['LOCAL_ADDR'];
        } else if(is_fn(array("gethostname", "gethostbyname"))) {
            $host = gethostname();
            $addr = gethostbyname($host);
        } else {
            $addr = 'UNKNOWN';
        }
        return $addr;
    }
}

if(!is_fn("get_network_hostname")) {
    function get_network_hostname() {
        $host = '';
        if(isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else if(isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'];
        } else if(!is_fn('gethostname')) {
            $host = gethostname();
        } else {
            $host = 'UNKNOWN';
        }

        return $host;
    }
}

if(!is_fn("check_secure_protocol")) {
    function check_secure_protocol() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }
}

if(!is_fn("get_os_platform")) {
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

if(!is_fn("get_network_outbound_addr")) {
    function get_network_outbound_addr($protocol="") {
        $addr = false;
        $config = get_config();

        // via icanhazip.com
        if(loadHelper("webpagetool")) {
            $remote_host = "http://" . ($protocol == "ipv6" ? "ipv6." : "") . "icanhazip.com";
            $response = get_web_page($remote_host, "get.cache");
            $addr = get_value_in_array("content", $response, $addr);
        }

        // via opendns.com
        if(!$addr && loadHelper("exectool")) {
            $cmd = "dig +short myip.opendns.com @resolver1.opendns.com";
            $addr = exec_command($cmd, "shell_exec", array(
                "cache" => true
            ));
        }

        return $addr;
    }
}

if(!is_fn("detect_perf_agent")) {
    function detect_perf_agent() {
        $evt = get_network_event();
        $agent = $evt['agent'];
        return preg_match('(PingdomPageSpeed|GTmetrix)', $agent) === 1;
    }
}
