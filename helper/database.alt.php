<?php
/**
 * @file database.alt.php
 * @date 2018-09-10
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Database alternative driver switcher
 */

if(!check_function_exists("exec_db_alt_callback")) {
    function exec_db_alt_callback($rules, $params=array(), $driver="") {
        $result = false;
        $db_driver = empty($driver) ? get_db_driver() : $driver;

        foreach($rules as $rule) {
            if($rule['driver'] == $db_driver) {
                if(loadHelper(sprintf("database.%s", $rule['driver']))) {
                    if(check_function_exists($rule['callback'])) {
                        if(is_array($params) && count($params) > 0) {
                            $result = call_user_func_array($rule['callback'], $params);
                        } else {
                            $result = call_user_func($rule['callback']);
                        }
                    }
                } else {
                    set_error(sprintf("Can not load %s database driver.", $rule['driver']));
                    show_errors();
                }
                break;
            }
        }

        return $result;
    }
}

if(!check_function_exists("get_db_alt_connect")) {
    function get_db_alt_connect($driver) {
        $conn = false;
        $config = get_config();

        $rules = array(
            array("driver" => "mysql.pdo", "callback" => "get_db_mysql_pdo_connect"),
            array("driver" => "mysql.imp", "callback" => "get_db_mysql_imp_connect"),
            array("driver" => "mysql.old", "callback" => "get_db_mysql_old_connect"),
            array("driver" => "mysql.cmd", "callback" => "get_db_mysql_cmd_connect"),
            array("driver" => "oracle", "callback" => "get_db_oracle_connect"),
            array("driver" => "pgsql", "callback" => "get_db_pgsql_connect"),
            array("driver" => "mssql.pdo", "callback" => "get_db_mssql_pdo_connect"),
        );

        $conn = exec_db_alt_callback($rules, array(), $driver);

        return $conn;
    }
}

if(!check_function_exists("exec_db_alt_query")) {
    function exec_db_alt_query($sql, $bind=array(), $options=array()) {
        $result = false;
        
        // allow custom db connection object
        if(array_key_empty("dbc", $options)) {
            if(!array_key_empty("driver", $options)) {
                $options['dbc'] = get_db_alt_connect($options['driver']);
            } else {
                $options['dbc'] = get_dbc_object();
            }
        }

        $rules = array(
            array("driver" => "mysql.pdo", "callback" => "exec_db_mysql_pdo_query"),
            array("driver" => "mysql.imp", "callback" => "exec_db_mysql_imp_query"),
            array("driver" => "mysql.old", "callback" => "exec_db_mysql_old_query"),
            array("driver" => "mysql.cmd", "callback" => "exec_db_mysql_cmd_query"),
            array("driver" => "oracle", "callback" => "exec_db_oracle_query"),
            array("driver" => "pgsql", "callback" => "exec_db_pgsql_query"),
            array("driver" => "mssql.pdo", "callback" => "exec_db_mssql_pdo_query"),
        );

        $result = exec_db_alt_callback($rules, array($sql, $bind, $options), $driver);

        return $result;
    }
}

if(!check_function_exists("exec_db_alt_fetch_all")) {
    function exec_db_alt_fetch_all($sql, $bind=array(), $options=array()) {
        $rows = array();

        $driver = get_value_in_array("driver", $options, "");
        $rules = array(
            array("driver" => "mysql.pdo", "callback" => "exec_db_mysql_pdo_fetch_all"),
            array("driver" => "mysql.imp", "callback" => "exec_db_mysql_imp_fetch_all"),
            array("driver" => "mysql.old", "callback" => "exec_db_mysql_old_fetch_all"),
            array("driver" => "mysql.cmd", "callback" => "exec_db_mysql_cmd_fetch_all"),
            array("driver" => "oracle", "callback" => "exec_db_oracle_fetch_all"),
            array("driver" => "pgsql", "callback" => "exec_db_pgsql_fetch_all"),
            array("driver" => "mssql.pdo", "callback" => "exec_db_mssql_fetch_all"),
        );

        $rows = exec_db_alt_callback($rules, array($sql, $bind, $options), $driver);

        return $rows;
    }
}

if(!check_function_exists("exec_db_alt_fetch")) {
    function exec_db_alt_fetch($sql, $bind) {
        $fetched = false;

        $rows = exec_db_alt_fetch_all($sql, $bind);
        foreach($rows as $row) {
            $fetched = $row;
            break;
        }

        return $fetched;
    }
}

if(!check_function_exists("get_db_alt_last_id")) {
    function get_db_alt_last_id($driver) {
        $last_id = false;

        if($driver == "mysql.imp") {
            $last_id = @mysqli_insert_id();
        } elseif($driver == "mysql.old") {
            $last_id = @mysql_insert_id();
        }

        return $last_id;
    }
}
