<?php
/**
 * @file zeroboard4.dbt.php
 * @date 2018-08-20
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Database Helper for Zeroboard 4 PL 9 (experimental)
 */

// get database prefix
if(!check_function_exists("zb4_get_db_prefix")) {
    function zb4_get_db_prefix() {
        return "zetyx_";
    }
}

// get table
if(!check_function_exists("zb4_get_db_table")) {
    function zb4_get_db_table($tablename) {
        return (zb4_get_db_prefix() . $tablename);
    }
}

// get write table
if(!check_function_exists("zb4_get_write_table")) {
    function zb4_get_write_table($tablename, $version=4) {
        $write_prefix = zb4_get_db_prefix() . "board_";
        $write_table = $write_prefix . $tablename;
        return $write_table;
    }
}

// write post
if(!check_function_exists("zb4_write_post")) {
    function zb4_write_post($tablename, $data=array()) {
        $result = 0;
        $write_table = zb4_get_write_table($tablename);
        $mb_id = get_current_user_name();

        // load helpers
        loadHelper("networktool");
        loadHelper("naturename.kr");

        $write_fields = array();
        $write_default_fields = array(
            //"no" => "", // auto increment
            "division" => "1",
            "headnum" => "0",
            "arrangenum" => "0",
            "depth" => "0",
            "prev_no" => "0",
            "next_no" => "0",
            "father" => "0",
            "child" => "0",
            "ismember" => "0",
            "islevel" => "10",
            "memo" => ""
            "ip" => get_network_client_addr(),
            "password" => "",
            "name" => naturename_kr_get_generated_name(),
            "homepage" => "",
            "email" => "",
            "subject" => "",
            "use_html" => "0",
            "reply_mail" => "0",
            "category" => "1",
            "is_secret" => "0",
            "sitelink1" => "",
            "sitelink2" => "",
            "file_name1" => "",
            "file_name2" => "",
            "s_file_name1" => "",
            "s_file_name2" => "",
            "download1" => "0",
            "download2" => "0",
            "reg_date" => "0",
            "hit" => "0",
            "vote" => "0",
            "total_comment" => "0",
            "x" => "",
            "y" => "",
        );

        foreach($write_default_fields as $k=>$v) {
            if(in_array($k, array("mb_id", "wr_num"))) {
                $write_fields[$k] = $v;
            } else {
                $write_fields[$k] = array_key_empty($k, $data) ? $v : $data[$k];
            }
        }

        foreach($data as $k=>$v) {
            if(!in_array($k, $write_default_fields)) {
                $write_fields[$k] = $v;
            }
        }

        if(count($write_fields) > 0) {
            $sql = get_bind_to_sql_insert($write_table, $write_fields);
            if(exec_db_query($sql, $write_fields)) {
                $result = get_db_last_id();
            }
        }

        return $result;
    }
}

if(!check_function_exists("zb4_get_posts")) {
    function zb4_get_posts($table_name, $page=1, $limit=20, $options=array()) {
        $sql = "select * from " . zb4_get_write_table($table_name) . " order by no desc" . get_page_range($page, $limit);
        return exec_db_fetch_all($sql);
    }
}

if(!check_function_exists("zb4_get_post_by_id")) {
    function zb4_get_post_by_id($table_name, $post_id) {
        $sql = "select * from " . zb4_get_write_table($table_name) . " where no = :no";
        return exec_db_fetch($sql, array(
            "no" => $post_id
        ));
    }
}
