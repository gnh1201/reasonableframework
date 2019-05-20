<?php
/**
 * @file vworld.php
 * @date 2018-01-11
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Geo Helper based on vWorld (vworld.kr, molit.go.kr)
 */

if(!check_function_exists("vworld_utf8_replace")) {
    function vworld_utf8_replace($data) {
        $regex = <<<'END'
        /
          (
            (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
            |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
            |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
            |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
            ){1,100}                        # ...one or more times
          )
        | .                                 # anything else
        /x
END;
        if (is_array($data)) {
            foreach ($data as $k=>$v) {
                $data[$k] = vworld_utf8_replace($v);
            }
        }
        else if (is_string($data)) {
            $data = preg_replace($regex, '$1', $data);
        }
        return $data;
    }
}

if(!check_function_exists("vworld_geocode_keyword")) {
    function vworld_geocode_keyword($keyword, $category="Poi", $multiple=false) {
        global $config;
        
        $geopoint = array(
            "address" => "",
            "latitude" => "", // y-pos
            "longitude" => "" // x-pos
        );

        $req_urls = array(
            "http://map.vworld.kr/search.do" // 키워드로 요청
        );

        // Poi 는 장소 검색. Jibun은 지번주소 검색. Juso는 도로명주소 검색
        $poss_cates = array("Poi", "Jibun", "Juso");
        if(!in_array($category, $poss_cates)) {
            $category = "Poi";
        }
        $callback = "";
        $q = $keyword;
        $pageUnit = 1;
        $output = "json";
        $pageIndex = 1;
        $apiKey = $config['vworld_api_key'];

        // 전송 내용 명시
        $req_data = array(
            "callback" => $callback,
            "q" => $q,
            "category" => $category,
            "pageUnit" => $pageUnit,
            "output" => $output,
            "pageIndex" => $pageIndex,
            "apiKey" => $apiKey
        );
        $req_data_query = http_build_query($req_data);
        $req_props = "";
        $req_cnt = 0;
        $succ_flag = false; // 성공했는지 여부
        foreach($req_urls as $base_url) {
            $req_props = "";
            $req_real_url = $base_url . '?' . $req_data_query;

            // request addr2coord
            $ch = curl_init(); 
            curl_setopt($ch,CURLOPT_URL, $req_real_url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
            //  curl_setopt($ch,CURLOPT_HEADER, false); 
            if($req_output = curl_exec($ch)) {
                if(!empty(trim($req_output))) {
                    $req_props = json_decode($req_output);
                    // 좌표만을 추출
                    $geo_list = array();
                    foreach($req_props->LIST as $req_row) {
                        $req_row = get_object_vars($req_row);
                        $geo_list[] = $req_row;
                    }

                    // 단일인지 복수인지
                    if(count($geo_list) > 0) {
                        if($multiple == false) {
                            $req_props = $geo_list[0];
                        } else {
                            $req_props = $geo_list;
                        }
                        
                        $succ_flag = true;
                    } else {
                        $succ_flag = false;
                    }
                }
            }
            curl_close($ch);
            
            // 요청 횟수를 기록
            $req_cnt++;

            // 성공했을 시 다음 주소로 넘어가지 않음
            if($succ_flag == true) {
                $xpos = $req_props['xpos'];
                $ypos = $req_props['ypos'];
                // store lat and lon
                if($ypos > 0 && $xpos > 0) {
                    $geopoint['address'] = $req_props['juso'];
                    $geopoint['latitude'] = $ypos;
                    $geopoint['longitude'] = $xpos;
                }
                break;
            } elseif($req_cnt = count($req_urls)) {
                $req_props = array();
            } else {
                $req_props = array();
            }
        }
        return $geopoint;
    }
}

// get geocode from vworld
if(!check_function_exists("vworld_geocode_addr2coord")) {
    function vworld_geocode_addr2coord($addr) {
        global $config;
        
        $geopoint = array(
            "address" => "",
            "latitude" => "", // y-pos
            "longitude" => "" // x-pos
        );
        // base url
        $req_urls = array(
            "http://apis.vworld.kr/jibun2coord.do", // 구주소 요청
            "http://apis.vworld.kr/new2coord.do" // 신주소 요청
        );
        
        // 기본 설정
        $apiKey = $config['vworld_api_key'];
        $domain = $config['vworld_api_domain'];
        $output = "json";
        $epsg = "EPSG:4326"; // default is EPSG:4326
        $callback = "";
        
        // 전송 내용 명시
        $req_data = array(
            "q" => $addr,
            "apiKey" => $apiKey,
            "domain" => $domain,
            "output" => $output,
            "epsg" => $epsg,
            "callback" => $callback
        );
        $req_data_query = http_build_query($req_data);
        
        $req_props = "";
        $req_cnt = 0;
        $succ_flag = false; // 성공했는지 여부
        foreach($req_urls as $base_url) {
            $req_props = "";
            $req_real_url = $base_url . '?' . $req_data_query;
            
            // request addr2coord
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $req_real_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //  curl_setopt($ch,CURLOPT_HEADER, false); 
            if($req_output = curl_exec($ch)) {
                if(!empty(trim($req_output))) {
                    $req_props = json_decode($req_output);
                    if(count($req_props) > 0) {
                        $req_props = get_object_vars($req_props);
                        $req_props = vworld_utf8_replace($req_props);
                        $succ_flag = true;
                    }
                }
            }
            curl_close($ch);
            
            // 요청 횟수를 기록
            $req_cnt++;
            // 성공했을 시 다음 주소로 넘어가지 않음
            if($succ_flag == true) {
                $xpos = 0;
                $ypos = 0;
                switch($epsg) {
                    case "EPSG:900913":
                        $xpos = $req_props['EPSG_900913_X'] ;
                        $ypos = $req_props['EPSG_900913_Y'];
                        if($xpos > 0 && $ypos> 0) {
                            $geopoint['address'] = $req_props['JUSO'];
                            $geopoint['latitude'] = $ypos;
                            $geopoint['longitude'] = $xpos;
                        }
                        break;
                    case "EPSG:4326": // default is EPSG:4326
                    default:
                        $xpos = $req_props['EPSG_4326_X'];
                        $ypos = $req_props['EPSG_4326_Y'];
                        if($xpos > 0 && $ypos> 0) {
                            $geopoint['address'] = $req_props['JUSO'];
                            $geopoint['latitude'] = $ypos;
                            $geopoint['longitude'] = $xpos;
                        }
                        break;
                }
                
                break;
            } elseif($req_cnt = count($req_urls)) {
                $req_props = "";
            } else {
                $req_props = "";
            }
        }
        return $geopoint;
    }
}

if(!check_function_exists("vworld_adaptive_addr2coord")) {
    function vworld_adaptive_addr2coord($addr) {
        $geopoint = array(
            "address" => "",
            "latitude" => "", // y-pos
            "longitude" => "" // x-pos
        );

        if(!empty($addr)) {
            $georesult = vworld_geocode_keyword($addr);
            if(empty($georesult["address"])) {
                $georesult = vworld_geocode_addr2coord($addr);
            }

            if(empty($georesult["address"])) {
                $addr_blocks = explode(' ', $addr);
                $newaddr = implode(' ', array_slice($addr_blocks, 0, -1));
                $georesult = vworld_adaptive_addr2coord($newaddr);
            }
        }

        return $georesult;
    }
}
