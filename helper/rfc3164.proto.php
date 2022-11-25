<?php
/*
 * @file rfc3164.proto.php
 * @author Go Namhyeon <abuse@catswords.net> (Modified)
 * @author Troy Davis (@tory) - https://gist.github.com/troy/2220679 (Original)
 * @brief Helper for RFC3164(The BSD Syslog Protocol) - https://tools.ietf.org/html/rfc3164
 * @created_on 2018-03-02
 * @updated_on 2020-01-23
 */

if(!is_fn("rfc3164_get_config")) {
    function rfc3164_get_config() {
        $config = get_config();
        return array(
            "enabled" => get_value_in_array("rfc3164_enabled", $config, ""),
            "host" => get_value_in_array("rfc3164_host", $config, ""),
            "port" => get_value_in_array("rfc3164_port", $config, "")
        );
    }
}

if(!is_fn("rfc3164_send_message")) {
    function rfc3164_send_message($message, $component = "web", $program = "next_big_thing") {
        $_config = rfc3164_get_config();

        $enabled = array_key_equals("enabled", $_config, 1);
        $host = get_value_in_array("host", $_config, "");
        $port = get_value_in_array("port", $_config, "");

        if($enabled !== false) {
            $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            foreach(explode("\n", $message) as $line) {
                $syslog_message = "<22>" . date('M d H:i:s ') . $program . ' ' . $component . ': ' . $line;
                socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, $host, $port);
            }
            socket_close($sock);
        }
    }
}
