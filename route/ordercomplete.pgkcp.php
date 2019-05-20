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
$ordr_idxx = get_requested_value("ordr_idxx");
$res_cd = get_requested_value("res_cd");
$pay_method_alias = get_requested_value("pay_method_alias");

// set action
// 0000: completed payment (완료된 결제)
// A001: free plan (무료)
// A002: hand-writing payment (수기결제, 무통장입금 등)
$action = "cancel";
if(in_array($res_cd, array("0000", "A001")) {
    $action = "complete";
} elseif(in_array($res_cd, array("A002")) {
    $action = "hold";
}

// check ordr_idxx
if(empty($ordr_idxx)) {
    set_error("ordr_idxx can not empty");
    set_errors();
}

// write storage file
$fd = json_encode($requests['_POST']);
$fw = write_storage_file($fd, array(
    "filename" => get_hashed_text($ordr_idxx) . ".json",
    "storage_type" => "payman"
));

// check write-protected
if(!$fw) {
    set_error("maybe, your storage is write-protected."); 
    show_errors();
}

// redirect
redirect_uri(get_final_link($redirect_url, array(
    "_token" => get_session_token(),
    "_route" => get_requested_value("route"),
    "_action" => $action,
    "_ordr_idxx" => $ordr_idxx,
    "_res_cd" => $res_cd,
    "_pay_method_alias" => $pay_method_alias
), false), array(
    "check_origin" => true
));
