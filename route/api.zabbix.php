<?php
loadHelper("zabbix.api");

$authenticate = zabbix_authenticate("127.0.0.1", "Admin", "zabbix");

var_dump($authenticate);

