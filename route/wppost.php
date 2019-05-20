<?php

loadHelper("webpagetool");
loadHelper("wprest");

$route = "wppost";
$wp_server_url = "http://wordpress.local";
$wp_access_token = get_session("wp_access_token");

$code = get_requested_value("code");
$action = get_requested_value("action");

$response = false;

switch($action) {
    case "write":
        $form_data = array(
            "title" => get_requested_value("title"),
            "content" => get_requested_value("content"),
            "author" => 2,
            "status" => get_requested_value("status"),
            "categories" => get_requested_value("categories")
        );

        // run post
        $response = write_wp_post($wp_server_url, $wp_access_token, $form_data);
        redirect_uri(get_route_link($route));

        break;

    default:
        // set session token
        set_session_token();

        // authenticate
        $client_id = "";
        $client_secret = "";
        authenticate_wp($wp_server_url, $client_id, $client_secret, $route, $code);

        $categories = get_wp_categories($wp_server_url, $wp_access_token);
        $data = array(
            "route" => $route,
            "categories" => $categories,
            "_token" => get_session_token()
        );

        renderView("view_wppost", $data);
}
