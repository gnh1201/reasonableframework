<?php
/**
 * @file api.setconfig.pgkcp.php
 * @date 2018-09-30
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief KCP PG(Payment Gateway) set configuration API
 */

loadHelper("pgkcp.lnk");
 
$site_cd = get_requested_value("site_cd");
$site_key = get_requested_value("site_key");
$site_name = get_requested_value("site_name");
$mode = get_requested_value("mode");

$api_config = array();
$config_filename = "api.config.pgkcp.json";

if($mode == "clear") {
    $rm = remove_stroage_file($config_filename, array(
        "storage_type" => "payman"
    ));
    if(!$rm) {
        echo get_callable_token("failed");
    } else {
        echo get_callable_token("success");
    }

    exit;
}

if($mode == "test") {
    $api_config['g_conf_gw_url'] = "testpaygw.kcp.co.kr";
    $api_config['g_conf_js_url'] = "https://testpay.kcp.co.kr/plugin/payplus_web.jsp";
    $api_config['g_conf_site_cd'] = "T0000";
    $api_config['g_conf_site_key'] = "3grptw1.zW0GSo4PQdaGvsF__";
    $api_config['g_conf_site_name'] = get_generated_name();
} else {
    $api_config['g_conf_gw_url'] = "paygw.kcp.co.kr";
    $api_config['g_conf_js_url'] = "https://pay.kcp.co.kr/plugin/payplus_web.jsp";
    $api_config['g_conf_site_cd'] = $site_cd;
    $api_config['g_conf_site_key'] = $site_key;
    $api_config['g_conf_site_name'] = (empty($site_name) ? get_generated_name() : $site_name);
}

$api_config_encoded = json_encode($api_config);
$fw = write_storage_file($api_config_encoded, array(
    "storage_type" => "payman",
    "filename" => $config_filename
));
if(!$fw) {
    echo get_callable_token("failed");
} else {
    echo get_callable_token("success");
}
