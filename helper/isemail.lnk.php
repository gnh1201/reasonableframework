<?php
/**
 * @file isemail.lnk.php
 * @date 2018-03-02
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief IsEmail library linker
 */

if(!check_function_exists("is_email")) {
    $inc_file = "./vendor/_dist/isemail/is_email.php";
    if(file_exists($inc_file)) {
        include($inc_file);
    }
}
