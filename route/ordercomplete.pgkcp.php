<?php
/**
 * @file ordercomplete.pgkcp.php
 * @date 2018-09-03
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief KCP PG(Payment Gateway) contoller when complete
 */

// detect CSRF attack
if(check_token_abuse_by_requests("_token", "_POST")) {
	set_error("Access denied. (Expired session or Website attacker)");
	show_errors();
}

// set token
set_session_token();

// set redirect variables
$redirect_url = get_requested_value("redirect_url");
$order_idxx = get_requested_value("order_idxx");
$res_cd = get_requested_value("res_cd");

if($res_cd == "0000") {
	$process_type = "complete";
} else {
	$process_tyee = "cancel";
}

// redirect
redirect_uri(get_final_link($redirect_url, array(
	"_token" => get_session_token(),
	"_route" => get_requested_value("route"),
	"process_type" => $process_type,
	"order_idxx" => $order_idxx
), false));
