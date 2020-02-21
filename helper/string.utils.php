<?php
/**
 * @file string.utils.php
 * @created_on 2018-05-27
 * @updated_on 2020-02-21
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief String utility helper
 */

if(!is_fn("get_converted_string")) {
    function get_converted_string($str, $to_charset="detect", $from_charset="detect") {
        $result = false;

        // detect charset (input)
        if($form_charset == "detect") {
            if(is_fn(array("mb_detect_encoding", "mb_detect_order"))) {
                $from_charset = mb_detect_encoding($str, mb_detect_order(), true);
            } else {
                $from_charset = "ISO-8859-1";
            }
        }
        
        // detect charset (output)
        if($to_charset == "detect") {
            if(is_fn("mb_internal_encoding")) {
                $to_charset = mb_internal_encoding();
            } elseif(is_fn("iconv_get_encoding")) {
                $to_charset = iconv_get_encoding("internal_encoding");
            } else {
                $_candidates = array(
                    ini_get("default_charset"),
                    ini_get("iconv.internal_encoding"),
                    ini_get("mbstring.internal_encoding"),
                    "UTF-8"
                );
                foreach($_candidates as $_candidate) {
                    if(!empty($_candidate)) {
                        $to_charset = $_candidate;
                        break;
                    }
                }
            }
        }

        // normalize charset (UPPERCASE)
        $from_charset = strtoupper($from_charset);
        $to_charset = strtoupper($to_charset);

        // test conditions
        if($from_charset == $to_charset) {
            $result = $str;
        } elseif(is_fn("iconv")) {
            $result = iconv($from_charset, $to_charset, $str);
        } elseif(is_fn("mb_convert_encoding")) {
            $result = mb_convert_encoding($str, $to_charset, $from_charset);
        }

        return $result;
    }
}

if(!is_fn("nl2p")) {
    function nl2p($str) {
        $paragraphs = "";
        foreach(explode_by_line($str) as $line) {
            if(trim($line)) {
                $paragraphs .= '<p>' . $line . '</p>';
            }
        }
        return $paragraphs;
    }
}

if(!is_fn("br2nl")) {
    function br2nl($string) {
        return preg_replace('/\<br(\s*)?\/?\>/i', DOC_EOL, $string); 
    }
}

if(!is_fn("br2p")) {
    function br2p($string) {
        return nl2p(br2nl($string));
    }
}

if(!is_fn("get_formatted_number")) {
    function get_formatted_number($value) {
        return number_format(floatval($value));
    }
}

if(!is_fn("get_cutted_string")) {
    function get_cutted_string($str, $start, $len=null, $charset="utf-8") {
        $result = "";

        if(is_fn("iconv_substr")) {
            $result = iconv_substr($str, $start, $len, $charset);
        } elseif(is_fn("mb_substr")) {
            $result = mb_substr($str, $start, $len, $charset);
        } else {
            $result = substr($str, $start, $len);
        }

        return $result;
    }
}

if(!is_fn("get_string_length")) {
    function get_string_length($str, $charset="utf-8") {
        $len = 0;

        if(is_fn("iconv_strlen")) {
            $len = iconv_strlen($str, $charset);
        } elseif(is_fn("mb_strlen")) {
            $len = mb_strlen($str, $charset);
        } else {
            $len = strlen($str);
        }

        return $len;
    }
}

if(!is_fn("get_splitted_strings")) {
    function get_splitted_strings($str, $len=32, $chsarset="utf-8") {
        $strings = array();

        $_len = get_string_length($str);
        $_pos = 0;
        if($len >= $_len) {
            $strings[] = $str;
        } else {
            $__len = ceil($_len / $len);
            for($i = 0; $i < $__len; $i++) {
                $_pos = $len * $i;
                $strings[] = get_cutted_string($str, $_pos, $len, $charset);
            }

            if($_len - $_pos > 0) {
                $strings[] = $strings[] = get_cutted_string($str, $_pos);
            }
        }

        return $strings;
    }
}

if(!is_fn("explode_by_line")) {
    function explode_by_line($str) {
        return preg_split('/\n|\r\n?/', $str);
    }
}

if(!is_fn("explode_storage_file_by_line")) {
    function explode_storage_file_by_line($filename, $options=array()) {
        return explode_by_line(read_storage_file($filename, $options));
    }
}

if(!is_fn("is_prefix")) {
    function is_prefix($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

if(!is_fn("is_suffix")) {
    function is_suffix($haystack, $needle) {
        $length = strlen($needle);
        if($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }
}

if(!is_fn("multi_explode")) {
    function multi_explode($delimiters, $string) {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }
}

if(!is_fn("multi_strpos")) {
    function multi_strpos($string, $delimiters, $offset=0) {
        $last_pos = strlen($string) - 1;
        $pos = $last_pos;

        if($offset > 0) {
            $string = substr($offset);
        }

        foreach($delimiters as $s) {
            $new_pos = strpos($string, $s);
            if($new_pos !== false && $pos > $new_pos) {
                $pos = $new_pos;
            }
        }

        return (($pos < $last_pos) ? $pos : false);
    }
}

if(!is_fn("parse_pipelined_data")) {
    function parse_pipelined_data($pipelined_data, $keynames=array()) {
        $result = array();
        $parsed_data = explode("|", $pipelined_data);

        if(count($keynames) > 0) {
            $i = 0;
            foreach($keynames as $name) {
                $result[$name] = $parsed_data[$i];
                $i++;
            }
        } else {
            $result = $parsed_data;
        }

        return $result;
    }
}

// https://stackoverflow.com/questions/10290849/how-to-remove-multiple-utf-8-bom-sequences
if(!is_fn("remove_utf8_bom")) {
    function remove_utf8_bom($text) {
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }
}

if(!is_fn("get_tokenized_text")) {
    function get_tokenized_text($text, $delimiters=array()) {
        if(count($delimiters) > 0) {
            return array_values(array_filter(multi_explode($delimiters, $text)));
        } else {
            return preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        }
    }
}

if(!is_fn("get_highlighted_html_by_words")) {
    function get_highlighted_html_by_word($word, $text, $delimiters=array()) {
        $html = $text;

        $words = get_tokenized_text($word, $delimiters);
        if(check_array_length($words, 0) > 0) {
            $html = preg_replace(sprintf("/%s/i", implode("|", $words)), "<strong class=\"highlight\">$0</strong>", $text);
        }

        return $html;
    }
}

if(!is_fn("get_floating_percentage")) {
    function get_floating_percentage($x, $a=5) {
        return round(floatval($x) / 100.0, $a);
    }
}

if(!is_fn("eregi")) {
    function eregi($pattern, $subject, &$matches=NULL) {
        return preg_match(sprintf("/%s/i", $pattern), $subject, $matches);
    }
}

if(!is_fn("eregi_replace")) {
    function eregi_replace($pattern, $replacement, $subject) {
        return preg_replace(sprintf("/%s/i", $pattern), $replacement, $subject);
    }
}
