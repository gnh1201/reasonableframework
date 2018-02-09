<?php
/**
 * @file security.php
 * @date 2018-01-18
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Security module for VSPF
 */

if(!function_exists("check_token_abuse")) {
	function check_token_abuse($_post_token, $_sess_token) {
		$abuse = false;
		
		$_check_token = $_post_token . $_sess_token;
		if(empty($_check_token) || $_post_token != $_sess_token) {
			$abuse = true;
		}
		
		return $abuse;
	}
}

if(!function_exists("make_random_id")) {
	function make_random_id($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}

if(!function_exists("set_session_token")) {
	function set_session_token() {
		$random_id = make_random_id(10);
		$_SESSION['random_id'] = $random_id;

		return $random_id;
	}
}

if(!function_exists("get_session_token")) {
	function get_session_token() {
		return $_SESSION['random_id'];
	}
}
