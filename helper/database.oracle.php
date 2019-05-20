<?php
/**
 * @file oracle.php
 * @date 2018-03-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Oracle database helper for ReasonableFramework
 */

if(!check_function_exists("check_db_oracle_installed")) {
    function check_db_oracle_installed() {
        $fn = check_invalid_function(array(
            "NO_FUNCTION_OCI_PARSE" => "oci_parse",
            "NO_FUNCTION_OCI_EXECUTE" => "oci_execute",
            "NO_FUNCTION_OCI_FETCH_ASSOC" => "oci_fetch_assoc",
            "NO_FUNCTION_OCI_FREE_STATEMENT" => "oci_free_statement",
            "NO_FUNCTION_OCI_CLOSE" => "oci_close",
        ));
        
        $is_installed = ($fn == -1);
        if(!$is_installed) {
            set_error($fn);
        }

        return $is_installed;
    }
}

if(!check_function_exists("get_db_orable_binded_sql")) {
    function get_db_orable_binded_sql($sql, $bind) {
        return get_db_binded_sql($sql, $bind);
    }
}

if(!check_function_exists("get_db_oracle_stmt")) {
    function get_db_oracle_stmt($sql, $bind) {
        $stmt = NULL;

        if(!check_db_oracle_installed()) {
            show_errors();
        }

        $sql = get_db_orable_binded_sql($sql, $bind);
        $stmt = oci_parse($conn, $sql);

        return $stmt;
    }
}

if(!check_function_exists("exec_db_oracle_connect")) {
    function exec_db_oracle_connect($host, $port, $user, $password, $options=array()) {
        $conn = NULL;
        $envs = get_value_in_array("envs", $options, array());

        if(!check_db_oracle_installed()) {
            show_errors();
        }

        if(array_key_empty("NLS_LANG", $envs)) {
            $envs["NLS_LANG"] = "KOREAN_KOREA.AL32UTF8";
        }

        // set environment variables
        foreach($envs as $env) {
            putenv($env);
        }

        // get oracle db connection info
        $dbs_id = read_storage_file("tnsname.orax", array(
            "storage_type" => "example",
        ));

        // set replace rules
        $dbs_rules = array(
            "protocol" => get_value_in_array("service_name", $options, "TCP"),
            "service_name" => get_value_in_array("service_name", $options, "ORCL"),
            "host" => $host,
            "port" => $port,
            "server_type" => "DEDICATED"
        );
        
        // parse db connection info
        foreach($dbs_rules as $k=>$v) {
            $dbs_id = str_replace("%" . $k . "%", $v, $dbs_id);
        }

        // set db connection
        $conn = @oci_connect($user, $password, $dbs_id);

        return $conn;
    }
}

if(!check_function_exists("exec_db_oracle_fetch_all")) {
    function exec_db_oracle_fetch_all($sql, $bind, $conn) {
        $rows = array();

        if(!check_db_oracle_installed()) {
            show_errors();
        }

        $stmt = get_db_oracle_stmt($sql, $bind);
        oci_execute($stmt);

        while($row = oci_fetch_assoc($stmt)) {
            $rows[] = $row;
        }

        oci_free_statement($stmt);

        return $rows;
    }
}

if(!check_function_exists("exec_db_oracle_query")) {
    function exec_db_oracle_query($sql, $bind, $conn) {
        $flag = false;

        if(!check_db_oracle_installed()) {
            show_errors();
        }

        $stmt = get_db_oracle_stmt($sql, $bind);
        $flag = oci_execute($stmt);

        oci_free_statement($stmt);

        return $flag;
    }
}

if(!check_function_exists("close_db_oracle_connect")) {
    function close_db_oracle_connect() {
        $dbc = get_scope("dbc");

        if(!check_db_oracle_installed()) {
            show_errors();
        }

        return @oci_close($dbc);
    }
}
