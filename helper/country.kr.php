<?php
/**
 * @file country.kr.php
 * @date 2018-04-15
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Country Helper (Korean)
 */

// parse telephone number
if(!check_function_exists("get_kr_parsed_tel_number")) {
    function get_kr_parsed_tel_number($tel) {
        $output = preg_replace("/[^0-9]/", "", $tel); // 숫자 이외 제거
        $local_code = substr($tel, 0, 2);
        if ($local_code == '02') {
            $output = preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
        } elseif (strlen($tel) == '8' && in_array($local_code, array('15', '16', '18'))) {
            $output = preg_replace("/([0-9]{4})([0-9]{4})$/", "\\1-\\2", $tel); // 지능망 번호이면
        } else {
            $output = preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
        }
        return $output;
    }
}

if(!check_function_exists("get_kr_get_lastname")) {
    function get_kr_get_lastname() 
        if(loadHelper("string.utils")) {
            $words = read_storage_file_by_line("kr.lastname.txt", array(
                "storage_type" => "country",
            )));
            return $words[get_random_index($words)];
        }
    }
}

if(!check_function_exists("get_kr_get_firstname")) {
    function get_kr_get_firstname() {
        if(loadHelper("string.utils")) {
            $words = read_storage_file_by_line("kr.firstname.txt", array(
                "storage_type" => "country",
            )));
            return $words[get_random_index($words)];
        }
    }
}

if(!check_function_exists("get_kr_get_generated_name")) {
    function get_kr_get_generated_name() {
        return sprintf("%s%s", get_kr_get_lastname(), get_kr_get_firstname());
    }
}
