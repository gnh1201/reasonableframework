<?php
loadHelper("zabbix.api");

$authenticate = zabbix_authenticate("192.168.0.90", "Admin", "zabbix");

echo "<h1>Zabbix API</h1>";

echo "<h2>Authenticate</h2>";
echo "<pre>";
var_dump($authenticate);
echo "</pre>";

echo "<h2>Zabbix Hosts</h2>";
$hosts = zabbix_retrieve_hosts();
echo "<pre>";
var_dump($hosts);
echo "<pre>";

foreach($hosts->result as $host) {
	$items = zabbix_get_items($host->hostid);

	echo "<h2>(Host ID: " . $host->hostid . ") " . $host->host . "</h2>";
	echo "<pre>";
	$items = zabbix_get_items($host->hostid);
	var_dump($items);
	echo "<pre>";
}
