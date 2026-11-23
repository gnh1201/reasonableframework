<?php
/**
 * @file base.php
 * @created_on 2018-04-13
 * @updated_on 2020-02-10
 * @author Go Namhyeon <gnh1201@catswords.re.kr>
 * @brief Base module
 */

// is_not_fn: mixed
if(!function_exists("is_not_fn")) {
    function is_not_fn($fn) {
        $err = -1;

        if(is_array($fn)) {
            foreach($fn as $k=>$v) {
                if(!function_exists($v) || !is_callable($v)) {
                    $err = $k;
                    break;
                }
            }
        } elseif(!function_exists($fn) || !is_callable($fn)) {
            $err++;
        }

        return $err;
    }
}

// is_fn: bool
if(!(is_not_fn("is_fn") < 0)) {
    function is_fn($fn) {
        return (is_not_fn($fn) < 0);
    }
}

if(!is_fn("is_deprecated_fn")) {
    function is_deprecated_fn($fn) {
        $flag = false;

        $config = get_config();
        $deprecated_fn = get_value_in_array("deprecated_fn", $config, array());
        $deprecated_fn_list = explode(",", $deprecated_fn);

        if(is_array($fn)) {
            foreach($fn as $k=>$v) {
                if(in_array($v, $deprecated_fn_list)) {
                    $flag = true;
                    write_common_log(sprintf("Deprecated: %s()", $v), "system/base");
                }
            }
        } else {
            if(in_array($fn, $deprecated_fn_list)) {
                $flag = true;
                write_common_log(sprintf("Deprecated: %s()", $fn), "system/base");
            }
        }

        return $flag;
    }
}

// set_shared_var: void
if(!is_fn("set_shared_var")) {
    function set_shared_var($k, $v) {
        global $shared_vars;
        $shared_vars[$k] = $v;
    }
}

// get shared var: mixed
if(!is_fn("get_shared_var")) {
    function get_shared_var($k) {
        global $shared_vars;
        return array_key_exists($k, $shared_vars) ? $shared_vars[$k] : null;
    }
}

// register loaded resources
if(!is_fn("register_loaded")) {
    function register_loaded($k, $v) {
        $loaded = get_shared_var("loaded");

        if(array_key_exists($k, $loaded)) {
            if(is_array($loaded[$k])) {
                $loaded[$k][] = $v;
            }
        }
        
        set_shared_var("loaded", $loaded);
    }
}

// sandbox for include function
if(!is_fn("include_isolate")) {
    function include_isolate($file, $data=array()) {
        if(count($data) > 0) {
            extract($data);
        }
        return include($file);
    }
}

// relative path with no parent segment
if(!is_fn("normalize_local_path")) {
    function normalize_local_path($path) {
        if(!is_string($path) || $path === "") {
            return false;
        }
        if(strpos($path, "\0") !== false) {
            return false;
        }

        $path = str_replace("\\", "/", $path);
        if(strpos($path, ":") !== false) {
            return false;
        }
        if(substr($path, 0, 1) === "/") {
            return false;
        }

        $parts = explode("/", $path);
        $safe = array();
        foreach($parts as $part) {
            if($part === "..") {
                return false;
            }
            if($part === "" || $part === ".") {
                continue;
            }
            $safe[] = $part;
        }
        if(count($safe) < 1) {
            return false;
        }

        return implode("/", $safe);
    }
}

// code file names: letters, digits, dot, underscore, hyphen
if(!is_fn("is_code_path_name")) {
    function is_code_path_name($path) {
        $path = normalize_local_path($path);
        if($path === false) {
            return false;
        }

        $parts = explode("/", $path);
        foreach($parts as $part) {
            if(!preg_match('/^[A-Za-z0-9._-]+$/', $part)) {
                return false;
            }
        }

        return true;
    }
}

if(!is_fn("local_paths_equal")) {
    function local_paths_equal($left, $right) {
        $left = rtrim(str_replace("\\", "/", $left), "/");
        $right = rtrim(str_replace("\\", "/", $right), "/");
        if(DIRECTORY_SEPARATOR === "\\") {
            $left = strtolower($left);
            $right = strtolower($right);
        }
        return $left === $right;
    }
}

// resolved file is strictly below the resolved base
if(!is_fn("local_path_is_inside")) {
    function local_path_is_inside($base, $path) {
        $base_real = realpath($base);
        $path_real = realpath($path);
        if($base_real === false || $path_real === false) {
            return false;
        }

        $base_norm = rtrim(str_replace("\\", "/", $base_real), "/");
        $path_norm = str_replace("\\", "/", $path_real);
        if(DIRECTORY_SEPARATOR === "\\") {
            $base_norm = strtolower($base_norm);
            $path_norm = strtolower($path_norm);
        }

        $prefix = $base_norm . "/";
        return substr($path_norm, 0, strlen($prefix)) === $prefix;
    }
}

// existing file, or a new file whose existing parent stays under base
if(!is_fn("local_target_is_inside")) {
    function local_target_is_inside($base, $target) {
        $base_real = realpath($base);
        if($base_real === false || !is_string($target) || $target === "") {
            return false;
        }
        if(file_exists($target)) {
            return local_path_is_inside($base, $target);
        }

        $parent = str_replace("\\", "/", $target);
        $guard = 0;
        while($guard < 32) {
            $next = dirname($parent);
            if($next === $parent || $next === "" || $next === "." || $next === "/") {
                return false;
            }
            $parent = $next;
            if(file_exists($parent)) {
                break;
            }
            $guard++;
        }
        if(!file_exists($parent)) {
            return false;
        }

        $parent_real = realpath($parent);
        if($parent_real === false) {
            return false;
        }
        if(local_paths_equal($base_real, $parent_real)) {
            return true;
        }
        return local_path_is_inside($base, $parent);
    }
}

if(!is_fn("scoped_php_file")) {
    function scoped_php_file($base, $name) {
        $safe = normalize_local_path($name);
        if($safe === false || !is_code_path_name($safe)) {
            return false;
        }

        $file = $base . "/" . $safe . ".php";
        if(!is_file($file) || !local_target_is_inside($base, $file)) {
            return false;
        }
        return $file;
    }
}

// set autoloader
if(!is_fn("set_autoloader")) {
    function set_autoloader() {
        return include('./vendor/autoload.php');
    }
}

// load view file
if(!is_fn("renderView")) {
    function renderView($name, $data=array()) {
        $flag = true;
        $views = explode(';', $name);
        foreach($views as $name2) {
            $viewfile = scoped_php_file("./view", $name2);
            if($viewfile !== false) {
                register_loaded("view", $name2);
                $flag = $flag && !include_isolate($viewfile, $data);
            }
        }
        return !$flag;
    }
}

// load view by rules
if(!is_fn("renderViewByRules")) {
    function renderViewByRules($rules, $data=array()) {
        foreach($rules as $k=>$v) {
            if(in_array($k, get_routes())) {
                renderView($v, $data);
            }
        }
    }
}

// load system module
if(!is_fn("loadModule")) {
    function loadModule($name) {
        $flag = true;
        $modules = explode(';', $name);
        foreach($modules as $name2) {
            $systemfile = scoped_php_file("./system", $name2);
            if($systemfile !== false) {
                register_loaded("system", $name2);
                $flag = $flag && !include_isolate($systemfile);
            } else {
                set_error("Module " . $name . "dose not exists");
            }
        }
        return !$flag;
    }
}

// load helper file
if(!is_fn("loadHelper")) {
    function loadHelper($name) {
        $flag = true;
        $helpers = explode(';', $name);
        foreach($helpers as $name2) {
            $helperfile = scoped_php_file("./helper", $name2);
            if($helperfile !== false) {
                register_loaded("helper", $name2);
                $flag = $flag && !include_isolate($helperfile);
            } else {
                set_error("Helper " . $name . "dose not exists");
            }
        }
        return !$flag;
    }
}

// load route file
if(!is_fn("loadRoute")) {
    function loadRoute($name, $data=array()) {
        $flag = true;
        $routes = explode(";", $name);
        foreach($routes as $name2) {
            $routefile = scoped_php_file("./route", $name2);
            if($routefile !== false) {
                register_loaded("route", $name2);
                $flag = $flag && !include_isolate($routefile, $data);
            } else {
                set_error("Route " . $name . "dose not exists");
            }
        }
        return !$flag;
    }
}

// load vendor file
if(!is_fn("loadVendor")) {
    function loadVendor($uses, $data=array()) {
        $flag = true;
        $usenames = array();

        if(is_string($uses) && !empty($uses)) {
            $usenames[] = $uses;
        } elseif(is_array($uses)) {
            $usenames = array_merge($usenames, $uses);
        } else {
            return !$flag;
        }

        foreach($usenames as $name) {
            $vendorfile = scoped_php_file("./vendor", $name);
            if($vendorfile !== false) {
                register_loaded("vendor", $name);
                $flag = $flag && !include_isolate($vendorfile, $data);
            } else {
                set_error("Vendor " . $name . "dose not exists");
            }
        }
        return !$flag;
    }
}

if(!is_fn("array_key_empty")) {
    function array_key_empty($key, $array) {
        $flag = true;
        
        if(is_array($array)) {
            if(array_key_exists($key, $array)) {
                $flag = $flag && empty($array[$key]);
            }
        }

        return $flag;
    }
}

if(!is_fn("array_key_equals")) {
    function array_key_equals($key, $array, $value) {
        $flag = false;

        if(is_array($array)) {
            if(array_key_exists($key, $array)) {
                $flag = ($array[$key] == $value);
            }
        }

        return $flag;
    }
}

if(!is_fn("array_key_is_array")) {
    function array_key_is_array($key, $array) {
        $flag = false;

        if(is_array($array)) {
            if(array_key_exists($key, $array)) {
                $flag = is_array($array[$key]);
            }
        }

        return $flag;
    }
}

// example: array_key_unset(array_search("apple", $fruits), $fruits);
if(!is_fn("array_key_unset")) {
    function array_key_unset($key, &$array) {
        if(array_key_exists($key, $array)) {
            unset($array[$key]);
        }
    }
}

if(!is_fn("array_keys_empty")) {
    function array_keys_empty($keys, $array) {
        $flag = false;
        foreach($keys as $key) {
            if(array_key_empty($key, $array)) {
                $flag = $key;
            }
        }
        return $flag;
    }
}

if(!is_fn("get_value_in_array")) {
    function get_value_in_array($name, $arr=array(), $default=false) {
        $output = false;

        $_name = "";
        if(is_array($name)) {
            foreach($name as $w) {
                if(!empty($w)) {
                    $_name = $w;
                    break;
                }
            }
        } else {
            $_name = $name;
        }

        if(is_array($arr)) {
            $output = array_key_empty($_name, $arr) ? $default : $arr[$_name];
        } else {
            $output = $default;
        }

        return $output;
    }
}

if(!is_fn("get_value_in_object")) {
    function get_value_in_object($name, $obj, $default="") {
        $output = $obj->$name;
        return $output;
    }
}

if(!is_fn("check_array_length")) {
    function check_array_length($arr, $len) {
        return ((!is_array($arr) ? -1 : count($arr)) - $len);
    }
}

if(!is_fn("check_is_empty")) {
    function check_is_empty($v, $d=true) {
        return (empty($v) ? $d : false);
    }
}

if(!is_fn("get_key_in_array")) {
    function get_key_in_array($val, $arr) {
        return array_search($val, $arr);
    }
}

// error handler (set error)
if(!is_fn("set_error")) {
    function set_error($msg, $code="ERROR") {
        global $shared_vars;
        $shared_vars['errors'][] = $code . ": " . $msg;
        write_common_log($msg, "set_error");
    }
}

// error handler (get errors)
if(!is_fn("get_errors")) {
    function get_errors($d=false, $e=false) { // d: display, e: exit
        global $shared_vars;
        return $shared_vars['errors'];
    }
}

// error handler (show errors)
if(!is_fn("show_errors")) {
    function show_errors($exit=true) {
        $errors = get_errors();
        foreach($errors as $err) {
            echo $err . DOC_EOL;
        }

        if($exit !== false) {
            exit;
        }
    }
}

// error handler (trigger error)
if(!is_fn("trigger_error")) {
    function trigger_error($msg, $code="ERROR") {
        set_error($msg, $code);
        show_errors();
    }
}

if(!is_fn("get_property_value")) {
    function get_property_value($prop, $obj, $ac=false) {
        $result = false;
        if(is_object($obj) && property_exists($obj, $prop)) {
            if($ac) {
                $reflection = new ReflectionClass($obj);
                $property = $reflection->getProperty($prop);
                $property->setAccessible($ac);
                $result = $property->getValue($obj);
            } else {
                $result = $obj->{$prop};
            }
        }
        return $result;
    }
}

if(!is_fn("get_routes")) {
    function get_routes() {
        $loaded = get_shared_var("loaded");
        return $loaded['route'];
    }
}

// Deprecated: array_multikey_empty() is changed to array_keys_empty(), since version 1.2
if(!is_fn("array_multikey_empty")) {
    function array_multikey_empty($keys, $array) {
        return array_keys_empty($keys, $array);
    }
}

// Deprecated: set_error_exit() is changed to do_error()
if(!is_fn("set_error_exit")) {
    function set_error_exit($msg, $code="ERROR") {
        do_error($msg, $code);
    }
}

$loaded = array(
    "module" => array(),
    "helper" => array(),
    "view" => array(),
    "route" => array(),
    "vendor" => array(),
);

$errors = array();

set_shared_var("loaded", $loaded);
set_shared_var("errors", $errors);
