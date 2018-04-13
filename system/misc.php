<?php
/**
 * @file misc.php
 * @date 2018-04-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Misc module
 */

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
