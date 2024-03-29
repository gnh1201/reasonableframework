<?php
/**
 * @file api.whois.kr.php
 * @date 2019-05-26
 * @author Go Namhyeon <abuse@catswords.net>
 * @brief KISA Whois/Domain/IP/AS Query Helper (https://whois.kr)
 */
 
if(!is_fn("get_whois_kr")) {
    function get_whois_kr($name, $key, $type="whois") {
        $response = false;

        switch($type) {
            case "whois":
                $response = get_web_json("http://whois.kisa.or.kr/openapi/whois.jsp", "get.cache", array(
                    "query" => $name,
                    "key" => $key,
                    "answer" => "json",
                ));
                break;
            case "ipascc":
                $response = get_web_json("http://whois.kisa.or.kr/openapi/ipascc.jsp", "get.cache", array(
                    "query" => $name,
                    "key" => $key,
                    "answer" => "json",
                ));
                break;
        }
    }
    
    return $response;
}
