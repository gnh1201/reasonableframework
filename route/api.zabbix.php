<?php
loadHelper("zabbix.api");

$data = array();

$data['authenticate'] = zabbix_authenticate("127.0.0.1", "Admin", "zabbix");
$data['hosts'] = zabbix_retrieve_hosts();
$data['items'] = array();

foreach($data['hosts']->result as $host) {
        $data['items'][$host->hostid] = zabbix_get_items($host->hostid);
}

header("Content-type: application/json");
echo json_encode($data);
