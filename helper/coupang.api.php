<?php
// Coupang Products Search API
// https://coupa.ng/bZ3Kba
// https://developers.coupangcorp.com/hc/ko/articles/360033461914-HMAC-Signature-%EC%83%9D%EC%84%B1

loadHelper("webpagetool");

if(!is_fn("coupang_get_signature")) {
    function coupang_get_signature($method, $path, $query, $ACCESS_KEY, $SECRET_KEY) {
        $datetime = date("ymd") . 'T' . date("His") . 'Z';
        $message = $datetime . strtoupper($method) . str_replace("?", "", $path) . http_build_query($query);
        $algorithm = "HmacSHA256";
        $signature = hmacsha256_sign_message($message, $SECRET_KEY);

        return "CEA algorithm=HmacSHA256, access-key=" . $ACCESS_KEY . ", signed-date=" . $datetime . ", signature=" . $signature;
    }
}

if(!is_fn("coupang_search_items")) {
    function coupang_search_items($keyword, $ACCESS_KEY, $SECRET_KEY) {
        $URL_PARTS = array("https://api-gateway.coupang.com", "/v2/providers/affiliate_open_api/apis/openapi/v1", "/products/search");
        $BASE_URL = $URL_PARTS[0] . $URL_PARTS[1];

		$method = "get";
        $path = $URL_PARTS[1] . $URL_PARTS[2];
		$query = array(
			"keyword" => $keyword,
			"limit" => 20,     // default is 20
			//"subId" => ""    // default is null
		);

        $response = get_web_page($BASE_URL . $URL_PARTS[2], $method, array(
            "headers" => array(
                "Authorization" => coupang_get_signature($method, $path, $query, $ACCESS_KEY, $SECRET_KEY)
            ),
			"data" => $query
        ));
		
		var_dump($response);
    }
}
