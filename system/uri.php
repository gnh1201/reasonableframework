<?php
/**
 * @file uri.php
 * @created_on 2018-04-13
 * @updated_on 2024-04-29
 * @author Go Namhyeon <abuse@catswords.net>
 * @brief URI module
 */

if(!is_fn("base_url")) {
    function base_url() {
        $base_url = "";

        // #133 Add support 'X-Forwarded-Host' header 
        $forwarded_host = get_header_value("X-Forwarded-Host");
        if(!empty($forwarded_host)) {
            $base_url = sprintf("https://%s", $forwarded_host);
        } else {
            $base_url = get_config_value("base_url");
            if(empty($base_url)) {
                $base_url = sprintf("https://%s", $_SERVER['HTTP_HOST']);
            }
        }

        return $base_url;
    }
}

if(!is_fn("base_api_url")) {
    function base_api_url() {
        return get_config_value("base_api_url");
    }
}

if(!is_fn("get_uri")) {
    function get_uri() {
        $requests = get_requests();
        
        $uri = get_requested_value("_uri");
        if(empty($uri)) {
            $uri = $requests["_URI"];
        }

        return $uri;
    }
}

if(!is_fn("read_route")) {
    function read_route() {
        $route = false;

        $config = get_config();
        $requests = get_requests();

        // get base route
        $base_route = get_value_in_array("base_route", $config, "/");

        // get requested route
        $route = get_requested_value("route");

        // get route in URI
        if(empty($route)) {
            if(loadHelper("networktool")) {
                //$nevt = get_network_event();   // unused

                $uri = $requests['_URI'];
                if(strpos($uri, '?') !== false) {
                    $uri = substr($uri, 0, strpos($uri, '?'));
                }

                if(strpos($uri, $base_route) == 0) {
                    $_routes = explode("/", substr($uri, strlen($base_route)));
                    foreach($_routes as $_route) {
                        if($_route != "index.php") {
                            $route = $_route;
                            break;
                        }
                    }
                }
            }
        }

        // default route: welcome
        if(empty($route)) {
            $route = get_value_in_array("default_route", $config, "welcome");
        }

        return $route;
    }
}

if(!is_fn("read_requests")) {
    function read_requests($options=array()) {
        $config = get_config();

        // alternative to HTTPS
        $https = strtolower(get_value_in_array("https", $config, ""));
        if(strtoupper($https) == "JCRYPTION") {
            if(loadHelper("jCryption.lnk")) {
                jCryption_load();
                eval(jCryption_get(0));
            }
        }

        // process requests
        $requests = array(
            "_ALL"    => $_REQUEST,
            "_POST"   => $_POST,
            "_GET"    => $_GET,
            "_URI"    => get_value_in_array("REQUEST_URI", $_SERVER, false),
            "_FILES"  => get_array($_FILES),
            "_RAW"    => file_get_contents("php://input"),
            "_JSON"   => false,
            "_SEAL"   => false,
            "_YAML"   => false,
            "_CSPT"   => false,
            "_SERVER" => array_map("make_safe_argument", get_array($_SERVER)),
            "_HEADER" => getallheaders()
        );

        // check if json or serialized request
        foreach(getallheaders() as $name=>$value) {
            if($name == "Content-Type") {
                $values = explode(";", $value);
                if(in_array("application/json", $values)) {
                    $options['json'] = true;
                } elseif(in_array("application/vnd.php.serialized", $values)) {
                    $options['serialized'] = true;
                }
                break;
            }
        }

        // check if `JSONData` request (referenced from `2018 NHBank-KISA-TheLoop API competition`)
        $jsondata = false;
        if(!array_key_empty("JSONData", $requests['_ALL'])) {
            $options['json'] = true;
            $jsondata = get_value_in_array("JSONData", $requests['_ALL'], false);
        }

        // check if json request
        if(array_key_equals("json", $options, true)) {
            $jsondata = ($jsondata !== false) ? $jsondata : $requests['_RAW'];
            $requests['_JSON'] = json_decode($jsondata);
        }

        // check if seal(serialize) request
        if(array_key_equals("serialized", $options, true)) {
            $requests['_SEAL'] = unserialize($requests['_RAW']);
        }
        
        // check if yaml request
        if(array_key_equals("yaml", $options, true)) {
            if(is_fn("yaml_parse")) {
                $requests['_YAML'] = yaml_parse($requests['_RAW']);
            }
        }

        // check if cspt(catsplit) request
        if(array_key_equals("catsplit", $options, true)) {
            if(loadHelper("catsplit.format")) {
                $requests['_CSPT'] = catsplit_decode($requests['_RAW']);
            }
        }

        // with security module
        $protect_methods = array("_ALL", "_GET", "_POST", "_JSON", "_SEAL", "_MIXED");
        if(is_fn("get_clean_xss")) {
            foreach($protect_methods as $method) {
                $requested_data = get_array(get_value_in_array($method, $requests, false));
                foreach($requested_data as $k=>$v) {
                    $requests[$method][$k] = is_string($v) ? get_clean_xss($v) : $v;
                }
            }
        } else {
            set_error("Disabled XSS Protection", "Security");
            show_errors();
        }

        // set alias
        $aliases = array(
            "all" => "_ALL",
            "post" => "_POST",
            "get" => "_GET",
            "uri" => "_URI",
            "files" => "_FILES",
            "raw" => "_RAW",
            "json" => "_JSON",
            "seal" => "_SEAL",
            "cspt" => "_CSPT",
        );
        foreach($aliases as $k=>$v) {
            $requests[$k] = $requests[$v];
        }

        return $requests;
    }
}

if(!is_fn("get_requests")) {
    function get_requests() {
        $requests = get_shared_var("requests");

        if(!is_array($requests)) {
            set_shared_var("requests", read_requests());
        }

        return get_shared_var("requests");
    }
}

if(!is_fn("get_final_link")) {
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

if(!is_fn("get_route_link")) {
    function get_route_link($route, $data=array(), $entity=true, $base_url="") {
        $_data = array(
            "route" => $route
        );
        foreach($data as $k=>$v) {
            $_data[$k] = $v;
        }

        if(empty($base_url)) {
            $base_url = base_url();
        }

        return get_final_link($base_url, $_data, $entity);
    }
}

// only for static resources (html, css, jpg, png, gif, ...)
if(!is_fn("get_cdn_link")) {
    function get_cdn_link($uri) {
        $config = get_config();
        
        $base_url = get_value_in_array("base_url", $config, "");
        $base_cdn_url = get_value_in_array("base_cdn_url", $config, $base_url);

        return sprintf("%s%s", $base_cdn_url, $uri);
    }
}

// only for video resources (avi, mp4, mpeg, ...)
if(!is_fn("get_vod_link")) {
    function get_vod_link($uri) {
        $config = get_config();
        
        $base_url = get_value_in_array("base_url", $config, "");
        $base_vod_url = get_value_in_array("base_vod_url", $config, $base_url);

        return sprintf("%s%s", $base_vod_url, $uri);
    }
}

// URI: Uniform Resource Identifier
// URL: Uniform Resource Locator
if(!is_fn("redirect_uri")) {
    function redirect_uri($uri, $permanent=false, $options=array()) {
        if(array_key_equals("check_origin", $options, true)) {
            if(!check_redirect_origin($uri)) {
                set_error("Invalid redirect URL");
                show_errors();
            }
        }
        
        if(array_key_equals("method", $options, "html")) {
            echo <<<EOF
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="refresh" content="1;url=$uri">
        <title>Redirect</title>
    </head>
    <body>
        <a id="goto" href="$uri">Go to the page</a>
        <script>window.onload = function() { document.getElementById("goto").click(); };</script>
    </body>
</html>
EOF;
        } else {
            header("Location: " . $uri, true, $permanent ? 301 : 302);
        }

        exit();
    }
}

if(!is_fn("redirect_with_params")) {
    function redirect_with_params($uri, $data=array(), $permanent=false, $entity=false) {
        redirect_uri(get_final_link($uri, $data, $entity), $permanent);
    }
}

if(!is_fn("redirect_route")) {
    function redirect_route($route, $data=array()) {
        redirect_uri(get_route_link($route, $data, false));
    }
}

if(!is_fn("get_requested_value")) {
    function get_requested_value($name, $method="_ALL", $escape_quotes=true, $escape_tags=false) {
        $value = false;
        $requests = get_requests();

        $req_methods = array();
        if(is_array($method)) {
            $req_methods = array_merge($req_methods, $method);
        } else {
            $req_methods[] = $method;
        }
        $req_methods = array_reverse($req_methods);

        // set validated value
        foreach($req_methods as $method) {
            if(array_key_exists($method, $requests)) {
                if(is_array($requests[$method])) {
                    $value = get_value_in_array($name, $requests[$method], $value);
                } elseif(is_object($requests[$method])) {
                    $value = get_property_value($name, $requests[$method]);
                }
            }
        }

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

        return $value;
    }
}

if(!is_fn("get_requested_values")) {
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

if(!is_fn("empty_requested_value")) {
    function empty_requested_value($name, $method="_ALL") {
        $value = get_requested_value($name, $method);
        return empty($value);
    }
}

if(!is_fn("get_binded_requests")) {
    function get_binded_requests($rules, $method="_ALL", $equals_kv=false) {
        $data = array();

        foreach($rules as $k=>$v) {
            if(!$equals_kv) {
                $data[$v] = get_requested_value($k); // if dictionary
            } else {
                $data[$v] = get_requested_value($v); // if non-dictionary
            }
        }

        return $data;
    }
}

if(!is_fn("get_array")) {
    function get_array($arr) {
        return is_array($arr) ? $arr : array();
    }
}


if(!is_fn("get_int")) {
    function get_int($str) {
        return intval(preg_replace('/[^0-9]/', '', $str));
    }
}

if(!is_fn("check_is_string_not_array")) {
    function check_is_string_not_array($str) {
        return (is_string($str) && !(is_array($str) || $str == "Array"));
    }
}

if(!is_fn("set_header_content_type")) {
    function set_header_content_type($type) {
        $type = strtolower($type);
        $rules = array(
            "json" => "application/json",
            "xml" => "text/xml",
            "txt" => "text/plain",
            "yaml" => "application/x-yaml",
            "html" => "text/html",
            "xhtml" => "application/xhtml+xml",
            "cspt" => "application/catsplit",
        );

        if(array_key_exists($type, $rules)) {
            header(sprintf("Content-type: %s", $rules[$type]));
        } else {
            header("Content-type: text/plain");
        }
    }
}

if(!is_fn("get_header_value")) {
    function get_header_value($name) {
        $value = false;

        $requests = get_requests();
        foreach ($requests['_HEADER'] as $k=>$v) {
            if (strtolower($k) == strtolower($name)) {
                $value = $v;
            }
        }

        return $value;
    }
}

if(!is_fn("test_user_agent")) {
    function test_user_agent($ua, $tua) {
        $result = false;

        if (!empty($ua)) {
            $words = explode(" ", str_replace("/", " ", $ua));
            foreach($words as $word) {
                if (in_array($word, $tua)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }
}
