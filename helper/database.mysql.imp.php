<?php
/**
 * @file database.mysql.imp.php
 * @date 2018-09-10
 * @author Go Namhyeon <abuse@catswords.net>
 * @brief MySQLi database helper
 */

if(!is_fn("get_db_mysql_imp_connect")) {
    function get_db_mysql_imp_connect() {
        $conn = false;
        $config = get_config();

        $conn = @mysqli_connect($config['db_host'], $config['db_username'], $config['db_password'], $config['db_name']);

        $errno = @mysqli_connect_errno();
        if($errno) {
            set_error(sprintf("Failed to connect to MySQL: %s", $errno));
            show_errors();
        }

        return $conn;
    }
}

if(!is_fn("exec_db_mysql_imp_query")) {
    function exec_db_mysql_imp_query($sql, $bind) {
        $result = false;
        $dbc = get_dbc_object();

        $binded_sql = get_db_binded_sql($sql, $bind);
        $result = @mysqli_query($dbc, $binded_sql);

        return $result;
    }
}

if(!is_fn("exec_db_mysql_imp_fetch_all")) {
    function exec_db_mysql_imp_fetch_all($sql, $bind) {
        $rows = array();
        $result = exec_db_mysql_imp_query($sql, $bind);

        while($row = mysqli_fetch_array($result)) {
            $rows[] = $row;
        }

        return $rows;
    }
}

if(!is_fn("close_db_mysql_imp_connect")) {
    function close_db_mysql_imp_connect() {
        $dbc = get_shared_var("dbc");
        return mysqli_close($dbc);
    }
}
