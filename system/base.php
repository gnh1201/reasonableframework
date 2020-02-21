<?php
/**
 * @file base.php
 * @created_on 2018-04-13
 * @updated_on 2020-02-10
 * @author Go Namhyeon <gnh1201@gmail.com>
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
            $viewfile = './view/' . $name2 . '.php';
            if(file_exists($viewfile)) {
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
            $systemfile = './system/' . $name2 . '.php';
            if(file_exists($systemfile)) {
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
            $helperfile = './helper/' . $name2 . '.php';
            if(file_exists($helperfile)) {
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
            $routefile = './route/' . $name2 . '.php';
            if(file_exists($routefile)) {
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
            $vendorfile = './vendor/' . $name . '.php';
            if(file_exists($vendorfile)) {
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
