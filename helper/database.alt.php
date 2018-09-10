<?php
/**
 * @file database.alt.php
 * @date 2018-09-10
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Database alternative connecter 
 */

if(function_exists("get_db_alt_connect")) {
	function get_db_alt_connect($db_driver) {
		$conn = false;

		switch($db_driver) {
			case "mysql.pdo":
				// currently, mysql.pdo is default driver
				break;

			case "mysql.imp":
				loadHelper("database.mysql.imp");
				if(function_exists("get_db_mysql_imp_connect")) {
					$conn = get_db_mysql_imp_connect();
				}

				break;
			case "mysql.old":
				loadHelper("database.mysql.old");
				if(function_exists("get_db_mysql_old_connect")) {
					$conn = get_db_mysql_old_connect();
				}

				break;

			case "oracle":
				loadHelper("database.oracle");
				if(function_exists("get_db_oracle_connect")) {
					$conn = get_db_oracle_connect();
				}
				break;
		}

		return $conn;
	}
}
