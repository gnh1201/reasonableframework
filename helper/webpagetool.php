<?php
/**
 * @file webpagetool.php
 * @created_on 2018-06-01
 * @updated_on 2020-04-08
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief WebPageTool helper
 */

/****** EXAMPLES { *****/
/* * GET: $response = get_web_page($url, "get", $data); */
/* * POST: $response = get_web_page($url, "post", $data); */
/* * GET/ASYNC: $response = get_web_page($url, "get.async", $data); */
/* * GET/CACHE: $response = get_web_page($url, "get.cache", $data); */
/* * POST/CACHE: $response = get_web_page($url, "post.cache", $data); */
/* * GET/CMD/CACHE: $response = get_web_page($url, "get.cmd.cache"); */
/* * GET/CMD/CACHE: $response = get_web_page($url, "get.sock.cache"); */
/* * GET/FGC: $response = get_web_page($url, "get.fgc"); */
/* * GET/WGET: $response = get_web_page($url, "get.wget"); */
/* * GET/ARIA: $response = get_web_page($url, "get.aria"); */
/* * GET/HTTPIE: $response = get_web_page($url, "get.httpie"); */
/* * PRINT: echo $response['content']; */
/****** } // END EXAMPLES *****/

if(!is_fn("get_web_fgc")) {
    function get_web_fgc($url) {
        return (ini_get("allow_url_fopen") ? file_get_contents($url) : false);
    }
}

if(!is_fn("get_web_build_qs")) {
    function get_web_build_qs($url="", $data) {
        $qs = "";
        if(empty($url)) {
            $qs = http_build_query($data);
        } else {
            $pos = strpos($url, '?');
            if ($pos === false) {
                $qs = $url . '?' . http_build_query($data, '', '&');
            } else {
                $qs = $url . '&' . http_build_query($data, '', '&');
            }
        }
        return $qs;
    }
}

if(!is_fn("get_web_binded_url")) {
    function get_web_binded_url($url="", $bind) {
        if(is_array($bind) && check_array_length($bind, 0) > 0) {
            $bind_keys = array_keys($bind);
            usort($bind_keys, "compare_db_key_length");
            foreach($bind_keys as $k) {
                $url = str_replace(":" . $k, $bind[$k], $url);
            }
        }
        return $url;
    }
}

if(!is_fn("get_web_cmd")) {
    function get_web_cmd($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45, $headers=array()) {
        $output = "";
        
        // set method
        $req_methods = explode(".", $method);
        $method = $req_methods[0];

        // set command
        $args = array("curl");
        $cmd = "";

        if(!loadHelper("exectool")) {
            set_error("Helper exectool is required");
            show_errors();
        }

        if($method == "get") {
            $args[] = sprintf("-A '%s'", get_web_user_agent($ua)); // set agent
            $args[] = "-k"; // allow self-signed certificate (the same as --insecure)
            foreach($headers as $k=>$v) {
                // the same as --header
                if(is_array($v)) {
                    if($k == "Authentication") {
                        if($v[0] == "Basic" && check_array_length($v, 3) == 0) {
                            $args[] = sprintf("-u '%s:%s'", make_safe_argument($v[1]), make_safe_argument($v[2]));
                        } else {
                            $args[] = sprintf("-H '%s: %s'", make_safe_argument($k), make_safe_argument(implode(" ", $v)));
                        }
                    }
                } else {
                    $args[] = sprintf("-H '%s: %s'", make_safe_argument($k), make_safe_argument($v));
                }
                
            }
            $args[] = sprintf("'%s'", make_safe_argument(get_web_build_qs($url, $data)));
        }

        if($method == "post") {
            $args[] = "-X POST"; // set post request (the same as --request)
            $args[] = sprintf("-A '%s'", get_web_user_agent($ua)); // set agent
            $args[] = "-k"; // allow self-signed certificate (the same as --insecure)
            
            foreach($headers as $k=>$v) {
                // the same as --header
                if(is_array($v)) {
                    if($k == "Authentication") {
                        if($v[0] == "Basic" && check_array_length($v, 3) == 0) {
                            $args[] = sprintf("-u '%s:%s'", make_safe_argument($v[1]), make_safe_argument($v[2]));
                        } else {
                            $args[] = sprintf("-H '%s: %s'", make_safe_argument($k), make_safe_argument(implode(" ", $v)));
                        }
                    }
                } else {
                    $args[] = sprintf("-H '%s: %s'", make_safe_argument($k), make_safe_argument($v));
                }
            }

            $_data = array();
            foreach($data as $k=>$v) {
                if(substr($v, 0, 1) == "@") { // if this is a file
                    // the same as --form
                    $args[] = sprintf("-F %s='%s'", make_safe_argument($k), make_safe_argument($v));
                } else {
                    $_data[$k] = $v;
                    /*
                    if(array_key_equals("Content-Type", $headers, "multipart/form-data")) {
                        $args[] = sprintf("-F %s='%s'", make_safe_argument($k), make_safe_argument($v));
                    } elseif(array_key_equals("Content-Type", $headers, "application/x-www-form-urlencoded")) {
                        $args[] = sprintf("--data-urlencode %s='%s'", make_safe_argument($k), make_safe_argument($v));
                    } else { // the same as --data
                        $args[] = sprintf("-d %s='%s'", make_safe_argument($k), make_safe_argument($v));
                    }
                    */
                }
            }

            // #110 [helper/webpagetool] post array data, but (string)`Array` given
            if(count($_data) > 0) {
                $args[] = sprintf("--data '%s'", get_web_build_qs("", $_data));
            }

            $args[] = $url;
        }

        if($method == "jsondata" || $method == "rawdata") {
            $args[] = "-X POST"; // set post request (the same as -X)
            $args[] = sprintf("-A '%s'", get_web_user_agent($ua)); // set agent
            $args[] = "-k"; // allow self-signed certificate (the same as --insecure)

            if($method == "jsondata") {
                $_data = json_encode($data);
                $headers['Content-Type'] = "application/json;charset=utf-8";
                $headers['Accept'] = "application/json, text/plain, */*";
            } else {
                // possible: application/octet-stream (RFC2046)
                $_data = $data;
                $headers['Content-Type'] = "text/plain;charset=utf-8";
                $headers['Accept'] = "text/plain, */*";
            }

            // get content size
            $headers['Content-Length'] = strlen($_data);

            // set headers
            foreach($headers as $k=>$v) {
                // the same as --header
                if(is_array($v)) {
                    if($k == "Authentication") {
                        if($v[0] == "Basic" && check_array_length($v, 3) == 0) {
                            $args[] = sprintf("-u '%s:%s'", make_safe_argument($v[1]), make_safe_argument($v[2]));
                        } else {
                            $args[] = sprintf("-H '%s: %s'", make_safe_argument($k), make_safe_argument(implode(" ", $v)));
                        }
                    }
                } else {
                    $args[] = sprintf("-H '%s: %s'", make_safe_argument($k), make_safe_argument($v));
                }
            }
            $args[] = sprintf("--data '%s'", $_data);
            $args[] = sprintf("'%s'", $url);
        }

        // complete and run command
        $cmd = trim(implode(" ", $args));

        // do async(background)
        if(in_array("async", $req_methods)) {
            $cmd = sprintf("%s 1>/dev/null 2>&1 & echo $!;", $cmd);
        }

        // run command
        if(!empty($cmd)) {
            $output = exec_command($cmd);
        }

        return $output;
    }
}

// http://dev.epiloum.net/109
if(!is_fn("get_web_sock")) {
    function get_web_sock($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $output     = "";

        $info       = parse_url($url);
        $req        = '';
        $line       = '';
        $agent      = $ua;
        $linebreak  = "\r\n";
        $headPassed = false;
        
        if(!array_key_empty("scheme", $info)) {        
            switch($info['scheme'] = strtolower($info['scheme'])) {
                case "http":
                    $info['port'] = 80;
                    break;
                case "https":
                    $info['ssl'] = "ssl://";
                    $info['port'] = 443;
                    break;
                default:
                    set_error("ambiguous protocol, HTTP or HTTPS");
                    show_errors();
                    return false;
            }
        } else {
            set_error("ambiguous protocol, HTTP or HTTPS");
            show_errors();
            return false;
        }

        // Setting Path
        if(array_key_empty("path", $info)) {
            $info['path'] = "/";
        }

        // Setting Request Header
        switch($method) {
            case 'get':
                if(array_key_empty("query", $info)) {
                    $info['path'] .= '?' . $info['query'];
                }

                $req .= 'GET ' . $info['path'] . ' HTTP/1.1' . $linebreak;
                $req .= 'Host: ' . $info['host'] . $linebreak;
                $req .= 'User-Agent: ' . $agent . $linebreak;
                $req .= 'Referer: ' . $url . $linebreak;
                $req .= 'Connection: Close' . $linebreak . $linebreak;
                break;

            case 'post':
                $req .= 'POST ' . $info['path'] . ' HTTP/1.1' . $linebreak;
                $req .= 'Host: ' . $info['host'] . $linebreak;
                $req .= 'User-Agent: ' . $agent . $linebreak; 
                $req .= 'Referer: ' . $url . $linebreak;
                $req .= 'Content-Type: application/x-www-form-urlencoded'.$linebreak; 
                $req .= 'Content-Length: '. strlen($info['query']) . $linebreak;
                $req .= 'Connection: Close' . $linebreak . $linebreak;
                $req .= $info['query']; 
                break;
        }

        // Socket Open
        $fsock = @fsockopen($info['ssl'] . $info['host'], $info['port']);
        if ($fsock)
        {
            fwrite($fsock, $req);
            while(!feof($fsock))
            {
                $line = fgets($fsock, 128);
                if($line == "\r\n" && !$headPassed)
                {
                    $headPassed = true;
                    continue;
                }
                if($headPassed)
                {
                    $output .= $line;
                }
            }
            fclose($fsock);
        }

        return $output;
    }
}

if(!is_fn("get_web_wget")) {
    function get_web_wget($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $content = false;
        
        $filename = make_random_id(32);
        $fw = write_storage_file("", array(
            "filename" => $filename,
            "mode" => "fake",
        ));

        $cmd = sprintf("wget '%s' -O %s", $url, $fw);
        if(loadHelper("exectool")) {
            exec_command($cmd);
            $content = read_storage_file($filename);
        }

        return $content;
    }
}

if(!is_fn("get_web_aria")) {
    function get_web_aria($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45, $headers=array()) {
        $content = false;

        $filename = make_random_id(32);
        $fw = write_storage_file("", array(
            "filename" => $filename,
            "mode" => "fake"
        ));

        // init args
        $args = array();

        // make args
        $args[] = "aria2c";
        $args[] = "--out=" . $fw; // set output file
        $args[] = "--connect-timeout=" . $ct_out; // connection timeout (seconds)
        $args[] = "--timeout=" . $t_out; // timeout (seconds)

        // set proxy
        if(!empty($proxy)) {
            $args[] = "--http-proxy=" . $proxy;
        }

        // set user agent
        if(!empty($ua)) {
            $args[] = "--user-agent=" . $ua;
        }

        // set headers
        foreach($headers as $k=>$v) {
            if(is_array($v)) {
                if($k == "Authentication") {
                    if($v[0] == "Basic" && check_array_length($v, 3) == 0) {
                        $args[] = sprintf("--header='%s: Basic %s'", $k, base64_decode($v[1] . ":" . $v[2]));
                    } else {
                        $args[] = sprintf("--header='%s: %s %s'", $k, make_safe_argument(implode(" ", $v)));
                    }
                }
            } else {
                $args[] = sprintf("--header='%s: %s'", make_safe_argument($k), make_safe_argument($v));
            }
        }

        // set URL
        $args[] = $url;

        // build a command
        $cmd = implode(" ", $args);

        // execute a command
        exec_command($cmd);

        // read contents
        $content = read_storage_file($filename);

        return $content;
    }
}

if(!is_fn("get_web_httpie")) {
    function get_web_httpie($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45, $headers=array()) {
        $content = false;

        $filename = make_random_id(32);
        $fw = write_storage_file("", array(
            "filename" => $filename,
            "mode" => "fake"
        ));

        // init args
        $args = array();

        // make args
        $args[] = "http";
        
        // set method, URL
        if($method == "jsondata") {
            $args[] = "POST";
            $args[] = $url;
            $args[] = sprintf("body='%s'", json_encode($data));
        } elseif($method == "post") {
            $args[] = "POST";
            $args[] = $url;
            $args[] = sprintf("body='%s'", get_web_build_qs("", $data));
        } else {
            $args[] = get_web_build_qs($url, $data);
        }

        // set timeout
        $args[] = "--timeout=" . $t_out;

        // set proxy
        if(!empty($proxy)) {
            $args[] = "--proxy=" . $proxy;
        }

        // set headers
        foreach($headers as $k=>$v) {
            if(is_array($v)) {
                if($k == "Authentication") {
                    if($v[0] == "Basic" && check_array_length($v, 3) == 0) {
                        $args[] = sprintf("-a '%s:%s'", $v[1], $v[2]);
                    } else {
                        $args[] = sprintf("'%s:%s'", $k, make_safe_argument(implode(" ", $v)));
                    }
                }
            } else {
                $args[] = sprintf("'%s:%s'", make_safe_argument($k), make_safe_argument($v));
            }
        }

        // set output file
        $args[] = ">";
        $args[] = $fw;

        // execute a command
        $cmd = implode(" ", $args);
        exec_command($cmd);

        // read contents
        $content = read_storage_file($filename);

        return $content;
    }
}

if(!is_fn("get_web_curl")) {
    function get_web_curl($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45, $headers=array()) {
        $content = false;
        $_headers = array();

        if(!in_array("curl", get_loaded_extensions())) {
            $error_msg = "cURL extension needs to be installed.";
            set_error($error_msg);
            show_errors();
        }

        $options = array(
            CURLOPT_URL            => $url,     // set remote url
            CURLOPT_PROXY          => $proxy,   // set proxy server
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_ENCODING       => "",       // handle compressed
            CURLOPT_USERAGENT      => $ua,      // name of client
            CURLOPT_AUTOREFERER    => true,     // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => $ct_out,  // time-out on connect
            CURLOPT_TIMEOUT        => $t_out,   // time-out on response
            CURLOPT_FAILONERROR    => true,     // get error code
            CURLOPT_SSL_VERIFYHOST => false,    // ignore ssl host verification
            CURLOPT_SSL_VERIFYPEER => false,    // ignore ssl peer verification
        );

        if(empty($options[CURLOPT_USERAGENT])) {
            $ua = get_web_user_agent($ua);
            $options[CURLOPT_USERAGENT] = $ua;
        }
        
        if(count($data) > 0) {
            if($method == "post") {
                foreach($data as $k=>$v) {
                    if(is_string($v) && substr($v, 0, 1) == "@") { // if this is a file
                        if(is_fn("curl_file_create")) { // php 5.5+
                            $data[$k] = curl_file_create(substr($v, 1));
                        } else {
                            $data[$k] = "@" . realpath(substr($v, 1));
                        }
                    }
                }

                $options[CURLOPT_POST] = 1;
                if(is_array($data)) {
                    $options[CURLOPT_POSTFIELDS] = get_web_build_qs("", $data);
                } else {
                    $options[CURLOPT_POSTFIELDS] = $data;
                }
            }

            if($method == "get") {
                $options[CURLOPT_URL] = get_web_build_qs($url, $data);
            }

            if($method == "jsondata") {
                $_data = json_encode($data);
                $options[CURLOPT_CUSTOMREQUEST] = "POST";
                $options[CURLOPT_POST] = 1;
                $options[CURLOPT_POSTFIELDS] = $_data;
                $headers['Content-Type'] = "application/json;charset=utf-8";
                $headers['Accept'] = "application/json, text/plain, */*";
                $headers['Content-Length'] = strlen($_data);
            }

            if($method == "rawdata") {
                $_data = $data;
                $options[CURLOPT_CUSTOMREQUEST] = "POST";
                $options[CURLOPT_POST] = 1;
                $options[CURLOPT_POSTFIELDS] = $_data;
                $headers['Content-Type'] = "text/plain;charset=utf-8";
                $headers['Accept'] = "text/plain, */*";
                $headers['Content-Length'] = strlen($_data);
            }
        }

        if(count($headers) > 0) {
            foreach($headers as $k=>$v) {
                if(is_array($v)) {
                    if($k == "Authentication") {
                        if($v[0] == "Basic" && check_array_length($v, 3) == 0) {
                            $options[CURLOPT_USERPWD] = sprintf("%s:%s", make_safe_argument($v[1]), make_safe_argument($v[2]));
                        } else {
                            $_headers[] = sprintf("%s: %s", make_safe_argument($k), make_safe_argument(implode(" ", $v)));
                        }
                    }
                } else {
                    $_headers[] = sprintf("%s: %s", make_safe_argument($k), make_safe_argument($v));
                }
            }
            $options[CURLOPT_HTTPHEADER] = $_headers;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $content = curl_exec($ch);
        $result = array(
            "content" => $content,
            "status" => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            "resno" => curl_getinfo($ch, CURLINFO_RESPONSE_CODE),
            "errno" => curl_errno($ch)
        );

        curl_close($ch);
        
        return $result;
    }
}

if(!is_fn("get_web_page")) {
    function get_web_page($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $status = false;
        $resno = false;
        $errno = false;
        $content = false;
        $_method = $method;
        
        // get process ID
        $pid = getmyid();

        // set user agent
        $ua = get_web_user_agent($ua);

        // set method
        $method = strtolower($method);
        $req_methods = explode(".", $method);

        // redefine data
        $headers = array();
        if(array_key_is_array("headers", $data)) {
            $headers = $data['headers'];
            $data = $data['data'];
        }

        // redefine data (JSON-RPC 1.1 / JSON-RPC 2.0)
        if(in_array("jsonrpc", $req_methods) && in_array("compatible", $req_methods)) {
            $req_methods[] = "jsondata";
            $headers['Content-Type'] = "application/json-rpc";
            $data = array_merge(array(
                "jsonrpc" => "1.1"
            ), $data);
        } elseif(in_array("jsonrpc", $req_methods) || in_array("jsonrpc2", $req_methods)) {
            $req_methods[] = "jsondata";
            $headers['Content-Type'] = "application/json-rpc";
            $data = array_merge(array(
                "jsonrpc" => "2.0"
            ), $data);
        }

        // do request
        if(in_array("cache", $req_methods)) {
            $content = get_web_cache($url, $method, $data, $proxy, $ua, $ct_out, $t_out, $headers);
        } elseif(in_array("async", $req_methods)) {
            $pid = get_web_async($url, $req_methods[0], $data, $proxy, $ua, $ct_out, $t_out, $headers);
            $content = posix_getpgid($pid);
        } elseif(in_array("cmd", $req_methods)) {
            $content = get_web_cmd($url, $req_methods[0], $data, $proxy, $ua, $ct_out, $t_out, $headers);
        } elseif(in_array("fgc", $req_methods)) {
            $content = get_web_fgc($url);
        } elseif(in_array("sock", $req_methods)) {
            $content = get_web_sock($url, $req_methods[0], $data, $proxy, $ua, $ct_out, $t_out);
        } elseif(in_array("wget", $req_methods)) {
            $content = get_web_wget($url, $req_methods[0], $data, $proxy, $ua, $ct_out, $t_out);
        } elseif(in_array("aria", $req_methods)) {
            $content = get_web_aria($url, $req_methods[0], $data, $proxy, $ua, $ct_out, $t_out);
        } elseif(in_array("httpie", $req_methods)) {
            $content = get_web_httpie($url, $req_methods[0], $data, $proxy, $ua, $ct_out, $t_out);
        } elseif(in_array("jsondata", $req_methods)) {
            $response = get_web_curl($url, "jsondata", $data, $proxy, $ua, $ct_out, $t_out, $headers);
            $content = $response['content'];
            $status = $response['status'];
            $resno = $response['resno'];
            $errno = $response['errno'];

            if(!($content !== false)) {
                $content = get_web_cmd($url, "jsondata", $data, $proxy, $ua, $ct_out, $t_out, $headers);
            }
        } else {
            $_result = get_web_curl($url, $method, $data, $proxy, $ua, $ct_out, $t_out, $headers);
            $content = $_result['content'];
            $status = $_result['status'];
            $resno = $_result['resno'];
            $errno = $_result['errno'];

            if(!($content !== false)) {
                $res = get_web_page($url, $method . ".cmd", $data, $proxy, $ua, $ct_out, $t_out);
                $content = $res['content'];
                $_method = $res['method'];
            }
        }

        $content_size = strlen($content);
        $gz_content = gzdeflate($content);
        $gz_content_size = strlen($gz_content);
        $gz_ratio = ($content_size > 0) ? (floatval($gz_content_size) / floatval($content_size)) : 1.0;

        $response = array(
            "content"    => $content,
            "size"       => $content_size,
            "status"     => $status,
            "resno"      => $resno,
            "errno"      => $errno,
            "id"         => get_web_identifier($url, $method, $data, $headers),
            "pid"        => $pid,
            "md5"        => get_hashed_text($content, "md5"),
            "sha1"       => get_hashed_text($content, "sha1"),
            "gz_content" => get_hashed_text($gz_content, "base64"),
            "gz_size"    => $gz_content_size,
            "gz_md5"     => get_hashed_text($gz_content, "md5"),
            "gz_sha1"    => get_hashed_text($gz_content, "sha1"),
            "gz_ratio"   => $gz_ratio,
            "method"     => $_method,
            "params"     => $data,
        );

        return $response;
    }
}

if(!is_fn("get_web_identifier")) {
    function get_web_identifier($url, $method="get", $data=array(), $headers=array()) {
        $checksum_data = (count($data) > 0) ? get_hashed_text(serialize($data)) : "*";
        $checksum_header = (count($headers) > 0) ? get_hashed_text(serialize($data)) : "*";
        $checksum_method = get_hashed_text($method);
        $checksum_url = get_hashed_text($url);

        $checksums = array($checksum_method, $checksum_url, $checksum_data);
        if($checksum_header != "*") { // compatible below 1.6
            $checksums[] = $checksum_header;
        }
        
        return get_hashed_text(implode(".", $checksums));
    }
}

if(!is_fn("get_web_async")) {
    function get_web_async($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45, $headers=array()) {
        $pid = 0;

        $_method = sprintf("%s.%s", $method, "async");
        $pid = get_web_cmd($url, $_method, $data, $proxy, $ua, $ct_out, $t_out, $headers);

        return $pid;
    }
}

if(!is_fn("get_web_cache")) {
    function get_web_cache($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45, $headers=array()) {
        $content = false;
        $config = get_config();
 
        $cache_enabled = array_key_equals("cache_enabled", $config, 1);
        // max_age(seconds), the value 0 is forever
        $cache_max_age = intval(get_value_in_array("cache_max_age", $config, 0));
        $cache_hits = 0;

        $gz_content = false;
        if($cache_enabled) {
            $identifier = get_web_identifier($url, $method, $data);
            $gz_content = read_storage_file($identifier, array(
                "storage_type" => "cache",
                "max_age" => $cache_max_age
            ));

            write_common_log("cache_max_age: " . $cache_max_age, "helper/webpagetool"); 
            if($gz_content !== false) {
                $content = gzinflate($gz_content);
                $cache_hits++;
                write_common_log(sprintf("Cache hit. %s, %s, %s", $identifier, strlen($content), strlen($gz_content)), "helper/webpagetool");
            } else {
                write_common_log(sprintf("Cache no hit. %s", $identifier), "helper/webpagetool");
            }
        }

        if($cache_hits == 0) {
            $_old_methods = explode(".", $method);
            $_new_methods = array();
            foreach($_old_methods as $v) {
                if($v != "cache") {
                    $_new_methods[] = $v;
                }
            }
            $_method = implode(".", $_new_methods);
 
            $response = get_web_page($url, $_method, $data, $proxy, $ua, $ct_out, $t_out);
            $content = $response['content'];
            if($cache_enabled) {
                $gz_content = gzdeflate($content);
                $fw = write_storage_file($gz_content, array(
                    "storage_type" => "cache",
                    "filename" => $identifier
                ));
                if(!$fw) {
                    write_common_log("Failed to write cache file", "helper/webpagetool");
                }
            }
        }
        
        return $content;
    }
}

if(!is_fn("get_web_json")) {
    function get_web_json($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $result = false;

        $response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);
        if($response['size'] > 0) {
            $result = get_parsed_json($response['content'], array("stdClass" => true));
        }

        return $result;
    }
}

if(!is_fn("get_web_dom")) {
    function get_web_dom($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $result = false;
        $response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);

        // load simple_html_dom
        if($response['size'] > 0) {
            $result = get_parsed_dom($response['content']);
        }

        return $result;
    }
}

if(!is_fn("get_web_meta")) {
    function get_web_meta($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $result = false;
        $response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);

        // load PHP-Metaparser
        if($response['size'] > 0) {
            if(loadHelper("metaparser.lnk")) {
                $parser = new MetaParser($response['content'], $url);
                $result = $parser->getDetails();
            }
        }

        return $result;
    }
}

if(!is_fn("get_web_xml")) {
    function get_web_xml($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $result = false;

        $response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);
        if($response['size'] > 0) {
            $result = get_parsed_xml($response['content']);
        }

        return $result;
    }
}

if(!is_fn("get_web_cspt")) {
    function get_web_cspt($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $result = false;

        $response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);
        if($response['size'] > 0) {
            if(loadHelper("casplit.format")) {
                $result = catsplit_decode($response['content']);
            }
        }

        return $result;
    }
}

if(!is_fn("get_parsed_json")) {
    function get_parsed_json($raw, $options=array()) {
        $result = false;

        if(!array_key_equals("stdClass", $options, false)) {
            $result = json_decode($raw);
        } else {
            $result = json_decode($raw, true);
        }

        return $result;
    }
}

if(!is_fn("get_parsed_xml")) {
    function get_parsed_xml($raw, $options=array()) {
        $result = false;

        if(is_fn("simplexml_load_string")) {
            $result = simplexml_load_string($response['content'], null, LIBXML_NOCDATA);
        }

        return $result;
    }
}

if(!is_fn("get_parsed_dom")) {
    function get_parsed_dom($raw, $options=array()) {
        $result = false;

        if(loadHelper("simple_html_dom")) {
            $result = is_fn("str_get_html") ? str_get_html($response['content']) : $raw;
        }

        return $result;
    }
}
    
// 2018-06-01: Adaptive JSON is always quotes without escape non-ascii characters
if(!is_fn("get_adaptive_json")) {
    function get_adaptive_json($data) {
        if(loadHelper("json.format")) {
            return json_encode_ex($data, array("adaptive" => true));
        }
    }
}

// 2018-09-10: support webproxy
if(!is_fn("get_webproxy_url")) {
    function get_webproxy_url($url, $route="webproxy") {
        return get_route_link($route, array(
            "url" => $url
        ));
    }
}

if(!is_fn("get_web_user_agent")) {
    function get_web_user_agent($ua="") {
        if(empty($ua)) {
            $ua = "ReasonableFramework/1.6-dev (https://github.com/gnh1201/reasonableframework)";
        } else {
            $ua = make_safe_argument($ua);
        }
        return $ua;
    }
}
