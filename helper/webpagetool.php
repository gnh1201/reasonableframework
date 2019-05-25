<?php
/**
 * @file webpagetool.php
 * @date 2018-06-01
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief WebPageTool helper
 */

/****** START EXAMPLES *****/
/* // REQUEST GET: $response = get_web_page($url, "get", $data); */
/* // REQUEST POST: $response = get_web_page($url, "post", $data); */
/* // REQUEST GET with CACHE: $response = get_web_page($url, "get.cache", $data); */
/* // REQUEST POST with CACHE: $response = get_web_page($url, "post.cache", $data); */
/* // REQUEST GET by CMD with CACHE: $response = get_web_page($url, "get.cmd.cache"); */
/* // REQUEST GET by SOCK with CACHE: $response = get_web_page($url, "get.sock.cache"); */
/* // REQUEST GET by FGC: $response = get_web_page($url, "get.fgc"); */
/* // REQUEST GET by WGET: $response = get_web_page($url, "get.wget"); */
/* // PRINT CONTENT: echo $response['content']; */
/****** END EXAMPLES *****/

if(!check_function_exists("get_web_fgc")) {
    function get_web_fgc($url) {
        return (ini_get("allow_url_fopen") ? file_get_contents($url) : false);
    }
}

if(!check_function_exists("get_web_build_qs")) {
    function get_web_build_qs($url="", $data) {
        $qs = "";
        if(empty($url)) {
            $qs = http_build_query($data);
        } else {
            $pos = strpos($url, '?');
            if ($pos === false) {
                $qs = $url . '?' . http_build_query($data);
            } else {
                $qs = $url . '&' . http_build_query($data);
            }
        }
        return $qs;
    }
}

if(!check_function_exists("get_web_cmd")) {
    function get_web_cmd($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45, $headers=array()) {
        $output = "";

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
            $args[] = get_web_build_qs($url, $data);
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
            foreach($data as $k=>$v) {
                if(substr($v, 0, 1) == "@") { // if this is a file
                    // the same as --form
                    $args[] = sprintf("-F %s='%s'", make_safe_argument($k), make_safe_argument($v));
                } else {
                    if(array_key_equals("Content-Type", $headers, "multipart/form-data")) {
                        $args[] = sprintf("-F %s='%s'", make_safe_argument($k), make_safe_argument($v));
                    } elseif(array_key_equals("Content-Type", $headers, "application/x-www-form-urlencoded")) {
                        $args[] = sprintf("--data-urlencode %s='%s'", make_safe_argument($k), make_safe_argument($v));
                    } else { // the same as --data
                        $args[] = sprintf("-d %s='%s'", make_safe_argument($k), make_safe_argument($v));
                    }
                }
            }
            $args[] = $url;
        }

        if($method == "jsondata") {
            $_data = json_encode($data);
            $args[] = "-X POST"; // set post request (the same as -X)
            $args[] = sprintf("-A '%s'", get_web_user_agent($ua)); // set agent
            $args[] = "-k"; // allow self-signed certificate (the same as --insecure)
            $headers['Content-Type'] = "application/json;charset=utf-8";
            $headers['Accept'] = "application/json, text/plain, */*";
            $headers['Content-Length'] = strlen($_data);
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
            $args[] = $url;
        }

        // complete and run command
        $cmd = trim(implode(" ", $args));

        // run command
        if(!empty($cmd)) {
            $output = exec_command($cmd);
        }

        return $output;
    }
}

// http://dev.epiloum.net/109
if(!check_function_exists("get_web_sock")) {
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

if(!check_function_exists("get_web_wget")) {
    function get_web_wget($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $content = false;
        
        $filename = make_random_id(32);
        $filepath = write_storage_file("", array(
            "filename" => $filename,
            "mode" => "fake",
        ));

        $cmd = sprintf("wget '%s' -O %s", $url, $filepath);
        if(loadHelper("exectool")) {
            exec_command($cmd, "shell_exec");
            $content = read_storage_file($filename);
        }

        return $content;
    }
}

if(!check_function_exists("get_web_curl")) {
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
                    if(substr($v, 0, 1) == "@") { // if this is a file
                        if(check_function_exists("curl_file_create")) { // php 5.5+
                            $data[$k] = curl_file_create(substr($v, 1));
                        } else {
                            $data[$k] = "@" . realpath(substr($v, 1));
                        }
                    }
                }

                $options[CURLOPT_POST] = 1;
                $options[CURLOPT_POSTFIELDS] = $data;
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

if(!check_function_exists("get_web_page")) {
    function get_web_page($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $status = false;
        $resno = false;
        $errno = false;
        $content = false;
        $_method = $method;

        // set user agent
        $ua = get_web_user_agent($ua);

        // redefine data
        $headers = array();
        if(array_key_is_array("headers", $data)) {
            $headers = $data['headers'];
            $data = $data['data'];
        }

        // set method
        $method = strtolower($method);
        $res_methods = explode(".", $method);

        if(in_array("cache", $res_methods)) {
            $content = get_web_cache($url, $method, $data, $proxy, $ua, $ct_out, $t_out);
        } elseif(in_array("cmd", $res_methods)) {
            $content = get_web_cmd($url, $res_methods[0], $data, $proxy, $ua, $ct_out, $t_out, $headers);
        } elseif(in_array("fgc", $res_methods)) {
            $content = get_web_fgc($url);
        } elseif(in_array("sock", $res_methods)) {
            $content = get_web_sock($url, $res_methods[0], $data, $proxy, $ua, $ct_out, $t_out);
        } elseif(in_array("wget", $res_methods)) {
            $content = get_web_wget($url, $res_methods[0], $data, $proxy, $ua, $ct_out, $t_out);
        } elseif(in_array("jsondata", $res_methods)) {
            $_result = get_web_curl($url, "jsondata", $data, $proxy, $ua, $ct_out, $t_out, $headers);
            $content = $_result['content'];
            $status = $_result['status'];
            $resno = $_result['resno'];
            $errno = $_result['errno'];

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
            "id"         => get_web_identifier($url, $method, $data),
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

if(!check_function_exists("get_web_identifier")) {
    function get_web_identifier($url, $method="get", $data=array()) {
        $hash_data = (count($data) > 0) ? get_hashed_text(serialize($data)) : "*";
        return get_hashed_text(sprintf("%s.%s.%s", get_hashed_text($method), get_hashed_text($url), $hash_data));
    }
}

if(!check_function_exists("get_web_cache")) {
    function get_web_cache($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $content = false;

        $identifier = get_web_identifier($url, $method, $data);
        $gz_content = read_storage_file($identifier, array(
            "storage_type" => "cache"
        ));

        if($gz_content === false) {
            $no_cache_method = str_replace(".cache", "", $method);
            $response = get_web_page($url, $no_cache_method, $data, $proxy, $ua, $ct_out, $t_out);
            $content = $response['content'];
            $gz_content = gzdeflate($content);

            // save web page cache
            write_storage_file($gz_content, array(
                "storage_type" => "cache",
                "filename" => $identifier
            ));
        } else {
            $content = gzinflate($gz_content);
        }

        return $content;
    }
}

if(!check_function_exists("get_web_json")) {
    function get_web_json($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $result = false;

        $response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);
        if($response['size'] > 0) {
            $result = get_parsed_json($response['content'], array("stdClass" => true));
        }

        return $result;
    }
}

if(!check_function_exists("get_web_dom")) {
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

if(!check_function_exists("get_web_meta")) {
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

if(!check_function_exists("get_web_xml")) {
    function get_web_xml($url, $method="get", $data=array(), $proxy="", $ua="", $ct_out=45, $t_out=45) {
        $result = false;

        $response = get_web_page($url, $method, $data, $proxy, $ua, $ct_out, $t_out);
        if($response['size'] > 0) {
            $result = get_parsed_xml($response['content']);
        }

        return $result;
    }
}

if(!check_function_exists("get_parsed_json")) {
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

if(!check_function_exists("get_parsed_xml")) {
    function get_parsed_xml($raw, $options=array()) {
        $result = false;

        if(check_function_exists("simplexml_load_string")) {
            $result = simplexml_load_string($response['content'], null, LIBXML_NOCDATA);
        }

        return $result;
    }
}

if(!check_function_exists("get_parsed_dom")) {
    function get_parsed_dom($raw, $options=array()) {
        $result = false;

        if(loadHelper("simple_html_dom")) {
            $result = check_function_exists("str_get_html") ? str_get_html($response['content']) : $raw;
        }

        return $result;
    }
}
    
// 2018-06-01: Adaptive JSON is always quotes without escape non-ascii characters
if(!check_function_exists("get_adaptive_json")) {
    function get_adaptive_json($data) {
        $result = "";
        $lines = array();
        foreach($data as $k=>$v) {
            if(is_array($v)) {
                $lines[] = sprintf("\"%s\":%s", make_safe_argument($k), get_adaptive_json($v));
            } else {
                $lines[] = sprintf("\"%s\":\"%s\"", make_safe_argument($k), make_safe_argument($v));
            }
        }
        $result = "{" . implode(",", $lines) . "}";

        return $result;
    }
}

// 2018-09-10: support webproxy
if(!check_function_exists("get_webproxy_url")) {
    function get_webproxy_url($url, $route="webproxy") {
        return get_route_link($route, array(
            "url" => $url
        ));
    }
}

if(!check_function_exists("get_web_user_agent")) {
    function get_web_user_agent($ua="") {
        if(empty($ua)) {
            $ua = "ReasonableFramework/1.2-dev (https://github.com/gnh1201/reasonableframework)";
        } else {
            $ua = make_safe_argument($ua);
        }
        return $ua;
    }
}
