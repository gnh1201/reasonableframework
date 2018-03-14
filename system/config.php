<?php
/**
 * @file config.php
 * @date 2018-01-18
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Configuration module for VSPF
 */

if(!function_exists("read_config")) {
    function read_config() {
	$config = array();

        if($handle = opendir('./config')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && @end(explode('.', $file)) == 'ini') {
                    $ini = parse_ini_file('./config/' . $file);
                    foreach($ini as $k=>$v) {
                        $config[$k] = $v;
                    }
                }
            }
            closedir($handle);
        }

	return $config;
    }
}

if(!function_exists("get_config")) {
	function get_config() {
		global $config;
		$config = is_array($config) ? $config : read_config();
		return $config;
	}
}

if(!function_exists("get_config_value")) {
	function get_config_value($key) {
		$config = get_config();

		$config_value = '';
		if(!array_key_empty($key, $config)) {
			$config_value = $config[$key];
		}

		return $config_value;
	}
}

$config = read_config();
