<?php
/**
 * @file logger.php
 * @date 2018-05-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Logger module for ReasonableFramework
 */
 
if(!function_exists("write_visit_log")) {
	function write_visit_log() {
		$fw = false;
		if(loadHelper("networktool")) {
			$data = "\r\n" . json_encode(get_network_event());
			$fw = write_storage_file($data, array(
				"storage_type" => "logs",
				"filename" => "vspf-network.log",
				"mode" => "a"
			));
		}

		return $fw;
	}
}
