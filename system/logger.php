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
        
        $event = get_network_event();
        if(loadHelper("networktool")) {
            if(loadHelper("rsf.format")) {
                $data = DOC_EOL . get_rsf_encoded($event);
            } else {
                $data = DOC_EOL . json_encode(get_network_event());
            }

            $fw = append_storage_file($data, array(
                "storage_type" => "logs",
                "filename" => "network.log",
                "chmod" => 0644,
            ));
        }

        return $fw;
    }
}
    
if(!check_function_exists("write_common_log")) {
    function write_common_log($msg) {
        $msg = DOC_EOL . $msg;
        return append_storage_file($msg, array(
            "storage_type" => "logs",
            "filename" => "common.log",
            "chmod" => 0644,
        ));
    }
}
