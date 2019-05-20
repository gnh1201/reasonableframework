<?php
define("VENDOR_PATH", './vendor/_dist');

// class loader
function my_autoloader($className) {
    if(!class_exists($className)) {
        $classFileName = str_replace("\\", "/", $className);
        include_once(VENDOR_PATH . '/' . $classFileName . '.php');
    }
}
spl_autoload_register('my_autoloader');
