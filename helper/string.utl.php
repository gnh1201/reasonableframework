<?php
/**
 * @file string.utl.php
 * @date 2018-05-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief String utility helper
 */

// for Korean Telephone Number
if(!check_function_exists("parse_tel_number_kr")) {
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

if(!check_function_exists("get_converted_string")) {
	function get_converted_string($str, $to_charset, $from_charset) {
		$result = false;

		if($form_charset == "detect") {
			$fn = check_invalid_function(array(
				"NO_FUNCTION_MB_DETECT_ENCODING" => "mb_detect_encoding",
				"NO_FUNCTION_MB_DETECT_ORDER" => "mb_detect_order",
			));

			if($fn == -1) {
				$from_charset = mb_detect_encoding($str, mb_detect_order(), true);
			} else {
				$from_charset = "ISO-8859-1";
			}
		}

		if(check_function_exists("iconv")) {
			$result = iconv($from_charset, $to_charset, $str);
		} elseif(check_function_exists("mb_convert_encoding")) {
			$result = mb_convert_encoding($str, $to_charset, $from_charset);
		}

		return $result;
	}
}

if(!check_function_exists("nl2p")) {
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

if(!check_function_exists("br2nl")) {
	function br2nl($string) {
		return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string); 
	}
}

if(!check_function_exists("br2p")) {
	function br2p($string) {
		return nl2p(br2nl($string));
	}
}

if(!check_function_exists("get_formatted_number")) {
	function get_formatted_number($value) {
		return number_format(floatval($value));
	}
}

if(!check_function_exists("get_cutted_string")) {
	function get_cutted_string($str, $start, $len=0, $charset="utf-8") {
		$result = "";

		if(check_function_exists("iconv_substr")) {
			$result = iconv_substr($str, $start, $len, $charset);
		} elseif(check_function_exists("mb_substr")) {
			$result = mb_substr($str, $start, $len, $charset);
		} else {
			$result = substr($str, $start, $len);
		}

		return $result;
	}
}

if(!check_function_exists("explode_by_line")) {
	function explode_by_line($str) {
		return preg_split('/\n|\r\n?/', $str);
	}
}

if(!check_function_exists("read_storage_file_by_line")) {
	function read_storage_file_by_line($filename, $options=array()) {
		return explode_by_line(read_storage_file($filename, $options));
	}
}

// https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
if(!check_function_exists("startsWith")) {
	function startsWith($haystack, $needle) {
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
}

if(!check_function_exists("endsWith")) {
	function endsWith($haystack, $needle) {
		$length = strlen($needle);
		if($length == 0) {
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}
}

// https://stackoverflow.com/questions/4955433/php-multiple-delimiters-in-explode/27767665#27767665
if(!check_function_exists("multiexplode")) {
	function multiexplode($delimiters, $string) {
		$ready = str_replace($delimiters, $delimiters[0], $string);
		$launch = explode($delimiters[0], $ready);
		return $launch;
	}
}

if(!check_function_exists("parse_pipelined_data")) {
	function parse_pipelined_data($pipelined_data, $keynames=array()) {
		$result = array();
		$parsed_data = explode("|", $pipelined_data);

		if(count($keynames) > 0) {
			$i = 0;
			foreach($keynames as $name) {
				$result[$name] = $parsed_data[$i];
				$i++;
			}
		} else {
			$result = $parsed_data;
		}

		return $result;
	}
}

if(!check_function_exists("eregi_compatible")) {
	function eregi_compatible($pattern, $subject, &$matches=NULL) {
		return preg_match(sprintf("/%s/i", $pattern), $subject, $matches);
	}
}

if(!check_function_exists("eregi_replace_compatible")) {
	function eregi_replace_compatible($pattern, $replacement, $subject) {
		return preg_replace(sprintf("/%s/i", $pattern), $replacement, $subject);
	}
}
