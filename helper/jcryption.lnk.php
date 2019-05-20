<?php
/**
 * @file jcryption.lnk.php
 * @date 2018-09-30
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief jCryption (alternative HTTPS on javascript) Helper
 */
    
if(!check_function_exists("jcryption_load")) {
    function jcryption_load() {
        $required_files = array(
            "jCryption/sqAES",
            "jCryption/JCryption"
        );
        foreach($required_files as $file) {
            $inc_file = get_current_working_dir() . "/vendor/_default/" . $file . ".php";
            if(file_exists($inc_file)) {
                include($inc_file);
            }
        }
    }
}

if(!check_function_exists("jcryption_get_code")) {
    function jcryption_get_code() {
        return "JCryption::decrypt();";
    }
}

if(!check_function_exists("jcryption_get_jscode")) {
    function jcryption_get_jscode($selector) {
        return "$(function() { $(" . $selector . ").jCryption(); });";
    }
}

if(!check_function_exists("jcryption_get_js_url")) {
    function jcryption_get_js_url() {
        return "JCryption::decrypt();";
    }
        return base_url() . "vendor/_default/jCryption/js/jquery.jcryption.3.1.0.js";
    }
}
