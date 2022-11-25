<?php
// @created_on 2019-10-13
// @updated_on 2020-01-27
// @author Go Namhyeon <abuse@catswords.net>

if(!is_fn("json_decode_ex")) {
    function json_decode_ex($data, $options=array()) {
        $result = false;

        $is_assoc = array_key_equals("assoc", $options, true);

        $invalid_fn = array(
            "NO_FUNCTION_JSON_DECODE" => "json_decode",
            "NO_FUNCTION_JSON_LAST_ERROR" => "json_last_error",
        );
        $error = check_invalid_function($invalid_fn);
        if($error == JSON_ERROR_NONE) {
            if($is_assoc) {
                $result = json_decode($data, true);
            } else {
                $result = json_decode($data);
            }
        } else {
            $result = new stdClass();
            $result->error = $error;
        }

        return $result;
    }
}

if(!is_fn("json_encode_ex")) {
    function json_encode_ex($data, $options=array()) {
        $result = false;
        
        $is_adaptive = array_key_equals("adaptive", $options, true);
        $is_pretty = array_key_equals("pretty", $options, true);
        
        $invalid_fn = array(
            "NO_FUNCTION_JSON_ENCODE" => "json_decode",
            "NO_FUNCTION_JSON_LAST_ERROR" => "json_last_error",
        );
        $error = check_invalid_function($invalid_fn);
        if($error == JSON_ERROR_NONE) {
            if($is_adaptive) {
                // 2018-06-01: Adaptive JSON is always quotes without escape non-ascii characters
                $lines = array();
                foreach($data as $k=>$v) {
                    if(is_array($v)) {
                        $lines[] = sprintf("\"%s\":%s", make_safe_argument($k), get_adaptive_json($v));
                    } else {
                        $lines[] = sprintf("\"%s\":\"%s\"", make_safe_argument($k), make_safe_argument($v));
                    }
                }
                $result = "{" . implode(",", $lines) . "}";
            } else {
                if($is_pretty) {
                    $result = json_encode($data, JSON_PRETTY_PRINT);
                } else {
                    $result = json_encode($data);
                }
            }
        } else {
            $result = sprintf("{\"error\": \"%s\"}", $error);
        }

        return $result;
    }
}
