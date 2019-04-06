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
    
    $sql = get_db_binded_sql($sql, $bind);

    if(loadHelper("exectool")) {
      $cmd = sprintf(
        "mysql -u%s -p%s -h%s -D %s -e '%s'",
        $config['db_username'],
        $config['db_password'],
        $config['db_host'],
        $config['db_name'],
        make_safe_argument($sql)
      );

      $executed = exec_command($cmd);
      if(strlen($executed) == 0) {
        $result = true;
      } else {
        set_error($executed);
        show_errors();
      }
    }
    
    return $result;
  }
}
