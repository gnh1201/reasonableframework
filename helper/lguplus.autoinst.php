<?php
/**
 * @file lguplus.autoinst.php
 * @date 2019-10-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief `LGU+`or `LGUPlus` is trandmark of LGUPlus Co. Ltd.
 */

if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

if(!check_function_exists("install_lguplus")) {
    function install_pgkcp() {
        $response = get_web_page("https://openapi.sms.uplus.co.kr/sdkFile/php_sdk.zip");

        $fw = write_storage_file($response['content'], array(
            "extension" => "zip"
        ));
        @unzip($fw, get_storage_path());
        
        // todo
    }
}
