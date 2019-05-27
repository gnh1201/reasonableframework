<?php
/**
 * @file logger.php
 * @date 2018-05-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Logger module for ReasonableFramework
 */
 
if(!check_function_exists("write_visit_log")) {
    function write_visit_log() {
        $fw = false;
        $data = "";
        
        if(loadHelper("networktool")) {
            $event = get_network_event();

            if(loadHelper("catsplit.format")) {
                $data = catsplit_encode($event);
            } else {
                $data = json_encode($event);
            }

            $fw = append_storage_file($data, array(
                "storage_type" => "logs",
                "filename" => "network.log",
                "chmod" => 0644,
                "nl" => "<",
            ));
        }

        return $fw;
    }
}

if(!check_function_exists("write_common_log")) {
    function write_common_log($msg, $type="None", $networks="") {
        $fw = false;

        $data = implode("\t", array(get_current_datetime(), $type, $msg));
        $fw = append_storage_file($data, array(
            "storage_type" => "logs",
            "filename" => "common.log",
            "chmod" => 0644,
            "nl" => "<",
        ));

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
