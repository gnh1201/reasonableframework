<?php
/**
 * @file logger.php
 * @date 2018-05-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Logger module for ReasonableFramework
 */

if(!check_function_exists("append_log_to_file")) {
    function append_log_to_file($data, $filename) {
        return append_storage_file($data, array(
            "storage_type" => "logs",
            "filename" => $filename,
            "chmod" => 0644,
            "nl" => "<",
        ));
    }
}

if(!check_function_exists("write_visit_log")) {
    function write_visit_log() {
        $fw = false;

        $data = "";
        if(loadHelper("networktool")) {
            $nevt = get_network_event();
            if(loadHelper("catsplit.format")) {
                $data = catsplit_encode($nevt);
            } else {
                $data = json_encode($nevt);
            }

            $fw = append_log_to_file($data, "network.log");
        }

        return $fw;
    }
}

if(!check_function_exists("write_common_log")) {
    function write_common_log($message, $component="None", $program="") {
        $fw = false;

        $data = implode("\t", array(get_current_datetime(), $component, $message));
        $fw = append_log_to_file($data, "common.log");

        // if enabled RFC3164 remote debugging
        if(loadHelper("rfc3164.proto")) {
            rfc3164_send_message($message, $component, $program);
        }

        return $fw;
    }
}

if(!check_function_exists("write_debug_log")) {
    function write_debug_log($message, $component="Debug", $program="") {
        $fw = false;

        // if not debug mode
        if(APP_DEVELOPMENT === false) return $fw;
        
        // if debug mode
        $data = implode("\t", array(get_current_datetime(), $type, $message));
        $fw = append_log_to_file($data, "debug.log");

        // if enabled RFC3164 remote debugging
        if(loadHelper("rfc3164.proto")) {
            rfc3164_send_message($message, $component, $program);
        }

        return $fw;
    }
}
