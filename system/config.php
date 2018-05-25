<?php
/**
 * @file config.php
 * @date 2018-04-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Configuration module
 */

if(!function_exists("read_config")) {
        function read_config() {
                $config = array();

                $files = retrieve_storage_files("config");
                foreach($files as $file) {
                        if(check_file_extension($file, "ini")) {
                                $ini = parse_ini_file($file);
                                foreach($ini as $k=>$v) {
                                        $config[$k] = $v;
                                }
                        }
                }

                return $config;
        }
}

if(!function_exists("get_config")) {
	function get_config() {
		$config = get_scope("config");

		if(!is_array($config)) {
			set_scope("config", read_config());
		}

		return get_scope("config");
	}
}

if(!function_exists("get_config_value")) {
	function get_config_value($key) {
		$config = get_config();

		$config_value = "";
		if(!array_key_empty($key, $config)) {
			$config_value = $config[$key];
		}

		return $config_value;
	}
}

if(!function_exists("get_current_datetime")) {
    function get_current_datetime() {
        $config = get_config();
        return date(get_value_in_array("timeformat", $config, "Y-m-d H:i:s"));
    }
}

set_scope("config", read_config());
