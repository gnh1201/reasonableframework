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
	function get_storage_path($type="data") {
		return sprintf("./%s/%s", get_storage_dir(), $type);
	}
}

if(!function_exists("get_storage_url")) {
	function get_storage_url($type="data") {
		return sprintf("%s%s/%s", base_url(), get_storage_dir(), $type);
	}
}

if(!function_exists("move_uploaded_file_to_storage")) {
	function move_uploaded_file_to_stroage($options=array()) {
		$files = array();
		$requests = get_requests();

		$storage_type = get_value_in_array("storage_type", $options, "data");
		$upload_base_dir = get_storage_path($storage_type);
		$upload_base_url = get_storage_url($storage_type);

		if(!array_key_empty("only_image", $options)) {
			$upload_allow_ext = array(
				"png", "gif", "jpg", "jpeg", "tif"
			);
		} elseif(!array_key_empty("only_docs", $options)) {
			$upload_allow_ext = array(
				"png", "gif", "jpg", "jpeg", "tif",
				"xls", "ppt", "doc", "xlsx", "pptx",
				"docx", "odt", "odp", "ods", "xlsm",
				"tiff", "pdf", "xlsm"
			);
		} elseif(!array_key_empty("only_audio", $options)) {
			$upload_allow_ext = array(
				"mp3", "ogg", "m4a", "wma", "wav"
			);
		} else {
			$upload_allow_ext = array();
		}

		foreach($files as $k=>$file) {
			$upload_ext = pathinfo($files[$k]['name'], PATHINFO_EXTENSION);
			$upload_name = make_random_id(32) . (empty($upload_ext) ? "" : "." . $upload_ext);
			$upload_file = $upload_base_dir . $upload_name;
			$upload_url = $upload_base_url . $upload_name;

			if(count($upload_allow_ext) == 0 || in_array($upload_ext, $upload_allow_ext)) {
				if(move_uploaded_file($files[$k]['tmp_name'], $upload_file)) {
					$response['files'][] = array(
						"storage_type" => $storage_type,
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

if(!function_exists("read_storage_file")) {
	function read_storage_file($filename, $options=array()) {
		$fcontents = "";
		$storage_type = get_value_in_array("storage_type", $options, "data");
		$upload_base_path = get_storage_path($storage_type);
		$upload_base_url = get_storage_url($storage_type);
		$upload_filename = $upload_base_path . "/" . $filename;

		if($fhandle = fopen($upload_filename, "r")) {
			$fcontents = fread($fhandle, filesize($filename));
			fclose($fhandle);
		}

		if(!array_key_empty("encode_base64", $options)) {
			$fcontents = base64_encode($file_contents);
		}

		return $fcontents;
	}
}

if(!function_exists("write_storage_file")) {
	function write_storage_file($data, $options=array()) {
		$result = "";
		$filename = make_random_id(32);

		$storage_type = get_value_in_array("storage_type", $options, "data");
		$upload_base_path = get_storage_path($storage_type);
		$upload_base_url = get_storage_url($storage_type);
		$upload_filename = $upload_base_path . "/" . $filename;
		
		if($fhandle = fopen($upload_filename)) {
			fwrite($fhandle, $data);
			$result = $upload_filename;
			fclose($fhandle);
		} else {
			set_error("maybe, your storage is write-protected.");
			show_errors();
		}
		
		return $result;
	}
}
