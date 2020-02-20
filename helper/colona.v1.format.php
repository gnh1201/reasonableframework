<?php
// Go Namhyeon <gnh1201@gmail.com>
// 2019-09-25

if(!is_fn("decode_colona_format")) {
    function decode_colona_format($data) {
        $lines = split_by_line(remove_utf8_bom($data));
        $jobargs = array();
        $eof = false;
        $delimiter = ":";

        $jobkey = "";
        $jobvalue = "";
        foreach($lines as $line) {
            $pos = strpos($line, $delimiter);

            if($eof) {
                if($line == "EOF;") {
                    $jobargs[$jobkey] = $jobvalue;
                    $eof = false; 
                } else {
                    $jobvalue .= $line;
                }
            } elseif($pos !== false) {
                $jobkey = rtrim(substr($line, 0, $pos));
                $jobvalue = ltrim(substr($line, $pos + strlen($delimiter)));
                if($jobvalue == "<<<EOF") {
                    $jobvalue = "";
                    $eof = true;
                } else {
                    $jobargs[$jobkey] = $jobvalue;
                }
            }
        }

        return $jobargs;
    }
}
