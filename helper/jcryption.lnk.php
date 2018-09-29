<?php
/**
 * @file jcryption.lnk.php
 * @date 2018-09-30
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief jCryption Helper
 */
  
if(!function_exists("jcryption_load")) {
  function jcryption_load() {
    $required_files = array(
      "jCryption/sqAES",
      "jCryption/JCryption"
    );
    foreach($required_files as $file) {
      $inc_file = "./vendor/" . $file . ".php";
      if(file_exists($inc_file)) {
        include($inc_file);
      }
    }
  }
}

if(!function_exists("jcryption_get_code")) {
  function jcryption_get_code() {
    return "JCryption::decrypt();";
  }
}

if(!function_exists("jcryption_get_jscode")) {
  function jcryption_get_jscode($selector) {
    return "$(function() { $(" . $selector . ").jCryption(); });";
  }
}
