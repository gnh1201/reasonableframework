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

		$write_table = gnb_get_write_table($tablename);
        $mb_id = get_current_user_name();

        // load network helper
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
            "wr_datetime" => get_current_datetime(),
            "wr_last" => get_current_datetime(),
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

        foreach($write_default_fields as $k=>$v) {
            $write_fields[$k] = array_key_empty($k, $data) ? $v : $data[$k];
        }

		if(count($write_fields) > 0) {
			$sql = get_bind_to_sql_insert($write_table, $write_fields);
			$result = exec_db_query($sql, $write_fields);
		}

        return $result;
    }
}

if(!function_exists("gnb_set_post_parameters")) {
    function gnb_set_post_parameters($tablename, $wr_id, $bind=array()) {
        $flag = false;
        $excludes = array("wr_id");

        $write_table = gnb_get_write_table($tablename);
        $bind['wr_id'] = get_value_in_array("wr_id", $bind, $wr_id);

        $sql = "update " . $write_table . " set " . get_bind_to_sql_update_set($bind, $excludes) . " where wr_id = :wr_id";
        $flag = exec_db_query($sql, $bind);

        return $flag;
    }
}

// get member data
if(!function_exists("gnb_get_member")) {
    function gnb_get_member($mb_id, $tablename="member") {
        $result = array();

        $bind = array(
            "mb_id" => $mb_id,
        );

        $member_table = gnb_get_db_prefix() . $tablename;
        $result = exec_db_fetch("select * from " . $member_table . " where mb_id = :mb_id", $bind);

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

// get config
if(!function_exists("gnb_get_config")) {
    function gnb_get_config($tablename="config") {
        $result = array();

        $config_table = gnb_get_db_prefix() . $tablename;
        $result = exec_db_fetch("select * from " . $config_table);

        return $result;
    }
}

// run login process
if(!function_exists("gnb_process_safe_login")) {
    function gnb_process_safe_login($user_name, $user_password) {
        $result = false;
        $mb = gnb_get_member($user_name);

        if(!array_key_empty("mb_id", $mb)) {
            $user_profile = array(
                "user_id" => $mb['mb_no'],
                "user_password" => get_password(gnb_get_password($mb['mb_password'])),
            );
            $result = process_safe_login($mb['mb_id'], gnb_get_password($mb['mb_password']), $user_profile);
        }
        
        return $result;
    }
}

// run join member
if(!function_exists("gnb_join_member")) {
	function gnb_join_member($user_name, $user_password, $data=array(), $tablename="member") {
        $result = false;

		$member_table = gnb_get_db_prefix() . $tablename;
		$gnb_config = gnb_get_config();

        // load network helper
        loadHelper("networktool");

		$member_fields = array();
		$member_default_fields = array(
			"mb_id" => $user_name,
			"ug_id" => "",
			"mb_password" => gnb_get_password($user_password),
			"mb_name" => "",
			"mb_jumin" => "",
			"mb_sex" => "",
			"mb_birth" => "",
			"mb_nick" => "",
			"mb_nick_date" => "",
			"mb_password_q" => "",
			"mb_password_a" => "",
			"mb_email" => "",
			"mb_homepage" => "",
			"mb_tel" => "",
			"mb_hp" => "",
			"mb_zip1" => "",
			"mb_zip2" => "",
			"mb_addr1" => "",
			"mb_addr2" => "",
			"mb_addr3" => "",
			"mb_addr_jibeon" => "",
			"mb_signature" => "",
			"mb_profile" => "",
			"mb_today_login" => get_current_datetime(),
			"mb_datetime" => get_current_datetime(),
			"mb_ip" => get_network_client_addr(),
			"mb_level" => get_value_in_array("cf_register_level", $gnb_config),
			"mb_recommend" => "",
			"mb_login_ip" => get_network_client_addr(),
			"mb_mailling" => "",
			"mb_sms" => "",
			"mb_open" => "",
			"mb_open_date" => get_current_datetime(),
			"mb_1" => "",
			"mb_2" => "",
			"mb_3" => "",
			"mb_4" => "",
			"mb_5" => "",
			"mb_6" => "",
			"mb_7" => "",
			"mb_8" => "",
			"mb_9" => "",
			"mb_10" => "",
		);

        foreach($member_default_fields as $k=>$v) {
            $member_fields[$k] = array_key_empty($k, $data) ? $v : $data[$k];
        }

		if(count($member_fields) > 0) {
			$sql = get_bind_to_sql_insert($member_table, $member_fields);
			$result = exec_db_query($sql, $member_fields);
		}

		return $result;
	}
}
