<?php
/**
 * @file api.twilio.php
 * @date 2019-04-15
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Twilio API controller
 */

loadHelper("twilio.api");

$action = get_requested_value("action", array("_JSON", "_ALL"));
$message = get_requested_value("message", array("_JSON", "_ALL"));
$to = get_requested_value("to", array("_JSON", "_ALL"));

$response = false;

switch($action) {
  case "text":
    $response = twilio_send_message($message, $to);
    break;

  case "voice":
    $response = twilio_send_voice($message, $to);
    break;
}

header("Content-Type: application/json");
echo json_encode($response);
