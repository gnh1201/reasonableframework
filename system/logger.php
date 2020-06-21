<?php
/**
 * @file logger.php
 * @created_on 2018-05-27
 * @updated_on 2020-06-21
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Logger module for ReasonableFramework
 */

if(!is_fn("append_log_to_file")) {
    function append_log_to_file($data, $filename) {
        $config = get_config();

        $rotate_size = get_value_in_array("log_rotate_size", $config, 0);
        $rotate_ratio = get_value_in_array("log_rotate_ratio", $config, 0.9);

        return append_storage_file($data, array(
            "storage_type" => "logs",
            "filename" => $filename,
            "chmod" => 0644,
            "nl" => "<",
            "rotate_size" => $rotate_size,
            "rotate_ratio" => $rotate_ratio,
        ));
    }
}

if(!is_fn("write_visit_log")) {
    function write_visit_log($mode="") {
        $fw = false;

        $nevt = false;
        if(loadHelper("networktool")) {
            $nevt = get_network_event();
        }

        if($nevt === false) return $fw;

        if($mode == "database") {
            $tablename = exec_db_table_create(array(
                "datetime" => array("datetime"),
                "server" => array("varchar", 255),
                "hostname" => array("varchar", 255),
                "client" => array("varchar", 255),
                "agent" => array("text"),
                "referrer" => array("text"),
                "self" => array("varchar", 255),
                "method" => array("varchar", 255)
            ), "rsf_visit_log", array(
                "setindex" => array(
                    "index_1" => array("datetime"),
                    "index_2" => array("client")
                )
            ));
            
            $bind = array(
                "datetime" => $nevt['datetime'],
                "server" => $nevt['server'],
                "hostname" => $nevt['hostname'],
                "client" => $nevt['client'],
                "agent" => $nevt['agent'],
                "referrer" => $nevt['referrer'],
                "self" => $nevt['self'],
                "method" => $nevt['method']
            );
            $sql = get_bind_to_sql_insert($tablename, $bind);
            exec_db_query($sql, $bind);
        } else {
            $line = "";
            if(loadHelper("catsplit.format")) {
                $line = catsplit_encode($nevt);
            } else {
                $line = json_encode($nevt);
            }
            $fw = append_log_to_file($line, "network.log"); 
        }
        
        return $fw;
    }
}

if(!is_fn("write_common_log")) {
    function write_common_log($message, $component="None", $program="") {
        $fw = false;
        
        $mypid = get_shared_var("mypid");
        $data = implode("\t", array(get_current_datetime(), $mypid, $component, $message));
        $fw = append_log_to_file($data, "common.log");

        // if enabled RFC3164 remote debugging
        if(loadHelper("rfc3164.proto")) {
            rfc3164_send_message($message, $component, $program);
        }

        return $fw;
    }
}

if(!is_fn("write_debug_log")) {
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
