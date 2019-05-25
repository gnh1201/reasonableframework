<?php
/**
 * @file webhooktool.php
 * @date 2019-05-04
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief WebhookTools
 * @trademark
 * * `NateOn` is trademark of SK Communications Co Ltd., SK Planet Co Ltd., or other SK businesses.
 * * `Discord' is trademark of Discord Inc. (Originally Hammer And Chisel)
 * * `Slack` is trademark of Slack Technologies Inc.
 */

if(!check_function_exists("send_web_hook")) {
    function send_web_hook($message, $networkid, $options=array()) {
        $response = false;

        $id = get_value_in_array("id", $options, "");
        $username = get_value_in_array("username", $options, "ReasonableBot");
        $message = str_replace("http:", "hxxp:", $message);

        switch($networkid) {
            case "nateon":
                $request_url = sprintf("https://teamroom.nate.com/api/webhook/%s", $id);
                if(loadHelper("webpagetool")) {
                    $response = get_web_page($request_url, "post", array(
                        "headers" => array(
                            "Content-Type" => "application/x-www-form-urlencoded",
                        ),
                        "data" => array(
                            "content" => urlencode($message),
                        ),
                    ));
                }


                break;

            case "discord":
                $request_url = sprintf("https://discordapp.com/api/webhooks/%s", $id);

                if(loadHelper("webpagetool")) {
                    $response = get_web_json($request_url, "jsondata", array(
                        "headers" => array(
                            "Content-Type" => "application/json",
                        ),
                        "data" => array(
                            "content" => $message,
                            "username" => $username,
                        ),
                    ));
                }
                break;

            case "slack":
                $request_url = sprintf("https://hooks.slack.com/services/%s", $id);
                if(loadHelper("webpagetool")) {
                    $response = get_web_json($request_url, "jsondata", array(
                        "headers" => array(
                            "Content-Type" => "application/json",
                        ),
                        "data" => array(
                            "channel" => sprintf("#%s", get_value_in_array("channel", $options, "general")),
                            "username" => $username,
                            "text" => $message,
                            "icon_emoji" => sprintf(":%s:", get_value_in_array("emoji", $options, "ghost")),
                        ),
                    ));
                }
                break;
        }

        return $response;
    }
}
