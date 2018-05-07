<?php
/**
 * @file stroage.php
 * @date 2018-05-05
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Stroage module
 */

if(function_exists("get_storage_dir")) {
	function get_storage_dir() {
		$config = get_config();
		return get_value_in_array("storage_dir", $config, "storage");
	}
}

if(!function_exists("get_storage_path")) {
	function get_storage_path($type) {
		return sprintf("./%s/%s", get_storage_dir(), $type);
	}
}

if(!function_exists("get_storage_url")) {
	function get_storage_url($type) {
		return sprintf("%s%s/%s", base_url(), get_storage_dir(), $type);
	}
}

if(!function_exists("move_uploaded_file_to_storage")) {
	function move_uploaded_file_to_stroage($type="data", $image=false) {
		$response = array("files" => array());

		$upload_base_dir = get_storage_path($type);
		$upload_base_url = get_storage_url($type);

		if($image == true) {
			$upload_allow_ext = array("png", "gif", "jpg", "jpeg", "tif");
		} else {
			$upload_allow_ext = array();
		}

		foreach($requests['files'] as $k=>$file) {
			$upload_ext = pathinfo($requests['files'][$k]['name'], PATHINFO_EXTENSION);
			$upload_name = make_random_id(10) . (empty($upload_ext) ? "" : "." . $upload_ext);
			$upload_file = $upload_base_dir . $upload_name;
			$upload_url = $upload_base_url . $upload_name;

			if(count($upload_allow_ext) == 0 || in_array($upload_ext, $upload_allow_ext)) {
				if(move_uploaded_file($requests['files'][$k]['tmp_name'], $upload_file)) {
					$response['files'][] = array(
						"upload_ext" => $upload_ext,
						"upload_name" => $upload_name,
						"upload_file" => $upload_file,
						"upload_url" => $upload_url,
						"upload_error" => ""
					);
				} else {
					$response['files'][] = array(
						"upload_error" => "File write error."
					);
				}
			} else {
				$response['files'][] = array(
					"upload_error" => "Not allowed file type."
				);
			}
		}

		return $response['files'];
	}
}


function read_storage_file($filename, $offset=0, $length=0) {
    // todo
}
