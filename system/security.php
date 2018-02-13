<?php
/**
 * @file security.php
 * @date 2018-01-18
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Security module for VSPF
 */

if(!function_exists("check_token_abuse")) {
	function check_token_abuse($_p_token, $_n_token) {
		$abuse = false;
		
		$_c_token = $_p_token . $_n_token;
		if(empty($_c_token) || $_p_token != $_n_token || strlen($_c_token) != (strlen($_p_token) + strlen($_n_token)) || !ctype_alnum($_c_token)) {
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

if(!function_exists("set_session")) {
	function set_session($session_name, $value) {
		if(PHP_VERSION < '5.3.0') {
			session_register($session_name);
		}
		$$session_name = $_SESSION["$session_name"] = $value;
	}
}

if(!function_exists("get_session")) {
	function get_session($session_name) {
		$session_value = "";
		if(!array_key_empty($session_name, $_SESSION)) {
			$session_value = $_SESSION[$session_name];
		}
		return $session_value;
	}
}

if(!function_exists("set_session_token")) {
	function set_session_token() {
		$_token = make_random_id(10);
		set_session("_token", $_token);

		return $_token;
	}
}

if(!function_exists("get_session_token")) {
	function get_session_token() {
		return get_session("_token");
	}
}

if(!function_exists("check_token_abuse_by_requests")) {
	function check_token_abuse_by_requests($name) {
		global $requests;
		
		$flag = false;
		if(array_key_empty($name, $requests['_POST'])) {
			$flag = true;
		} else {
			$flag = check_token_abuse($requests['_POST'][$name], get_session($name));
		}

		return $flag;
	}
}

if(!function_exists("check_login_session")) {
	function check_login_session($ss_key, $config) {
		$flag = false;

		$session_name = get_password($ss_key);
		$session_file = $config['session_dir'] . '/' . protect_dir_path($session_name);
		$session_stored_key = "";

		if(file_exists($session_file)) {
			$fh = fopen($session_file, 'r');
			if($session_stored_key = fread($fh, filesize($session_file))) {
				if(!check_token_abuse($session_stored_key, $session_name)) {
					$flag = true;
				}
			}
		}

		return $flag;
	}
}

if(!function_exists("store_login_session")) {
	function store_login_session($ss_key, $config) {
		$flag = false;

		$session_name = get_password($ss_key);
		$session_file = $config['session_dir'] . '/' . protect_dir_path($session_name);

		$fh = fopen($session_file, 'w');
		if($fh !== false) {
			if(fwrite($fh, $session_name)) {
				$flag = check_login_session($ss_key, $config);
			}
			@chmod($session_file, 0777);
		}

		return $flag;
	}
}

if(!function_exists("process_safe_login")) {
	function process_safe_login($user_name, $user_password) {
		global $config;

		$flag = false;
		$ss_key = get_session("ss_key");

		if(!empty($ss_key) && check_login_session($ss_key, $config)) {
			$flag = true;
		} else {
			$ss_key = make_random_id(10);

			//if(check_match_password($hashed_password, $user_password) {
			if(true) {
				set_session("ss_user_name", $user_name);
				set_session("ss_key", $ss_key);

				$flag = store_login_session($ss_key, $config);
			}
		}

		return $flag;
	}
}

if(!function_exists("check_empty_requests")) {
	function check_empty_requests($no_empty_fields, $method_get=true) {
		global $requests;

		$errors = array();
		$check_data = $method_get ? $requests['_GET'] : $requests['_POST'];

		foreach($no_empty_fields as $fieldname) {
			if(array_key_empty($fieldname, $check_data)) {
				$errors[] = array(
					"fieldname" => $fieldname,
					"message"   => "{$fieldname} 항목은 공백일 수 없습니다."
				);
			}
		}

		return $errors;
	}
}

if(!function_exists("get_salt")) {
	function get_salt() {
		global $config;
		
		$salt = "H6hclwzFplRQw39C";
		if(!array_key_empty("salt", $config)) {
			$salt = $config['salt'];
		}

		return $salt;
	}
}

if(!function_exists("get_password")) {
	function get_password($text, $algo="sha1") {
		global $config;

		$salt = get_salt();
		$is_not_supported = false;

		$plain_text = $text;
		$hashed_text = "";

		if(!empty($salt)) {
			$plain_text .= $salt;
		}

		switch($algo) {
			case "sha1":
				$hashed_text = sha1($plain_text);
				break;
			case "md5":
				$hashed_text = md5($plain_text);
				break;
			case "crypt":
				$hashed_text = crypt($plain_text);
			default:
				$is_not_supported = true;
		}

		if($is_not_supported) {
			$hashed_text = $plain_text;
		}

		return $hashed_text;
	}
}

if(!function_exists("check_match_password")) {
	function check_match_password($p, $n, $algo="sha1") {
		$flag = false;
		$salt = get_salt();
		
		$n_plain_text = $n . $salt;
		$n_hashed_text = "";

		switch($algo) {
			case "sha1":
				$n_hashed_text = sha1($n_plain_text);
				$flag = ($n_hashed_text == $p);
				break;
			case "md5":
				$n_hashed_text = md5($n_plain_text);
				$flag = ($n_hashed_text == $p);
				break;
			case "crypt":
				$flag = (crypt($n_plain_text, $p) == $p);
				break;
			default:
				$flag = false;
		}
		
		return $flag;
	}
}

if(!function_exists("protect_dir_path")) {
	function protect_dir_path($path) {
		$path = str_replace('/', '_', $path);
		return $path;
	}
}

if(!function_exists("session_logout")) {
	function session_logout() {
		global $config;

		$flag = false;
		
		$ss_user_name = get_session("ss_user_name");
		$ss_key = get_session("ss_key");
		
		if(!empty($ss_key)) {
			set_session("ss_user_name", "");
			set_session("ss_key", "");
		}

		// delete session file
		@unlink($config['session_dir'] . '/' . protect_dir_path($ss_key));

		// permanently destory
		session_unset();
		session_destroy();

		// check ereased token
		$abuse = check_token_abuse($ss_user_name, get_session("ss_user_name"));
		$abuse = ($abuse && check_token_abuse($ss_key, get_session("ss_key")));

		// apply result
		$flag = $abuse;

		return $flag;
	}
}

if(!function_exists("check_current_user_name")) {
	function check_current_user_name() {
		$current_user_name = "";

		$ss_user_name = get_session("ss_user_name");
		$ss_key = get_session("ss_key");
		
		$abuse = check_token_abuse($ss_user_name, $ss_user_name); // self check
		$abuse = ($abuse && check_token_abuse($ss_key, $ss_key)); // self check

		if(!$abuse) {
			$current_user_name = $ss_user_name;
		}

		return $current_user_name;
	}
}
