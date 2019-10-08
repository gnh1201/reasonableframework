<?php
// @date 2019-10-07
// @author Go Namhyeon <gnh1201@gmail.com>
// @brief `LGU+` is trandmark of LGUPlus Co. Ltd.

require_once("vendor/_dist/lguplus/openapi/message.php");
use openapi\message;

function lguplus_get_config() {
    $config = get_config();
    return array(
        "enabled" => $config['lguplus_enabled'],
        "key" => $config['lguplus_key'],
        "secret" => $config['lguplus_secret'],
        "from" => $config['lguplus_from'],
        "country" => $config['lguplus_country'],
        "subject" => $config['lguplus_subject']
    );
}

function lguplus_send_message($message, $to="") {
    $cnf = lguplus_get_config();

    $data = array(
        "response" => false,
        "error" => false
    );

    if(array_key_equals("lguplus_enabled", $cnf, 0)) {
        $data['error'] = "this is disabled. please set lguplus_enabled to 1";
        return $data;
    }
    
    try {
        $API_KEY = $cnf['key'];
        $API_PWD = $cnf['secret']; 

        $msg = new message($API_KEY, $API_PWD, 1, false);

        $ch = $msg->getHandle( "/v1/send" );
        
        $data = array(
            "send_type" => "S", // 발송형태(R:예약,S:즉시)
            "msg_type" => "S", // SMS : S, LMS : L, MMS : M
            "to" => $to, // 수신자번호, ","으로 구분하여 100개까지 지정 가능하다.
            "from" => get_value_in_array("from", $cnf, "01000000000"), // 발신자 번호, 발신자 번호는 사전등록된 번호여야 한다.
            "subject" => get_value_in_array("subject", $cnf, "Untitled text message"), // LMS, MMS 의 경우, 제목을 입력할 수 있다.
            "msg" => $message, // 메시지 본문 내용
            "device_id" => "", // 디바이스 아이디를 지정하여 특정 디바이스를 발송제어할 수 있다. 
            "datetime" => "", // 예약시간(YYYYMMDDHH24MI)
            "country" => get_value_in_array("country", $cnf, "82"), // 국가 코드
        );

        $msg->setData( $ch, $data );

        $response = $msg->sendPost($ch);
        $data['response'] = $response;
        if ($response === FALSE) {
            $data['error'] = array(
                "code" => curl_error($ch),
                "message" => "CURL_ERROR"
            );
        }
    } catch(Exception $e) {
        $data['error'] = array(
            "code" => $e->getCode(),
            "message" => $e->getMessage()
        );
    }

    return $data; 
}
