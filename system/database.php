<?php
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

if(!function_exists("sql_query")) {
	function sql_query($sql, $bind=array()) {
		global $conn;

		$stmt = $conn->prepare($sql);
		if(count($bind) > 0) {
			foreach($bind as $k=>$v) {
				$stmt->bindParam(':' . $k, $v);
			}
		}

		return $stmt;
	}
}
