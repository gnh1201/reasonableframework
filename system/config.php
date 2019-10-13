<?php
/**
 * @file config.php
 * @date 2018-04-13
 * @updated 2019-10-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Configuration module
 */

if(!check_function_exists("read_config")) {
    function read_config() {
        $config = array();
		$is_legacy_version = version_compare(phpversion(), "5.3.0", "<");

        $files = retrieve_storage_dir("config");
        foreach($files as $file) {
            $ini = array();

			// `parse_ini_string` function is not supported under 5.3.0. if you use legacy, please use `.ini` file only.
            if(!$is_legacy_version && check_file_extension($file, "ini.php", array("multiple" => true))) {
                $ini = parse_ini_string($str);
            } elseif(check_file_extension($file, "ini")) {
                $ini = parse_ini_file($file);
            }

            foreach($ini as $k=>$v) {
                $config[$k] = $v;
            }
        }

        return $config;
    }
}

if(!check_function_exists("get_config")) {
    function get_config() {
        $config = get_scope("config");

        if(!is_array($config)) {
            set_scope("config", read_config());
        }

        return get_scope("config");
    }
}

if(!check_function_exists("get_config_value")) {
    function get_config_value($key) {
        $config = get_config();

        $config_value = "";
        if(!array_key_empty($key, $config)) {
            $config_value = $config[$key];
        }

        return $config_value;
    }
}

if(!check_function_exists("get_current_datetime")) {
    function get_current_datetime($options=array()) {
        $datetime = false;

        $config = get_config();
        $timestamp = time();
        $timeformat = get_value_in_array("timeformat", $config, "Y-m-d H:i:s");

        if(!array_key_empty("timeserver", $config)) {
            if(loadHelper("timetool")) {
                $timestamp = get_server_time($config['timeserver']);
            }
        }

        if(!array_key_empty("now", $options)) {
            try {
                $dateTimeObject = \DateTime::createFromFormat($timeformat, $options['now']);
                $timestamp = $dateTimeObject->getTimestamp();
            } catch(Exception $e) {
                $timestamp = strtotime($options['now']);
            }
        }

        if(!array_key_empty("adjust", $options)) {
            $timestamp = strtotime($options['adjust'], $timestamp);
        }

        $datetime = date($timeformat, $timestamp);
        return $datetime;
    }
}
