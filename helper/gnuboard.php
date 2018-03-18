<?php
/**
 * @file gnuboard.php
 * @date 2018-01-01
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Helper Library for Gnuboard CMS (4/5), Content Driver for ReasonableFramework
 */

if(!function_exists("gb_get_write_table")) {
	function gb_get_write_table($tablename, $version=4) {
		$write_prefix = ($version > 4) ? "g5_write_" : "g4_write_";
		$write_table = $write_prefix . $tablename;
		return $write_table;
	}
}

if(!function_exists('gb_write_post')) {
	function gb_write_post($tablename, $data=array(), $version=4) {
		$result = false;
		
		$my_fields = "";

		$my_fields .= "wr_id,wr_num,wr_reply,wr_parent,wr_comment_reply,";
		$my_fields .= "ca_name,wr_option,wr_subject,wr_content,wr_link1,";
		$my_fields .= "wr_link2,wr_link1_hit,wr_link2_hit,wr_trackback,wr_hit,";
		$my_fields .= "wr_good,wr_nogood,mb_id,wr_password,wr_name,";
		$my_fields .= "wr_email,wr_homepage,wr_homepage,wr_last,wr_ip,";
		$my_fields .= "wr_1,wr_2,wr_3,wr_4,wr_5,wr_6,wr_7,wr_8,wr_9,wr_10";

		$valid_fields = explode(',', $my_fields);

		$filtered_keys = array();
		$filtered_values = array();
		foreach($data as $k=>$v) {
			if(in_array($k, $valid_fields) && $k != "wr_id") {
				$filtered_keys[] = $k;
				$filtered_values[$k] = $v;
			}
		}

		$sql = "";
		$write_table = gb_get_write_tabled($tablename);

		// make SQL statements
		if(count($filtered_keys) > 0) {
			$sql .= "insert into " . $write_table . " (";
			$sql .= implode(', ', $filtered_keys); // key names
			$sql .= ") values (";
			$sql .= implode(', :', $filtered_keys); // bind key names
			$sql .= ")";

			$result = sql_query($sql, $filtered_values);
		}

		return $result;
	}
}

