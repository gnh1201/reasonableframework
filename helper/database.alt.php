<?php
/**
 * @file database.alt.php
 * @date 2018-09-10
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Database alternative driver switcher
 */

if(!function_exists("exec_db_alt_callback")) {
	function exec_db_alt_callback($rules) {
		$result = false;

		foreach($rules as $rule) {
			if($rule['driver'] == $driver) {
				if(loadHelper(sprintf("database.%s", $rule['driver']))) {
					$result = function_exists($rule['callback']) ? call_user_func($rule['callback']) : $result;
				}
				break;
			}
		}
		
		return $result;
	}
}

if(!function_exists("get_db_alt_connect")) {
	function get_db_alt_connect($driver) {
		$conn = false;
		$config = get_config();

		$rules = array(
			array(
				"driver" => "mysql.pdo",
				"callback" => "get_db_mysql_pdo_connect"
			),
			array(
				"driver" => "mysql.imp",
				"callback" => "get_db_mysql_imp_connect"
			),
			array(
				"driver" => "mysql.old",
				"callback" => "get_db_mysql_old_connect"
			),
			array(
				"driver" => "oracle",
				"callback" => "get_db_oracle_connect"
			)
		);
		
		$conn = exec_db_alt_callback($rules);

		return $conn;
	}
}

if(!function_exists("exec_db_alt_query")) {
	function exec_db_alt_query($sql, $bind=array(), $driver="") {
		$result = false;

		$rules = array(
			array(
				"driver" => "mysql.pdo",
				"callback" => "exec_db_mysql_pdo_query"
			),
			array(
				"driver" => "mysql.imp",
				"callback" => "exec_db_mysql_imp_query"
			),
			array(
				"driver" => "mysql.old",
				"callback" => "exec_db_mysql_old_query"
			),
			array(
				"driver" => "oracle",
				"callback" => "exec_db_oracle_query"
			)
		);
		
		$result = exec_db_alt_callback($rules);

		return $result;
	}
}

if(!function_exists("exec_db_alt_fetch_all")) {
	function exec_db_alt_fetch_all($sql, $bind=array()) {
		$rows = array();

		$rules = array(
			array(
				"driver" => "mysql.pdo",
				"callback" => "exec_db_mysql_pdo_fetch_all"
			),
			array(
				"driver" => "mysql.imp",
				"callback" => "exec_db_mysql_imp_fetch_all"
			),
			array(
				"driver" => "mysql.old",
				"callback" => "exec_db_mysql_old_fetch_all"
			),
			array(
				"driver" => "oracle",
				"callback" => "exec_db_oracle_fetch_all"
			)
		);
		
		$rows = exec_db_alt_callback($rules);

		return $rows;
	}
}

if(!function_exists("exec_db_alt_fetch")) {
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
