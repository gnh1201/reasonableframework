<?php
/**
  * @file hybridauth.lnk.php
  * @date 2018-09-26
  * @author Go Namhyeon <gnh1201@gmail.com>
  * @brief HybridAuth library RSF Linker
***/

if(!function_exists("load_hybridauth")) {
	function load_hybridauth() {
		$required_files = array(
			"hybridauth/hybridauth/library/Hybrid/Auth",
			"hybridauth/hybridauth/library/Hybrid/Endpoint",
			"facebook-sdk-v5/src/Facebook/autoload"
		);
		foreach($required_files as $file) {
			include("./vendor/" . $file . ".php");	
		}
	}
}

