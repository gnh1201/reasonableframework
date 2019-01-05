<?php
// this is example
loadHelper("exectool");

$result = array(
        "success" => true,
        "data" => array()
);

$directories = array(
        "/home/reasonableframework/"
);

foreach($directories as $dir) {
        $cmd = sprintf("du -Ss %s", $dir);
        $output = exec_command($cmd, "shell_exec");

        $terms = explode("\t", $output);
        $size = intval($terms[0]);

        $result['data'] = array(
                "directory" => $dir,
                "size" => $size
        );
}

set_header_content_type("json");
echo json_encode($result);
