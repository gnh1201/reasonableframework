<?php
/**
 * @file api.socialhub.php
 * @date 2018-09-26
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief SocialHub API (refactoring from SocioRouter API)
 */

loadHelper("hybridauth.lnk");
loadHelper("hybridauth.dbt");

$provider = get_requested_value("provider");
$action = get_requested_value("action");
$redirect_url = get_requested_value("redirect_url");
$user_id = get_requested_value("user_id");

$connection_id = get_requested_value("connection_id");
$message = get_requested_value("message");

$api_session_id = get_session("api_session_id");
$session_data = array();
if(!empty($api_session_id)) {
	$fr = read_storage_file($api_session_id, array(
		"storage_type" => "session"
	));
	if(!$fr) {
		$api_session_id = ""; // renew api session id
	} else {
		$session_data = json_decode($fr);
		$provider = get_property_value("provider", $session_data);
		$action = get_property_value("action", $session_data);
		$redirect_url = get_property_value("redirect_url", $session_data);
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
			// nothing
		}
	}
}

// check hybridauth request
if(hybridauth_check_redirect()) {
	if($hauth->isConnectedWith($provider)) {
		$hauth_session = $hauth->getSessionData();
		$connection_id = store_hybridauth_session($hauth_session, $user_id);
		if($connection_id) {
			$session_flag = true;
		}
	}
}

// save session
$api_session_id = get_hashed_text(make_random_id(32));
$session_data = array(
	"api_session_id" => $api_session_id,
	"provider" => $provider,
	"action" => $action,
	"redirect_url" => $redirect_url,
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
} catch(Exception $e) {
	// nothing
}

if(!$session_flag) {
	// if failed authenticate
	redirect_uri(get_route_link("api.socialhub", array(
		"provider" => $provider,
		"action" => $action,
		"redirect_url" => $redirect_url,
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
		$hauth_adapter->setUserStatus($message);
		break;
	case "new":
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
	case "cancel": // listen cancel authenticated callback
		break;
	case "delete": // listen delete ping 
		break;
	default:
		set_error("Unknown action");
		show_errors();
}

