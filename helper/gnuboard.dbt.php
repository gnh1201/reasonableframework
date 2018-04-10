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

// write post
if(!function_exists("gnb_write_post")) {
	function gnb_write_post($tablename, $data=array(), $version=4) {
		$result = false;

		$my_fields = "";
		$my_fields .= "wr_id,wr_num,wr_reply,wr_parent,wr_comment_reply,";
		$my_fields .= "ca_name,wr_option,wr_subject,wr_content,wr_link1,";
		$my_fields .= "wr_link2,wr_link1_hit,wr_link2_hit,wr_trackback,wr_hit,";
		$my_fields .= "wr_good,wr_nogood,mb_id,wr_password,wr_name,";
		$my_fields .= "wr_email,wr_homepage,wr_homepage,wr_last,wr_ip,";
		$my_fields .= "wr_1,wr_2,wr_3,wr_4,wr_5,wr_6,wr_7,wr_8,wr_9,wr_10";
		$valid_fields = explode(",", $my_fields);

		$bind = array();
		foreach($data as $k=>$v) {
			if(in_array($k, $valid_fields) && $k != "wr_id") {
				$bind[$k] = $v;
			}
		}
		$bind_keys = array_key($bind);

		$sql = "";
		$write_table = gnb_get_write_table($tablename);

		// make SQL statements
		if(count($filtered_keys) > 0) {
			$sql .= "insert into " . $write_table . " (";
			$sql .= implode(", ", $bind_keys); // key names
			$sql .= ") values (";
			$sql .= implode(", :", $bind_keys); // bind key names
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
