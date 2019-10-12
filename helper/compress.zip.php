<?php
// @date 2019-10-13
// @author Go Namhyeon <gnh1201@gmail.com>

if(!check_function_exists("unzip")) {
    function unzip($src, $dst, $options=array()) {
        $flag = false;

        $method = get_value_in_array("method", $options, "ZipArchive");
        switch($method) {
            case "ZipArchive":
                $handle = new ZipArchive;
                $res = $handle->open($src);  
                if ($res === TRUE) {
                    $zip->extractTo($dst);
                    $zip->close();
                    $flag = true;
                }
                break;
        
            case "unzip":
                if(loadHelper("exectool")) {
                    exec_command(sprintf("unzip -d '%s' '%s'", make_safe_argument($src), make_safe_argument($dst)));
                    $flag = true;
                }
                break;
        }
    
        return $flag;
    }
}
