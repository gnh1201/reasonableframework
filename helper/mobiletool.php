<?php
/**
 * @file mobiletool.php
 * @date 2019-04-29
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Mobile Tool
 * @documentation https://www.w3.org/Mobile/training/device-detection/mobile_detector.txt
 */

if(!check_function_exists("detect_mobile")) {
    function detect_mobile() {
        // This function returns the value of a local variable ($dm)
        // that is 0 if a desktop client is detected and > 0 for mobile.
        // Adapted only very lightly from original code posted PascalV in response to 
        // the lightweight device detection (http://mobiforge.com/developing/story/lightweight-device-detection-php)

        $dm = 0;

        if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $dm++;
        }

        if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $dm++;
        }    

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
            'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
            'wapr','webc','winw','winw','xda','xda-'
        );

        if(in_array($mobile_ua, $mobile_agents)) {
            $dm++;
        }

        if(strpos(strtolower($_SERVER['ALL_HTTP']),'operamini') > 0) {
            $dm++;
        }

        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']),' ppc;') > 0) {
            $dm++;
        }

        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows ce') > 0) {
            $dm++;
        } elseif(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') > 0) {
            $dm = 0;
        }

        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'iemobile') > 0) {
            $dm++;
        }

        // detect android os
        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'android') > 0) {
            $dm++;
        }

        // detect tizen os
        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'tizen') > 0) {
            $dm++;
        }

        return $dm;
    }
}
