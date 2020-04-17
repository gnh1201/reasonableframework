<?php
// https://stackoverflow.com/questions/3464113/is-it-possible-to-read-ssl-information-in-php-from-any-website

if(!class_exists("SSL")) {
    class SSL {
        public $domain, $validFrom, $validTo, $issuer, $validity, $validitytot, $crtValRemaining;

        private static function instantiate($url, $info) {
            $obj = new static;
            $obj->domain = $url;
            $obj->validFrom = $info['validFrom'];
            $obj->validTo = $info['validTo'];
            $obj->issuer = $info['issuer'];
            $obj->validity = $info['validity'];
            $obj->validitytot = $info['validitytot'];
            $obj->crtValRemaining = $info['crtValRemaining'];

            return $obj;
        }

        public static function getSSLinfo($url) {
            $ssl_info = [];
            $certinfo = static::getCertificateDetails($url);
            $validFrom_time_t_m = static::dateFormatMonth($certinfo['validFrom_time_t']);
            $validTo_time_t_m = static::dateFormatMonth($certinfo['validTo_time_t']);

            $validFrom_time_t = static::dateFormat($certinfo['validFrom_time_t']);
            $validTo_time_t = static::dateFormat($certinfo['validTo_time_t']);
            $current_t = static::dateFormat(time());

            $ssl_info['validFrom'] = $validFrom_time_t_m;
            $ssl_info['validTo'] = $validTo_time_t_m;
            $ssl_info['issuer'] = $certinfo['issuer']['O'];

            $ssl_info['validity'] = static::diffDate($current_t, $validTo_time_t)." days";
            $ssl_info['validitytot'] = (static::diffDate($validFrom_time_t, $validTo_time_t)-1).' days';

            $ssl_info['crtValRemaining'] = $certinfo['validTo_time_t'];

            return static::instantiate($url, $ssl_info); // return an object
        }

        private static function getCertificateDetails($url) {
            $urlStr = strtolower(trim($url)); 

            $parsed = parse_url($urlStr);// add http://
            if (empty($parsed['scheme'])) {
                $urlStr = 'http://' . ltrim($urlStr, '/');
            }
            $orignal_parse = parse_url($urlStr, PHP_URL_HOST);
            $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
            $read = stream_socket_client("ssl://".$orignal_parse.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
            $cert = stream_context_get_params($read);
            $certinfo = openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
            return $certinfo;
        }

        private static function dateFormat($stamp) {
            return  strftime("%Y-%m-%d", $stamp);
        }

        private static function dateFormatMonth($stamp) {
            return  strftime("%Y-%b-%d", $stamp);
        }

        private static function diffDate($from, $to) {
            $date1=date_create($from);
            $date2=date_create($to);
            $diff=date_diff($date1,$date2);
            return ltrim($diff->format("%R%a"), "+");
        }
    }
}
