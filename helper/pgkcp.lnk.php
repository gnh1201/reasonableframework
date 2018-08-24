<?php
/**
 * @file pgkcp.lnk.php
 * @date 2018-08-25
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief KCP PG(Payment Gateway) Helper
 */

if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

// include configuration file
$inc_file = get_current_working_dir() . "/vendor/pgkcp/cfg/site_conf_inc.php";
if(file_exists($inc_file)) {
	include($inc_file);
} else {
	set_error("PGKCP Configuration File does not exists.");
	show_errors();
}
