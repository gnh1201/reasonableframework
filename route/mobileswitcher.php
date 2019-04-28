<?php
loadHelper("mobiletool");

$from = get_requested_value("from");
$redirect_url = get_requested_value("redirect_url");
$dm = detect_mobile();

if($from == "pc") {
    $dm = 1;
} elseif($from == "mobile") {
    $dm = 0;
}

$data = array(
    "from" => $from,
    "dm" => $dm,
    "redirect_url" => get_final_link($redirect_url, array(
        "action" => "mobileswitcher",
        "dm" => $dm,
    )),
);

renderView("view_mobileswitcher", $data);
