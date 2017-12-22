<?php
if(!function_exists("base_url")) {
	function base_url() {
		global $config;
		
		$base_url = '';
		if(array_key_exists("base_url", $config)) {
			$base_url = $config["base_url"];
		}

		return $base_url;
	}
}
