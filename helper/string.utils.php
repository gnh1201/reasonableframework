<?php
/**
 * @file string.utils.php
 * @date 2018-05-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief String utility helper
 */

if(!check_function_exists("get_converted_string")) {
    function get_converted_string($str, $to_charset, $from_charset) {
        $result = false;

        if($form_charset == "detect") {
            if(check_function_exists(array("mb_detect_encoding", "mb_detect_order"))) {
                $from_charset = mb_detect_encoding($str, mb_detect_order(), true);
            } else {
                $from_charset = "ISO-8859-1";
            }
        }

        if(check_function_exists("iconv")) {
            $result = iconv($from_charset, $to_charset, $str);
        } elseif(check_function_exists("mb_convert_encoding")) {
            $result = mb_convert_encoding($str, $to_charset, $from_charset);
        }

        return $result;
    }
}

if(!check_function_exists("nl2p")) {
    function nl2p($string) {
        $paragraphs = '';
        foreach (explode("\n", $string) as $line) {
            if (trim($line)) {
                $paragraphs .= '<p>' . $line . '</p>';
            }
        }
        return $paragraphs;
    }
}

if(!check_function_exists("br2nl")) {
    function br2nl($string) {
        return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string); 
    }
}

if(!check_function_exists("br2p")) {
    function br2p($string) {
        return nl2p(br2nl($string));
    }
}

if(!check_function_exists("get_formatted_number")) {
    function get_formatted_number($value) {
        return number_format(floatval($value));
    }
}

if(!check_function_exists("get_cutted_string")) {
    function get_cutted_string($str, $start, $len=0, $charset="utf-8") {
        $result = "";

        if(check_function_exists("iconv_substr")) {
            $result = iconv_substr($str, $start, $len, $charset);
        } elseif(check_function_exists("mb_substr")) {
            $result = mb_substr($str, $start, $len, $charset);
        } else {
            $result = substr($str, $start, $len);
        }

        return $result;
    }
}

if(!check_function_exists("split_by_line")) {
    function split_by_line($str) {
        return preg_split('/\n|\r\n?/', $str);
    }
}

if(!check_function_exists("read_storage_file_by_line")) {
    function read_storage_file_by_line($filename, $options=array()) {
        return split_by_line(read_storage_file($filename, $options));
    }
}

// https://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
if(!check_function_exists("startsWith")) {
    function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

if(!check_function_exists("endsWith")) {
    function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}

// https://stackoverflow.com/questions/4955433/php-multiple-delimiters-in-explode/27767665#27767665
if(!check_function_exists("multi_explode")) {
    function multi_explode($delimiters, $string) {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }
}

if(!check_function_exists("multi_strpos")) {
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

if(!check_function_exists("multi_str_split")) {
    function multi_str_split($string, $delimiters) {
        $strings = array();

        if(is_string($string)) {
            $offset = 0;
            $pos = -1;
            while(!($pos !== false)) {
                $offset = $pos + 1;
                $pos = multi_strpos($string, $delimiters, $offset);
                $strings[] = substr($string, $offset, $pos - $offset);
            }
        }

        return $strings;
    }
}

if(!check_function_exists("parse_pipelined_data")) {
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

if(!check_function_exists("get_tokenized_text")) {
    function get_tokenized_text($text, $delimiters=array(",", " ", "|", "-", "+")) {
        return array_filter(multi_explode($delimiters, $text));
    }
}

if(!check_function_exists("get_highlighted_html_by_words")) {
    function get_highlighted_html_by_word($word, $text, $delimiters=array(",", " ", "|", "-", "+")) {
        $html = $text;

        $words = get_tokenized_text($word, $delimiters);
        if(check_array_length($words, 0) > 0) {
            $html = preg_replace(sprintf("/%s/i", implode("|", $words)), "<strong class=\"highlight\">$0</strong>", $text);
        }

        return $html;
    }
}

if(!check_function_exists("eregi_compatible")) {
    function eregi_compatible($pattern, $subject, &$matches=NULL) {
        return preg_match(sprintf("/%s/i", $pattern), $subject, $matches);
    }
}

if(!check_function_exists("eregi_replace_compatible")) {
    function eregi_replace_compatible($pattern, $replacement, $subject) {
        return preg_replace(sprintf("/%s/i", $pattern), $replacement, $subject);
    }
}
