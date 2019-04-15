<?php

loadHelper("mailgun.api");

$content = get_requested_value("content", array("_JSON", "_ALL"));
$subject = get_requested_value("subject", array("_JSON", "_ALL"));
$to = get_requested_value("to", array("_JSON", "_ALL"));

