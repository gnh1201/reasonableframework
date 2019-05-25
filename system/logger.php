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

        if(loadHelper("networktool")) {
            $data = DOC_EOL . json_encode(get_network_event());
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
    function write_common_log($message, $type="None", $forward_to = "") {
        $fw = false;
     
        $forwards = explode(",", $forward_to);
        $datetime = get_current_datetime();
        $data = implode("\t", $datetime, $type, $msg);
        $fw = append_storage_file($data, array(
            "storage_type" => "logs",
            "filename" => "common.log",
            "chmod" => 0644,
        ));

        // forwarding to messenger networks
        if(count($forwards) > 0 && loadHelper("webhooktool")) {
           foreach($forwards as $nw) {
               @send_web_hook($message, $nw);
           }
        }

        return $fw;
    }
}
