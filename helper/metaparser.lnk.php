<?php
/**
 * @file metaparser.lnk.php
 * @date 2018-03-21
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief PHP-Metaparser library linker
 */

$inc_file = "./vendor/PHP-Metaparser/MetaParser.class.php";
if(file_exists($inc_file)) {
	include($inc_file);
}
