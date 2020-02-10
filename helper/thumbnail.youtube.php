<?php

if(!is_fn("get_youtube_thumbnail")) {
    function get_youtube_thumbnail($vi) {
        $ytimgs = array();
        $ytimg_urls = array();

        $ytimg_url = "http://img.youtube.com/vi/:vi/:rs.jpg";
        $ytimg_types = array(
            "t0" => "0",
            "t1" => "1",
            "t2" => "2",
            "t3" => "3",
            "de" => "default",
            "mq" => "mqdefault",
            "hq" => "hqdefault",
            "sd" => "sddefault",
            "mx" => "maxresdefault"
        );

        foreach($ytimg_types as $k=>$v) {
            $url = get_web_binded_url($ytimg_url, array(
                "vi" => $vi,
                "rs" => $v
            ));
            $response = get_web_page($url, "get.cache");
            $ytimgs[$k] = $response['content'];
            $fpath = write_storage_file($response['content'], array(
                "filename" => $response['sha1']
            ));
            $ytimg_urls[$k] = get_storage_url() . "/" . $response['sha1'];
        }
    
        return $ytimg_urls;
    }
}
