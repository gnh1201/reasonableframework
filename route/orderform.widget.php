<?php
if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

set_session_token();

$pay_method_alias = get_requested_value("pay_method_alias");

$data = array(
    "_token" => get_session_token(),
    "_next_route" => "orderpay.pgkcp",
    "redirect_url" => get_requested_value("redirect_url"),
    "pay_method_alias" => (!empty($pay_method_alias) ? $pay_method_alias : "CRE"),
    "good_name" => get_requested_value("good_name"),
    "good_mny" => get_requested_value("good_mny"),
    "buyr_name" => get_requested_value("buyr_name"),
    "buyr_mail" => get_requested_value("buyr_mail"),
    "buyr_tel1" => get_requested_value("buyr_tel1"),
    "pay_data" => get_requested_value("pay_data"),
);

renderView("view_orderform.widget", $data);
