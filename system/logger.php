<?php
/**
 * @file logger.php
 * @date 2018-05-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Logger module for ReasonableFramework
 */

if(!check_function_exists("write_log_to_file")) {
    function write_log_to_file($data, $filename) {
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

            $fw = write_log_to_file($data, "network.log");
        }

        return $fw;
    }
}

if(!check_function_exists("write_common_log")) {
    function write_common_log($msg, $type="None", $networks="") {
        $fw = false;

        $data = implode("\t", array(get_current_datetime(), $type, $msg));
        $fw = write_log_to_file($data, "common.log");

        // send to networks
        $_networks = explode(",", $networks);
        if(loadHelper("webhooktool")) {
            foreach($_networks as $n) {
                @send_web_hook($data, $n);
            }
        }

        return $fw;
    }
}

if(!check_function_exists("write_debug_log")) {
    function write_debug_log($msg, $type="None") {
        if(APP_DEVELOPMENT !== false) {
            $data = implode("\t", array(get_current_datetime(), $type, $msg));
            return write_log_to_file($data, "debug.log");
        }
    }
}
