<?php
/**
 * @file socialhub.utl.php
 * @date 2018-09-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief SocialHub Utilities (refactoring from SocioRouter Utilities)
 */
 
if(!function_exists("socialhub_send_message")) {
	function socialhub_send_message($provider, $adapter, $message, $options=array()) {
		$object_id = false;

		$response = false;
		$status = array(
			"message" => $message
		);
		
		switch($provider) {
			case "facebook":
				$status['link'] = get_value_in_array("link", $options, "");
				$status['picture'] = get_value_in_array("picture", $options, "");
				$response = $adapter->setUserStatus($status);
				break;
	
			case "linkedin":
				$status['content'] => array(
					"title" => get_value_in_array("title", $options, "");
					"description" => get_value_in_array("description", $options, "");
					"submitted-url" => get_value_in_array("link", $options, "");
					"submitted-image-url" => get_value_in_array("picture", $options, "");
				);
				$status['visibility'] => array(
					"code" => "anyone",
				);
				$response = $adapter->setUserStatus($status);
				break;

			case "twitter":
				$status['link'] = get_value_in_array("link", $options, "");
				$status['picture'] = get_value_in_array("picture", $options, "");
				$response = $adapter->setUserStatus($status);
				break;
				
			default:
				set_error("Unknown provider");
				show_errors();
		}
	}
}
