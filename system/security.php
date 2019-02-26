<?php
/**
 * @file security.php
 * @date 2018-05-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Security module for ReasonableFramework
 */

if(!check_valid_function("check_token_abuse")) {
	function check_token_abuse($_p_token, $_n_token) {
		$abuse = false;
		
		$_c_token = $_p_token . $_n_token;
		if(empty($_c_token) || $_p_token != $_n_token || strlen($_c_token) != (strlen($_p_token) + strlen($_n_token)) || !ctype_alnum($_c_token)) {
			$abuse = true;
		}

		return $abuse;
	}
}

if(!check_valid_function("make_random_id")) {
	function make_random_id($length = 10) {
		$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}

if(!check_valid_function("set_session")) {
	function set_session($session_name, $value) {
		if(PHP_VERSION < '5.3.0') {
			session_register($session_name);
		}
		$$session_name = $_SESSION["$session_name"] = $value;
	}
}

if(!check_valid_function("get_session")) {
	function get_session($session_name) {
		$session_value = "";
		if(!array_key_empty($session_name, $_SESSION)) {
			$session_value = $_SESSION[$session_name];
		}
		return $session_value;
	}
}

if(!check_valid_function("set_session_token")) {
	function set_session_token() {
		$_token = make_random_id(10);
		set_session("_token", $_token);

		return $_token;
	}
}

if(!check_valid_function("get_session_token")) {
	function get_session_token() {
		return get_session("_token");
	}
}

if(!check_valid_function("check_token_abuse_by_requests")) {
	function check_token_abuse_by_requests($name, $method="_POST") {
		$requests = get_requests();
		
		$flag = false;
		if(array_key_empty($name, $requests[$method])) {
			$flag = true;
		} else {
			$flag = check_token_abuse($requests[$method][$name], get_session($name));
		}

		return $flag;
	}
}

if(!check_valid_function("check_login_session")) {
	function check_login_session($ss_key, $config) {
		$flag = false;

		$session_name = get_password($ss_key);
		$session_stored_name = read_storage_file($session_name, array(
			"storage_type" => get_value_in_array("session_dir", $config, "session"),
		));
		$flag = !check_token_abuse($session_stored_name, $session_name);

		return $flag;
	}
}

if(!check_valid_function("store_login_session")) {
	function store_login_session($ss_key, $config) {
		$flag = false;

		$session_name = get_password($ss_key);
		$fw = write_storage_file($session_name, array(
			"filename" => $session_name,
			"storage_type" => get_value_in_array("session_dir", $config, "session"),
			"chmod" => 0700, // only access by owner
		));

		if($fw) {
			$flag = check_login_session($ss_key, $config);
		}

		return $flag;
	}
}

if(!check_valid_function("process_safe_login")) {
	function process_safe_login($user_name, $user_password, $user_profile=array(), $escape_safe=false) {
		$config = get_config();

		$flag = false;
		$ss_key = get_session("ss_key");

		$user_id = 0;
		$stored_password = "";
		if(!array_key_empty("user_id", $user_profile)) {
			$user_id = $user_profile['user_id'];
		}
		if(!array_key_empty("user_password", $user_profile)) {
			$stored_password = $user_profile['user_password'];
		}

		if(!empty($ss_key) && check_login_session($ss_key, $config)) {
			$flag = true;
		} else {
			$ss_key = make_random_id(10);

			if(check_match_password($stored_password, $user_password) || $escape_safe == true) {
				set_session("ss_user_id", $user_id);
				set_session("ss_user_name", $user_name);
				set_session("ss_key", $ss_key);

				$flag = store_login_session($ss_key, $config);
			}
		}

		return $flag;
	}
}

if(!check_valid_function("check_empty_requests")) {
	function check_empty_requests($fieldnames, $method="get") {
		$requests = get_requests();
		$errors = array();

		if(is_bool($method)) {
			$method = $method ? "get" : "post";
		}

		if(array_key_exists($method, $requests)) {
			$data = $requests[$method];

			foreach($fieldnames as $fieldname) {
				if(array_key_empty($fieldname, $data)) {
					$errors[] = array(
						"fieldname" => $fieldname,
						"message"   => "{$fieldname}: can not empty."
					);
				}
			}
		}

		return $errors;
	}
}

if(!check_valid_function("get_hashed_text")) {
	function get_hashed_text($text, $algo="sha1", $options=array()) {
		$hashed_text = false;

		// with salt
		if(!array_key_empty("salt", $options)) {
			if(!array_key_equals("2p", $options, true)) {
				if($options['salt'] == true) {
					$text .= get_salt();
				} elseif(is_string($options['salt']) && $strlen($options['salt']) > 0) {
					$text .= $options['salt'];
				}
			}
		}

		// with 2-pass
		if(array_key_equals("2p", $options, true)) {
			$options['2p'] = false;
			$text = get_hashed_text($text, $algo, $options);
		}

		// choose algorithm
		switch($algo) {
			case "sha1":
				$hashed_text = sha1($text);
				break;
			case "md5":
				$hashed_text = md5($text);
				break;
			case "crypt":
				$hashed_text = crypt($text);
				break;
			case "crc32":
				if(!array_key_equals("decimal", $options, true)) {
					$hashed_text = str_pad(dechex(crc32($text)), 8, '0', STR_PAD_LEFT);
				} else {
					$hashed_text = crc32($text);
				}
				break;
			case "base64":
				if(!array_key_equals("decode", $options, true)) {
					$hashed_text = base64_encode($text);
				} else {
					$hashed_text = base64_decode($text);
				}
				break;
			default:
				if(hash_algo_exists($algo)) {
					$hashed_text = hash($algo, $text, false); 
				}
				break;
		}

		return $hashed_text;
	}
}

if(!check_valid_function("hash_algo_exists")) {
	function hash_algo_exists($algo) {
		$flag = false;
		if(check_valid_function("hash_algos")) {
			$flag = in_array($algo, hash_algos());
		}
		return $flag;
	}
}

if(!check_valid_function("get_salt")) {
	function get_salt() {
		$config = get_config();
		
		$salt = "";
		if(!array_key_empty("salt", $config)) {
			$salt = $config['salt'];
		} else {
			$salt = make_random_id(16);
		}

		return $salt;
	}
}

if(!check_valid_function("get_password")) {
	function get_password($text, $algo="sha1") {
		$config = get_config();

		$salt = get_salt();
		$is_not_supported = false;

		$plain_text = $text;
		$hashed_text = "";

		if(!empty($salt)) {
			$plain_text .= $salt;
		}
		
		$hashed_text = get_hashed_text($plain_text, $algo);
		if(empty($hashed_text)) {
			$is_not_supported = true;
		}

		if($is_not_supported) {
			$hashed_text = $plain_text;
		}

		return $hashed_text;
	}
}

if(!check_valid_function("check_match_password")) {
	function check_match_password($p, $n, $algo="sha1") {
		$flag = false;
		$salt = get_salt();
		
		$n_plain_text = $n . $salt;
		$n_hashed_text = "";

		switch($algo) {
			case "crypt":
				$flag = (crypt($n_plain_text, $p) == $p);
				break;
			default:
				$n_hashed_text = get_hashed_text($n_plain_text, $algo);
				$flag = ($n_hashed_text == $p);
				break;
		}

		return $flag;
	}
}

if(!check_valid_function("protect_dir_path")) {
	function protect_dir_path($path) {
		$path = str_replace('/', '_', $path);
		return $path;
	}
}

if(!check_valid_function("session_logout")) {
	function session_logout() {
		$config = get_config();

		$flag = false;
		
		$ss_user_name = get_session("ss_user_name");
		$ss_key = get_session("ss_key");

		// delete session file
		$session_name = get_password($ss_key);
		remove_storage_file($session_name, array(
			"storage_type" => get_value_in_array("session_dir", $config, "session"),
		));

		// reset session
		if(!empty($ss_key)) {
			set_session("ss_user_name", "");
			set_session("ss_key", "");
		}

		// permanently destory
		session_unset();
		session_destroy();

		// check ereased token
		$abuse_ss_user_name = check_token_abuse($ss_user_name, get_session("ss_user_name"));
		$abuse_ss_key = check_token_abuse($ss_key, get_session("ss_key"));

		// return result
		$flag = ($abuse_ss_user_name && $abuse_ss_key);

		return $flag;
	}
}

if(!check_valid_function("get_current_user_id")) {
	function get_current_user_id() {
		return get_current_session_data("ss_user_id");
	}
}

if(!check_valid_function("get_current_user_name")) {
	function get_current_user_name() {
		return get_current_session_data("ss_user_name");
	}
}

if(!check_valid_function("get_current_session_data")) {
	function get_current_session_data($name) {
		$current_data = "";

		$ss_data = get_session($name);
		$ss_key = get_session("ss_key");

		$abuse = check_token_abuse($ss_data, $ss_data); // self check
		$abuse = ($abuse && check_token_abuse($ss_key, $ss_key)); // self check

		if(!$abuse) {
			$current_data = $ss_data;
		}

		return $current_data;
	}
}

if(!check_valid_function("get_user_profile")) {
	function get_user_profile() {
		$user_profile = array(
			"user_id"   => get_current_user_id(),
			"user_name" => get_current_user_name()
		);

		return $user_profile;
	}
}

if(!check_valid_function("get_fixed_id")) {
	function get_fixed_id($str, $len=0, $salt="") {
		$config = get_config();

		$init_salt = empty($salt) ? $config['salt'] : $salt;
		$init_len = ($len < 1) ? $config['autolen'] : $len;
		return substr(get_hashed_text(get_hashed_text($str, "sha1") . $init_salt, "sha1"), 0, $init_len);
	}
}

// https://stackoverflow.com/questions/1996122/how-to-prevent-xss-with-html-php
if(!check_valid_function("get_clean_xss")) {
	function get_clean_xss($data, $notags=0) {
		if(is_string($data)) {
			// if no tags (equals to strip_tags)
			if($notags > 0) {
				return strip_tags($data);
			}

			// Fix &entity\n;
			$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
			$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
			$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
			$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

			// Remove any attribute starting with "on" or xmlns
			$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

			// Remove javascript: and vbscript: protocols
			$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
			$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
			$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

			// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
			$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
			$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
			$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

			// Remove namespaced elements (we do not need them)
			$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

			do
			{
				// Remove really unwanted tags
				$old_data = $data;
				$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
			}
			while ($old_data !== $data);
		}

		// we are done...
		return $data;
	}
}

if(!check_valid_function("get_clean_newlines")) {
	function get_clean_newlines($data) {
		return is_string($data) ? trim(preg_replace('~[\r\n]+~', ' ', $data)) : $data;
	}
}

if(!check_valid_function("get_clean_text")) {
	function get_clean_text($data) {
		return is_string($data) ? get_clean_newlines(get_clean_xss($data, 1)) : $data;
	}
}

// support curl or jsonp(callback)
if(!check_valid_function("get_callable_token")) {
	function get_callable_token($token, $callback="", $charset="utf-8") {
		$callback = get_clean_xss($callback);
		$retdata = "";

		if(empty($callback)) {
			$retdata = $token;
		} else {
			$retdata = "function $callback() { return '$token'; }";
		}

		return $retdata;
	}
}

if(!check_valid_function("encapsulate_text")) {
	function encapsulate_text($text, $algo="aes-128-cbc", $key="", $iv="", $hash="", $hash_algo="sha1") {
		$config = get_config();

		$encapsulated_text = "";
		$encrypted_text = "";

		// when fail hash test
		if(!empty($hash)) {
			if($hash != get_hashed_text($text, $hash_algo)) {
				return $encapsulated_text;
			}
		}

		// initialize text
		$init_text = get_hashed_text($text, "base64");

		if($algo == "base64") {
			$encapsulated_text = $init_text;
		} else {
			$init_key = empty($key) ? $config['masterkey'] : $key;
			$init_iv = empty($iv) ? $config['masteriv'] : $iv;

			if(check_valid_function("openssl_encrypt")) {
				$encrypted_text = @openssl_encrypt($init_text, $algo, $init_key, true, $init_iv);
			} else {
				$encrypted_text = xor_this($init_key, $init_text);
			}

			if(!empty($encrypted_text)) {
				$encapsulated_text = get_hashed_text($encrypted_text, "base64");
			}
		}
		
		return $encapsulated_text;
	}
}

if(!check_valid_function("decapsulate_text")) {
	function decapsulate_text($text, $algo="aes-128-cbc", $key="", $iv="", $hash="", $hash_algo="sha1") {
		$config = get_config();

		$decapsulate_text = "";
		$decrypted_text = "";

		// initialize text
		$init_text = get_hashed_text($text, "base64", array("decode" => true));

		if($algo = "base64") {
			$decapsulate_text = $init_text;
		} else {
			$init_key = empty($key) ? $config['masterkey'] : $key;
			$init_iv = empty($iv) ? $config['masteriv'] : $iv;

			if(check_valid_function("openssl_decrypt")) {
				$decrypted_text = @openssl_decrypt($init_text, $algo, $init_key, true, $init_iv);
			} else {
				$decrypted_text = xor_this($init_key, $init_text);
			}

			if(!empty($encrypted_text)) {
				$decapsulate_text = get_hashed_text($decrypted_text, "base64", array("decode" => true));
			}
		}

		// when fail hash test
		if(!empty($hash)) {
			if($hash != get_hashed_text($decapsulate_text, $hash_algo)) {
				$decapsulate_text = "";
			}
		}

		return $decapsulate_text;
	}
}

if(!check_valid_function("make_safe_argument")) {
	function make_safe_argument($str) {
		return addslashes($str);
	}
}

// https://stackoverflow.com/questions/14673551/encrypt-decrypt-with-xor-in-php
if(!check_valid_function("xor_this")) {
	function xor_this($key, $string, $debug=false) {
		$text = $string;
		$outText = "";

		for($i = 0; $i < strlen($text); ) {
			for($j = 0; ($j < strlen($key) && $i < strlen($text)); $j++, $i++) {
				$outText .= $text{$i} ^ $key{$j};

				if($debug) {
					echo 'i=' . $i . ', ' . 'j=' . $j . ', ' . $outText{$i} . '<br />';
				}
			}
		}

		return $outText;
	}
}

// https://wiki.ubuntu.com/DevelopmentCodeNames
if(!check_valid_function("get_generated_name")) {
	function get_generated_name() {
		$config = get_config();

		$generated_name = "";

		$adjectives = explode(',', $config['adjectives']);
		$animals = explode(',', $config['animals']);

		$c_adjective = ucfirst($adjectives[rand(0, count($adjectives) - 1)]);
		$c_animal = ucfirst($animals[rand(0, count($animals) - 1)]);

		$generated_name = $c_adjective . " " . $c_animal;

		return $generated_name;
	}
}

if(!check_valid_function("check_redirect_origin")) {
	function check_redirect_origin($url) {
		$flag = false;

		$to_resource = parse_url($url);
		$to_host = str_replace("www.", "", get_value_in_array("host", $to_resource, ""));

		$base_url = base_url();
		$base_resource = parse_url($base_url);
		$base_host = str_replace("www.", "", get_value_in_array("host", $base_resource, ""));

		$flag = !check_token_abuse($to_host, $base_host);

		return $flag;
	}
}


// start session (enable $_SESSION)
session_start();
