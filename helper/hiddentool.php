<?php
/**
 * @file hiddentool.php
 * @created_on 2021-05-24
 * @updated_on 2022-09-13
 * @author Go Namhyeon <abuse@catswords.net>
 * @brief Tools for Hidden Services (e.g. Tor, I2P, etc...)
 */

if (!is_fn("detect_hidden_service")) {
    function detect_hidden_service() {
        $score = 0;

        $suffixes = array("onion", "i2p", "crypto");
        $forwarded_host = get_header_value("X-Forwarded-Host");
        if (!empty($forwarded_host)) {
            if (in_array(end(explode('.', $forwarded_host)), $suffixes)) {
                $score += 1;
            }
        }
        
        return $score;
    };
}
