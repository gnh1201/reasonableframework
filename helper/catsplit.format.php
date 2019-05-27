<?php
/**
 * @file catsplit.format.php
 * @date 2019-05-28
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Catsplit format encoder
 * @documentation https://github.com/gnh1201/catsplit-format
 */

if(!check_function_exists("catsplit_encode")) {
    function catsplit_encode($data) {
        $_ks = array();
        $_vs = array();
        foreach($data as $k=>$v) {
            $_ks[] = $k;
            $_vs[] = make_safe_argument($v);
        }
        return sprintf("('%s')<=(%s)", implode("','", $_vs), implode(",", $_ks));
    }
}
