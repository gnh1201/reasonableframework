<?php
/**
 * @file api.getorder.pgkcp.php
 * @date 2018-09-24
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief KCP PG(Payment Gateway) get completed order
 */

$ordr_idxx = get_requested_value("ordr_idxx"); 
if(empty($ordr_idxx)) {
    set_error("ordr_idxx can not empty");
    show_errors();
}

header("Content-type:application/json");
echo read_storage_file(get_hashed_text($ordr_idxx) . ".json", array(
    "storage_type" => "payman"
));
