<?php
// view render
if(!function_exists('renderView')) {
	function renderView($name, $data=array()) {
		if(count($data) > 0) {
			extract($data);
		}

		// set user profile
		if(function_exists("get_user_profile")) {
			$user_profile = get_user_profile();
		}

		// load view file
		$viewfile = './view/' . $name . '.php';
		if(file_exists($viewfile)) {
			include($viewfile);
		}
	}
}

// load helper
if(!function_exists('loadHelper')) {
	function loadHelper($name) {
		// set user profile
		if(function_exists("get_user_profile")) {
			$user_profile = get_user_profile();
		}
		
		// load helper file

		$helperfile = './helper/' . $name . '.php';
		if(file_exists($helperfile)) {
			include($helperfile);
		}
	}
}

if(!function_exists('array_key_empty')) {
	function array_key_empty($key, $array) {
		$empty = true;
		
		if(is_array($array)) {
			if(array_key_exists($key, $array)) {
				if(!empty($array[$key])) {
					$empty = false;
				}
			}
		}
		
		return $empty;
	}
}

if(!function_exists("cut_str")) {
	function cut_str($str, $start, $len=0) {
		$cutted_str = "";
		if(function_exists("iconv_substr")) {
			$cutted_str = iconv_substr($str, $start, $len, "utf-8");
		} elseif(function_exists("mb_substr")) {
			$cutted_str = mb_substr($str, $start, $len);
		} else {
			$cutted_str = substr($start, $len);
		}
		
		return $cutted_str;
	}
}

if(!function_exists("read_file_by_line")) {
	function read_file_by_line($filename) {
		$lines = array();
		$handle = fopen($filename, "r");
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				$lines[] .= $line;
			}

			fclose($handle);
		}
		
		return $lines;
	}
}

if(!function_exists("nl2p")) {
	function nl2p($string) {
		$paragraphs = '';
		foreach (explode("\n", $string) as $line) {
			if (trim($line)) {
				$paragraphs .= '<p>' . $line . '</p>';
			}
		}
		return $paragraphs;
	}
}
