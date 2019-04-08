<?php
// @file zabbix.api.php

if(!check_function_exists("zabbix_get_base_url")) {
  function zabbix_get_base_url() {
    return "http://localhost";
  }
}

if(!check_function_exists("zabbix_authenticate")) {
  function zabbix_authenticate($username, $password) {
    $response = false;
    
    if(loadHelper("webpagetool")) {
      $response = get_web_json(zabbix_get_base_url() . "/zabbix/api_jsonrpc.php", "post", array(
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

    return $response;
  }
}
