<?php
/**
 * @file oracle.php
 * @date 2018-03-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Oracle database helper for ReasonableFramework
 */

function get_db_orable_binded_sql($sql, $bind) {
	return get_db_binded_sql($sql, $bind);
}

function get_db_oracle_stmt($sql, $bind) {
	$stmt = NULL;

	$sql = get_db_orable_binded_sql($sql, $bind);
	$stmt = oci_parse($conn, $sql);
	
	return $stmt;
}

function exec_db_oracle_connect($host, $port, $user, $password, $options=array()) {
	$conn = NULL;
	$envs = array();

	if(!function_exists("oci_connect") {
		exit("OCI (Oracle Extension for PHP) not installed!");	
	}

	if(count($options) == 0) {
		$options["ENV.NLS_LANG"] = "KOREAN_KOREA.AL32UTF8";
		$options["DESCRIPTION.ADDRESS_LIST.ADDRESS.PROTOCOL"] = "TCP";
		$options["DESCRIPTION.CONNECT_DATA.SERVER"] = "DEDICATED";
		$options["DESCRIPTION.CONNECT_DATA.SERVICE_NAME"] = "ORCL";
	}

	// set envs
	foreach($options as $k=>$v) {
		$k_terms = explode(".", $k);
		if(count($k_terms) > 1) {
			if($k_terms[0] == "ENV") {
				$envs[] = $k_terms[1] . "=" . $options[$k];
			}
		}
	}

	foreach($envs as $env) {
		putenv($env);
	}

	// set host, port
	$options["DESCRIPTION.ADDRESS_LIST.ADDRESS.HOST"] = $host;
	$options["DESCRIPTION.ADDRESS_LIST.ADDRESS.PORT"] = $port;

	$dbsid = "(
	  DESCRIPTION =
	  (ADDRESS_LIST = 
	   (ADDRESS = 
	    (PROTOCOL = " . $options["DESCRIPTION.ADDRESS_LIST.ADDRESS.PROTOCOL"] . ")
	    (HOST = " . $options["DESCRIPTION.ADDRESS_LIST.ADDRESS.HOST"] . ")
	    (PORT = " . $options["DESCRIPTION.ADDRESS_LIST.ADDRESS.PORT"] . ")
	   )
	  )
	  
	  (CONNECT_DATA =
	   (SERVER = " . $options["DESCRIPTION.CONNECT_DATA.SERVER"] . ")
	   (SERVICE_NAME = " . $options["DESCRIPTION.CONNECT_DATA.SERVICE_NAME"] . ")
	  )
	) ";

	$conn = @oci_connect($user, $password, $dbsid);

	return $conn;
}

function exec_db_oracle_fetch_all($sql, $bind, $conn) {
	$rows = array();

	$required_functions = array("oci_parse", "oci_execute", "oci_fetch_assoc", "oci_free_statement");
	foreach($required_functions as $func_name) {
		if(!function_exists($func_name)) {
			exit("OCI (Oracle Extension for PHP) not installed!");	
		}
	}

	$stmt = get_db_oracle_stmt($sql, $bind);
	oci_execute($stmt);

	while($row = oci_fetch_assoc($stmt)) {
		$rows[] = $row;
	}

	oci_free_statement($stmt);

	return $rows;
}

function exec_db_oracle_query($sql, $bind, $conn) {
	$flag = false;

	$stmt = get_db_oracle_stmt($sql, $bind);
	$flag = oci_execute($stmt);

	oci_free_statement($stmt);

	return $flag;
}
