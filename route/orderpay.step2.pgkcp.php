<?php
/**
 * @file orderpay.step2.pgkcp.php
 * @date 2018-08-27
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief KCP PG(Payment Gateway) contoller when done
 */

if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

// detect CSRF attack
if(check_token_abuse_by_requests("_token", "_POST")) {
    set_error("Access denied. (Expired session or Website attacker)");
    show_errors();
}

loadHelper("webpagetool"); // load webpage tools
loadHelper("networktool"); // load network tools
loadHelper("string.urils"); // load string utility
loadHelper("pgkcp.lnk"); // load KCP PG Helper
loadHelper("JSLoader.class"); // load javascript loader

// load PGKCP configuration
$pgkcp_config = get_pgkcp_config();

// extract PGKCP configuration
extract($pgkcp_config);

// load PGKCP library
load_pgkcp_library();

// 01. 지불 요청 정보 설정
$payres = array();
$payinfo = array();
$fieldnames = array(
    "req_tx",
    "tran_cd",
    "cust_ip",
    "ordr_idxx",
    "good_name",
    "res_cd",
    "res_msg",
    "res_en_msg",
    "tno",
    "buyr_name",
    "buyr_tel1",
    "buyr_tel2",
    "buyr_mail",
    "pay_method_alias",
    "pay_method",
    "use_pay_method",
    "bSucc",
    "app_time",
    "amount",
    "total_amount",
    "coupon_mny",
    "app_time",
    "amount",
    "total_amount",
    "coupon_mny",
    "card_cd",
    "card_name",
    "app_no",
    "noinf",
    "quota",
    "partcanc_yn",
    "card_bin_type_01",
    "card_bin_type_02",
    "card_mny",
    "bank_name",
    "bank_code",
    "bk_mny",
    "bankname",
    "depositor",
    "account",
    "va_date",
    "pnt_issue",
    "pnt_amount",
    "pnt_app_time",
    "pnt_app_no",
    "add_pnt",
    "use_pnt",
    "rsv_pnt",
    "commid",
    "mobile_no",
    "shop_user_id",
    "tk_van_code",
    "tk_app_no",
    "cash_yn",
    "cash_authno",
    "cash_tr_code",
    "cash_id_info",
    "cash_no",
    "pay_data"
);
foreach($fieldnames as $name) {
    $payinfo[$name] = make_safe_argument(get_requested_value($name));
}

// set current ip address
$payinfo['cust_ip'] = get_network_client_addr();

// set converted result message
$payinfo['res_msg'] = get_converted_string($payinfo['res_msg'], "utf-8", "cp949");

// extract payinfo
extract($payinfo);

// initalize data
$data = array(
    "payres" => $payres,
    "payinfo" => $payinfo,
    "redirect_url" => get_requested_value("redirect_url"),
    "_token" => get_session_token(),
    "_next_route" => "orderpay.step3.pgkcp",
);

// 02. 인스턴스 생성 및 초기화
$c_PayPlus = new C_PP_CLI;
$c_PayPlus->mf_clear();

// 03. 처리 요청 정보 설정
if($req_tx == "pay") {
    $c_PayPlus->mf_set_ordr_data( "ordr_mony",  get_requested_value("good_mny") );
    $c_PayPlus->mf_set_encx_data( get_requested_value("enc_data"), get_requested_value("enc_info") );
}

// 04. 실행
if($tran_cd != "") {
    // 응답 전문 처리
    $c_PayPlus->mf_do_tx( "", $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key, $tran_cd, "",
                          $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $ordr_idxx,
                          $cust_ip, $g_conf_log_level, 0, 0, $g_conf_log_path );

    $payres['res_cd']  = $c_PayPlus->m_res_cd;  // 결과 코드
    $payres['res_msg'] = $c_PayPlus->m_res_msg; // 결과 메시지

    // 결과 영문 메세지
    //$payres['res_en_msg'] = $c_PayPlus->mf_get_res_data( "res_en_msg" );
} else {
    $c_PayPlus->m_res_cd  = "9562";
    $c_PayPlus->m_res_msg = "연동 오류 tran_cd값이 설정되지 않았습니다.";
}

// 05. 승인 결과 값 추출
if($req_tx == "pay") {
    if($res_cd == "0000") {
        $payres['tno']       = $c_PayPlus->mf_get_res_data("tno");          // KCP 거래 고유 번호
        $payres['amount']    = $c_PayPlus->mf_get_res_data("amount");       // KCP 실제 거래 금액
        $payres['pnt_issue'] = $c_PayPlus->mf_get_res_data("pnt_issue");    // 결제 포인트사 코드
        $payres['coupon_mny'] = $c_PayPlus->mf_get_res_data("coupon_mny" ); // 쿠폰금액

        switch($use_pay_method) {
            case "100000000000": // 05-1. 신용카드 승인 결과 처리
                $payres['card_cd']   = $c_PayPlus->mf_get_res_data( "card_cd"   ); // 카드사 코드
                $payres['card_name'] = $c_PayPlus->mf_get_res_data( "card_name" ); // 카드 종류
                $payres['app_time']  = $c_PayPlus->mf_get_res_data( "app_time"  ); // 승인 시간
                $payres['app_no']    = $c_PayPlus->mf_get_res_data( "app_no"    ); // 승인 번호
                $payres['noinf']     = $c_PayPlus->mf_get_res_data( "noinf"     ); // 무이자 여부 ( 'Y' : 무이자 )
                $payres['quota']     = $c_PayPlus->mf_get_res_data( "quota"     ); // 할부 개월 수
                $payres['partcanc_yn'] = $c_PayPlus->mf_get_res_data( "partcanc_yn" ); // 부분취소 가능유무
                $payres['card_bin_type_01'] = $c_PayPlus->mf_get_res_data( "card_bin_type_01" ); // 카드구분1
                $payres['card_bin_type_02'] = $c_PayPlus->mf_get_res_data( "card_bin_type_02" ); // 카드구분2
                $payres['card_mny'] = $c_PayPlus->mf_get_res_data( "card_mny" ); // 카드결제금액

                // 05-1.1. 복합결제(포인트+신용카드) 승인 결과 처리
                if(in_array($pnt_issue, array("SCSK", "SCWB"))) {
                    $payres['pnt_amount']   = $c_PayPlus->mf_get_res_data ( "pnt_amount"   ); // 적립금액 or 사용금액
                    $payres['pnt_app_time'] = $c_PayPlus->mf_get_res_data ( "pnt_app_time" ); // 승인시간
                    $payres['pnt_app_no']   = $c_PayPlus->mf_get_res_data ( "pnt_app_no"   ); // 승인번호
                    $payres['add_pnt']      = $c_PayPlus->mf_get_res_data ( "add_pnt"      ); // 발생 포인트
                    $payres['use_pnt']      = $c_PayPlus->mf_get_res_data ( "use_pnt"      ); // 사용가능 포인트
                    $payres['rsv_pnt']      = $c_PayPlus->mf_get_res_data ( "rsv_pnt"      ); // 총 누적 포인트
                    $payres['total_amount'] = $amount + $pnt_amount;                          // 복합결제시 총 거래금액
                }

                break; // END 05-1

            case "010000000000": // 05-2. 계좌이체 승인 결과 처리
                $payres['app_time']  = $c_PayPlus->mf_get_res_data( "app_time"   );  // 승인 시간
                $payres['bank_name'] = $c_PayPlus->mf_get_res_data( "bank_name"  );  // 은행명
                $payres['bank_code'] = $c_PayPlus->mf_get_res_data( "bank_code"  );  // 은행코드
                $payres['bk_mny'] = $c_PayPlus->mf_get_res_data( "bk_mny" ); // 계좌이체결제금액
                
                break; // END 05-2
            
            case "001000000000": // 05-3. 가상계좌 승인 결과 처리
                $payres['bankname']  = $c_PayPlus->mf_get_res_data( "bankname"  ); // 입금할 은행 이름
                $payres['depositor'] = $c_PayPlus->mf_get_res_data( "depositor" ); // 입금할 계좌 예금주
                $payres['account']   = $c_PayPlus->mf_get_res_data( "account"   ); // 입금할 계좌 번호
                $payres['va_date']   = $c_PayPlus->mf_get_res_data( "va_date"   ); // 가상계좌 입금마감시간

                break; // END 05-3
                
            case "000100000000": // 05-4. 포인트 승인 결과 처리
                $payres['pnt_amount']   = $c_PayPlus->mf_get_res_data( "pnt_amount"   ); // 적립금액 or 사용금액
                $payres['pnt_app_time'] = $c_PayPlus->mf_get_res_data( "pnt_app_time" ); // 승인시간
                $payres['pnt_app_no']   = $c_PayPlus->mf_get_res_data( "pnt_app_no"   ); // 승인번호 
                $payres['add_pnt']      = $c_PayPlus->mf_get_res_data( "add_pnt"      ); // 발생 포인트
                $payres['use_pnt']      = $c_PayPlus->mf_get_res_data( "use_pnt"      ); // 사용가능 포인트
                $payres['rsv_pnt']      = $c_PayPlus->mf_get_res_data( "rsv_pnt"      ); // 적립 포인트

                break; // END 05-4

            case "000010000000": // 05-5. 휴대폰 승인 결과 처리
                $payres['app_time']  = $c_PayPlus->mf_get_res_data( "hp_app_time"  ); // 승인 시간
                $payres['commid']    = $c_PayPlus->mf_get_res_data( "commid"       ); // 통신사 코드
                $payres['mobile_no'] = $c_PayPlus->mf_get_res_data( "mobile_no"    ); // 휴대폰 번호

                break; // END 05-5
                
            case "000000001000": // 05-6. 상품권 승인 결과 처리
                $payres['app_time']    = $c_PayPlus->mf_get_res_data( "tk_app_time"  ); // 승인 시간
                $payres['tk_van_code'] = $c_PayPlus->mf_get_res_data( "tk_van_code"  ); // 발급사 코드
                $payres['tk_app_no']   = $c_PayPlus->mf_get_res_data( "tk_app_no"    ); // 승인 번호

                break; // END 05-6
        }

        // 05-7. 현금영수증 결과 처리
        $payres['cash_authno']  = $c_PayPlus->mf_get_res_data( "cash_authno"  ); // 현금 영수증 승인 번호
        $payres['cash_no']      = $c_PayPlus->mf_get_res_data( "cash_no"      ); // 현금 영수증 거래 번호
    }
}

// checking vaild payment method
$res_succ_flag = false;
$pay_method_alias = get_value_in_array("pay_method_alias", $payinfo, "");
$pay_method_rules = array(
    "CRE" => "100000000000", // 신용카드
    "ACC" => "010000000000", // 계좌이체
    "VAC" => "001000000000", // 가상계좌
    "POI" => "000100000000", // 포인트
    "PHO" => "000010000000", // 휴대폰
    "GIF" => "000000001000", // 상품권
    "ARS" => "000000000010", // ARS
    "CAV" => "111000000000", // 신용카드/계좌이체/가상계좌
    "NOP" => ""              // 수기결제/무통장입금
);
foreach($pay_method_rules as $k=>$v) {
    if($pay_method_alias == $k) {
        $payres['use_pay_method_alias'] = $k;
        $res_succ_flag = true;
        break;
    }
}
$payres['bSucc'] = $res_succ_flag ? "true" : "false";

// set result
extract($payres);

// cancel payment when failed
if($req_tx == "pay") {
    if($res_cd == "0000") {
        if($bSucc == "false") {
            $c_PayPlus->mf_clear();

            $payres['tran_cd'] = "00200000";
            $c_PayPlus->mf_set_modx_data( "tno", $tno );                            // KCP 원거래 거래번호
            $c_PayPlus->mf_set_modx_data( "mod_type", "STSC" );                     // 원거래 변경 요청 종류
            $c_PayPlus->mf_set_modx_data( "mod_ip", $cust_ip);                      // 변경 요청자 IP
            $c_PayPlus->mf_set_modx_data( "mod_desc", "결과 처리 오류 - 자동 취소" );        // 변경 사유

            // 응답 전문 처리
            $c_PayPlus->mf_do_tx(
                "", $g_conf_home_dir, $g_conf_site_cd, $g_conf_site_key, $tran_cd, "",
                $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $ordr_idxx,
                $cust_ip, $g_conf_log_level, 0, 0, $g_conf_log_path
            );

            $payres['res_cd']  = $c_PayPlus->m_res_cd;
            $payres['res_msg'] = $c_PayPlus->m_res_msg;
        }
    }
} // End of [res_cd = "0000"]

// set result
extract($payres);

// 08. 폼 구성 및 결과페이지 호출

// set javascript files
$jsloader = new JSLoader();
$jsloader->add_scripts(get_webproxy_url("https://code.jquery.com/jquery-3.3.1.min.js"));
$jsloader->add_scripts(base_url() . "view/public/js/route/orderpay.step2.pgkcp.js");
$jsoutput = $jsloader->get_output();
$data['jsoutput'] = $jsoutput;

// convert payres to payinfo
foreach($payres as $k=>$v) {
    $payinfo[$k] = $v;
}
$data['payinfo'] = $payinfo;

// 결제 진행 URL
$data['pgkcp_action_url'] = base_url();

// 결제창 불러오기
renderView("view_orderpay.step2.pgkcp", $data);
