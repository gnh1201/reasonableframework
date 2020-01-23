<?php
/* @file rfc3164.proto.php
 * @author Go Namhyeon <gnh1201@gmail.com> (Modified)
 * @author Troy Davis (@tory) - https://gist.github.com/troy/2220679 (Original)
 * @brief Helper for RFC3164(The BSD Syslog Protocol) - https://tools.ietf.org/html/rfc3164
 * @created_on 2018-03-02
 * @updated_on 2020-01-23
 */

function rfc3164_get_config() {
    $config = get_config();
    return array(
        "hostname" => get_value_in_array("rfc3164_hostname", $config, ""),
        "port" => get_value_in_array("rfc3164_port", $config, "")
    );
}

if(check_function_exists("rfc3164_send_message")) {
    function rfc3164_send_message($message, $component = "web", $program = "next_big_thing") {
        $rfc3164_config = rfc3164_get_config();

        $hostname = $rfc3164_config['hostname'];
        $port = $rfc3164_config['port'];

        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        foreach(explode("\n", $message) as $line) {
            $syslog_message = "<22>" . date('M d H:i:s ') . $program . ' ' . $component . ': ' . $line;
            socket_sendto($sock, $syslog_message, strlen($syslog_message), 0, $hostname, $port);
        }
        socket_close($sock);
    }
}
