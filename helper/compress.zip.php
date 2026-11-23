<?php
// @date 2019-10-13
// @author Go Namhyeon <gnh1201@catswords.re.kr>

if(!is_fn("unzip")) {
    function unzip($src, $dst, $options=array()) {
        if(!is_string($src) || !is_string($dst) || $src === "" || $dst === "") {
            return false;
        }
        if(!class_exists("ZipArchive") || !is_fn("normalize_local_path") || !is_fn("local_target_is_inside")) {
            return false;
        }
        if(!is_dir($dst)) {
            return false;
        }

        $handle = new ZipArchive;
        $res = $handle->open($src);
        if($res !== true) {
            return false;
        }

        $names = array();
        $count = $handle->numFiles;
        for($i = 0; $i < $count; $i++) {
            $name = $handle->getNameIndex($i);
            if(!is_string($name) || strpos($name, "\0") !== false) {
                $handle->close();
                return false;
            }
            $is_dir_entry = (substr($name, -1) === "/" || substr($name, -1) === "\\");
            $relative = str_replace("\\", "/", $name);
            if(substr($relative, -1) === "/") {
                $relative = substr($relative, 0, -1);
            }
            if($relative === "") {
                continue;
            }
            $relative = normalize_local_path($relative);
            if($relative === false) {
                $handle->close();
                return false;
            }
            $target = rtrim(str_replace("\\", "/", $dst), "/") . "/" . $relative;
            if(!local_target_is_inside($dst, $target)) {
                $handle->close();
                return false;
            }
            if(!$is_dir_entry) {
                $names[] = array($name, $target);
            }
        }

        foreach($names as $item) {
            if(!$handle->extractTo($dst, $item[0])) {
                $handle->close();
                return false;
            }
            if(file_exists($item[1]) && !local_path_is_inside($dst, $item[1]) && !local_paths_equal(realpath($dst), realpath($item[1]))) {
                $handle->close();
                return false;
            }
        }

        $handle->close();
        return true;
    }
}
