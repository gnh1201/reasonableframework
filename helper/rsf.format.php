<?php
// rsf.format.php

if(!check_function_exists("get_rsf_encoded")) {
    function get_rsf_encoded($data) {
        return sprint("(%s)=>('%s')", array_keys($data), implode("','", $data));
    }
}
