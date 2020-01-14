<?php
// @date 2019-10-13
// @author Go Namhyeon <gnh1201@gmail.com>

if(!check_function_exists("json_decode_ex")) {
    function json_decode_ex($data, $options=array()) {
        $result = false;
        
        $is_assoc = array_key_equals("assoc", $options, true);

        $invalid_fn = array(
            "NO_FUNCTION_JSON_DECODE" => "json_decode",
            "NO_FUNCTION_JSON_LAST_ERROR" => "json_last_error",
        );
        $error = check_invalid_function($invalid_fn);

        if($error < 0) {
            if($is_assoc) {
                $result = json_decode($data, true);
            } else {
                $result = json_decode($data);
            }
        }

        return $result;
    }
}

if(!check_function_exists("json_encode_ex")) {
    function json_encode_ex($data, $options=array()) {
        $result = false;
        
        $is_adaptive = array_key_equals("adaptive", $options, true);

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
            $result = json_encode($data);
        }

        return $result;
    }
}
