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
			"facebook-sdk-v5/src/Facebook/autoload" // support facebook
		);
		foreach($required_files as $file) {
			$inc_file = "./vendor/" . $file . ".php";
			if(!file_exists($inc_file)) {
				set_error("File not exists. " . $inc_file);
				show_erros();
			} else {
				include("./vendor/" . $file . ".php");	
			}
		}
	}
}

