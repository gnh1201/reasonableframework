<?php
/**
 * @file obfuscator.php
 * @date 2018-10-21
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief PHP Obfuscator for ReasonableFramework
 */

if(!check_function_exists("get_obfuscator")) {
  function get_obfuscator() {
    $obfuscator = rand(1, 15);
    set_scope("obfuscator", $obfuscator);
    return $obfuscator;
  }
}

if(!check_function_exists("get_obfuscated_result")) {
  function get_obfuscated_result($raw) {
    $result = false;

    switch($method) {
      case 1: $result = base64_encode(str_rot13(gzdeflate($raw))); break;
      case 2: $result = str_rot13(base64_encode(gzdeflate($raw)); break;
      case 3: $result = str_rot13(base64_encode(base64_encode(gzdeflate($raw)))); break;
      case 4: $result = base64_encode(gzcompress($raw)); break;
      case 5: $result = base64_encode(str_rot13(gzcompress($raw))); break;
      case 6: $result = str_rot13(base64_encode(gzcompress($raw))); break;
      case 7: $result = base64_encode($raw); break;
      case 8: $result = base64_encode(gzdeflate(str_rot13($raw))); break;
      case 9: $result = str_rot13(strrev(base64_encode(gzdeflate($raw)))); break;
      case 10: $result = strrev(base64_encode(gzdeflate($raw))); break;
      case 11: $result = str_rot13(base64_encode(gzdeflate($raw))); break;
      case 12: $result = strrev(str_rot13(base64_encode(gzdeflate($raw)))); break;
      case 13: $result = base64_encode(gzcompress(base64_encode($raw))); break;
      case 14: $result = rawurlencode(base64_encode(gzdeflate($raw))); break;
      case 15: $result = base64_encode(str_rot13(gzdeflate(str_rot13($raw)))); break;
      default: $result = get_obfuscated_result($raw, get_obfuscator());
    }

    return $result;
  }
}
