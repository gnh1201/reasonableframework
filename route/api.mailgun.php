<?php
/**
 * @file api.mailgun.php
 * @date 2019-04-15
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Mailgun API controller
 */

loadHelper("mailgun.api");

$content = get_requested_value("content", array("_JSON", "_ALL"));
$subject = get_requested_value("subject", array("_JSON", "_ALL"));
$to = get_requested_value("to", array("_JSON", "_ALL"));

$response = mailgun_send_message($content, $to, $subject);

header("Content-Type: application/json");
echo json_encode($response);
