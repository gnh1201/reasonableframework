<?php
/**
 * @file api.social.php
 * @date 2018-09-26
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief SocialTools API (refactoring from SocioRouter API)
 */

loadHelper("hybridauth.lnk");
loadHelper("hybridauth.dbt");
loadHelper("socialtool");

set_session_token();
$_token = get_session_token();

$provider = get_requested_value("provider");
$action = get_requested_value("action");
$redirect_uri = get_requested_value("redirect_uri");
$user_id = get_requested_value("user_id");

$connection_id = get_requested_value("connection_id");
$message = get_requested_value("message");

// if make new connection
if($action != "new") {
    $api_session_id = get_session("api_session_id");
} else {
    $api_session_id = "";
    set_session("api_session_id", $api_session_id);
}

$session_data = array();
if(!empty($api_session_id)) {
    $fr = read_storage_file($api_session_id, array(
        "storage_type" => "session"
    ));
    if(!$fr) {
        // renew api session id
        $api_session_id = "";
        set_session("api_session_id", $api_session_id);
    } else {
        $session_data = json_decode($fr);
        $provider = get_property_value("provider", $session_data);
        $action = get_property_value("action", $session_data);
        $redirect_uri = get_property_value("redirect_uri", $session_data);
        $user_id = get_property_value("user_id", $session_data);
        $connection_id = get_property_value("connection_id", $session_data);
        $message = get_property_value("message", $session_data);
    }
}

if(empty($provider)) {
    set_error("provider is required field.");
    show_errors();
}

$hauth_adapter = null;
$hauth_session = null;
$hauth_profile = null;

// load library
$configfile = hybridauth_load($provider);
if(!$configfile) {
    set_error("can not load hybridauth library");
    show_errors();
}
$hauth = new Hybrid_Auth($configfile);

// try session restore
$session_flag = false;
if(!empty($connection_id)) {
    $hauth_session = get_stored_hybridauth_session($connection_id);
    if(!empty($hauth_session)) {
        try {
            $hauth->restoreSessionData($hauth_session);
            $session_flag = true;
        } catch(Exception $e) {
            set_error("maybe, your connection is broken.");
            show_errors();
        }
    }
}

// check hybridauth request
if($hauth->isConnectedWith($provider)) {
    $hauth_session = $hauth->getSessionData();
    $connection_id = store_hybridauth_session($hauth_session, $user_id);
    if($connection_id) {
        $session_flag = true;
    }
}

// save session
$api_session_id = get_hashed_text(make_random_id(32));
$session_data = array(
    "api_session_id" => $api_session_id,
    "provider" => $provider,
    "action" => $action,
    "redirect_uri" => $redirect_uri,
    "user_id" => $user_id,
    "connection_id" => $connection_id,
    "message" => $message
);
$fw = write_storage_file(json_encode($session_data), array(
    "storage_type" => "session",
    "filename" => $api_session_id
));
if(!$fw) {
    set_error("maybe, your storage is write-protected.");
    show_errors();
} else {
    set_session("api_session_id", $api_session_id);
}

if(hybridauth_check_redirect()) {
    hybridauth_process();
}

// try authenticate
try {
    if(!$session_flag) {
        $hauth_adapter = $hauth->authenticate($provider);
    } else {
        $hauth_adapter = $hauth->getAdapter($provider);
    }
    $session_flag = true;
} catch(Exception $e) {
    $hauth_adapter = $hauth->authenticate($provider);
}

if(!$session_flag) {
    // if failed authenticate
    redirect_uri(get_route_link("api.social", array(
        "provider" => $provider,
        "action" => $action,
        "redirect_uri" => $redirect_uri,
        "user_id" => $user_id,
        "connection_id" => $connection_id
    ), false));
}

// get user profile
$hauth_profile = $hauth_adapter->getUserProfile();

// do action
$context = array();
switch($action) {
    case "inbound":
        break;
    case "outbound":
        $response = social_send_message($provider, $hauth_adapter, $message);
        $object_id = social_parse_object_id($provider, $response);
        $context = array(
            "success"   => !(!$object_id),
            "message"   => "Have a nice day",
            "user_id"   => $user_id,
            "provider"  => $provider,
            "object_id" => $object_id
        );
        break;
    case "new":
        $context = array(
            "success"  => true,
            "message"  => "Authenticated",
            "user_id"  => $user_id,
            "provider" => $provider,
            "profile"  => $hauth_profile,
        );
        break;
    case "login":
        $context = array(
            "success"  => true,
            "message"  => "Authenticated",
            "user_id"  => $user_id,
            "provider" => $provider,
            "profile"  => $hauth_profile,
        );
        break;
    case "bgworker":
        $response = social_send_message($provider, $hauth_adapter, $message);
        $object_id = social_parse_object_id($provider, $response);
        $context = array(
            "success"    => !(!$object_id),
            "message"    => "Have a nice day",
            "id"         => $user_id,
            "connection" => $connection_id,
            "provider"   => $provider,
            "object_id"  => $object_id
        );
        break;
    case "cancel": // listen cancel authenticated callback
        break;
    case "delete": // listen delete ping
        break;
    case "accept": // listen accept ping
        break;
    case "object": // get object by id
        $object_id = get_requested_value("object_id");
        $context = array(
            "success" => true,
            "message" => "Found",
            "response" => social_get_object($provider, $hauth_adapter, $object_id)
        );
        break;
    default:
        set_error("Unknown action");
        show_errors();
}

if(empty($redirect_uri)) {
    header("Content-Type: application/json");
    echo json_encode($context);
} else {
    $_display_name = get_hashed_text($hauth_profile->displayName, "base64");
    $_idt_hash = get_hashed_text($hauth_profile->identifier, "sha1");
    $_idt_name = $_idt_hash . "@" . $provider;
    $_idt = get_hashed_text($_idt_name, "sha1");

    // renew api session id
    $api_session_id = "";
    set_session("api_session_id", $api_session_id);

    // go to redirect uri
    redirect_with_params($redirect_uri, array(
        "connection_id" => $connection_id,
        "provider" => $provider,
        "display_name" => $_display_name,
        "idt" => $_idt,
        "_token" => $_token
    ));
}
