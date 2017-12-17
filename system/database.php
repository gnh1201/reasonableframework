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