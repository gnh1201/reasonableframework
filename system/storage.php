<?php
/**
 * @file storage.php
 * @date 2018-05-27
 * @updated 2026-11-24
 * @author Go Namhyeon <gnh1201@catswords.re.kr>
 * @brief Stroage module for ReasonableFramework
 */

if(!is_fn("get_current_working_dir")) {
    function get_current_working_dir($method="getcwd") {
        $working_dir = "";

        switch($method) {
            case "getcwd":
                $working_dir = getcwd();
                break;
            case "dirname":
                $working_dir = dirname(__FILE__);
                break;
            case "basename":
                $working_dir = basename(__DIR__);
                break;
            case "unix":
                if(loadHelper("exectool")) {
                    $working_dir = exec_command("pwd");
                }
                break;
            case "windows":
                if(loadHelper("exectool")) {
                    $exec_contents = implode("\r\n", array("@echo off", "ECHO %cd%"));
                    $exec_file = write_storage_file($exec_contents, array(
                        "filename" => "pwd.bat"
                    ));
                    $working_dir = exec_command($exec_file);
                }
                break;
        }

        return $working_dir;
    }
}

if(!is_fn("get_storage_dir")) {
    function get_storage_dir() {
        return "storage";
    }
}

if(!is_fn("get_safe_path")) {
    function get_safe_path($path) {
        if(!is_fn("normalize_local_path")) {
            return false;
        }
        return normalize_local_path($path);
    }
}

if(!is_fn("get_storage_path")) {
    function get_storage_path($type="data") {
        $safe_type = get_safe_path($type);
        if($safe_type === false) {
            set_error("Could not create directory.");
            return false;
        }

        $storage_root = get_current_working_dir() . "/" . get_storage_dir();
        $dir_path = $storage_root . "/" . $safe_type;

        if(!is_dir($dir_path)) {
            if(!@mkdir($dir_path, 0777)) {
                set_error("Could not create directory. " . $dir_path);
                show_errors();
            }
        }
        if(!is_fn("local_target_is_inside") || !is_fn("local_paths_equal")) {
            set_error("Could not create directory.");
            return false;
        }
        $root_real = realpath($storage_root);
        $dir_real = realpath($dir_path);
        if($root_real === false || $dir_real === false) {
            set_error("Could not create directory.");
            return false;
        }
        if(!local_paths_equal($root_real, $dir_real) && !local_target_is_inside($storage_root, $dir_path)) {
            set_error("Could not create directory.");
            return false;
        }
        return $dir_path;
    }
}

if(!is_fn("get_storage_url")) {
    function get_storage_url($type="data") {
        $safe_type = get_safe_path($type);
        if($safe_type === false) {
            return false;
        }
        return sprintf("%s/%s/%s", base_url(), get_storage_dir(), $safe_type);
    }
}

if(!is_fn("get_storage_file_path")) {
    function get_storage_file_path($storage_type, $filename) {
        $base = get_storage_path($storage_type);
        $safe_name = get_safe_path($filename);
        if($base === false || $safe_name === false || !is_fn("local_target_is_inside")) {
            return false;
        }

        $path = $base . "/" . $safe_name;
        if(!local_target_is_inside($base, $path)) {
            return false;
        }
        return $path;
    }
}

if(!is_fn("allocate_uploaded_files")) {
    function allocate_uploaded_files($options=array()) {
        $response = array(
            "files" => array()
        );
        
        $config = get_config();
        $requests = get_requests();
        $files = $requests['_FILES'];

        $storage_type = get_value_in_array("storage_type", $options, "data");
        $upload_base_path = get_storage_path($storage_type);
        $upload_base_url = get_storage_url($storage_type);
        $upload_allow_ext = array();
        if($upload_base_path === false || $upload_base_url === false) {
            return $response['files'];
        }

        // storage/config/security.ini -> allowextensionsdisabled, allowextensions
        $allow_extensions_disabled = get_value_in_array("allowextensionsdisabled", $config, 0);
        if(empty($allow_extensions_disabled)) {
            $allow_extensions = get_value_in_array("allowextensions", $config, $upload_allow_ext);
        }

        foreach($files as $k=>$file) {
            $upload_ext = get_file_extension($files[$k]['name']);
            if(!is_string($upload_ext)) {
                $upload_ext = "";
            }
            $upload_ext = str_replace("\\", "/", $upload_ext);
            if($upload_ext !== "" && (strpos($upload_ext, "/") !== false || !preg_match('/^[A-Za-z0-9]+$/', $upload_ext))) {
                $response['files'][$k] = array(
                    "upload_error" => "Not allowed file type."
                );
                continue;
            }
            $upload_name = make_random_id(32) . (empty($upload_ext) ? "" : "." . $upload_ext);
            $upload_file = $upload_base_path . "/" . $upload_name;
            $upload_url = $upload_base_url . "/" . $upload_name;
            if(!is_fn("local_target_is_inside") || !local_target_is_inside($upload_base_path, $upload_file)) {
                $response['files'][$k] = array(
                    "upload_error" => "File write error."
                );
                continue;
            }

            if(count($upload_allow_ext) == 0 || in_array($upload_ext, $upload_allow_ext)) {
                if(move_uploaded_file($files[$k]['tmp_name'], $upload_file)) {
                    // get file source name
                    $upload_source_name = $files[$k]['name'];
                    if(strlen($upload_source_name) == 0) {
                        $upload_source_name = $upload_name;
                    }

                    // make file data
                    $response['files'][$k] = array(
                        "storage_type" => $storage_type,
                        "upload_ext" => $upload_ext,
                        "upload_name" => $upload_name,
                        "upload_file" => $upload_file,
                        "upload_url" => $upload_url,
                        "upload_source_name" => $upload_source_name,
                        "upload_size" => filesize($upload_file),
                        "upload_error" => ""
                    );
                } else {
                    $response['files'][$k] = array(
                        "upload_error" => "File write error."
                    );
                }
            } else {
                $response['files'][$k] = array(
                    "upload_error" => "Not allowed file type."
                );
            }
        }

        return $response['files'];
    }
}

if(!is_fn("read_storage_file")) {
    function read_storage_file($filename, $options=array()) {
        $result = false;

        $storage_type = get_value_in_array("storage_type", $options, "data");
        $max_age = intval(get_value_in_array("max_age", $options, 0)); // max_age (seconds), the value 0 is forever
        $upload_filename = get_storage_file_path($storage_type, $filename);
        if($upload_filename === false) {
            return false;
        }

        if(file_exists($upload_filename)) {
            $is_valid = false;
            $upload_filesize = filesize($upload_filename);
            $upload_mtime = filemtime($upload_filename);
            $upload_age = get_current_timestamp() - $upload_mtime;

            if($upload_filesize > 0) {
                $is_valid = ($max_age > 0) ? ($upload_age <= $max_age) : true;
            }

            if($is_valid) {
                if($fp = fopen($upload_filename, "r")) {
                    if(array_key_equals("safemode", $options, true)) {
                        $result = "";
                        while(!feof($fp)) {
                            $blocksize = get_value_in_array("blocksize", $options, 8192);
                            $result .= fread($fp, $blocksize);
                        }
                    } else {
                        $result = fread($fp, $upload_filesize);
                    }
                    fclose($fp);
                }

                if(!array_key_empty("encode_base64", $options)) {
                    $result = base64_encode($result);
                }

                if(!array_key_empty("format", $options)) {
                    if(loadHelper("webpagetool")) {
                        if($options['format'] == "json") {
                            $result = get_parsed_json($result, array("stdClass" => true));
                        } elseif($options['format'] == "xml") {
                            $result = get_parsed_xml($result);
                        } elseif($options['format'] == "dom") {
                            $result = get_parsed_dom($result);
                        }
                    }
                }
            }
        }

        return $result;
    }
}

if(!is_fn("iterate_storage_files")) {
    function iterate_storage_files($storage_type, $options=array()) {
        $filenames = array();

        $excludes = array(".", "..");
        $storage_path = get_storage_path($storage_type);
        if($storage_path === false) {
            return $filenames;
        }

        if(is_dir($storage_path)) {
            if($handle = opendir($storage_path)) {
                while(false !== ($file = readdir($handle))) {
                    if(!in_array($file, $excludes)) {
                        $filenames[] = $file;
                    }
                }
            }
        }
        return $filenames;
    }
}

if(!is_fn("remove_storage_file")) {
    function remove_storage_file($filename, $options=array()) {
        $result = false;

        $storage_type = get_value_in_array("storage_type", $options, "data");
        $max_age = intval(get_value_in_array("max_age", $options, 0)); // max_age (seconds), the value 0 is forever
        $upload_filename = get_storage_file_path($storage_type, $filename);
        if($upload_filename === false) {
            return false;
        }

        // add option: encryption
        $encryption = get_value_in_array("encryption", $options, "");
        if(!empty($encryption)) {
            if(!loadHelper("encryptiontool")) {
                $encryption = "";
            }
        }

        if(file_exists($upload_filename)) {
            if(array_key_equals("count_only", $options, true)) {
                return false;
            }

            $is_valid = false;
            $upload_mtime = filemtime($upload_filename);
            $upload_age = get_current_timestamp() - $upload_mtime;

            if(file_exists($upload_filename)) {
                if($max_age > 0) {
                    $is_valid = ($upload_age > $max_age);
                } else {
                    $is_valid = true;
                }
            }

            if($is_valid) {
                if(!array_key_empty("chmod", $options)) {
                    @chmod($upload_filename, $options['chmod']);
                }

                if(!array_key_empty("chown", $options)) {
                    @chown($upload_filename, $options['chown']);
                }

                $shell = "";
                if(is_array($options) && array_key_exists("shell", $options)) {
                    $shell = $options["shell"];
                }
                if($shell === "windows" || $shell === "unix") {
                    if(loadHelper("exectool")) {
                        $exec_cmd = ($shell === "windows") ? "del '%s'" : "rm -f '%s'";
                        exec_command(sprintf($exec_cmd, make_safe_argument($upload_filename)));
                    }
                } else {
                    @unlink($upload_filename);
                }

                $result = !file_exists($upload_filename);
            }
        }

        return $result;
    }
}

if(!is_fn("remove_storage_files")) {
    function remove_storage_files($storage_type, $options=array()) {
        $failed = 0;

        if(!is_array($options)) {
            $options = array();
        }
        $max_age = intval(get_value_in_array("max_age", $options, 0));
        $excludes = get_array(get_value_in_array("excludes", $options, array()));
        $storage_path = get_storage_path($storage_type);
        if($storage_path === false || !is_dir($storage_path)) {
            return $failed;
        }

        $handle = opendir($storage_path);
        if($handle === false) {
            return $failed;
        }
        while(false !== ($file = readdir($handle))) {
            if($file === "." || $file === "..") {
                continue;
            }
            if(in_array($file, $excludes)) {
                continue;
            }
            $file_options = $options;
            $file_options["storage_type"] = $storage_type;
            $file_options["max_age"] = $max_age;
            $rm = remove_storage_file($file, $file_options);
            if(!$rm) {
                $failed++;
            }
        }
        closedir($handle);

        return $failed;
    }
}

if(!is_fn("remove_volatile_files")) {
    function remove_volatile_files($storage_type, $max_age=0, $options=array()) {
        if(!is_array($options)) {
            $options = array();
        }
        $options["max_age"] = $max_age;
        if(!array_key_exists("excludes", $options)) {
            $options["excludes"] = array("index.php", "index.html");
        }
        return remove_storage_files($storage_type, $options);
    }
}

if(!is_fn("write_storage_file")) {
    function write_storage_file($data, $options=array()) {
        $result = false;

        $filename = get_value_in_array("filename", $options, make_random_id(32));
        if(!array_key_empty("extension", $options)) {
            $filename = sprintf("%s.%s", $filename, $options['extension']);
        }

        $storage_type = get_value_in_array("storage_type", $options, "data");
        $mode = get_value_in_array("mode", $options, "w");
        $safe_name = get_safe_path($filename);
        $upload_base_path = get_storage_path($storage_type);
        $upload_base_url = get_storage_url($storage_type);
        if($safe_name === false || $upload_base_path === false || $upload_base_url === false) {
            return false;
        }
        $upload_filename = $upload_base_path . "/" . $safe_name;
        if(!is_fn("local_target_is_inside") || !local_target_is_inside($upload_base_path, $upload_filename)) {
            return false;
        }

        $encryption = get_value_in_array("encryption", $options, "");
        if(!empty($encryption)) {
            if(!loadHelper("encryptiontool")) {
                $encryption = "";
            }
        }

        if(file_exists($upload_filename) && in_array($mode, array("fake"))) {
            if(!array_key_empty("filename", $options)) {
                $result = $upload_filename;
            } else {
                $result = write_storage_file($data, $options);
            }
        } else {
            if($mode == "fake") {
                $result = $upload_filename;
            } elseif($fhandle = fopen($upload_filename, $mode)) {
                // if it is append, check the `rotate_size` option
                if($mode == "a") {
                    $rotate_size = intval(get_value_in_array("rotate_size", $options, 0));
                    $rotate_ratio = floatval(get_value_in_array("rotate_ratio", $options, 0.9));
                    $size_limit = floor($rotate_size * $rotate_ratio);
                    if($rotate_size > 0) {
                        if($rotate_size > filesize($upload_filename)) {
                            if(loadHelper("exectool")) {
                                exec_command(sprintf("tail -c %s '%s' > '%s'", $size_limit, $upload_filename, $upload_filename));
                            } else {
                                write_common_log("failed load exectool helper", "system/storage");
                            }
                        }
                    }
                }

                // write a file
                if(fwrite($fhandle, $data)) {
                    $result = $upload_filename;
                    if(!array_key_empty("chmod", $options)) {
                        @chmod($result, $options['chmod']);
                    }
                    if(!array_key_empty("chown", $options)) {
                        @chown($result, $options['chown']);
                    }
                }
                fclose($fhandle);
            } else {
                write_common_log("maybe, your storage is write-protected. " . $upload_filename, "system/storage");
                $result = false;
            }
        }

        if(array_key_equals("basename", $options, true)) {
            $result = basename($result);
        }

        if(array_key_equals("url", $options, true)) {
            $result = sprintf("%s/%s", $upload_base_url, $safe_name);
        }

        return $result;
    }
}

if(!is_fn("append_storage_file")) {
    function append_storage_file($data, $options=array()) {
        $options['mode'] = "a";

        if(!array_key_empty("nl", $options)) {
            switch($options['nl']) {
                case "<": $data = DOC_EOL . $data; break;
                case ">": $data = $data . DOC_EOL; break;
                case "<>": $data = DOC_EOL . $data . DOC_EOL; break;
            }
        }

        return write_storage_file($data, $options);
    }
}

if(!is_fn("get_real_path")) {
    function get_real_path($filename) {
        $filename = get_safe_path($filename);
        if($filename === false || !file_exists($filename)) {
            return false;
        }
        return realpath($filename);
    }
}

if(!is_fn("retrieve_storage_dir")) {
    function retrieve_storage_dir($type, $recursive=false, $excludes=array(".", ".."), $files=array()) {
        $storage_path = get_storage_path($type);
        if($storage_path === false) {
            return $files;
        }

        if(is_dir($storage_path)) {
            if($handle = opendir($storage_path)) {
                while(false !== ($file = readdir($handle))) {
                    if(!in_array($file, $excludes)) {
                        if(!is_fn("normalize_local_path") || normalize_local_path($file) === false) {
                            continue;
                        }
                        $file_path = sprintf("%s/%s", $storage_path, $file);
                        if(is_file($file_path)) {
                            $files[] = $file_path;
                        } elseif($recursive) {
                            $files = retrieve_storage_dir($type . "/" . $file, $recursive, $excludes, $files);
                        }
                    }
                }
                closedir($handle);
            }
        }

        return $files;
    }
}

if(!is_fn("get_file_extension")) {
    function get_file_extension($file, $options=array()) {
        $result = false;

        // option 'multiple': extension a.b.c.d.f...z
        if(array_key_equals("multiple", $options, true)) {
            $name = basename($file);
            $pos = strpos($name, '.');
            $result = substr($name, $pos + 1);
        } else {
            $result = pathinfo($file, PATHINFO_EXTENSION);
        }

        return $result;
    }
}

if(!is_fn("check_file_extension")) {
    function check_file_extension($file, $extension, $options=array()) {
        return (get_file_extension($file, $options) === $extension);
    }
}

if(!is_fn("get_file_name")) {
    function get_file_name($name, $extension="", $basepath="") {
        $result = "";

        $result .= empty($basepath) ? "" : ($name . "/");
        $result .= $name;
        $result .= empty($extension) ? "" : ("." . $extension);

        return $result;
    }
}
