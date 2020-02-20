<?php
/**
 * @file database.php
 * @created_on 2018-04-13
 * @updated_on 2020-02-20
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Database module
 */

if(!is_fn("get_db_driver")) {
    function get_db_driver() {
        $config = get_config();
        return get_value_in_array("db_driver", $config, false);
    }
}

if(!is_fn("check_db_driver")) {
    function check_db_driver($db_driver) {
        return (get_db_driver() == $db_driver);
    }
}

if(!is_fn("get_db_connect")) {
    function get_db_connect($_RETRY=0) {
        $conn = false;

        $config = get_config();
        $db_driver = get_db_driver();
        $dsn = "mysql:host=%s;dbname=%s;charset=utf8";

        $_RETRY_LIMIT = get_value_in_array("db_retry_limit", $config, 3);

        if(in_array($db_driver, array("mysql", "mysql.pdo"))) {
            try {
                $conn = new PDO(
                    sprintf($dsn, $config['db_host'], $config['db_name']),
                    $config['db_username'],
                    $config['db_password'],
                    array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                    )
                );
                //$conn->query("SET NAMES utf8");
            } catch(Exception $e) {
                if($_RETRY > $_RETRY_LIMIT) {
                    set_error($e->getMessage());
                    show_errors();
                } else {
                    $_RETRY++;
                    $conn = get_db_connect($_RETRY);
                }
            }
        } elseif(loadHelper("database.alt")) {
            $conn = call_user_func("get_db_alt_connect", $db_driver);
        }

        return $conn;
    }
}

if(!is_fn("exec_stmt_query")) {
    function exec_stmt_query($sql, $bind=array()) {
        $stmt = get_db_stmt($sql, $bind);
        $stmt->execute();

        return $stmt;
    }
}

if(!is_fn("get_dbc_object")) {
    function get_dbc_object($renew=false) {
        if($renew) {
            set_shared_var("dbc", get_db_connect());
        }
        return get_shared_var("dbc");
    }
}


// 2018-08-19: support lower php version (not supported anonymous function)
if(!function_exists("compare_db_key_length")) {
    function compare_db_key_length($a, $b) {
        return strlen($b) - strlen($a);
    }
}

if(!function_exists("get_db_binded_sql")) {
    function get_db_binded_sql($sql, $bind=array()) {
        if(is_array($bind) && check_array_length($bind, 0) > 0) {
            $bindkeys = array_keys($bind);

            // 2018-08-19: support lower php version (not supported anonymous function)
            usort($bindkeys, "compare_db_key_length");

            // bind values
            foreach($bindkeys as $k) {
                $sql = str_replace(":" . $k, "'" . addslashes($bind[$k]) . "'", $sql);
            }
        }
        return $sql;
    }
}

if(!is_fn("get_db_stmt")) {
    function get_db_stmt($sql, $bind=array(), $options=array()) {
        $stmt = NULL;
        
        $dbc = get_dbc_object();
        $binder = get_value_in_array("binder", $options, "php");

        if($binder == "pdo") {
            $stmt = $dbc->prepare($sql);
            foreach($bind as $k=>$v) {
                $stmt->bindParam(sprintf(":%s", $k), $v);
            }
        } else {
            $sql = get_db_binded_sql($sql, $bind);
            $stmt = $dbc->prepare($sql);
        }

        return $stmt;
    }
}

if(!is_fn("get_db_last_id")) {
    function get_db_last_id() {
        $last_id = false;

        $dbc = get_dbc_object();
        $config = get_config();
        $db_driver = get_db_driver();

        if(in_array($db_driver, array("mysql", "mysql.pdo"))) {
            $last_id = $dbc->lastInsertId();
        } elseif(loadHelper("database.dbt")) {
            $last_id = call_user_func("get_db_alt_last_id", $db_driver);
        }

        return $last_id;
    }
}

if(!is_fn("exec_db_query")) {
    function exec_db_query($sql, $bind=array(), $options=array()) {
        $flag = false;

        // set variable
        $dbc = get_dbc_object();
        $terms = explode(" ", trim($sql));

        // check sql insert or not
        if($terms[0] == "insert") {
            $stmt = get_db_stmt($sql, $bind, array(
                "binder" => "pdo"
            ));
            $flag = $stmt->execute($bind);
        } else {
            $stmt = get_db_stmt($sql, $bind, array(
                "binder" => "php"
            ));
            $flag = $stmt->execute();
        }

        // get errors
        $errors = $stmt->errorInfo();

        // if failed
        if(!$flag) {
            write_common_log(get_hashed_text($sql), "DATABASE-FAILED-EXECUTE");
            write_common_log($sql, "DATABASE-FAILED-QUERY");
            write_common_log(get_db_binded_sql($sql, $bind), "DATABASE-FAILED-BINDED-QUERY");
            write_common_log(implode(",", $errors), "DATABASE-FAILED-ERROR");
        }

        return $flag;
    }
}

if(!is_fn("exec_db_fetch_all")) {
    function exec_db_fetch_all($sql, $bind=array(), $options=array()) {
        $result = array();

        // set is counted
        $is_counted = false;

        $rows = array();
        $stmt = get_db_stmt($sql, $bind);

        // get rows
        if($stmt->execute() && $stmt->rowCount() > 0) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // get rows with removed keys (1.6 or above)
        $_rows = array();
        if(array_key_equals("getvalues", $options, true)) {
            foreach($rows as $row) {
                $_rows[] = array_values($row);
            }
            $rows = $_rows;
        }
        
        // get number of rows
        $num_rows = 0;
        if(array_key_equals("getcount", $options, true)) {
            $sql = sprintf("select count(*) as value from (%s) a", get_db_binded_sql($sql, $bind));
            $rows = exec_db_fetch_all($sql, $bind);
            foreach($rows as $row) {
                $num_rows += intval($row['value']);
            }
            $is_counted = true;
        } elseif(array_key_equals("getcount", $options, "php")) {
            $num_rows = count($rows);
            $is_counted = true;
        } elseif(array_key_equals("getcount", $options, "pdo")) {
            $num_rows = $stmt->rowCount();
            $is_counted = true;
        }

        // make a result
        if($is_counted) {
            $result = array(
                "count" => $num_rows,
                "data" => $rows
            );
        } else {
            $result = $rows;
        }
        
        return $result;
    }
}

if(!is_fn("get_db_zero")) {
    function get_db_zero($value) {
        return ($value > 0 ? $value : '0');
    }
}

if(!is_fn("exec_db_fetch")) {
    function exec_db_fetch($sql, $bind=array(), $start=0) {
        $row = NULL;
        $rows = NULL;

        $config = get_config();
        $fetch_mode = get_value_in_array("db_fetch_mode", $config, "sql");
        
        if($fetch_mode == "sql") {
            $_bind = $bind;
            $_sql = sprintf("%s limit %s, 1", $sql, get_db_zero($start));
            $rows = exec_db_fetch_all($_sql, $_bind);
        } elseif($fetch_mode == "php") {
            $_bind = $bind;
            $_sql = $sql;
            $rows = exec_db_fetch_all($_sql, $_bind);
            $rows = array_slice($rows, get_db_zero($start), 1);
        }

        // get first of rows
        $row = current($rows);
 
        // return row
        return $row;
    }
}

if(!is_fn("get_page_range")) {
    function get_page_range($page=1, $limit=15) {
        $sql = false;
        
        $page = ($page > 1 ? $page : 1);
        if($limit > 0) {
            $_START = get_db_zero(($page - 1) * $limit);
            $_LIMIT = get_db_zero($limit);
            $sql = sprintf(" limit %s, %s", $_START, $_LIMIT);
        }

        return $sql;
    }
}

if(!is_fn("get_bind_to_sql_insert")) {
    function get_bind_to_sql_insert($tablename, $bind, $options=array()) {
        $sql = false;

        // get not duplicatable fieldnames
        $setkeys = get_array(get_value_in_array("setkeys", $options, false));
        $setfixeds = get_array(get_value_in_array("setfixeds", $options, false));
        $setignores = get_array(get_value_in_array("setignores", $options, false));
        $setwheres = get_array(get_value_in_array("setwheres", $options, false));
        
        // safemode_off (default: false)
        $safemode_off = array_key_equals("safemode_off", $options, true);

        // set variables
        $num_keys = count($setkeys);
        $num_wheres = count($setkeys);
        $num_duplicates = 0;
        $num_ignores = 0;

        // check duplicates
        if(count($setkeys) > 0) {
            $_bind_K = array();
            $_bind_V = array();
            foreach($bind as $k=>$v) {
                if(!in_array($k, $setfixeds)) {
                    if(in_array($k, $setkeys)) {
                        $_bind_K[$k] = $v;
                    } else {
                        $_bind_V[$k] = $v;
                    }
                }
            }
            $_options = array(
                "getcount" => true
            );
            $_sql = get_bind_to_sql_select($tablename, $_bind_K, $_options);
            $_rows = exec_db_fetch_all($_sql, $_bind_K);
            foreach($_rows as $_row) {
                $num_duplicates += intval($_row['value']);
            }
        }

        // preventing accidentally query
        $num_conditions = sum($num_keys, $num_wheres);
        if($num_conditions == 0 && $safemode_off !== true) {
            write_common_log("Blocked accidentally query. Set safemode_off to TRUE if you want disable", "system/database");
            return false;
        }

        // check ignores
        if(count($setignores) > 0) {
            $_bind = false;
            $_options = array(
                "getcount" => true,
                "setwheres" => $setignores
            );
            $_sql = get_bind_to_sql_select($tablename, $_bind, $_options);
            $_rows = exec_db_fetch_all($_sql, $_bind);
            foreach($_rows as $_row) {
                $num_ignores += intval($_row['value']);
            }
        }

        // make statements
        if($num_ignores > 0) {
            $sql = "select 1";
        } elseif($num_duplicates > 0) {
            $sql = get_bind_to_sql_update($tablename, $bind, array(
                "setkeys" => array_keys($_bind_K)
            ));
        } else {
            $sql = "insert into `%s` (%s) values (:%s)";
            $bindkeys = array_keys($bind);
            $s1 = $tablename;
            $s2 = sprintf("`%s`", implode("`, `", $bindkeys));
            $s3 = implode(", :", $bindkeys);
            $sql = sprintf($sql, $s1, $s2, $s3);
        }

        return $sql;
    }
}

if(!is_fn("exec_db_bulk_start")) {
    function exec_db_bulk_start() {
        $bulkid = make_random_id();
        set_shared_var("bulk_" . $bulkid, array());
        //write_common_log("bulk started: " . $bulkid, "system/database");
        return $bulkid;
    }
}

if(!is_fn("exec_db_bulk_push")) {
    function exec_db_bulk_push($bulkid, $bind) {
        $rows = get_shared_var("bulk_" . $bulkid);
        $rows[] = $bind;
        set_shared_var("bulk_" . $bulkid, $rows);
        //write_common_log("bulk pushed: " . $bulkid . " / " . count($rows), "system/database");
    }
}

if(!is_fn("exec_db_bulk_end")) {
    function exec_db_bulk_end($bulkid, $tablename, $bindkeys) {
        $result = false;

        $rows = get_shared_var("bulk_" . $bulkid);
        if(count($rows) == 0) {
            write_common_log("bulk ended: empty", "system/database");
            return;
        }

        $sql = "insert into `%s` (%s) values (%s)";
        $s1 = $tablename;
        $s2 = sprintf("`%s`", implode("`, `", $bindkeys));

        $s3a = array();
        foreach($rows as $row) {
            $s3a[] = sprintf("'%s'", implode("', '", $row));
        }
        $s3 = implode("), (", $s3a);

        $sql = sprintf($sql, $s1, $s2, $s3);
        $result = exec_db_query($sql);

        write_common_log("bulk ended: " . substr($sql, 0, 200) . "...", "system/database");

        return $result;
    }
}

// Deprecated: get_bind_to_sql_where($bind, $excludes) - lower than 1.6
// Now: get_bind_to_sql_where($bind, $options) - 1.6 or above

if(!is_fn("get_bind_to_sql_where")) {
    // warning: variable k is not protected. do not use variable k and external variable without filter
    function get_bind_to_sql_where($bind, $options=array(), $_options=array()) {
        $s3 = "";
        $sp = "";

        $excludes = get_value_in_array("excludes", $options, array());
        if(get_old_version() == "1.5") { // compatible 1.5 or below
            $excludes = $options;
        }
        
        if(is_array($bind)) {    
            foreach($bind as $k=>$v) {
                if(!in_array($k, $excludes)) {
                    $s3 .= sprintf(" and %s = :%s", $k, $k);
                }
            }
        }
        
        // s1a: s1 additonal (set new fields)
        $s1a = array();
        if(array_key_is_array("setfields", $options)) {
            $setfields = $options['setfields'];
            $s1a = get_bind_to_sql_fields($setfields);
        }
        
        if(!array_keys_empty(array("settimefield", "setminutes"), $options)) {
            $s3 .= get_bind_to_sql_past_minutes($options['settimefield'], $options['setminutes']);
        }

        if(!array_key_empty("setwheres", $options)) {
            if(is_array($options['setwheres'])) {
                foreach($options['setwheres'] as $opts) {
                    if(check_is_string_not_array($opts)) {
                        $s3 .= sprintf(" and (%s)", $opts);
                    } elseif(check_array_length($opts, 3) == 0 && is_array($opts[2])) {
                        $s3 .= sprintf(" %s (%s)", $opts[0], get_db_binded_sql($opts[1], $opts[2]));
                    } elseif(check_array_length($opts, 2) == 0 && is_array($opts[1])) {
                        if(is_array($opts[1][0])) {
                            // recursive
                            $s3 .= sprintf(" %s (%s)", $opts[0], get_bind_to_sql_where(false, array(
                                "setwheres" => $opts[1]
                            )));
                        } elseif($opts[1][0] == "like") {
                            if(check_array_length($opts[1][2], 0) > 0) {
                                $s3a = array();
                                foreach($opts[1][2] as $word) {
                                    $s3a[] = sprintf("%s like '%s'", get_value_in_array($opts[1][1], $s1a, $opts[1][1]), "%{$word}%");
                                }
                                $s3 .= sprintf(" %s (%s)", $opts[0], implode(" and ", $s3a));
                            } else {
                                $s3 .= sprintf(" %s (%s like %s)", $opts[0], get_value_in_array($opts[1][1], $s1a, $opts[1][1]), "'%{$opts[1][2]}%'");
                            }
                        } elseif($opts[1][0] == "left") {
                            if(check_array_length($opts[1][2], 0) > 0) {
                                $s3a = array();
                                foreach($opts[1][2] as $word) {
                                    $s3a[] = sprintf("%s like '%s'", get_value_in_array($opts[1][1], $s1a, $opts[1][1]), "{$word}%");
                                }
                                $s3 .= sprintf(" %s (%s)", $opts[0], implode(" and ", $s3a));
                            } else {
                                $s3 .= sprintf(" %s (%s like %s)", $opts[0], get_value_in_array($opts[1][1], $s1a, $opts[1][1]), "'{$opts[1][2]}%'");
                            }
                        } elseif($opts[1][0] == "right") {
                            if(check_array_length($opts[1][2], 0) > 0) {
                                $s3a = array();
                                foreach($opts[1][2] as $word) {
                                    $s3a[] = sprintf("%s like '%s'", get_value_in_array($opts[1][1], $s1a, $opts[1][1]), "%{$word}");
                                }
                                $s3 .= sprintf(" %s (%s)", $opts[0], implode(" and ", $s3a));
                            } else {
                                $s3 .= sprintf(" %s (%s like %s)", $opts[0], get_value_in_array($opts[1][1], $s1a, $opts[1][1]), "'%{$opts[1][2]}'");
                            }
                        } elseif($opts[1][0] == "in") {
                            if(check_array_length($opts[1][2], 0) > 0) {
                                $s3 .= sprintf(" %s (%s in ('%s'))", $opts[0], $opts[1][1], implode("', '", $opts[1][2]));
                            }
                        } elseif($opts[1][0] == "set") {
                            if(check_array_length($opts[1][2], 0) > 0) {
                                $s3a = array();
                                foreach($opts[1][2] as $word) {
                                    $s3a[] = sprintf("find_in_set('%s', %s)", $word, $opts[1][1]);
                                }
                                $s3 .= sprintf(" %s (%s)", $opts[0], implode(" and ", $s3a));
                            }
                        } elseif($opts[1][0] == "interval") {
                            $s3u = array("s" => 1, "m" => 60, "h" => 120, "d" => 86400);
                            // todo
                        } else {
                            $ssts = array(
                                "eq" => "=",
                                "lt" => "<",
                                "lte" => "<=",
                                "gt" => ">",
                                "gte" => ">=",
                                "not" => "<>"
                            );
                            $opfield = $opts[1][1];
                            $opcode = get_value_in_array($opts[1][0], $ssts, $opts[1][0]);
                            $opvalue = $opts[1][2];
                            // Fixed issue: mysql where clause not working if column value is null #91
                            if($opcode == "<>") {
                                if(is_null($opvalue)) {
                                    $s3 .= sprintf(" %s (%s is not null)", $opts[0], $opfield);
                                } else {
                                    $s3 .= sprintf(" %s (%s %s '%s' or %s is null)", $opts[0], $opfield, $opcode, $opvalue, $opfield);
                                }
                            } else {
                                $s3 .= sprintf(" %s (%s %s '%s')", $opts[0], $opfield, $opcode, $opvalue);
                            }
                        }
                    } elseif(check_array_length($opts, 2) == 0) {
                        $s3 .= sprintf(" %s (%s)", $opts[0], $opts[1]);
                    }
                }
            }
        }

        if(!array_key_empty("sql_where", $options)) {
            $s3 .= sprintf(" %s", $options['sql_where']);
        }

        // set start prefix
        $s3 = trim($s3);
        $s3a = explode(" ", $s3);
        if(in_array($s3a[0], array("and", "or"))) {
            $sp = ($s3a[0] == "and" ? "1" : "0");
        } else {
            $sp = "1";
        }

        return sprintf("%s %s", $sp, $s3);
    }
}

if(!is_fn("check_table_is_separated")) {
    function check_table_is_separated($tablename) {
        $config = get_config();
        $db_separated_tables = explode(",", $config['db_separated_tables']);
        return in_array($tablename, $db_separated_tables);
    }
}

if(!is_fn("get_db_tablenames")) {
    function get_db_tablenames($tablename, $end_dt="", $start_dt="") {
        $tablenames = array();

        $is_separated = check_table_is_separated($tablename);
        if(!$is_separated) {
            $tablenames[] = $tablename;
        } else {
            $a = empty($end_dt);
            $b = empty($start_dt);
            $c = array( (!$a && !$b), ($a && !$b), (!$a && $b) );
            
            $setwheres = array();
            foreach($c as $k=>$v) {
                if($v !== false) {
                    switch($v) {
                        case 0:
                            $setwheres[] = array("and", array("lte", "datetime", $end_dt));
                            $setwheres[] = array("and", array("gte", "datetime", $start_dt));
                            break;
                        case 1:
                            $setwheres[] = array("and", array("gte", "datetime", $start_dt));
                            break;
                        case 2:
                            $setwheres[] = array("and", array("lte", "datetime", $end_dt));
                            break;
                    }
                }
            }

            $bind = false;
            $sql = get_bind_to_sql_select(sprintf("%s.tables", $tablename), $bind, array(
                "setwheres" => $setwheres,
                "setorders" => array(
                    array("desc", "datetime")
                )
            ));

            $rows = exec_db_fetch_all($sql, $bind);
            foreach($rows as $row) {
                $tablenames[] = $row['table_name'];
            }
        }

        return $tablenames;
    }
}

if(!is_fn("get_bind_to_sql_fields")) {
    function get_bind_to_sql_fields($fields) {
        $s1a = array();

        foreach($setfields as $k=>$v) {
            // add
            if(!array_keys_empty("add", $v)) {
                $s1a[$k] = sprintf("(%s + %s)", $k, $v['add']);
            }

            // sub
            if(!array_keys_empty("sub", $v)) {
                $s1a[$k] = sprintf("(%s - %s)", $k, $v['sub']);
            }

            // mul
            if(!array_key_empty("mul", $v)) {
                $s1a[$k] = sprintf("(%s * %s)", $k, $v['mul']);
            }

            // div
            if(!array_key_empty("div", $v)) {
                $s1a[$k] = sprintf("(%s / %s)", $k, $v['div']);
            }

            // eval (warning: do not use if you did not understand enough)
            if(!array_key_empty("eval", $v)) {
                $s1a[$k] = sprintf("(%s)", $k, $v['eval']);
            }

            // concat and delimiter
            if(!array_keys_empty("concat", $v)) {
                $delimiter = get_value_in_array("delimiter", $v, ",");
                $s1a[$k] = sprintf("concat(%s)", implode(sprintf(", '%s', ", $delimiter), $v['concat']));
            }

            // group_concat and delimiter, condition
            if(!array_keys_empty("group_concat", $v)) {
                $arguments = $v['group_concat'];
                $delimiter = get_value_in_array("delimiter", $v, ",");
                // group_concat(a, b, c); a=fieldname or value(if true), b=condition, c=fieldname or value(if false)
                if(check_array_length($arguments, 3) == 0) {
                    $s1a[$k] = sprintf("group_concat(if(%s, '%s', '%s'))", $arguments[1], make_safe_argument($arguments[0]), make_safe_argument($arguments[2]));
                } elseif(check_array_length($arguments, 2) == 0) {
                    $s1a[$k] = sprintf("group_concat(if(%s, '%s', null))", $arguments[1], make_safe_argument($arguments[0]));
                } elseif(check_array_length($arguments, 1) == 0) {
                    $s1a[$k] = sprintf("group_concat(%s)", $arguments[0]);
                } else {
                    $s1a[$k] = sprintf("group_concat(%s)", $arguments);
                }
            }

            // use mysql function
            if(!array_key_empty("call", $v)) {
                if(check_array_length($v['call'], 1) > 0) {
                    // add to s1a
                    $s1a[$k] = sprintf("%s(%s)", $v['call'][0], implode(", ", array_slice($v['call'], 1)));
                }
            }
            
        }
        
        return $s1a;
    }
}

if(!is_fn("get_bind_to_sql_select")) {
    // warning: variable k is not protected. do not use variable k and external variable without filter
    function get_bind_to_sql_select($tablename, $bind=array(), $options=array()) {
        $sql = "select %s from `%s` where %s %s %s";

        // is_separated: check it is seperated table
        $is_separated = check_table_is_separated($tablename);

        // s1: select fields
        $s1 = "";
        if(!array_key_empty("fieldnames", $options)) {
            $s1 .= (check_array_length($options['fieldnames'], 0) > 0) ? implode(", ", $options['fieldnames']) : "*";
        } elseif(array_key_equals("getcount", $options, true)) {
            $s1 .= sprintf("count(%s) as value", ($options['getcount'] === true ? "*" : $options['getcount']));
        } elseif(!array_key_empty("getsum", $options)) {
            $s1 .= sprintf("sum(%s) as value", $options['getsum']);
        } else {
            $s1 .= "*";
        }

        // s1a: s1 additonal (set new fields)
        $s1a = array();
        if(array_key_is_array("setfields", $options)) {
            $setfields = $options['setfields'];
            $s1a = get_bind_to_sql_fields($setfields);
        }

        // s2: set table name
        $s2 = "";
        if(!empty($tablename)) {
            $s2 .= $tablename;
        } else {
            set_error("tablename can not empty");
            show_errors();
        }

        // s3: fields of where clause
        $s3 = get_bind_to_sql_where($bind, $options);
        
        // s3i: set groups
        $s3i = "";
        if(!array_key_empty("setgroups", $options)) {
            $s3i = sprintf(" group by `%s`", implode("`, `", get_array($options['setgroups'])));
            $s3 .= $s3i;
        }

        // s4: set orders
        $s4 = "";
        $s4a = array();
        if(!array_key_empty("setorders", $options)) {
            if(is_array($options['setorders'])) {
                $s4 .= "order by ";
                foreach($options['setorders'] as $opts) {
                    if(check_is_string_not_array($opts)) {
                        $s4a[] = $opts;
                    } elseif(check_array_length($opts, 2) == 0) {
                        // example: array("desc", "datetime")
                        $s4a[] = sprintf("%s %s", get_value_in_array($opts[1], $s1a, $opts[1]), $opts[0]);
                    }
                }
                $s4 .= implode(", ", $s4a);
            }
        }

        // s5: set page and limit
        $s5 = "";
        if(!array_keys_empty(array("setpage", "setlimit"), $options)) {
            $s5 .= get_page_range($options['setpage'], $options['setlimit']);
        }
        
        // sql: make completed SQL
        if(!$is_separated) {
            $sql = sprintf($sql, $s1, $s2, $s3, $s4, $s5);
        } else {
            $separated_sqls = array();
            $tablenames = get_db_tablenames($tablename);
            foreach($tablenames as $_tablename) {
                $separated_sqls[] = sprintf($sql, $s1, $_tablename, $s3, $s4, $s5);
            }
            $sql = sprintf("%s", implode(" union all ", $separated_sqls));
        }

        return $sql;
    }
}

if(!is_fn("get_bind_to_sql_update_set")) {
    // warning: variable k is not protected. do not use variable k and external variable without filter
    function get_bind_to_sql_update_set($bind, $options=array()) {
        $sql = "";
        
        // set variables
        $sa = array();
        
        // setkeys
        $setkeys = get_array(get_value_in_array("setkeys", $options, false));

        // do process
        foreach($bind as $k=>$v) {
            if(!in_array($k, $setkeys)) {
                $sa[] = sprintf("%s = :%s", $k, $k);
            }
        }

        // set SQL statements
        $sql = implode(", ", $sa);

        return $sql;
    }
}

if(!is_fn("get_bind_to_sql_update")) {
    function get_bind_to_sql_update($tablename, $bind, $options=array()) {
        $sql = "update %s set %s where %s";
        
        // bind `where` clause 
        $_bind_K = array();
        $_bind_V = array();

        // setkeys
        $setkeys = get_array(get_value_in_array("setkeys", $options, false));
        foreach($bind as $k=>$v) {
            if(in_array($k, $setkeys)) {
                $_bind_K[$k] = $v;
            } else {
                $_bind_V[$k] = $v;
            }
        }
        
        // s1: make `tablename` clause
        $s1 = $tablename;

        // s2: make 'update set' clause
        $s2 = get_bind_to_sql_update_set($bind, $options);

        // s3: make 'where' clause
        $s3 = get_bind_to_sql_where($_bind_K, $options);

        // make completed statements
        $sql = get_db_binded_sql(sprintf($sql, $s1, $s2, $s3), $bind);

        return $sql;
    }
}

if(!is_fn("get_bind_to_sql_delete")) {
    function get_bind_to_sql_delete($tablename, $bind, $options=array()) {
        $sql = sprintf("delete from `%s` where %s", $tablename, get_bind_to_sql_where($bind, $options));
        return $sql;
    }
}

if(!is_fn("get_bind_to_sql_past_minutes")) {
    function get_bind_to_sql_past_minutes($fieldname, $minutes=5) {
        $sql_past_minutes = "";
        if($minutes > 0) {
            $sql_past_minutes = sprintf(" and %s > DATE_SUB(now(), INTERVAL %d MINUTE)", $fieldname, $minutes);
        }
        return $sql_past_minutes;
    }
}

// SQL eXtensible
if(!is_fn("get_bind_to_sqlx")) {
    function get_bind_to_sqlx($filename, $bind, $options=array()) {
        $result = false;
        $sql = read_storage_file(get_file_name($filename, "sqlx"), array(
            "storage_type" => "sqlx"
        ));

        if(!empty($sql)) {
            $result = get_db_binded_sql($sql, $bind);
        }
        return $result;
    }
}

// alias sql_query from exec_db_query
if(!is_fn("sql_query")) {
    function sql_query($sql, $bind=array(), $options=array()) {
        return exec_db_query($sql, $bind, $options);
    }
}

// get timediff
if(!is_fn("get_timediff_on_query")) {
    function get_timediff_on_query($a, $b) {
        $dt = 0;

        $sql = "select timediff(:a, :b) as dt";
        $bind = array(
            "a" => $a,
            "b" => $b
        );
        $row = exec_db_fetch($sql, $bind);
        $dt = get_value_in_array("dt", $row, $dt);

        return $dt;
    }
}

// make sql statement to create table
if(!is_fn("get_bind_to_sql_create")) {
    function get_bind_to_sql_create($schemes, $options=array()) {
        $sql = "";
        
        $_prefix = get_value_in_array("prefix", $options, "");
        $_suffix = get_value_in_array("suffix", $options, "");
        $_tablename = get_value_in_array("tablename", $options, "");
        $_temporary = get_value_in_array("temporary", $options, false);
        $_engine = get_value_in_array("engine", $options, false);
        $_schemes = array();

        if(!empty($_tablename)) {
            $tablename = sprintf("%s%s%s", $_prefix, $_tablename, $_suffix);
            
            foreach($schemes as $k=>$v) {
                if(is_array($v)) {
                    $_argc = count($v);
                    if($_argc == 1) {
                        $_schemes[] = sprintf("`%s` %s", $k, $v[0]);
                    } elseif($_argc == 2) {
                        $_schemes[] = sprintf("`%s` %s(%s)", $k, $v[0], $v[1]);
                    } elseif($_argc == 3) {
                        $_schemes[] = sprintf("`%s` %s(%s) %s", $k, $v[0], $v[1], ($v[2] === true ? "not null" : ""));
                    }
                }
            }

            if($_temporary !== false) {
                $sql .= sprintf("create temporary table if not exists `%s` (%s)", $tablename, implode(",", $_schemes));
            } else {
                $sql .= sprintf("create table if not exists `%s` (%s)", $tablename, implode(",", $_schemes));
            }

            if($_engine !== false) {
                $sql .= sprintf(" engine=%s", $_engine);
            }
        }

        return $sql;
    }
}

// table creation
if(!is_fn("exec_db_table_create")) {
    function exec_db_table_create($schemes, $tablename, $options=array()) {
        $_prefix = get_value_in_array("prefix", $options, "");
        $_suffix = get_value_in_array("suffix", $options, "");
        $_tablename = sprintf("%s%s%s", $_prefix, $tablename, $_suffix);
        $_tablename_p = sprintf("%s%s", $_prefix, $tablename);
        $_tablename_s = sprintf("%s%s", $tablename, $_suffix);
        $_tablename_t = sprintf("%s.tables", $_tablename_p);

        // get index options
        $config = get_config();
        $setindex = get_value_in_array("setindex", $options, false);
        $setunique = get_value_in_array("setunique", $options, false);
        $setfulltext = get_value_in_array("setfulltext", $options, false);
        $setspatial = get_value_in_array("setspatial", $options, false);

        // check if exists table
        $bind = array(
            "TABLE_SCHEMA" => $config['db_name'],
            "TABLE_NAME" => $_tablename
        );
        $sql = "select TABLE_NAME from information_schema.tables where TABLE_SCHEMA = :TABLE_SCHEMA and TABLE_NAME = :TABLE_NAME";
        $rows = exec_db_fetch_all($sql, $bind);
        foreach($rows as $row) {
            return $row['TABLE_NAME'];
        }

        // create table
        $sql = get_bind_to_sql_create($schemes, array(
            "tablename" => $_tablename
        ));

        if(!exec_db_query($sql)) {
            return false;
        } else {
            if($_suffix != ".tables") {
                // create meta table
                $schemes_t = array(
                    "table_name" => array("varchar", 255),
                    "datetime" => array("datetime")
                );
                $_tablename_t = exec_db_table_create($schemes_t, $tablename, array(
                    "prefix" => $_prefix,
                    "suffix" => ".tables",
                    "setindex" => array(
                        "index_1" => array("datetime")
                    ),
                    "setunique" => array(
                        "unique_1" => array("table_name")
                    )
                ));

                // add table name to meta table
                $bind = array(
                    "table_name" => $_tablename,
                    "datetime" => get_current_datetime()
                );
                $sql = get_bind_to_sql_insert($_tablename_t, $bind);
                exec_db_query($sql, $bind);
            }

            // create index
            foreach($setindex as $k=>$v) {
                $sql = sprintf("create index `%s` on `%s` (%s)", $k, $_tablename, implode(", ", $v));
                exec_db_query($sql);
            }

            // create unique (type of index)
            foreach($setunique as $k=>$v) {
                $sql = sprintf("create unique index `%s` on `%s` (%s)", $k, $_tablename, implode(", ", $v));
                exec_db_query($sql);
            }
            
            // create fulltext (type of index)
            foreach($setfulltext as $k=>$v) {
                $sql = sprintf("create fulltext index `%s` on `%s` (%s)", $k, $_tablename, implode(", ", $v));
                exec_db_query($sql);
            }
            
            // create spatial(geometry) (type of index)
            foreach($setspatial as $k=>$v) {
                $sql = sprintf("create spatial index `%s` on `%s` (%s)", $k, $_tablename, implode(", ", $v));
                exec_db_query($sql);
            }
        }

        return $_tablename;
    }
}

if(!is_fn("exec_db_table_drop")) {
    function exec_db_table_drop($tablename, $end_dt="", $start_dt="") {
        $flag = true;

        $tablenames = get_db_tablenames($tablename, $end_dt, $start_dt);
        foreach($tablenames as $_tablename) {
            $sql = sprintf("drop table `%s`", $_tablename);
            $flag &= exec_db_query($sql);
        }

        return $flag;
    }
}

if(!is_fn("exec_db_table_update")) {
    function exec_db_table_update($tablename, $bind=array(), $options=array()) {
        $flag = true;

        $tablenames = get_db_tablenames($tablename);
        foreach($tablenames as $_tablename) {
            $sql = get_bind_to_sql_update($tablename, $bind, $options);
            $flag &= exec_db_query($sql);
        }
        
        return $flag;
    }
}

if(!is_fn("exec_db_table_insert")) {
    function exec_db_table_insert($tablename, $bind=array(), $options=array()) {
        $flag = true;

        // set number of copy
        $num_copy = get_value_in_array("setcopy", $options, 1);

        // get tablenames
        $tablenames = get_db_tablenames($tablename);
        if($num_copy > 0) {
            $num_tables = count($tablenames);
            $num_copy = min($num_copy, $num_tables);
            if($num_copy < $num_tables) {
                $tablenames = array_slice(get_db_tablenames($tablename), 0, $num_copy);
            }
        }

        // do copy
        foreach($tablenames as $_tablename) {
            $sql = get_bind_to_sql_insert($tablename, $bind, $options);
            $flag &= exec_db_query($sql);
        }

        return $flag;
    }
}

// temporary table creation
if(!is_fn("exec_db_temp_create")) {
    function exec_db_temp_create($schemes, $options=array()) {
        $flag = false;
        
        // set tablename
        $tablename = make_random_id();

        // set track information
        $temptables = get_array(get_shared_var("temptables"));
        $temptables[] = $tablename;
        set_shared_var("temptables", $temptables);

        // set engine (default: memory)
        $_engine = get_value_in_array("engine", $options, "memory");

        // create temporary table
        $sql = get_bind_to_sql_create($schemes, array(
            "tablename" => $tablename,
            "temporary" => true,
            "engine" => $_engine
        ));
        $flag = exec_db_query($sql);

        return ($flag ? $tablename : false);
    }
}

if(!is_fn("exec_db_temp_start")) {
    function exec_db_temp_start($sql, $bind=array(), $options=array()) {
        $flag = false;

        // set engine (default: memory)
        $_engine = get_value_in_array("engine", $options, "memory");

        // create temporary table
        $tablename = make_random_id();
        if($_engine !== false) {
            $sql = sprintf("create temporary table if not exists `%s` %s", $tablename, $sql);
            $flag = exec_db_query($sql, $bind);
        } else {
            $sql = sprintf("create temporary table if not exists `%s` engine=%s %s", $tablename, $_engine, $sql);
            $flag = exec_db_query($sql, $bind);
        }

        return ($flag ? $_tablename : false);
    }
}

// clear specific temporary table
if(!is_fn("exec_db_temp_end")) {
    function exec_db_temp_end($tablename) {
        $sql = sprintf("drop temporary table `%s`", $tablename);
        return exec_db_query($sql);
    }
}

// clear temporery tables
if(!is_fn("exec_db_temp_clear")) {
    function exec_db_temp_clear() {
        $temptables = get_array(get_shared_var("temptables"));
        foreach($temptables as $tablename) {
            exec_db_temp_end($tablename);
        }
    }
}

// close db connection
if(!is_fn("close_db_connect")) {
    function close_db_connect() {
        $dbc = get_shared_var("dbc");
        $dbc->query("KILL CONNECTION_ID()");
        set_shared_var("dbc", null);
    }
}

// json decode to assoc
if(!is_fn("json_decode_assoc")) {
    function json_decode_assoc($data) {
        if(loadHelper("json.format")) {
            return json_decode_ex($data, array("assoc" => true));
        }
    }
}
