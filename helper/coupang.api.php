<?php
// Coupang Products Search API
// https://coupa.ng/bZ3Kba

loadHelper("webpagetool");

if(!is_fn("coupang_get_signature")) {
    function coupang_get_signature($method = "GET", $path, $ACCESS_KEY, $SECRET_KEY) {
        $datetime = date("ymd") . 'T' . date("His") . 'Z';
        $message = $datetime . $method . str_replace("?", "", $path);
        $algorithm = "HmacSHA256";

        $signature = hmacsha256_sign_message($message, $SECRET_KEY);
        return "CEA algorithm=HmacSHA256, access-key=" . $ACCESS_KEY . ", signed-date=" . $datetime . ", signature=" . $signature;
    }
}

if(!is_fn("coupang_search_items")) {
    function coupang_search_items($keyword, $ACCESS_KEY, $SECRET_KEY) {
        $URL_PARTS = array("https://api-gateway.coupang.com", "/v2/providers/affiliate_open_api/apis/openapi/v1", "/products/search");
        $BASE_URL = $URL_PARTS[0] . $URL_PARTS[1];

        $path = $URL_PARTS[1] . $URL_PARTS[2];

        return get_web_json($BASE_URL . $URL_PARTS[2], "get", array(
            "headers" => array(
                "Authorization" => coupang_get_signature("GET", $path, $ACCESS_KEY, $SECRET_KEY),
                "data" => array(
                    "keyword" => $keyword,
                    //"limit" => 20,   // default is 20
                    //"subId" => ""    // default is null
                )
            )
        ));
    }
}
