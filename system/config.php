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

            // `parse_ini_string` function is not supported under 5.3.0. Use only 'ini' file
            if(!$is_legacy_version && check_file_extension($file, "ini.php", array("multiple" => true))) {
                $str = include($file);
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
        $config = get_shared_var("config");

        if(!is_array($config)) {
            set_shared_var("config", read_config());
        }

        return get_shared_var("config");
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

if(!check_function_exists("get_current_timestamp")) {
    function get_current_timestamp($options=array()) { 
        $timestamp = time();

        $config = get_config();

        // get timeformat
        $timeformat = get_value_in_array("timeformat", $config, "Y-m-d H:i:s");
        if(!array_key_empty("timeformat", $options)) {
            $timeformat = $options['timeformat'];
        }

        // get time from NTP server
        if(!array_key_empty("timeserver", $config)) {
            if(loadHelper("timetool")) {
                $timestamp = get_server_time($config['timeserver']);
            }
        }

        // set now time
        if(!array_key_empty("now", $options)) {
            try {
                $dateTimeObject = DateTime::createFromFormat($timeformat, $options['now']);
                $timestamp = $dateTimeObject->getTimestamp();
            } catch(Exception $e) {
                $timestamp = strtotime($options['now']);
            }
        }

        // adjust time
        if(!array_key_empty("adjust", $options)) {
            $adjust = trim($options['adjust']);
            $_adjust = "";
            if(strlen($adjust) > 0) {
                $units = array(
                    "s" => array(    1, "second", "seconds"),
                    "m" => array(   60, "minute", "minutes"),
                    "h" => array(  120, "hour",   "hours"  ),
                    "d" => array(86400, "day",    "days"   )
                );
                $_L = intval(substr($adjust, 0, -1));
                $_R = substr($adjust, -1);
                if(array_key_exists($_R, $units)) {
                    if(abs($_L) > 1) {
                        $_adjust = sprintf("%s %s", $_L, $units[$_R][2]);
                    } else {
                        $_adjust = sprintf("%s %s", $_L, $units[$_R][1]);
                    }
                } else {
                    $_adjust = $adjust;
                }
                $timestamp = strtotime($_adjust, $timestamp);
            }
        }

        return $timestamp;
    }
}

if(!check_function_exists("get_current_datetime")) {
    function get_current_datetime($options=array()) {
        $config = get_config();

        // get timeformat
        $timeformat = get_value_in_array("timeformat", $config, "Y-m-d H:i:s");
        if(!array_key_empty("timeformat", $options)) {
            $timeformat = $options['timeformat'];
        }

        // get timestamp
        $timestamp = get_current_timestamp($options);

        // set datetime
        $datetime = date($timeformat, $timestamp);

        return $datetime;
    }
}

if(!check_function_exists("get_old_version")) {
    function get_old_version() {
        $config = get_config();
        return get_value_in_array("old_version", $config, 0);
    }
}
