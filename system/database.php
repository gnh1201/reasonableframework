<?php
/**
 * @file database.php
 * @date 2018-04-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Database module
 */

if(!check_function_exists("get_db_driver")) {
    function get_db_driver() {
        $config = get_config();
        return get_value_in_array("db_driver", $config, false);
    }
}

if(!check_function_exists("check_db_driver")) {
    function check_db_driver($db_driver) {
        return (get_db_driver() == $db_driver);
    }
}

if(!check_function_exists("get_db_connect")) {
    function get_db_connect($a=3, $b=0) {
        $conn = false;
        $config = get_config();
        $db_driver = get_db_driver();

        if(in_array($db_driver, array("mysql", "mysql.pdo"))) {
            try {
                $conn = new PDO(
                    sprintf(
                        "mysql:host=%s;dbname=%s;charset=utf8",
                        $config['db_host'],
                        $config['db_name']
                    ),
                    $config['db_username'],
                    $config['db_password'],
                    array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                    )
                );
                //$conn->query("SET NAMES 'utf8'");
            } catch(Exception $e) {
                if($b > $a) {
                    set_error($e->getMessage());
                    show_errors();
                } else {
                    $b++;
                    sleep(0.03);
                    $conn = get_db_connect($a, $b);
                }
            }
        } elseif(loadHelper("database.alt")) {
            $conn = call_user_func("get_db_alt_connect", $db_driver);
        }

        return $conn;
    }
}

if(!check_function_exists("exec_stmt_query")) {
    function exec_stmt_query($sql, $bind=array()) {
        $stmt = get_db_stmt($sql, $bind);
        $stmt->execute();

        return $stmt;
    }
}

if(!check_function_exists("get_dbc_object")) {
    function get_dbc_object($renew=false) {
        $dbc = get_scope("dbc");

        if($renew) {
            set_scope("dbc", get_db_connect());
        }

        return get_scope("dbc");
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
            $bind_keys = get_array(array_keys($bind));
            if(check_array_length($bind_keys, 0) > 0) {
                // 2018-08-19: support lower php version (not supported anonymous function)
                usort($bind_keys, "compare_db_key_length");

                // bind values
                foreach($bind_keys as $k) {
                    $sql = str_replace(":" . $k, "'" . addslashes($bind[$k]) . "'", $sql);
                }
            }
        return $sql;
    }
}

if(!check_function_exists("get_db_stmt")) {
    function get_db_stmt($sql, $bind=array(), $bind_pdo=false, $show_sql=false) {
        $sql = !$bind_pdo ? get_db_binded_sql($sql, $bind) : $sql;
        $stmt = get_dbc_object()->prepare($sql);

        if($show_sql) {
            set_error($sql, "DATABASE-SQL");
            show_errors(false);
        }

        // bind parameter by PDO statement
        if($bind_pdo) {
            if(check_array_length($bind, 0) > 0) {
                foreach($bind as $k=>$v) {
                    $stmt->bindParam(':' . $k, $v);
                }
            }
        }

        return $stmt;
    }
}

if(!check_function_exists("get_db_last_id")) {
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

if(!check_function_exists("exec_db_query")) {
    function exec_db_query($sql, $bind=array(), $options=array()) {
        $dbc = get_dbc_object();

        $validOptions = array();
        $optionAvailables = array("is_check_count", "is_commit", "display_error", "show_debug", "show_sql");
        foreach($optionAvailables as $opt) {
            if(!array_key_empty($opt, $options)) {
                $validOptions[$opt] = $options[$opt];
            } else {
                $validOptions[$opt] = false;
            }
        }
        extract($validOptions);

        $flag = false;
        $is_insert_with_bind = false;

        $sql_terms = explode(" ", $sql);
        if($sql_terms[0] == "insert") {
            $stmt = get_db_stmt($sql);
            if(check_array_length($bind, 0) > 0) {
                $is_insert_with_bind = true;
            }
        } else {
            if($show_sql) {
                $stmt = get_db_stmt($sql, $bind, false, true);
            } else {
                $stmt = get_db_stmt($sql, $bind);
            }
        }

        if($is_commit) {
            $dbc->beginTransaction();
        }

        // execute statement (insert->execute(bind) or if not, sql->bind->execute)
        $stmt_executed = $is_insert_with_bind ? $stmt->execute($bind) : $stmt->execute();

        if($show_debug) {
            $stmt->debugDumpParams();
        }

        if($display_error) {
            $error_info = $stmt->errorInfo();
            if(check_array_length($error_info, 0) > 0) {
                set_error(implode(" ", $error_info), "DATABASE-ERROR");
            }
            show_errors(false);
        }

        if($is_check_count == true) {
            if($stmt_executed && $stmt->rowCount() > 0) {
                $flag = true;
            }
        } else {
            $flag = $stmt_executed;
        }

        if($is_commit) {
            $dbc->commit();
        }

        if($flag === false) {
            set_error(get_hashed_text($sql), "DATABASE-QUERY-FAILURE");
        }

        return $flag;
    }
}

if(!check_function_exists("exec_db_fetch_all")) {
    function exec_db_fetch_all($sql, $bind=array(), $options=array()) {
        $response = array();
        $length = 0;
        $is_not_countable = false;
        
        $rows = array();
        $stmt = get_db_stmt($sql, $bind);

        if($stmt->execute() && $stmt->rowCount() > 0) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if(array_key_equals("do_count", $options, true)) {
            $count_sql = sprintf("select count(*) as cnt from (%s) a", get_db_binded_sql($sql, $bind));
            $count_data = exec_db_fetch($count_sql);
            $length = get_value_in_array("cnt", $count_data, $length);
        } elseif(array_key_equals("do_count", $options, "fn_count")) {
            $length = count($rows);
        } else {
            $response = $rows;
            $is_not_countable = true;
        }

        if(!$is_not_countable) {
            $response = array(
                "length" => $length,
                "data" => $rows,
            );
        }
        
        return $response;
    }
}

if(!check_function_exists("exec_db_fetch")) {
    function exec_db_fetch($sql, $bind=array(), $start=0, $bind_limit=false) {
        $fetched = NULL;
        $rows = array();

        if($bind_limit == true) {
            $sql = $sql . " limit 1";
        }
        $rows = exec_db_fetch_all($sql, $bind);

        if(check_array_length($rows, $start) > 0) {
            $idx = 0;
            foreach($rows as $row) {
                if($idx >= $start) {
                    $fetched = $row;
                    break;
                }
                $idx++;
            }
        }

        return $fetched;
    }
}

if(!check_function_exists("get_page_range")) {
    function get_page_range($page=1, $limit=16) {
        $append_sql = "";

        if($page < 1) {
            $page = 1;
        }

        if($limit > 0) {
            $record_start = ($page - 1) * $limit;
            $number_of_records = $limit;
            $append_sql .= sprintf(" limit %s, %s", $record_start, $number_of_records);
        }

        return $append_sql;
    }
}

if(!check_function_exists("get_bind_to_sql_insert")) {
    function get_bind_to_sql_insert($tablename, $bind) {
        $bind_keys = array_keys($bind);
        $sql = "insert into %s (%s) values (:%s)";

        $s1 = $tablename;
        $s2 = implode(", ", $bind_keys);
        $s3 = implode(", :", $bind_keys);

        $sql = sprintf($sql, $s1, $s2, $s3);

        return $sql;
    }
}

if(!check_function_exists("get_bind_to_sql_where")) {
    // warning: variable k is not protected. do not use variable k and external variable without filter
    function get_bind_to_sql_where($bind, $excludes=array()) {
        $sql_where = "";

        foreach($bind as $k=>$v) {
            if(!in_array($k, $excludes)) {
                $sql_where .= sprintf(" and %s = :%s", $k, $k);
            }
        }

        return $sql_where;
    }
}

if(!check_function_exists("get_bind_to_sql_update_set")) {
    // warning: variable k is not protected. do not use variable k and external variable without filter
    function get_bind_to_sql_update_set($bind, $excludes=array()) {
        $sql_update_set = "";
        $set_items = "";

        foreach($bind as $k=>$v) {
            if(!in_array($k, $excludes)) {
                $set_items[] = sprintf("%s = :%s", $k, $k);
            }
        }
        $sql_update_set = implode(", ", $set_items);

        return $sql_update_set;
    }
}

if(!check_function_exists("get_bind_to_sql_select")) {
    // warning: variable k is not protected. do not use variable k and external variable without filter
    function get_bind_to_sql_select($tablename, $bind=array(), $options=array()) {
        $sql = "select %s from %s where 1 %s %s %s";

        // s1: select fields
        $s1 = "";
        if(!array_key_empty("fieldnames", $options)) {
            $s1 .= (check_array_length($options['fieldnames'], 0) > 0) ? implode(", ", $options['fieldnames']) : "*";
        } elseif(array_key_equals("getcnt", $options, true)) {
            $s1 .= "count(*) as cnt";
        } elseif(!array_key_empty("getsum", $options)) {
            $s1 .= sprintf("sum(%s) as sum", $options['getsum']);
        } else {
            $s1 .= "*";
        }
        
        // s1a: s1 additonal (set new fields)
        $s1a = array();
        if(array_key_is_array("setfields", $options)) {
            $addfields = $options['setfields'];

            foreach($addfields as $k=>$v) {
                // concat and delimiter
                if(!array_keys_empty(array("concat", "delimiter"), $v)) {
                    // add to s1a
                    $s1a[$k] = sprintf("concat(%s)", implode(sprintf(", '%s', ", $v['delimiter']), $v['concat']));
                }

                // use function
                if(!array_key_empty("call_func", $v)) {
                    if(check_array_length($v['call_func'], 1) > 0) {
                        // add to s1a
                        $s1a[$k] = sprintf("%s(%s)", $v['call_func'][0], implode(", ", array_slice($v['call_func'], 1)));
                    }
                }
            }
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
        $s3 = get_bind_to_sql_where($bind);
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
                        if($opts[1][0] == "like") {
                            if(check_array_length($opts[1][2], 0) > 0) {
                                $s3a = array();
                                foreach($opts[1][2] as $word) {
                                    $s3a[] = sprintf("%s like '%s'", $s1a[$opts[1][1]], "%{$word}%");
                                }
                                $s3 .= sprintf(" %s (%s)", $opts[0], implode(" and ", $s3a));
                            } else {
                                $s3 .= sprintf(" %s (%s like %s)", $opts[0], $s1a[$opts[1][1]], "'%{$opts[1][2]}%'");
                            }
                        } elseif($opts[1][0] == "in") {
                            if(check_array_length($opts[1][2], 0) > 0) {
                                $s3 .= sprintf(" %s (%s in ('%s'))", $opts[0], $opts[1][1], implode("', '", $opts[1][2]));
                            }
                        } else {
                            $ssts = array(
                                "eq" => "=",
                                "lt" => "<",
                                "lte" => "<=",
                                "gt" => ">",
                                "gte" => ">=",
                                "not" => "<>"
                            );
                            $opcode = get_value_in_array($opts[1][0], $ssts, $opts[1][0]);
                            if(!empty($opcode)) {
                                $s3 .= sprintf(" %s (%s %s '%s')", $opts[0], $opts[1][1], $opcode, $opts[1][2]);
                            }
                        }
                    } elseif(check_array_length($opts, 2) == 0) {
                        $s3 .= sprintf(" %s (%s)", $opts[0], $opts[1]);
                    }
                }
            }
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
                        $s4a[] = sprintf("%s %s", $opts[1], $opts[0]);
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

        // sql: make completed sql
        $sql = sprintf($sql, $s1, $s2, $s3, $s4, $s5);

        return $sql;
    }
}

if(!check_function_exists("get_bind_to_sql_update")) {
    function get_bind_to_sql_update($tablename, $bind, $filters=array(), $options=array()) {
        // process filters
        $excludes = array();
        $bind_wheres = array();
        foreach($bind as $k=>$v) {
            if(!array_key_empty($k, $filters)) {
                if($filters[$k] === true) {
                    $bind_wheres[$k] = $v;
                    $excludes[] = $k;
                } else {
                    $bind_wheres[$k] = $filters[$k];
                }
            }
        }

        // make sql 'where' clause
        $sql_where = get_db_binded_sql(get_bind_to_sql_where($bind_wheres), $bind_wheres);
        if(!array_key_empty("sql_where", $options)) {
            $sql_where .= $options['sql_where'];
        }

        // make sql 'update set' clause
        $sql_update_set = get_bind_to_sql_update_set($bind, $excludes);

        // make completed sql statement
        $sql = sprintf("update %s set %s where 1 %s", $tablename, $sql_update_set, $sql_where);

        return $sql;
    }
}

if(!check_function_exists("get_bind_to_sql_delete")) {
    function get_bind_to_sql_delete($tablename, $bind, $options=array()) {
        $sql = "delete from %s where 1" . get_bind_to_sql_where($bind);
        return $sql;
    }
}

if(!check_function_exists("get_bind_to_sql_past_minutes")) {
    function get_bind_to_sql_past_minutes($fieldname, $minutes=5) {
        $sql_past_minutes = "";
        if($minutes > 0) {
            $sql_past_minutes = sprintf(" and %s > DATE_SUB(now(), INTERVAL %d MINUTE)", $fieldname, $minutes);
        }
        return $sql_past_minutes;
    }
}

// SQL eXtensible
if(!check_function_exists("get_bind_to_sqlx")) {
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
if(!check_function_exists("sql_query")) {
    function sql_query($sql, $bind=array(), $options=array()) {
        return exec_db_query($sql, $bind, $options);
    }
}

// get timediff
if(!check_function_exists("get_timediff_on_query")) {
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

// get assoc from json raw data
if(!check_function_exists("json_decode_to_assoc")) {
    function json_decode_to_assoc($data) {
        $result = array();

        $invalid_fn = array(
            "NO_FUNCTION_JSON_DECODE" => "json_decode",
            "NO_FUNCTION_JSON_LAST_ERROR" => "json_last_error",
        );

        $error = check_invaild_function($invalid_fn);
        if($error < 0) {
            $obj = @json_decode($data, true);
            $result = (@json_last_error() === 0) ? $obj : $result;
        }

        return $result;
    }
}

// close db connection
if(!check_function_exists("close_db_connect")) {
    function close_db_connect() {
        $dbc = get_scope("dbc");
        $dbc->close();
        set_scope("dbc", null);
    }
}

// set scope dbc
set_scope("dbc", get_db_connect());
