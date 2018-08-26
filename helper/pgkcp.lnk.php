<?php
/**
 * @file pgkcp.lnk.php
 * @date 2018-08-25
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief KCP PG(Payment Gateway) Helper
 */

if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

if(!function_exists("get_pgkcp_config")) {
	function get_pgkcp_config() {
		$pgkcp_config = array();

		// include configuration file
		$inc_file = get_current_working_dir() . "/vendor/pgkcp/cfg/site_conf_inc.php";
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
		} else {
			set_error("PGKCP Configuration File does not exists.");
			show_errors();
		}
		
		return $pgkcp_config;
	}
}
