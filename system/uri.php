<?php
/**
 * @file uri.php
 * @date 2018-04-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief URI module
 */

if(!check_function_exists("base_url")) {
    function base_url() {
        return get_config_value("base_url");
    }
}

if(!check_function_exists("base_api_url")) {
    function base_api_url() {
        return get_config_value("base_api_url");
    }
}

if(!check_function_exists("get_uri")) {
    function get_uri() {
        $requests = get_requests();

        $request_uri = "";
        if(!array_key_empty("REQUEST_URI", $_SERVER)) {
            $request_uri = $requests["_URI"];
        }

        return $request_uri;
    }
}

if(!check_function_exists("read_route")) {
    function read_route($route=false) {
        $route = false;

        $config = get_config();
        $requests = get_requests();

        // get requested route
        $route = get_requested_value("route");

        // if empty route
        if(empty($route)) {
            $route = get_value_in_array("default_route", $config, "welcome");
        }

        return $route;
    }
}

if(!check_function_exists("read_requests")) {
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
            "_SERVER" => array_map("make_safe_argument", get_array($_SERVER)),
        );

        // check if json or serialized request
        foreach(getallheaders() as $name=>$value) {
            if($name == "Content-Type") {
                if($value == "application/json") {
                    $options['json'] = true;
                } elseif($value == "application/vnd.php.serialized") {
                    $options['serialized'] = true;
                }
                break;
            }
        }

        // check if `JSONData` request (referenced from NHBank API competition)
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

        // with security module
        $protect_methods = array("_ALL", "_GET", "_POST", "_JSON", "_SEAL", "_MIXED");
        if(check_function_exists("get_clean_xss")) {
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
        );
        foreach($aliases as $k=>$v) {
            $requests[$k] = $requests[$v];
        }

        return $requests;
    }
}

if(!check_function_exists("get_requests")) {
    function get_requests() {
        $requests = get_scope("requests");

        if(!is_array($requests)) {
            set_scope("requests", read_requests());
        }

        return get_scope("requests");
    }
}

if(!check_function_exists("get_final_link")) {
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

if(!check_function_exists("get_route_link")) {
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

// URI: Uniform Resource Identifier
// URL: Uniform Resource Locator
if(!check_function_exists("redirect_uri")) {
    function redirect_uri($uri, $permanent=false, $options=array()) {
        if(array_key_equals("check_origin", $options, true)) {
            if(!check_redirect_origin($uri)) {
                set_error("Invalid redirect URL");
                show_errors();
            }
        }

        header("Location: " . $uri, true, $permanent ? 301 : 302);
        exit();
    }
}

if(!check_function_exists("redirect_with_params")) {
    function redirect_with_params($uri, $data=array(), $permanent=false, $entity=false) {
        redirect_uri(get_final_link($uri, $data, $entity), $permanent);
    }
}

if(!check_function_exists("redirect_route")) {
    function redirect_route($route, $data=array()) {
        redirect_uri(get_route_link($route, $data, false));
    }
}

if(!check_function_exists("get_requested_value")) {
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

if(!check_function_exists("get_requested_values")) {
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

if(!check_function_exists("empty_requested_value")) {
    function empty_requested_value($name, $method="_ALL") {
        $value = get_requested_value($name, $method);
        return empty($value);
    }
}

if(!check_function_exists("get_binded_requests")) {
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

if(!check_function_exists("get_array")) {
    function get_array($arr) {
        return is_array($arr) ? $arr : array();
    }
}

if(!check_function_exists("check_is_string_not_array")) {
    function check_is_string_not_array($str) {
        return (is_string($str) && !(is_array($str) || $str == "Array"));
    }
}

if(!check_function_exists("set_header_content_type")) {
    function set_header_content_type($type) {
        $type = strtolower($type);
        $rules = array(
            "json" => "application/json",
            "xml" => "text/xml",
            "text" => "text/plain",
            "html" => "text/html",
            "xhtml" => "application/xhtml+xml"
        );

        if(array_key_exists($type, $rules)) {
            header(sprintf("Content-type: %s", $rules[$type]));
        } else {
            header("Content-type: plain/text");
        }
    }
}

if(!check_function_exists("get_requested_jsondata")) {
    function get_requested_jsondata($name, $escape_quotes=true, $escape_tags=false) {
        return get_requested_jsondata($name, "_JSON", $escape_quotes, $escape_tags);
    }
}

// set scope
set_scope("requests", read_requests());

//EOF
