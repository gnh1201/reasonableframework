<?php
// @file migrate.1.5.php
// migration helper from ResaonbleFramework 1.5

if(!is_fn("check_function_exists")) {
    function check_function_exists($fn) {
        return is_fn($fn);
    }
}

if(!is_fn("set_scope")) {
    function set_scope($k, $v) {
        return set_shared_var($k, $v);
    }
}

if(!is_fn("get_scope")) {
    functon get_scope($k) {
        return get_shared_var($k);
    }
}
