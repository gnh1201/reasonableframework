<?php
/**
 * @file mobileswitcher.php
 * @date 2019-04-29
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief mobile device switcher
 */

loadHelper("mobiletool");

$do = get_requested_value("do");
$from = get_requested_value("from");
$redirect_url = get_requested_value("redirect_url");
$dm = detect_mobile();

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
);

/* note: mobileswitcher client example */
/*
// mobileswitcher
if($_GET['action'] == "mobileswitcher") {
    if($_GET['do'] == "logout") {
        session_unset();
        session_destroy();
        set_cookie("ck_mb_id", "", 0);
        set_cookie("ck_auto", "", 0);

        // go to redirect url
        if(!empty($_GET['redirect_url'])) {
            header("location: " . $_GET['redirect_url']);
        } else {
            header("location: /");
        }
    }

    // get session value
    $_SESSION['dm'] = $_GET['dm'];
}

// detect mobile
if(!array_key_exists("dm", $_SESSION)) {
    header("location: /payman/?route=mobileswitcher&redirect_url=" . urlencode("http://" . $_SERVER['HTTP_HOST']));
}
*/

renderView("view_mobileswitcher", $data);
