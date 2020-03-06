<?php
/**
 * @file zabbix.api.php
 * @created_on 2019-04-08
 * @updated_on 2020-03-05
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Zabbix JSON-RPC API (3.0) interface module
 * @documentation https://www.zabbix.com/documentation/current/ (4.4)
 */

if(!is_fn("get_zabbix_config")) {
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

if(!is_fn("zabbix_get_base_url")) {
    function zabbix_get_api_url() {
        $cnf = get_zabbix_config();
        return sprintf("%s://%s/zabbix/api_jsonrpc.php", $cnf['protocol'], $cnf['host']);
    }
}

if(!is_fn("zabbix_get_id")) {
    function zabbix_get_id() {
        return 1;
    }
}

if(!is_fn("zabbix_authenticate")) {
    function zabbix_authenticate() {
        $response = false;

        // get zabbix configuration
        $cnf = get_zabbix_config();

        // get zabbix api url
        $zabbix_api_url = zabbix_get_api_url($cnf['host'], $cnf['protocol']);

        // connect to zabbix server
        if(loadHelper("webpagetool")) {
            $response = get_web_json($zabbix_api_url, "jsonrpc2.cache", array(
                "method" => "user.login",
                "params" => array(
                    "user" => $cnf['username'],
                    "password" => $cnf['password'],
                ),
                "id" => zabbix_get_id(),
                "auth" => null
            ));
        }

        // set connection to global scope
        set_scope("zabbix_api_url", $zabbix_api_url);
        set_scope("zabbix_auth", get_property_value("result", $response));

        return $response;
    }
}

if(!is_fn("zabbix_get_hostgroups")) {
    function zabbix_get_hostgroups() {
        $hostgroups = false;
        $response = false;
        
        // get zabbix authentication
        $zabbix_api_url = get_scope("zabbix_api_url");
        $zabbix_auth = get_scope("zabbix_auth");

        // connect to zabbix server
        if(loadHelper("webpagetool")) {
            $response = get_web_json($zabbix_api_url, "jsonrpc2.cache", array(
                "method" => "hostgroup.get",
                "params" => array(
                    "output" => "extend"
                ),
                "id" => zabbix_get_id(),
                "auth" => $zabbix_auth
            ));
            
            $hostgroups = get_property_value("result", $response);
        }

        return $hostgroups;
    }
}

if(!is_fn("zabbix_get_hosts")) {
    function zabbix_get_hosts() {
        $hosts = false;
        $response = false;

        // get zabbix authentication
        $zabbix_api_url = get_scope("zabbix_api_url");
        $zabbix_auth = get_scope("zabbix_auth");

        // connect to zabbix server
        if(loadHelper("webpagetool")) {
            $response = get_web_json($zabbix_api_url, "jsonrpc2.cache", array(
                "method" => "host.get",
                "params" => array(
                    "output" => array("hostid", "host"),
                    "selectInterfaces" => array("interfaceid", "ip"),
                    "selectGroups" => "extend"
                ),
                "id" => zabbix_get_id(),
                "auth" => $zabbix_auth
            ));

            $hosts = get_property_value("result", $response);
        }

        return $hosts;
    }
}

if(!is_fn("zabbix_retrieve_hosts")) {
    function zabbix_retrieve_hosts() {
        return zabbix_get_hosts();
    }
}

if(!is_fn("zabbix_get_items")) {
    function zabbix_get_items($hostids=null) {
        $items = false;
        $results = false;
        $response = false;

        // get zabbix authentication
        $zabbix_api_url = get_scope("zabbix_api_url");
        $zabbix_auth = get_scope("zabbix_auth");

        // connect to zabbix server
        if(loadHelper("webpagetool")) {
            $response = get_web_json($zabbix_api_url, "jsonrpc2.cache", array(
                "method" => "host.get",
                "params" => array(
                    "selectInventory" => true,
                    "selectItems" => array("name", "key_", "status", "lastvalue", "units", "itemid", "lastclock", "value_type", "itemid"),
                    "output" => "extend",
                    "hostids" => $hostids,
                    "expandDescription" => 1,
                    "expandData" => 1,
                ),
                "id" => zabbix_get_id(),
                "auth" => $zabbix_auth
            ));
            $results = get_property_value("result", $response);
            foreach($results as $result) {
                $items = get_property_value("items", $result);
                break;
            }
        }

        return $items;
    }
}

if(!is_fn("zabbix_get_problems")) {
    function zabbix_get_problems($hostids=null) {
        $problems = false;
        $response = false;

        // get zabbix authentication
        $zabbix_api_url = get_scope("zabbix_api_url");
        $zabbix_auth = get_scope("zabbix_auth");

        // connect to zabbix server
        if(loadHelper("webpagetool")) {
            $response = get_web_json($zabbix_api_url, "jsonrpc2.cache", array(
                "method" => "problem.get",
                "params" => array(
                    "output" => "extend",
                    "selectAcknowledges" => "extend",
                    "selectTags" => "extend",
                    "selectSuppressionData" => "extend",
                    "hostids" => $hostids,
                    "recent" => "false",
                    //"suppressed" => "false",
                    //"acknowledged" => "false",
                    //"sortfield" => ["eventid"],
                    //"sortorder" => "DESC",
                    //"time_from" => get_current_datetime(array("adjust" => "1 hour"))
                ),
                "id" => zabbix_get_id(),
                "auth" => $zabbix_auth
            ));
        }

        $problems = get_property_value("result", $response);

        return $problems;
    }
}

if(!is_fn("zabbix_get_triggers")) {
    function zabbix_get_triggers($hostids=null) {
        $triggers = false;
        $response = false;

        // get zabbix authentication
        $zabbix_api_url = get_scope("zabbix_api_url");
        $zabbix_auth = get_scope("zabbix_auth");

        if(loadHelper("webpagetool")) {
            $response = get_web_json($zabbix_api_url, "jsonrpc2.cache", array(
                "method" => "trigger.get",
                "params" => array(
                    "hostids" => $hostids,
                    "output" => "extend",
                    "selectFunctions" => "extend",
                    "filter" => array(
                        "value" => 1,
                        "status" => 0
                    )
                ),
                "id" => zabbix_get_id(),
                "auth" => $zabbix_auth
            ));
        }
        $triggers = get_property_value("result", $response);

        return $triggers;
    }
}

if(!is_fn("zabbix_get_alerts")) {
    function zabbix_get_alerts($hostids=null, $time_from=0, $time_till=0) {
        $alerts = false;
        $response = false;

        // get zabbix authentication
        $zabbix_api_url = get_scope("zabbix_api_url");
        $zabbix_auth = get_scope("zabbix_auth");

        if(loadHelper("webpagetool")) {
            $params = array(
                "output" => "extend",
                "hostids" => $hostids,
                "sortfield" => array("clock", "eventid"),
                "sortorder" => "DESC"
            );

            if($time_from > 0) {
                $params['time_from'] = $time_from - 1;
            }

            if($time_till > 0) {
                $params['time_till'] = $time_till + 1;
            }

            $response = get_web_json($zabbix_api_url, "jsonrpc2.cache", array(
                "method" => "event.get",
                "params" => array(
                    "output" => "extend",
                    "hostids" => $hostids,
                    "sortfield" => array("clock", "eventid"),
                    "sortorder" => "DESC"
                ),
                "auth" => $zabbix_auth,
                "id" => zabbix_get_id()
            ));

            $alerts = get_property_value("result", $response);
        }

        return $alerts; 
    }
}

if(!is_fn("zabbix_get_records")) {
    function zabbix_get_records($itemids, $now_dt="", $adjust="-24h", $value_type=3) {
        $records = false;
        $response = false;
        
        // get current datetime
        if(empty($now_dt)) {
            $now_dt = get_current_datetime();
        }

        // get zabbix authentication
        $zabbix_api_url = get_scope("zabbix_api_url");
        $zabbix_auth = get_scope("zabbix_auth");

        // set time range variables
        $time_from = get_current_timestamp(array("now" => $now_dt, "adjust" => $adjust));
        $time_till = get_current_timestamp(array("now" => $now_dt));

        // get history
        // 0-numeric float; 1-character; 2-log; 3-numeric unsigned; 4-text
        if(loadHelper("webpagetool")) {
            $params = array(
                "output" => "extend",
                "history" => $value_type,
                "itemids" => $itemids,
                "sortfield" => "clock",
                "sortorder" => "DESC",
                "time_from" => $time_from,
                "time_till" => $time_till
            );
            
            $response = get_web_json($zabbix_api_url, "jsonrpc2.cache", array(
                "method" => "history.get",
                "params" => $params,
                "auth" => $zabbix_auth,
                "id" => zabbix_get_id()
            ));

            $records = get_property_value("result", $response);
        }

        return $records; 
    }
}
