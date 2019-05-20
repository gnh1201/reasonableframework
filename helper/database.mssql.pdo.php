<?php
// database.mssql.pdo.php

if(!check_function_exists("get_db_mssql_pdo_connect")) {
    function get_db_mssql_pdo_connect() {
        $conn = false;

        $config = get_config();

        try {
            $conn = new PDO(
                sprintf(
                    "dblib:host=%s;dbname=%s",
                    $config['db_mssql_host'],
                    $config['db_mssql_name']
                ),
                $config['db_mssql_username'],
                $config['db_mssql_password']
            );
            $conn->exec("SET CHARACTER SET utf8");
            $conn->query("SET ANSI_NULLS ON");
            $conn->query("SET ANSI_WARNINGS ON");
        } catch(PDOException $e) {
            set_error($e->getMessage());
            show_errors();
        }

        return $conn;
    }
}

if(!check_function_exists("exec_db_mssql_pdo_query")) {
    function exec_db_mssql_pdo_query($sql, $bind=array(), $options=array()) {
        $dbc = get_value_in_array("dbc", $options, get_dbc_object());

        if($dbc !== false) {
            $binded_sql = get_db_binded_sql($sql, $bind);
            $sth = $dbc->prepare($binded_sql);
            $sth->execute();
        }

        return $sth; 
    }
}

if(!check_function_exists("exec_db_mssql_pdo_fetch_all")) {
    function exec_db_mssql_pdo_fetch_all($sql, $bind=array(), $options=array()) {
        $rows = array();

        $sth = exec_db_mssql_pdo_query($sql, $bind, $options);
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $rows = $sth->fetchAll();

        return $rows;
    }
}
