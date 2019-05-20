<?php
/**
 * @file database.mysql.cmd.php
 * @date 2019-04-06
 * @auther Go Namhyeon <gnh1201@gmail.com>
 * @brief MySQL(MariaDB) command line driver
 */

if(function_exists("exec_db_mysql_cmd_query")) {
    function exec_db_mysql_cmd_query($sql, $bind) {
        $result = false;
        $config = get_config();

        $args = array("mysql");
        $sql = get_db_binded_sql($sql, $bind);

        if(loadHelper("exectool")) {
            $args[] = sprintf("-u%s", $config['db_username']);
            $args[] = sprintf("-p%s", $config['db_password']);
            $args[] = sprintf("-h%s", $config['db_host']);
            $args[] = sprintf("-D %s", $config['db_name']);
            $args[] = sprintf("-e '%s'", make_safe_argument($sql));

            $cmd = trim(implode(" ", $args));
            $result = exec_command($cmd);
        }

        return $result;
    }
}
