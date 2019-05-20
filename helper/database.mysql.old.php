<?php
/**
 * @file database.mysql.old.php
 * @date 2018-09-14
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief MySQL-old (lower than 5.4) database helper
 */

if(!check_function_exists("get_db_mysql_old_connect")) {
    function get_db_mysql_old_connect() {
        $conn = false;
        $config = get_config();

        $conn = @mysql_connect($config['db_host'], $config['db_username'], $config['db_password']);
        if(!$conn) {
            set_error("Could not connect: " . @mysql_error());
            show_errors();
        }

        if(!@mysql_select_db($config['db_name'], $conn)) {
            set_error("Could not select database.");
            show_errors();
        }

        return $conn;
    }
}

if(!check_function_exists("exec_db_mysql_old_query")) {
    function exec_db_mysql_old_query($sql, $bind) {
        $result = false;
        $dbc = get_dbc_object();

        $binded_sql = get_db_binded_sql($sql, $bind);
        $result = @mysql_query($dbc, $binded_sql);

        return $result;
    }
}

if(!check_function_exists("exec_db_mysql_old_fetch_all")) {
    function exec_db_mysql_old_fetch_all($sql, $bind) {
        $rows = array();
        $result = exec_db_mysql_old_query($sql, $bind);

        while($row = @mysql_fetch_array($result)) {
            $rows[] = $row;
        }

        return $rows;
    }
}

if(!check_function_exists("close_db_mysql_old_connect")) {
    function close_db_mysql_old_connect() {
        $dbc = get_scope("dbc");
        return mysql_close($dbc);
    }
}
