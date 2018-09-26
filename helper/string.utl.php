<?php
/**
 * @file string.utl.php
 * @date 2018-05-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief String utility helper
 */

// for Korean Telephone Number
if(!function_exists("parse_tel_number_kr")) {
	function parse_tel_number_kr($tel) {
		$output = preg_replace("/[^0-9]/", "", $tel); // 숫자 이외 제거
		$local_code = substr($tel, 0, 2);

		if ($local_code == '02') {
			$output = preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
		} elseif (strlen($tel) == '8' && in_array($local_code, array('15', '16', '18'))) {
			$output = preg_replace("/([0-9]{4})([0-9]{4})$/", "\\1-\\2", $tel); // 지능망 번호이면
		} else {
			$output = preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
		}

		return $output;
	}
}

if(!function_exists("get_converted_string")) {
	function get_converted_string($str, $to_charset, $from_charset) {
		$result = false;
		
		if($form_charset == "detect") {
			if(function_exists("mb_detect_encoding") && function_exists("mb_detect_order")) {
				$from_charset = mb_detect_encoding($str, mb_detect_order(), true);
			} else {
				$from_charset = "ISO-8859-1";
			}
		}

		if(function_exists("iconv")) {
			$result = iconv($from_charset, $to_charset, $str);
		} elseif(function_exists("mb_convert_encoding")) {
			$result = mb_convert_encoding($str, $to_charset, $from_charset);
		}

		return $result;
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

if(!function_exists("br2nl")) {
	function br2nl($string) {
		return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string); 
	}
}

if(!function_exists("br2p")) {
	function br2p($string) {
		return nl2p(br2nl($string));
	}
}

if(!function_exists("get_formatted_number")) {
	function get_formatted_number($value) {
		return number_format(floatval($value));
	}
}

if(!function_exists("get_cutted_string")) {
	function get_cutted_string($str, $start, $len=0, $charset="utf-8") {
		$result = "";

		if(function_exists("iconv_substr")) {
			$result = iconv_substr($str, $start, $len, $charset);
		} elseif(function_exists("mb_substr")) {
			$result = mb_substr($str, $start, $len, $charset);
		} else {
			$result = substr($str, $start, $len);
		}

		return $result;
	}
}

if(!function_exists("explode_by_line")) {
	function explode_by_line($str) {
		return preg_split('/\n|\r\n?/', $str);
	}
}

if(!function_exists("read_storage_file_by_line")) {
	function read_storage_file_by_line($filename, $options=array()) {
		return explode_by_line(read_storage_file($filename, $options));
	}
}

if(!function_exists("startsWith")) {
	function startsWith($haystack, $needle) {
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
}

if(!function_exists("endsWith")) {
	function endsWith($haystack, $needle) {
		$length = strlen($needle);
		if($length == 0) {
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}
}
