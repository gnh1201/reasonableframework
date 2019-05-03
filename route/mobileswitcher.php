<?php
/**
 * @file mobileswitcher.php
 * @date 2019-04-29
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief mobile device switcher
 */

loadHelper("mobiletool");
loadHelper("networktool");

$do = get_requested_value("do");
$from = get_requested_value("from");
$redirect_url = get_requested_value("redirect_url");
$dm = detect_mobile();
$ne = get_network_event();

if($from == "pc") {
    $dm = 1;
} elseif($from == "mobile") {
    $dm = 0;
}

$data = array(
    "action" => $action,
    "from" => $from,
    "dm" => $dm,
    "redirect_url" => get_final_link($redirect_url, array(
        "action" => "mobileswitcher",
        "dm" => $dm,
        "do" => $do,
        "redirect_url" => get_final_link($redirect_url, array(
            "action" => "mobileswitcher",
            "dm" => $dm,
        ), false),
    )),
    "ua" => get_hashed_text($ne['agent'], "base64"),
);

renderView("view_mobileswitcher", $data);
