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


function get_db_stmt($sql, $bind=array()) {
	global $dbc;

	$stmt = $dbc->prepare($sql);
	if(count($bind) > 0) {
		foreach($bind as $k=>$v) {
			$stmt->bindParam(':' . $k, $v);
		}
	}
	return $stmt;
}

function get_db_last_id() {
	global $dbc;

	return $dbc->lastInsertId();
}

function exec_db_query($sql, $bind=array(), $options=array()) {
	global $dbc;

	$flag = false;
	$stmt = get_db_stmt($sql, $bind);
	
	$validOptions = array();
	$optionAvailables = array("is_check_count", "is_commit");
	foreach($optionAvailables as $opt) {
		if(!array_key_empty($opt, $options)) {
			$validOptions[$opt] = $options[$opt];
		} else {
			$validOptions[$opt] = false;
		}
	}
	extract($validOptions);
	
	if($is_commit) {
		$dbc->beginTransaction();
	}

	if($is_check_count == true) {
		if($stmt->execute() && $stmt->rowCount() > 0) {
			$flag = true;
		}
	} else {
		$flag = $stmt->execute();
	}

	if($is_commit) {
		$dbc->commit();
	}

	return $flag;
}

// set global db connection variable
$dbc = get_db_connect();
