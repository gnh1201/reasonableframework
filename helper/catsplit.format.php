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

if(!check_function_exists("catsplit_decode")) {
	function catsplit_decode($data) {
		$s_final = array();

		// step 1
		$s1 = explode(")<=(", substr($data, 1, -1));

		// step 2
		$s2a = explode(",", $s1[0]);
		$s2b = explode(",", $s1[1]);

		// step 3
		$s3 = array_combine($s2b, $s2a);

		// step 4
		foreach($s3 as $k=>$v) {
			$s_final[$k] = substr(stripslashes($v), 1, -1);
		}

		return $s_final;
	}
}
