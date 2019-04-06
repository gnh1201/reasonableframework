<?php
/**
 * @file exectool.php
 * @date 2018-07-22
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @forked from https://github.com/scipag/PHPUtilities
 * @brief ExecTool helper
 */

/*
 * exec_test() executes a command (like "whoami") with different
 * PHP functions in order to figure out which fuctions are supported 
 * in the webserver configuration. The function execTests returns an array, which 
 * contains names of all successful tested PHP functions.
 */
if(!check_function_exists("exec_test")) {
    function exec_test() {
        $cmd = "whoami";
        $cmdPath = "/usr/bin/whoami";
        $return = "";
        $output = "";
        $methodArray = array();

        // Testing system()
        $return = ""; $output = "";
        ob_start();
        $output = system("$cmd 2>&1", $return);
        ob_end_clean();
        if (strlen($output) > 0 && $return == 0) {       
            $methodArray[] = "system";
        } 

        // Testing exec()
        $return = ""; $output = "";
        exec($cmd, $output, $return);        
        if (strlen($output[0]) > 0 && $return == 0) {
            $methodArray[] = "exec";
        }

        // Testing shell_exec()
        $return = ""; $output = "";
        $output = shell_exec($cmd);
        if (strlen($output) > 0) {       
            $methodArray[] = "shell_exec";
        }

        // Testing backticks
        $return = ""; $output = "";
        $output = `$cmd`;        
        if (strlen($output) > 0) {       
            $methodArray[] = "backticks";
        }

        // Testing passthru()  
        $return = ""; $output = "";
        ob_start();
        passthru($cmd, $return);
        $output = ob_get_contents();
        ob_end_clean();
        if (strlen($output[0]) > 0 && $return == 0) {
            $methodArray[] = "passthru";
        }        

        // Testing popen()
        $return = ""; $output = "";
        $handle = popen($cmdPath, "r");
        $output = fread($handle, 2096);
        pclose($handle);
        if (strlen($output) > 0) {       
            $methodArray[] = "popen";
        }

        // Testing proc_open()
        $return = ""; $output = "";
        $descriptorspec = array(
           0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
           1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
           2 => array("file", "/tmp/error-output.txt", "a") // stderr is a file to write to
        );  
        $cwd = '/tmp';
        $env = array('some_option' => 'aeiou');    
        $process = proc_open($cmd, $descriptorspec, $pipes, $cwd, $env);
        if (is_resource($process)) {
            // $pipes now looks like this:
            // 0 => writeable handle connected to child stdin
            // 1 => readable handle connected to child stdout
            // Any error output will be appended to /tmp/error-output.txt
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            // It is important that you close any pipes before calling
            // proc_close in order to avoid a deadlock
            $return = proc_close($process);
        }
        if (strlen($output) > 0 && $return == 0) {       
            $methodArray[] = "proc_open";
        }

        return $methodArray;
    }
}

/*
 * exec_command() executes a command (like "whoami") with the submited method    
 */
if(!check_function_exists("exec_command")) {
    function exec_command($command, $method="shell_exec", $options=array()) {
        $return = false;
        
        // command cache
        if(array_key_equals("cache", $options, true)) {
            // set command cache filename
            $filename = get_hashed_text($command, array(
                "salt" => true,
                "2p" => true
            ));

            // read command cache
            $return = read_storage_file($filename, array(
                "storage_type" => "cache"
            ));

            // write command cache
            if(!$return) {
                $options['cache'] = false;
                $return = exec_command($command, $method, $options);
                write_stroage_file($return, array(
                    "storage_type" => "cache",
                    "filename" => $filename
                ));
            }

            return $return;
        }

        if ($method == "") {
            // ob_start() will turn on output buffering to collect all output from
            // exec_test() and ob_end_clean() will clean the buffer afterwards ("garbage collection") 
            ob_start();
            $methodArray = exec_test();
            ob_end_clean();

            if (is_array($methodArray)) {
                $method = $methodArray[0];            
            } else {
                echo "[!] No method available";
                exit;
            }            
        }

        ob_start();

        switch ($method) {   
            case "system":
                system("$command 2>&1");
                break;

            case "exec":
                exec($command, $output);
                var_dump($output);  
                break;

            case "shell_exec":
                echo shell_exec($command);
                break;

            case "backticks":
                echo `$command`;
                break;

            case "passthru":
                echo passthru($command);
                break;

            case "popen":
                $handle = popen($command, "r");
                echo fread($handle, 2096);
                pclose($handle);
                break;

            case "proc_open":
                $descriptorspec = array(
                   0 => array("pipe", "r"),  
                   1 => array("pipe", "w"),  
                   2 => array("file", "/tmp/error-output.txt", "a")
                );  
                $cwd = '/tmp';
                $env = array('some_option' => 'aeiou');    
                $process = proc_open($command, $descriptorspec, $pipes, $cwd, $env);
                if (is_resource($process)) {           
                    echo stream_get_contents($pipes[1]);
                    fclose($pipes[1]);
                    proc_close($process);
                }
                break;

            default: 
                echo "[!] No method defined";
                break;
        }

        $return = ob_get_clean();

        return $return;
    }
}
