<?php
/**
 * @file database.mysql.cmd.php
 * @date 2019-04-06
 * @auther Go Namhyeon <gnh1201@gmail.com>
 * @brief MySQL(MariaDB) command line driver
 */

if(!is_fn("get_db_mysql_cmd_connect")) {
    function get_db_mysql_cmd_connect() {
        $result = false;
        $config = get_config();
        
        if(loadHelper("exectool")) {
            $args = array("mysql");
            $args[] = sprintf("-u'%s'", $config['db_username']);
            $args[] = sprintf("-p'%s'", $config['db_password']);
            $args[] = sprintf("-h'%s'", $config['db_host']);
            $args[] = "-s"; // --slient
            $args[] = "-N"; // --skip-column-names
            $args[] = "-e'select 1'";
            
            $cmd = implode(" ", $args);
            $result = exec_command($cmd);
        }
        
        return $result;
    }
}

if(!is_fn("exec_db_mysql_cmd_query")) {
    function exec_db_mysql_cmd_query($sql, $bind) {
        $result = false;
        $config = get_config();
        $sql = get_db_binded_sql($sql, $bind);

        if(loadHelper("exectool")) {
            $args = array("mysql");
            $args[] = sprintf("-u'%s'", $config['db_username']);
            $args[] = sprintf("-p'%s'", $config['db_password']);
            $args[] = sprintf("-h'%s'", $config['db_host']);
            $args[] = "-s"; // --slient
            $args[] = sprintf("-D'%s'", $config['db_name']);
            $args[] = sprintf("-e'%s'", make_safe_argument($sql));

            $cmd = implode(" ", $args);
            $result = exec_command($cmd);
        }

        return $result;
    }
}

if(!is_fn("exec_db_mysql_cmd_fetch_all")) {
    function exec_db_mysql_cmd_fetch_all($sql, $bind) {
        $result = false;

        $tsvData = exec_db_mysql_cmd_query($sql, $bind);
        $lines = explode(DOC_EOL, $tsvData);
        $rows = array();

        if(is_fn("str_getcsv")) {
            foreach($lines as $line) {
                $rows[] = str_getcsv($line, "\t");
            }
        } else {
            foreach($lines as $line) {
                $rows[] = explode("\t", $line);
            }
        }

        if(count($rows) > 0) {
            $result = $rows;
        }

        return $result;
    }
}
