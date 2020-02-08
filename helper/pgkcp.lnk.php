<?php
/**
 * @file pgkcp.lnk.php
 * @created_on 2018-08-25
 * @updated_on 2020-01-13
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief KCP PG(Payment Gateway) Helper
 */

if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

loadHelper("json.format");
loadHelper("webpagetool");
loadHelper("compress.zip");
loadHelper("exectool");

if(!check_function_exists("get_pgkcp_config")) {
    function get_pgkcp_dir() {
        return get_current_working_dir() . "/vendor/_dist/pgkcp";
    }
}

if(!check_function_exists("get_pgkcp_config")) {
    function get_pgkcp_config() {
        $pgkcp_config = array();

        // include configuration file
        $inc_file = get_pgkcp_dir() . "/cfg/site_conf_inc.php";
        if(file_exists($inc_file)) {
            include($inc_file);

            $pgkcp_config = array(
                "g_conf_home_dir" => $g_conf_home_dir,
                "g_conf_log_path" => $g_conf_log_path,
                "g_conf_gw_url" => $g_conf_gw_url,
                "g_conf_js_url" => $g_conf_js_url,
                "g_wsdl" => $g_wsdl,
                "g_conf_site_cd" => $g_conf_site_cd,
                "g_conf_site_key" => $g_conf_site_key,
                "g_conf_site_name" => $g_conf_site_name,
                "g_conf_log_level" => $g_conf_log_level,
                "g_conf_gw_port" => $g_conf_gw_port,
                "module_type" => $module_type,
            );
            
            // read configuration file
            $fr = read_storage_file("api.config.pgkcp.json", array(
                "storage_type" => "payman"
            ));
            if(!empty($fr)) {
                $_pgkcp_config = json_decode($fr);
                $pgkcp_config['g_conf_gw_url'] = get_property_value("g_conf_gw_url", $_pgkcp_config);
                $pgkcp_config['g_conf_js_url'] = get_property_value("g_conf_js_url", $_pgkcp_config);
                $pgkcp_config['g_conf_site_cd'] = get_property_value("g_conf_site_cd", $_pgkcp_config);
                $pgkcp_config['g_conf_site_key'] = get_property_value("g_conf_site_key", $_pgkcp_config);
                $pgkcp_config['g_conf_site_name'] = get_property_value("g_conf_site_name", $_pgkcp_config);
            }
        } else {
            set_error("PGKCP configuration file does not exists.");
            show_errors();
        }

        // check installed platform
        $platform = get_pgkcp_platform($pgkcp_config);
        if(empty($platform)) {
            set_error("pp_cli(pp_cli.exe) file is not found or executable");
            show_errors();
        } else {
            $pgkcp_config['g_conf_platform'] = $platform;
        }

        return $pgkcp_config;
    }
}

if(!check_function_exists("get_pgkcp_platform")) {
    function get_pgkcp_platform($pgkcp_config) {
        $platform = false;

        $executables = array(
            "default" => $pgkcp_config['g_conf_home_dir'] . "/bin/pp_cli",
            "win32" => $pgkcp_config['g_conf_home_dir'] . "/bin/pp_cli.exe"
        );

        foreach($executables as $k=>$v) {
            if(file_exists($v) && is_executable($v)) {
                $platform = $k;
                break;
            }
        }

        return $platform;
    }
}

if(!check_function_exists("load_pgkcp_library")) {
    function load_pgkcp_library() {
        $inc_file = get_pgkcp_dir() . "/sample/pp_cli_hub_lib.php";
        if(file_exists($inc_file)) {
            include($inc_file);
        } else {
            set_error("PGKCP payment library file does not exists.");
            show_errors();
        }
    }
}
