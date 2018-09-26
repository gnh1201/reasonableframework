<?php
/**
  * @file hybridauth.lnk.php
  * @date 2018-09-26
  * @author Go Namhyeon <gnh1201@gmail.com>
  * @brief HybridAuth library RSF Linker
***/

if(!function_exists("load_hybridauth")) {
	function load_hybridauth($provider="") {
		$result = false;

		$configfile = "./vendor/hybridauth/hybridauth/config.php";
		$required_files = array(
			"hybridauth/hybridauth/Hybrid/Auth",
			"hybridauth/hybridauth/Hybrid/Endpoint"
		);

		// support facebook (php graph api v5)
		switch($provider) {
			case "facebook":
				$required_files[] = "facebook-sdk-v5/src/Facebook/autoload";
				break;
		}

		// load required files
		foreach($required_files as $file) {
			$inc_file = "./vendor/" . $file . ".php";
			if(!file_exists($inc_file)) {
				set_error("File not exists. " . $inc_file);
				show_errors();
			} else {
				include("./vendor/" . $file . ".php");	
			}
		}

		if(file_exists($configfile)) {
			$result = $configfile;
		}

		return $result;
	}
}

if(!function_exists("check_hybridauth")) {
	function check_hybridauth() {
		$flag = false;
		$requests = get_requests();
		
		if(loadHelper("string.utl")) {
			foreach($requests['_ALL'] as $k=>$v) {
				if(startsWith($k, "hauth.")) {
					$flag = true;
					break;
				}
			}
		}

		return $flag;
	}
}
