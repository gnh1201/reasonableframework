<?php
/**
 * @file metaparser.lnk.php
 * @date 2018-03-21
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief PHP-Metaparser library linker
 */

if(!class_exists("MetaParser")) {
	loadVendor("PHP-MetaParser/MetaParser.class");
}
