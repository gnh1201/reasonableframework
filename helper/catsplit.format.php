<?php
/**
 * @file catsplit.format.php
 * @date 2019-05-28
 * @author Go Namhyeon <abuse@catswords.net>
 * @brief Catsplit format encoder
 * @documentation https://github.com/gnh1201/catsplit-format
 */

if(!is_fn("catsplit_unescape")) {
    function catsplit_unescape($data) {
        return trim($data);
    }
}

if(!is_fn("casplit_escape")) {
    function casplit_escape($data) {
        return htmlspecialchars($data);
    }
}

if(!is_fn("catsplit_encode")) {
    function catsplit_encode($data) {
        $_ks = array();
        $_vs = array();
        foreach($data as $k=>$v) {
            $_ks[] = $k;
            $_vs[] = make_safe_argument($v);
        }
        $_ks = array_map("casplit_escape", $_ks);
        $_vs = array_map("casplit_escape", $_vs);
        
        return sprintf("('%s')<=(%s)", implode("','", $_vs), implode(",", $_ks));
    }
}

if(!is_fn("catsplit_decode")) {
    function catsplit_decode($data) {
        $s_final = array();

        // step 1
        $s1 = explode(")<=(", substr($data, 1, -1));

        // step 2
        $s2a = array_map("catsplit_unescape", explode(",", $s1[0]));
        $s2b = array_map("catsplit_unescape", explode(",", $s1[1]));

        // step 3
        $s3 = array_combine($s2b, $s2a);

        // step 4
        foreach($s3 as $k=>$v) {
            $s_final[$k] = substr(stripslashes($v), 1, -1);
        }

        return $s_final;
    }
}
