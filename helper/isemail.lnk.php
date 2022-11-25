<?php
/**
 * @file isemail.lnk.php
 * @date 2018-03-02
 * @author Go Namhyeon <abuse@catswords.net>
 * @brief IsEmail library linker
 */

if(!is_fn("is_email")) {
    $inc_file = "./vendor/_dist/isemail/is_email.php";
    if(file_exists($inc_file)) {
        include($inc_file);
    }
}
