<?php
/**
 * @file string.utl.php
 * @date 2018-05-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief String utility helper
 */

if(!function_exists("add_hyphen")) {
	function add_hyphen($tel) {
		$tel = preg_replace("/[^0-9]/", "", $tel); // 숫자 이외 제거
		if (substr($tel,0,2)=='02')
			return preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
		else if (strlen($tel)=='8' && (substr($tel,0,2)=='15' || substr($tel,0,2)=='16' || substr($tel,0,2)=='18'))
			// 지능망 번호이면
			return preg_replace("/([0-9]{4})([0-9]{4})$/", "\\1-\\2", $tel);
		else
			return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
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
