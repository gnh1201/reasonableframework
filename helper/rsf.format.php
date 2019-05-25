<?php
/**
 * @file rsf.format.php
 * @date 2019-05-28
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief the another data format for ReasonableFramework
 */

if(!check_function_exists("get_rsf_encoded")) {
    function get_rsf_encoded($data) {
        $_ks = array();
        $_vs = array();
        foreach($data as $k=>$v) {
            $_ks[] = $k;
            $_vs[] = make_safe_argument($v);
        }
        return sprintf("('%s')=>(%s)", implode("','", $_vs), implode(",", $_ks));
    }
}
