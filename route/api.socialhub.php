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

$user_id = get_reqeusted_value("user_id");
$connection_id = get_requested_value("connection_id");
$message = get_requested_value("message");

$hauth_adapter = null;
$hauth_session = null;
$hauth_profile = null;

// check hauth parameters
$is_hauth = false;
foreach($requests['_ALL'] as $k=>$v) {
	if(strpos($k, "hauth") === false) {
		$is_hauth = true;
		break;
	}
}

// load library
$configfile = load_hybridauth($provider);
if(!$configfile) {
	set_error("can not load hybridauth library");
	show_errors();
}
$hauth = new Hybrid_Auth($configfile);

// try session restore
$session_flag = false;
if(empty($connection_id)) {
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

// do authenticate
if(!$session_flag) {
	try {
		$hauth_adapter = $hauth->authenticate($provider);
	} catch(Exception $e) {
		// nothing
	}

	if(!is_null($hauth_adapter)) {
		$hauth_session = $hauth->getSessionData();
		$connection_id = store_hybridauth_session($hauth_session, $user_id);
		if($connection_id) {
			$session_flag = true;
			$hauth_profile = $hauth_adapter->getUserProfile();
		}
	}
}

// if failed authenticate
if(!$session_flag) {
	// if failed authenticate
	redirect_uri(get_route_link("api.socialhub", array(
		"provider" => $provider,
		"action" => $action,
		"redirect_url" => $redirect_url,
		"user_id" => $user_id
	), false));
}

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
}
