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

// process redirect url
$redirect_url = get_requested_value("redirect_url");
$order_idxx = get_requested_value("order_idxx");
redirect_uri(get_final_link($redirect_url, array(
	"action" => "ordercomplete",
	"order_idxx" => $order_idxx,
	"good_mny" => $good_mny
), false));
