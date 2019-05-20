<?php
/**
 * @file hybridauth.dbt.php
 * @date 2018-04-15
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief HybridAuth DB Helper
 */

if(!check_function_exists("store_hybridauth_session")) {
    function store_hybridauth_session($data, $user_id) {
        $connection_id = false;

        $bind = array(
            "user_id" => $user_id,
            "hybridauth_session" => $data
        );
        $sql = get_bind_to_sql_insert("users_connections", $bind);
        if(exec_db_query($sql, $bind)) {
            $connection_id = get_db_last_id();
        }

        return $connection_id;
    }
}

if(!check_function_exists("get_stored_hybridauth_session")) {
    function get_stored_hybridauth_session($connection_id) {
        $stored_session = false;

        $bind = array(
            "connection_id" => $connection_id
        );
        $sql = get_bind_to_sql_select("users_connections", $bind);
        $row = exec_db_fetch($sql, $bind);

        $stored_session = get_value_in_array("hybridauth_session", $row, $stored_session);

        return $stored_session;
    }
}

if(!check_function_exists("get_hybridauth_connection_info")) {
    function get_hybridauth_connection_info($connection_id) {
        $connection_info = false;

        $bind = array(
            "connection_id" => $connection_id
        );
        $sql = get_bind_to_sql_select("users_connections", $bind);
        $row = exec_db_fetch($sql, $bind);

        if(!array_key_empty("connection_id", $row)) {
            $connection_info = $row;
        }

        return $connection_info;
    }
}

if(!check_function_exists("get_hybridauth_connection_id")) {
    function get_hybridauth_connection_id($user_id) {
        $connection_id = false;

        $bind = array(
            "user_id" => $user_id
        );
        $sql = get_bind_to_sql_select("users_connections", $bind, array(
            "setorders" => array("connection_id desc"),
            "setpage" => 1,
            "setlimit" => 1
        ));
        $row = exec_db_fetch($sql, $bind);

        $connection_id = get_value_in_array("connection_id", $row, $connection_id);

        return $connection_id;
    }
}
