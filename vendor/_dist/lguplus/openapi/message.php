<?php
/******************************************************************************************************************************************************
작성자 : 정중배
작성일 : 2017-09-05
******************************************************************************************************************************************************/
namespace openapi;

if (!function_exists('curl_init')) {
    throw new Exception('Needs the CURL PHP extension.', 301);
}
if (!function_exists('json_decode')) {
    throw new Exception('Needs the JSON PHP extension.', 301);
}

class message
{
	const URL = "https://openapi.sms.uplus.co.kr:4443"; // 운영환경

	private $API_KEY = "";
    private $API_PWD = "";
    private $algorithm = 1;

	private $debug = false;
	
	 /**
     * 생성자
     */
    public function __construct( $api_key, $pwd, $algorithm=1, $debug=false )
    {
        $this->API_KEY = $api_key;
        $this->API_PWD = $pwd;
        $this->algorithm = $algorithm;
		$this->debug = $debug;
    }
	
	/*
	전문 발송용 cURL 핸들 생성
	*/
	function getHandle( $cur_url ) {
		$ch = curl_init();
		
		// SSL 접속 설정
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 인증서 체크같은데 true 시 안되는 경우가 많다.
		curl_setopt($ch, CURLOPT_SSLVERSION,CURL_SSLVERSION_TLSv1_2); // SSL 버젼 (https 접속시에 필요)
				
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // 결과값 수신여부
				
		curl_setopt($ch, CURLOPT_URL, self::URL . $cur_url);

		if($this->debug==true) echo "URL : ".self::URL.$cur_url."\r\n";
		return $ch;
	}

	/*
	전문 헤더 생성
	*/
	function getHeader() {
		$timestamp = time(true)*1000;
		$randomStr = rand(1000000000, 9999999999); // 10자리 랜덤숫자

		$hmac = $this->get_hmac($timestamp, $randomStr);

		$headers = array(
			'charset=utf-8',
			'api_key: '.$this->API_KEY,
			'algorithm: '.$this->algorithm,
			'hash_hmac: '.$hmac,
			'cret_txt: '.$randomStr,
			'timestamp: '.$timestamp,
		);
		if($this->debug==true) echo "getHeader()=".print_r($headers, true)."\r\n";
		
		return $headers;
	}

	/*
	HASH_HMAC 생성
	*/
	function get_hmac( $timestamp, $randomStr ) {
		
		$sb = $this->API_KEY;
		$sb .= $timestamp;
		$sb .= $randomStr; // 10자리 랜덤숫자
		$sb .= $this->API_PWD; // 사용자 API_KEY 비밀번호
		if($this->debug==true) echo "sb = ".$sb."\n";

		switch ($this->algorithm) {
			case "0":
				$hmac = hash( "sha256", $sb ); 
				break;
			case "1":
				$hmac = sha1( $sb );
				break;
			case "2":
				$hmac = md5( $sb ); 
				break;	
		}
		
		if($this->debug==true) echo "hmac = ".$hmac."\r\n";
		return $hmac;
	}

	function setData( $ch, $data ) {
		$headers = $this->getHeader();
		$headers[] = 'Accept: application/json';
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getData($data));
	}
	
	/*
		multipart/form-data Request body 정보 생성
	*/
	function setDataFile($ch, $postfields) {
		$algos = hash_algos();
		$hashAlgo = null;
		foreach ( array('sha1', 'md5') as $preferred ) {
			if ( in_array($preferred, $algos) ) {
				$hashAlgo = $preferred;
				break;
			}
		}
		if ( $hashAlgo === null ) { list($hashAlgo) = $algos; }
		$boundary =
			'----------------------------' .
			substr(hash($hashAlgo, 'cURL-php-multiple-value-same-key-support' . microtime()), 0, 12);

		$body = array();
		$crlf = "\r\n";
		$fields = array();
		foreach ( $postfields as $key => $value ) {
			if ( is_array($value) ) {
				foreach ( $value as $v ) {
					$fields[] = array($key, $v);
				}
			} else {
				$fields[] = array($key, $value);
			}
		}
		foreach ( $fields as $field ) {
			list($key, $value) = $field;
			if ( strpos($value, '@') === 0 ) {
				preg_match('/^@(.*?)$/', $value, $matches);
				list($dummy, $filename) = $matches;
				$body[] = '--' . $boundary;
				$body[] = 'Content-Disposition: form-data; name="' . $key . '"; filename="' . basename($filename) . '"';
				$body[] = 'Content-Type: image/jpeg';
				$body[] = '';
				$body[] = file_get_contents($filename);
			} else {
				$body[] = '--' . $boundary;
				$body[] = 'Content-Disposition: form-data; name="' . $key . '"';
				$body[] = 'Content-Type: application/json';
				$body[] = '';
				$body[] = $value;
			}
		}
		$body[] = '--' . $boundary . '--';
		$body[] = '';
		
		$content = join($crlf, $body);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

		$headers = $this->getHeader();
		$headers[] = 'Content-Type: multipart/form-data';
		
		$contentLength = strlen($content);
		$headers[] = 'Content-Length: ' . $contentLength;
		$headers[] = 'Expect: 100-continue';

		$contentType = 'boundary=' . $boundary;
		// $contentType = 'multipart/form-data; boundary=' . $boundary;
		$headers[] = 'Content-Type: ' . $contentType;
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}

	/*
		POST 방식으로 API를 호출한다. (발송용)
	*/
	function sendPost( $ch ) {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		$response = curl_exec($ch);

		$response_header = curl_getinfo($ch);
		if($this->debug==true) echo "http_code = ".$response_header["http_code"]."\n";
//		if($this->debug==true) echo "response = ".$response."\n";

		curl_close($ch);

		return $response;
	}
	
	/*
		GET 방식으로 API를 호출한다. (조회용)
	*/
	function sendGet( $ch ) {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

		$response = curl_exec($ch);

		$response_header = curl_getinfo($ch);
		if($this->debug==true) echo "http_code = ".$response_header["http_code"]."\n";
//		if($this->debug==true) echo "response = ".$response."\n";

		curl_close($ch);

		return $response;
	}
	
	/*
		첨부파일 정보 생성
	*/
	function getFile( $fileName ) {
		$file_name_with_full_path = realpath( $fileName );
		$cFile = '@' . realpath($file_name_with_full_path);
		return $cFile;
	}

	/*
		데이터를 json 형태 string 으로 변환
	*/
	function getData( $data ) {
		if ($data == "" || $data == null) 
			$str = "{}";
		else {
			$str = json_encode($data);
			if ($str == "[]") 
				$str = "{}";
		}

		if($this->debug==true) echo "getData = ".$str."\r\n";
		return $str;
	}
}
?>
