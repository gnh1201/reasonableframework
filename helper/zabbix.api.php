<?php
/**
 * @file zabbix.api.php
 * @date 2019-04-08
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Zabbix JSON-RPC API (3.0) interface module
 * @documentation https://www.zabbix.com/documentation/3.0/manual/api
 */

if(!check_function_exists("get_zabbix_config")) {
    function get_zabbix_config() {
        $config = get_config();
        
        return array(
            "host" => get_value_in_array("zabbix_host", $config, "127.0.0.1"),
            "username" => get_value_in_array("zabbix_username", $config, "Admin"),
            "password" => get_value_in_array("zabbix_password", $config, "zabbix"),
            "protocol" => get_value_in_array("zabbix_protocol", $config, "http"),
        );
    }
}

if(!check_function_exists("zabbix_get_base_url")) {
    function zabbix_get_api_url() {
        $cnf = get_zabbix_config();
        return sprintf("%s://%s/zabbix/api_jsonrpc.php", $cnf['protocol'], $cnf['host']);
    }
}

if(!check_function_exists("zabbix_get_id")) {
    function zabbix_get_id() {
        return rand(10000, 99999) * rand(10000, 99999);
    }
}

if(!check_function_exists("zabbix_authenticate")) {
    function zabbix_authenticate() {
        $response = false;

        // get zabbix configuration
        $cnf = get_zabbix_config();

        // get zabbix api url
        $zabbix_api_url = zabbix_get_api_url($cnf['host'], $cnf['protocol']);

        // connect to zabbix server
        if(loadHelper("webpagetool")) {
            $response = get_web_json($zabbix_api_url, "jsondata", array(
                "headers" => array(
                    "Content-Type" => "application/json-rpc",
                ),
                "data" => array(
                    "jsonrpc" => "2.0",
                    "method" => "user.login",
                    "params" => array(
                        "user" => $cnf['username'],
                        "password" => $cnf['password'],
                    ),
                    "id" => zabbix_get_id(),
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

if(!check_function_exists("zabbix_retrieve_hosts")) {
    function zabbix_retrieve_hosts() {
        $response = false;

        // get zabbix authentication
        $zabbix_api_url = get_scope("zabbix_api_url");
        $zabbix_auth = get_scope("zabbix_auth");

        // connect to zabbix server
        if(loadHelper("webpagetool")) {
            $response = get_web_json($zabbix_api_url, "jsondata", array(
                "headers" => array(
                    "Content-Type" => "application/json-rpc",
                ),
                "data" => array(
                    "jsonrpc" => "2.0",
                    "method" => "host.get",
                    "params" => array(
                        "output" => array("hostid", "host"),
                        "selectInterfaces" => array("interfaceid", "ip"),
                    ),
                    "id" => zabbix_get_id(),
                    "auth" => $zabbix_auth,
                ),
            ));
        }

        return $response;
    }
}

if(!check_function_exists("zabbix_get_items")) {
    function zabbix_get_items($hostids="") {
        $response = false;

        // get zabbix authentication
        $zabbix_api_url = get_scope("zabbix_api_url");
        $zabbix_auth = get_scope("zabbix_auth");

        // connect to zabbix server
        if(loadHelper("webpagetool")) {
            $response = get_web_json($zabbix_api_url, "jsondata", array(
                "headers" => array(
                    "Content-Type" => "application/json-rpc",
                ),
                "data" => array(
                    "jsonrpc" => "2.0",
                    "method" => "host.get",
                    "params" => array(
                        "selectInventory" => true,
                        "selectItems" => array("name", "lastvalue", "units", "itemid", "lastclock", "value_type", "itemid"),
                        "output" => "extend",
                        "hostids" => $hostids,
                        "expandDescription" => 1,
                        "expandData" => 1,
                    ),
                    "id" => zabbix_get_id(),
                    "auth" => $zabbix_auth,
                ),
            ));
        }

        return $response;
    }
}
