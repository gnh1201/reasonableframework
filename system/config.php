<?php
/**
 * @file security.php
 * @date 2018-01-18
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Configuration module for VSPF
 */

if(!function_exists("set_config")) {
    function set_config() {
        global $config;

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
    }
}

function get_config() {
	global $config;

	return $config;
}

$config = array();
set_config();

$config = get_config();
