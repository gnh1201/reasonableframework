<?php
define("VENDOR_PATH", './vendor/_dist');

// class loader
function my_autoloader($className) {
    if(!class_exists($className)) {
        if(!function_exists("is_fn") || !is_fn("normalize_local_path") || !is_fn("is_code_path_name") || !is_fn("local_target_is_inside")) {
            return;
        }
        $classFileName = normalize_local_path(str_replace("\\", "/", $className));
        if($classFileName === false || !is_code_path_name($classFileName)) {
            return;
        }
        $file = VENDOR_PATH . '/' . $classFileName . '.php';
        if(is_file($file) && local_target_is_inside(VENDOR_PATH, $file)) {
            include_once($file);
        }
    }
}
spl_autoload_register('my_autoloader');
