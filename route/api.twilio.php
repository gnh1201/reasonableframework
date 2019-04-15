<?php
/**
 * @file api.twilo.php
 * @date 2019-04-15
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Twilo API
 */

loadHelper("twilio.api");

$action = get_requested_value("action", array("_JSON", "_ALL"));
$from = get_requested_value("from", array("_JSON", "_ALL"));
$to = get_requested_value("to", array("_JSON", "_ALL"));

$twi = twilio_get_config();

$sid = $twi['twilio_sid'];
$token = $twi['twilio_token'];

$response = "";
switch($action) {
  case "message":
    $response = twilio_send_message($message, $from, $to, $sid, $token);
    break;

  case "voice":
    $response = twilio_send_voice("", $from, $to, $sid, $token);
    break;    
}

header("Content-Type: application/json");
echo json_encode($response);
