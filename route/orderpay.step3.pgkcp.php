<?php
/**
 * @file orderpay.step3.pgkcp.php
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

// set token
set_session_token();

loadHelper("webpagetool"); // load webpage tools
loadHelper("networktool"); // load network tools
loadHelper("pgkcp.lnk"); // load KCP PG Helper 
loadHelper("JSLoader.class"); // load javascript loader

// load PGKCP configuration
$pgkcp_config = get_pgkcp_config();

// extract PGKCP configuration
extract($pgkcp_config);

// 지불 결과
$payres = array();
$payinfo = array(
    // 공통
    "site_cd" => get_requested_value("site_cd"),               // 사이트코드
    "req_tx" => get_requested_value("req_tx"),                 // 요청 구분(승인/취소)
    "use_pay_method" => get_requested_value("use_pay_method"), // 사용 결제 수단
    "bSucc" => get_requested_value("bSucc"),                   // 업체 DB 정상처리 완료 여부
    "res_cd" => get_requested_value("res_cd"),                 // 업체 DB 정상처리 완료 여부
    "res_msg" => get_requested_value("res_msg"),               // 결과메시지
    "res_msg_bsucc" => "",                                     // 결과메시지 상세
    "amount" => get_requested_value("amount"),                 // KCP 실제 거래 금액
    "ordr_idxx" => get_requested_value("ordr_idxx"),           // 주문번호
    "tno" => get_requested_value("tno"),                       // KCP 거래번호
    "good_name" => get_requested_value("good_name"),           // 상품명
    "buyr_name" => get_requested_value("buyr_name"),           // 구매자명
    "buyr_tel1" => get_requested_value("buyr_tel1"),           // 구매자 전화번호
    "buyr_tel2" => get_requested_value("buyr_tel2"),           // 구매자 휴대폰번호
    "buyr_mail" => get_requested_value("buyr_mail"),           // 구매자 E-Mail

    // 공통 2
    "pnt_issue" => get_requested_value("pnt_issue"),           // 포인트 서비스사
    "app_time" => get_requested_value("app_time"),             // 승인시간 (공통)

    // 신용카드
    "card_cd" => get_requested_value("card_cd"),               // 카드코드
    "card_name" => get_requested_value("card_name"),           // 카드명
    "noinf" => get_requested_value("noinf"),                   // 무이자 여부
    "quota" => get_requested_value("quota"),                   // 할부개월
    "app_no" => get_requested_value("app_no"),                 // 승인번호

    // 계좌이체
    "bank_name" => get_requested_value("bank_name"),           // 은행명
    "bank_code" => get_requested_value("bank_code"),           // 은행코드

    // 가상계좌
    "bankname" => get_requested_value("bankname"),             // 입금할 은행
    "depositor" => get_requested_value("depositor"),           // 입금할 계좌 예금주
    "account" => get_requested_value("account"),               // 입금할 계좌 번호
    "va_date" => get_requested_value("va_date"),               // 가상계좌 입금마감시간

    // 포인트
    "add_pnt" => get_requested_value("add_pnt"),               // 발생 포인트
    "use_pnt" => get_requested_value("use_pnt"),               // 사용가능 포인트
    "rsv_pnt" => get_requested_value("rsv_pnt"),               // 총 누적 포인트
    "pnt_app_time" => get_requested_value("pnt_app_time"),     // 승인시간
    "pnt_app_no" => get_requested_value("pnt_app_no"),         // 승인번호
    "pnt_amount" => get_requested_value("pnt_amount"),         // 적립금액 or 사용금액
    
    // 상품권
    "tk_van_code" => get_requested_value("tk_van_code"),       // 발급사 코드
    "tk_app_no" => get_requested_value("tk_app_no"),           // 승인 번호
    
    // 휴대전화
    "commid" =>  get_requested_value("tk_app_no"),             // 통신사 코드
    "mobile_no" => get_requested_value("mobile_no"),           // 휴대폰 번호
    
    // 현금영수증
    "cash_yn" => get_requested_value("cash_yn"),               // 현금영수증 등록 여부
    "cash_authno" => get_requested_value("cash_authno"),       // 현금영수증 승인 번호
    "cash_tr_code" => get_requested_value("cash_authno"),      // 현금영수증 발행 구분
    "cash_id_info" => get_requested_value("cash_id_info"),     // 현금영수증 등록 번호
    "cash_no" => get_requested_value("cash_no"),               // 현금영수증 거래 번호

    // 확장
    "pay_method_alias" => get_requested_value("pay_method_alias"), // 결제방법 별칭
    "pay_method" => get_requested_value("pay_method"),         // 사용 결제 수단

    // 요청 상세 전문
    "pay_data" => get_requested_value("pay_data"),             // 요청 상세 전문
);

// extract payinfo
extract($payinfo);

// initalize data
$data = array(
    "payres" => $payres,
    "payinfo" => $payinfo,
    "redirect_url" => get_requested_value("redirect_url"),
    "_token" => get_session_token(),
    "_next_route" => "ordercomplete.pgkcp",
);

// 이름 지정
$req_tx_name = "";
$req_tx_names = array(
    "pay" => "지불",
    "mod" => "매입/취소"
);
if(array_key_exists($req_tx, $req_tx_names)) {
    $req_tx_name = $req_tx_names[$req_tx];
}
$payres['req_tx_name'] = $req_tx_name;

// 가맹점 측 DB 처리 실패시 상세 결과 메시지 설정
$res_msg_bsucc = "";
if($req_tx == "pay") {
    if($bSucc == "false") {
        if ($res_cd == "0000") {
            $res_msg_bsucc = "결제는 정상적으로 이루어졌지만 업체에서 결제 결과를 처리하는 중 오류가 발생하여 시스템에서 자동으로 취소 요청을 하였습니다. 업체로 문의하여 확인하시기 바랍니다.";
        } else {
            $res_msg_bsucc = "결제는 정상적으로 이루어졌지만 업체에서 결제 결과를 처리하는 중 오류가 발생하여 시스템에서 자동으로 취소 요청을 하였으나, 취소가 실패 되었습니다. 업체로 문의하여 확인하시기 바랍니다.";
        }
    }
}
$payres['res_msg_bsucc'] = $req_tx_name;

// extract payres
extract($payres);

// set javascript files
$jsloader = new JSLoader();
$jsloader->add_scripts(get_webproxy_url("https://code.jquery.com/jquery-3.3.1.min.js"));
$jsloader->add_scripts(base_url() . "view/public/js/route/orderpay.step3.pgkcp.js");
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
renderView("view_orderpay.step3.pgkcp", $data);
