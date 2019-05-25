<?php
// rsf.format.php

if(!check_function_exists("get_rsf_encoded")) {
    function get_rsf_encoded($data) {
        $_ks = array();
        $_vs = array();
        foreach($data as $k=>$v) {
            $_ks[] = $k;
            $_vs[] = make_safe_argument($v);
        }
        return sprint("(%s)=>('%s')", implode(",", $_ks), implode("','", $_vs));
    }
}
