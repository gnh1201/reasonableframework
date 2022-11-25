<?php
/**
 * @file pgkcp.install.php
 * @date 2019-10-13
 * @author Go Namhyeon <abuse@catswords.net>
 * @brief KCP PG(Payment Gateway) Auto-Install Tool
 */

if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

loadHelper("pgkcp.lnk");

if(!is_fn("install_pgkcp")) {
    function install_pgkcp() {
        $response = get_web_page("https://admin8.kcp.co.kr/assist/download/sampleDownload", "get", array(
            "type1" => "FM01",
            "type2" => "FS04"
        ));

        // step 1
        $fw = write_storage_file($response['content'], array(
            "extension" => "zip"
        ));
        @unzip($fw, get_storage_path());

        // step 2
        $fw = write_storage_file("", array(
            "mode" => "fake",
            "filename" => sprintf("NHNKCP_PAYMENT_STANDARD_PHP/NHNKCP_PAYMENT_STANDARD_LINUX_PHP.zip"),
        ));
        @unzip($fw, get_storage_path());

        // step 3
        exec_command("cp -r %s/NHNKCP_PAYMENT_STANDARD_LINUX_PHP/* %s/", get_storage_path(), get_pgkcp_dir());

        // if success, directory exists
        return is_dir(get_pgkcp_dir());
    }
}
