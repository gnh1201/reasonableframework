<?php
/**
 * @file database.php
 * @date 2018-04-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Database module
 */

if(!function_exists("get_db_connect")) {
	function get_db_connect() {
		$config = get_config();

		try {
			$conn = new PDO(
				sprintf(
					"mysql:host=%s;dbname=%s;charset=utf8",
					$config['db_host'],
					$config['db_name']
				),
				$config['db_username'],
				$config['db_password'],
				array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
			);
			$conn->query("SET NAMES 'utf8'");
		} catch(Exception $e) {
			set_error($e->getMessage());
		}

		return $conn;
	}
}

if(!function_exists("exec_stmt_query")) {
	function exec_stmt_query($sql, $bind=array()) {
		$stmt = get_db_stmt($sql, $bind);
		$stmt->execute();

		return $stmt;
	}
}

if(!function_exists("get_dbc_object")) {
	function get_dbc_object($renew=false) {
		$dbc = get_scope("dbc");

		if($renew) {
			set_scope("dbc", get_db_connect());
		}

		return get_scope("dbc");
	}
}

if(!function_exists("get_db_binded_sql")) {
	function get_db_binded_sql($sql, $bind) {
		if(count($bind) > 0) {
			$bind_keys = array_keys($bind);

			usort($bind_keys, function($a, $b) {
				return strlen($b) - strlen($a);
			});

			foreach($bind_keys as $k) {
				$sql = str_replace(":" . $k, "'" . addslashes($bind[$k]) . "'", $sql);
			}
		}
		
		return $sql;
	}
}

if(!function_exists("get_db_stmt")) {
	function get_db_stmt($sql, $bind=array(), $bind_pdo=false, $show_sql=false) {
		$sql = !$bind_pdo ? get_db_binded_sql($sql, $bind) : $sql;
		$stmt = get_dbc_object()->prepare($sql);

		if($show_sql) {
			set_error($sql, "DATABASE-SQL");
			show_errors(false);
		}

		// bind parameter by PDO statement
		if($bind_pdo) {
			if(count($bind) > 0) {
				foreach($bind as $k=>$v) {
					$stmt->bindParam(':' . $k, $v);
				}
			}
		}

		return $stmt;
	}
}

if(!function_exists("get_db_last_id")) {
	function get_db_last_id() {
		return get_dbc_object()->lastInsertId();
	}
}

if(!function_exists("exec_db_query")) {
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
			if(count($bind) > 0) {
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
			if(count($error_info) > 0) {
				foreach($error_info as $err) {
					set_error($err, "DATABASE-ERROR");
				}
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
			set_error(md5($sql), "DATABASE-QUERY-FAILURE");
		}

		return $flag;
	}
}

if(!function_exists("exec_db_fetch_all")) {
	function exec_db_fetch_all($sql, $bind=array()) {
		$rows = array();
		$stmt = get_db_stmt($sql, $bind);

		if($stmt->execute() && $stmt->rowCount() > 0) {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		return $rows;
	}
}

if(!function_exists("exec_db_fetch")) {
	function exec_db_fetch($sql, $bind=array(), $start=0, $bind_limit=false) {
		$fetched = NULL;
		$rows = array();

		if($bind_limit == true) {
			$sql = $sql . " limit 1";
		}
		$rows = exec_db_fetch_all($sql, $bind);

		if(count($rows) > $start) {
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

if(!function_exists("get_page_range")) {
	function get_page_range($page=1, $limit=0) {
		$append_sql = "";
		
		if($limit > 0) {
			$record_start = ($page - 1) * $limit;
			$record_end = $record_start + $limit - 1;
			$append_sql .= " limit $record_start, $record_end";
		}
		
		return $append_sql;
	}
}

if(!function_exists("get_bind_to_sql_insert")) {
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

if(!function_exists("get_bind_to_sql_where")) {
	// warning: variable k is not protected. do not use variable k and external variable without filter
	function get_bind_to_sql_where($bind, $excludes=array()) {
		$sql_where = "";
		foreach($bind as $k=>$v) {
			if(!in_array($k, $excludes)) {
				$sql_where .= " and {$k} = :{$k}";
			}
		}
		return $sql_where;
	}
}

if(!function_exists("get_bind_to_sql_update_set")) {
	// warning: variable k is not protected. do not use variable k and external variable without filter
	function get_bind_to_sql_update_set($bind, $excludes=array()) {
		$sql_update_set = "";

		$set_items = "";
		foreach($bind as $k=>$v) {
			if(!in_array($k, $excludes)) {
				$set_items[] = "{$k} = :{$k}";
			}
		}
		$sql_update_set = implode(", ", $set_items);

		return $sql_update_set;
	}
}

if(!function_exists("get_bind_to_sql_past_minutes")) {
	function get_bind_to_sql_past_minutes($fieldname, $minutes=5) {
		$sql_past_minutes = "";
		if($minutes > 0) {
			$sql_past_minutes = sprintf(" and " . $fieldname . " > DATE_SUB(now(), INTERVAL %d MINUTE)", $minutes);
		}
		return $sql_past_minutes;
	}
}

// alias sql_query from exec_db_query
if(!function_exists("sql_query")) {
	function sql_query($sql, $bind=array()) {
		return exec_db_query($sql, $bind);
	}
}

// get timediff
if(!function_exists("get_timediff_on_query")) {
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
if(!function_exists("json_decode_to_assoc")) {
	function json_decode_to_assoc($data) {
		$result = array();
		
		$func_rules = array(
			"json_decode" => "Dose not exists json_decode function",
			"json_last_error" => "Dose not exists json_last_error function",
		);

		if(check_function_exists($func_rules)) {
			$obj = @json_decode($data, true);
			$result = (@json_last_error() === 0) ? $obj : $result;
		}

		return $result;
	}
}

// close db connection
if(!function_exists("close_db_connect")) {
	function close_db_connect() {
		$dbc = get_scope("dbc");
		$dbc->close();
		set_scope("dbc", null);
	}
}

// set scope dbc
set_scope("dbc", get_db_connect());
