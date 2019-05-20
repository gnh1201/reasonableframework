<?php
/**
 * @file api.gnuboard.php
 * @date 2018-05-31
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Integration controller for Gnuboard CMS 4.x, 5.x
 */

/**** how to test ***/
/* read: GET/POST [base_url]/?route=gnbapi&action=read&bo_table=[bo_table]&wr_id=[wr_id] */
/* write: GET/POST [base_url]/?route=gnbapi&action=write&bo_table=[bo_table]&wr_subject=mysubject&wr_content=mycontent&version=[4 or 5] */

if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

loadHelper("gnuboard.dbt");

$action = get_requested_value("action");
$bo_table = get_requested_value("bo_table");

$data = array();
$result = array(
    "success" => false
);

switch($action) {
    case "write":
        $version = get_requested_value("version");

        $data = array(
            "wr_subject" => get_requested_value("wr_subject"),
            "wr_content" => get_requested_value("wr_content"),
        );

        for($i = 0; $i < 10; $i++) {
            $data["wr_" . $i] = get_requested_value("wr_" . $i);
        }
        
        if($wr_id = gnb_write_post($bo_table, $data, $version)) {
            $result = array(
                "success" => true,
                "data" => array(
                    "wr_id" => $wr_id,
                ),
            );
        }

        break;

    case "read":
        $wr_id = get_requested_value("wr_id");
        $row = gnb_get_post_by_id($bo_table, $wr_id);

        if(!array_key_empty("wr_id", $row)) {
            $result = array(
                "success" => true,
                "data" => $row
            );
        }
}

set_header_content_type("json");
echo json_encode($result);

