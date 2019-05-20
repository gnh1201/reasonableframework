<?php
/**
  * @file hybridauth.lnk.php
  * @date 2018-09-26
  * @author Go Namhyeon <gnh1201@gmail.com>
  * @brief HybridAuth library RSF Linker
***/

if(!check_function_exists("hybridauth_load")) {
    function hybridauth_load($provider="") {
        $result = false;

        $configfile = "./vendor/hybridauth/hybridauth/config.php";
        $required_files = array(
            "hybridauth/hybridauth/Hybrid/Auth",
            "hybridauth/hybridauth/Hybrid/Endpoint"
        );

        // support facebook (php graph api v5)
        $provider = strtolower($provider);
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

if(!check_function_exists("hybridauth_check_redirect")) {
    function hybridauth_check_redirect() {
        $flag = false;
        $requests = get_requests();

        if(loadHelper("string.utils")) {
            foreach($requests['_ALL'] as $k=>$v) {
                if(startsWith($k, "hauth")) {
                    $flag = true;
                    break;
                }
            }
        }

        return $flag;
    }
}

if(!check_function_exists("hybridauth_process")) {
    function hybridauth_process() {
        Hybrid_Endpoint::process();
    }
}
