<?php
/**
 * @file database.php
 * @date 2018-01-01
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Database module for ReasonableFramework
 */

if(!function_exists("get_db_connect")) {
	function get_db_connect() {
		global $config;

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
		global $dbc;

		if($renew) {
			$dbc = get_db_connect();
		}

		return $dbc;
	}
}

if(!function_exists("get_binded_sql")) {
	function get_binded_sql($sql, $bind) {
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
		$sql = !$bind_pdo ? get_binded_sql($sql, $bind) : $sql;
		$stmt = get_dbc_object()->prepare($sql);
		
		if($show_sql) {
			var_dump($sql);
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
			var_dump($stmt->debugDumpParams());
		}

		if($display_error) {
			var_dump($stmt->errorInfo());
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

// alias sql_query from exec_db_query
if(!function_exists("sql_query")) {
	function sql_query($sql, $bind=array()) {
		return exec_db_query($sql, $bind);
	}
}

// set global db connection variable
$dbc = get_db_connect();
