<?php
/**
 * @file gnuboard.php
 * @date 2018-04-11
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Database Helper for Gnuboard 4, Gnuboard 5
 */

// get database prefix
if(!function_exists("gnb_get_db_prefix")) {
    function gnb_get_db_prefix($version=4) {
        return ($version > 4) ? "g5_" : "g4_";
    }
}

// get write table
if(!function_exists("gnb_get_write_table")) {
    function gnb_get_write_table($tablename, $version=4) {
        $write_prefix = gnb_get_db_prefix() . "write_";
        $write_table = $write_prefix . $tablename;
        return $write_table;
    }
}

// get write next
if(!function_exists("gnb_get_write_next")) {
    function gnb_get_write_next($tablename) {
        $row = exec_db_fetch("select min(wr_num) as min_wr_num from " . gnb_get_write_table($tablename));
        return (int)($row['min_wr_num'] - 1);
    }
}

// write post
if(!function_exists("gnb_write_post")) {
    function gnb_write_post($tablename, $data=array(), $version=4) {
        $result = false;
        $mb_id = get_current_user_name();

        loadHelper("networktool");

        $write_fields = array();
        $write_default_fields = array(
            "mb_id" => $mb_id,
            "wr_num" => gnb_get_write_next($tablename),
            "wr_reply" => "",
            "wr_parent" => "",
            "wr_comment_reply" => "",
            "ca_name" => "",
            "wr_option" => "",
            "wr_subject" => make_random_id(),
            "wr_content" => make_random_id(),
            "wr_link1" => "",
            "wr_link2" => "",
            "wr_link1_hit" => 0,
            "wr_link2_hit" => 0,
            "wr_trackback" => "",
            "wr_hit" => 0,
            "wr_good" => 0,
            "wr_nogood" => 0,
            "wr_password" => gnb_get_password(make_random_id()),
            "wr_name" => get_generated_name(),
            "wr_email" => "",
            "wr_homepage" => "",
            "wr_last" => "",
            "wr_ip" => get_network_client_addr(),
            "wr_1" => "",
            "wr_2" => "",
            "wr_3" => "",
            "wr_4" => "",
            "wr_5" => "",
            "wr_6" => "",
            "wr_7" => "",
            "wr_8" => "",
            "wr_9" => "",
            "wr_10" => "",
        );

        foreach($data as $k=>$v) {
            if(in_array($k, $write_default_fields)) {
                $write_fields[$k] = $v;
            }
        }
        $write_keys = array_keys($write_fields);

        $sql = "";
        $write_table = gnb_get_write_table($tablename);

        // make SQL statements
        if(count($write_keys) > 0) {
            $sql .= "insert into " . $write_table . " (";
            $sql .= implode(", ", $write_keys); // key names
            $sql .= ") values (";
            $sql .= implode(", :", $write_keys); // bind key names
            $sql .= ")";

            $result = exec_db_query($sql, $bind);
        }

        return $result;
    }
}

// get member data
if(!function_exists("gnb_get_member")) {
    function gnb_get_member($mb_id, $tablename="member") {
        $result = false;
        $bind = array(
            "mb_id" => $mb_id,
        );

        $member_table = gnb_get_db_prefix() . $tablename;
        $result = exec_db_fetch("select * from {$member_table} where mb_id = :mb_id", $bind);

        return $result;
    }
}

// get password
if(!function_exists("gnb_get_password")) {
    function gnb_get_password($password) {
        $bind = array(
            "password" => $password,
        );
        $row = exec_db_fetch("select password(:password) as pass", $bind);
        return $row['pass'];
    }
}

// run login process
if(!function_exists("gnb_process_login")) {
    function gnb_process_login($mb_id, $mb_password) {
        $result = false;
        $mb = gnb_get_member($mb_id);

        if(!array_key_empty("mb_id", $mb)) {
            $user_profile = array(
                "user_id" => $mb['mb_no'],
                "user_password" => get_password(gnb_get_password($mb['mb_password'])),
            );
            $result = process_safe_login($mb['mb_id'], $mb['mb_password'], $user_profile, true);
        }
        
        return $result;
    }
}

