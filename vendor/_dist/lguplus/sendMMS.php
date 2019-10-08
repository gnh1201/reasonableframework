<?php
	require_once("./openapi/message.php");
	use openapi\message;
	
	try {
		$API_KEY = "사전 등록된 API KEY";
		$API_PWD = "API KEY의 비밀번호"; 
		
	    $msg = new message($API_KEY, $API_PWD, 1,  true);

		$ch = $msg->getHandle( "/v1/sendMMS" );
		
		$params = array(
			"send_type" => "S", // 발송형태(R:예약,S:즉시)
			"msg_type" => "S", // SMS : S, LMS : L, MMS : M
			"to" => "01011112222", // 수신자번호, ","으로 구분하여 100개까지 지정 가능하다.
			"from" => "01000000000", // 발신자 번호, 발신자 번호는 사전등록된 번호여야 한다.
			"subject" => "sampleTest", // LMS, MMS 의 경우, 제목을 입력할 수 있다.
			"msg" => "본문 내용", // 메시지 본문 내용
			"device_id" => "", // 디바이스 아이디를 지정하여 특정 디바이스를 발송제어할 수 있다. 
			"datetime" => "", // 예약시간(YYYYMMDDHH24MI)
			"country" => "82", // 국가 코드
		);

		$data = array(
			"jsonData" => $msg->getData($params),
			"image" => array ( $msg->getFile("./test.jpg"), $msg->getFile("./test.jpg"), $msg->getFile("./test.jpg")),
		);
		
		$msg->setDataFile( $ch, $data );

		$response = $msg->sendPost($ch);
		if ($response === FALSE) {
			die(curl_error($ch));
		}
		
		echo "response = ".$response."\n";
				
	} catch(Exception $e) {
		echo $e->getMessage(); // get error message
		echo $e->getCode(); // get error code
	}

?>
