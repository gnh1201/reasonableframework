<?php
/**
 * @file metaparser.lnk.php
 * @date 2018-03-21
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief PHP-Metaparser library linker
 */

if(!class_exists("MetaParser")) {
    $usenames = array(
        "PHP-MetaParser/MetaParser.class"
    );
    foreach($usenames as $name) {
        include("./vendor/" . $name . ".php");
    }
}
