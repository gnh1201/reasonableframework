<?php
	require_once("./openapi/message.php");
	use openapi\message;
	
	try {
		$API_KEY = "사전 등록된 API KEY";
		$API_PWD = "API KEY의 비밀번호"; 
		
	    $msg = new message($API_KEY, $API_PWD, 1,  true);

		$ch = $msg->getHandle( "/v1/cash" );
		
		$msg->setData( $ch, null );

		$response = $msg->sendGet($ch);
		if ($response === FALSE) {
			die(curl_error($ch));
		}
		
		echo "response = ".$response."\n";
				
	} catch(Exception $e) {
		echo $e->getMessage(); // get error message
		echo $e->getCode(); // get error code
	}

?>
