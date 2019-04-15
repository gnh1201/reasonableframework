<?php
/**
 * @file twilio.api.php
 * @date 2019-04-08
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Twilio REST API interface module
 * @documentation https://www.twilio.com/docs/sms/send-messages
 */
 
if(!check_function_exists("twilio_send_message")) {
	function twilio_send_message($message, $from, $to, $sid, $token) {
		$response = false;

		if(loadHelper("webpagetool")) {
			$request_url = sprintf("https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json", $sid);
			$response = get_web_json($request_url, "post", array(
				"headers" = array(
					"Authentication" => array("Basic", $sid, $token),
					"Content-Type" => "application/x-www-form-urlencoded",
				),
				"data" => array(
					"Body" => $message,
					"From" => $from,
					"To" => $to,
				),
			);
		}
		
		return $response;
	}
}

if(!check_function_exists("twilio_send_voice")) {
	function twilio_send_voice($url="", $from, $to, $sid, $token) {
		$response = false;
		
		if(empty($url)) {
			$url = "http://demo.twilio.com/docs/voice.xml";
		}

		if(loadHelper("webpagetool")) {
			$request_url = sprintf("https://api.twilio.com/2010-04-01/Accounts/%s/Calls.json", $sid);
			$response = get_web_json($request_url, "post", array(
				"headers" = array(
					"Authentication" => array("Basic", $sid, $token),
					"Content-Type" => "application/x-www-form-urlencoded",
				),
				"data" => array(
					"Url" => $url,
					"From" => $from,
					"To" => $to,
				),
			);
		}
		
		return $response;
	}
}
