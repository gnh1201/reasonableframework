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
