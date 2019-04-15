<?php
/**
 * @file api.zabbix.php
 * @date 2019-04-15
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Zabbix API
 */

loadHelper("zabbix.api");

$zbx = get_zabbix_config();

$data = array();
$data['authenticate'] = zabbix_authenticate($zbx['host'], $zbx['username'], $zbx['password']);
$data['hosts'] = zabbix_retrieve_hosts();
$data['items'] = array();

foreach($data['hosts']->result as $host) {
        $data['items'][$host->hostid] = zabbix_get_items($host->hostid);
}

header("Content-type: application/json");
echo json_encode($data);
