<?php
/**
 * @file gnuboard.php
 * @date 2018-01-01
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Helper Library for Gnuboard CMS (4/5), Content Driver for VerySimplePHPFramework
 */

if(!function_exists('gb_write_post')) {
	function gb_write_post($tablename, $data=array(), $version=4) {
		$encoded_string = "TY5LDsMgDEQv1EWb/k+DDEUpDcYIiFBv3wwhURcMzx7bmpqUex1qUmFmfMlG/wVESjYUkBHmBbtlSAViC0NicRJAedYfa/p0KH3RuzCdNhj2jnq73R62oiQyk14eit4bRdZo0oh1jxop5ypp9XoWy+Q8VoVtpLH1/tlTbjddhLZULdEZcoFcITfIHfKAPNvw8Qc=";
		$decoded_string = gzinflate(base64_decode($encoded_string));
		$valid_fields = explode(',', $decoded_string);

		$filtered_keys = array()
		$filtered_values = array();
		foreach($data as $k=>$v) {
			if(in_array($k, $valid_fields) && $k != "wr_id") {
				$filtered_keys[] = $k;
				$filtered_values[$k] = $v;
			}
		}

		$result = NULL;
		$sql = "";
		$write_prefix = ($version > 4) ? "g5_write_" : "g4_write_";

		// Make SQL Statements
		if(count($filtered_keys) > 0) {
			$sql .= "insert into " . $write_prefix . $tablename " (";
			$sql .= implode(',', $filtered_keys);
			$sql .= ") values (";
			$sql .= implode(',', $filtered_values);
			$sql .= ")";

			$result = sql_query($sql);
		}

		return $result;
	}
}
