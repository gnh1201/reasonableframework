<?php
/**
 * @file database.alt.php
 * @date 2018-09-10
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Database alternative connecter 
 */

if(function_exists("get_db_alt_connect")) {
	function get_db_alt_connect($driver) {
		$conn = false;
		
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

		foreach($rules as $rule) {
			if($rule['driver'] == $driver) {
				if(loadHelper(sprintf("database.%s", $rule['driver']))) {
					$conn = function_exists($rule['callback']) ? call_user_func($rule['callback']) : $conn;
				}
				break;
			}
		}

		return $conn;
	}
}

if(!function_exists("exec_db_alt_sql_query")) {
	function exec_db_alt_sql_query($sql, $bind=array(), $driver="") {
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

		foreach($rules as $rule) {
			if($rule['driver'] == $driver) {
				if(loadHelper(sprintf("database.%s", $rule['driver']))) {
					$result = function_exists($rule['callback']) ? call_user_func($rule['callback']) : $conn;
				}
				break;
			}
		}

		return $result;
	}
}
