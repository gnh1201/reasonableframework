<?php
/**
 * @file metaparser.lnk.php
 * @date 2018-03-21
 * @author Go Namhyeon <abuse@catswords.net>
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
