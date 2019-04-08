<?php
/**
 * @file zabbix.api.php
 * @date 2019-04-08
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Zabbix JSON-RPC API (3.0) interface module
 * @documentation https://www.zabbix.com/documentation/3.0/manual/api
 */

if(!check_function_exists("zabbix_get_base_url")) {
	function zabbix_get_api_url($host, $protocol="http") {
		return sprintf("%s://%s/zabbix/api_jsonrpc.php", $protocol, $host);
	}
}

if(!check_function_exists("zabbix_authenticate")) {
	function zabbix_authenticate($host, $username, $password, $protocol="http") {
		$response = false;
		
		// get zabbix api url
		$zabbix_api_url = zabbix_get_api_url($host, $protocol);
		
		// connect to zabbix server
		if(loadHelper("webpagetool")) {
			$response = get_web_json($zabbix_api_url, "post", array(
				"headers" => array(
					"Content-Type" => "application/json-rpc",
				),
				"data" => array(
					"jsonrpc" => "2.0",
					"method" => "user.login",
					"params" => array(
						"user" => $username,
						"password" => $password,
					),
					"id" => 1,
					"auth" => null,
				),
			));
		}
		
		// set connection to global scope
		set_scope("zabbix_api_url", $zabbix_api_url);
		set_scope("zabbix_auth", get_property_value("result", $response));

		return $response;
	}
}

if(!check_function_exists("zabbix_retrive_hosts")) {
	function zabbix_retrive_hosts() {
		$response = false;
		
		// get zabbix authentication
		$zabbix_api_url = get_scope("zabbix_api_url");
		$zabbix_auth = get_scope("zabbix_auth");
		
		// connect to zabbix server
		if(loadHelper("webpagetool")) {
			$response = get_web_json($zabbix_api_url, array(
				"headers" => array(
					"Content-Type" => "application/json-rpc",
				),
				"data" => array(
					"jsonprc" => "2.0",
					"method" => "host.get",
					"params" => array(
						"output" => array("hostid", "host"),
						"selectInterfaces" => array("interfaceid", "ip"),
					),
					"id" => 2,
					"auth" => $zabbix_auth
				),
			));
		}
		
		return $response;
	}
}