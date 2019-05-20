<?php
/**
 * @file orderpay.pgkcp.php
 * @date 2018-08-25
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief KCP PG(Payment Gateway) Controller
 */

if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

$debug = get_requested_value("debug");

if($debug != "true") {
    // 필수 항목 체크
    $required_fields = array("pay_method_alias", "good_name", "good_mny", "buyr_name", "buyr_mail", "buyr_tel1", "chk_agree");
    foreach($required_fields as $name) {
        if(array_key_empty($name, $requests['_ALL'])) {
            set_error_exit("required field is empty. " . $name);
        }
    }

    // detect CSRF attack
    if(check_token_abuse_by_requests("_token")) {
        set_error("Access denied. (Expired session or Website attacker)");
        show_errors();
    }
}

set_session_token();

loadHelper("pgkcp.lnk"); // load KCP PG Helper
loadHelper("JSLoader.class"); // load javascript loader

// load PG KCP configuration
$pgkcp_config = get_pgkcp_config();

// extract PGKCP configuration
extract($pgkcp_config);

// initalize data
$payinfo = array();
$data = array(
    "payinfo" => $payinfo,
    "redirect_url" => get_requested_value("redirect_url"),
    "_token" => get_session_token(),
    "_next_route" => "orderpay.step2.pgkcp",
);

// 1. 주문 정보 입력: 결제에 필요한 주문 정보를 입력 및 설정합니다.
$fieldnames = array(
    "pay_method",         // 지불 방법
    "pay_method_alias",   // 지불 방법 별칭
    "ordr_idxx",          // 주문 번호
    "good_name",          // 상품 이름
    "good_mny",           // 결제 금액
    "buyr_name",          // 주문자 이름
    "buyr_mail",          // 주문자 전자우편(이메일) 주소
    "buyr_tel1",          // 주문자 연락처 1
    "buyr_tel2",          // 주문자 연락처 2
    "pay_data"            // 주문 상세 데이터
);
foreach($fieldnames as $name) {
    $payinfo[$name] = make_safe_argument(get_requested_value($name));
}

// pay_method 처리
$pay_method_rules = array(
    "CRE" => "100000000000", // 신용카드
    "ACC" => "010000000000", // 계좌이체
    "VAC" => "001000000000", // 가상계좌
    "POI" => "000100000000", // 포인트
    "PHO" => "000010000000", // 휴대폰
    "GIF" => "000000001000", // 상품권
    "ARS" => "000000000010", // ARS
    "CAV" => "111000000000"  // 신용카드/계좌이체/가상계좌
);
$pay_method = get_value_in_array("pay_method", $payinfo, $pay_method_rules['CRE']);
$pay_method_alias = get_value_in_array("pay_method_alias", $payinfo, "");
foreach($pay_method_rules as $k=>$v) {
    if(array_key_exists($pay_method_alias, $pay_method_rules)) {
        $pay_method = $pay_method_rules[$pay_method_alias];
    }
}
$payinfo['pay_method'] = $pay_method;

// 2.가맹점 필수 정보 설정: 승인(pay)/취소,매입(mod)
$req_tx = get_requested_value("req_tx");
$payinfo['req_tx'] = in_array($req_tx, array("pay", "mod")) ? $req_tx : "pay";
$payinfo['site_cd'] = $g_conf_site_cd;
$payinfo['site_name'] = $g_conf_site_name;

// 할부옵션: 0 ~ 18 개월까지, 50,000원 이상만 가능
$payinfo['quotaopt'] = get_requested_value("quotaopt");
if(array_key_empty("quotaopt", $payinfo)) {
    $payinfo['quotaopt'] = 12;
}

// 결제 금액/화폐단위: 필수항목
$currency = get_requested_value("currency");
if(array_key_empty("currency", $payinfo)) {
    $payinfo['currency'] = "WON";
}

// 3. 변경 제한 영역: 표준 웹 설정 영역
$payinfo['module_type'] = $module_type;
$payinfo['res_cd'] = "";
$payinfo['res_msg'] = "";
$payinfo['enc_info'] = "";
$payinfo['enc_data'] = "";
$payinfo['ret_pay_method'] = "";
$payinfo['ordr_chk'] = ""; // 주문정보 검증 관련 정보

// 변경 제한 영역: 현금영수증 관련 정보
$payinfo['cash_yn'] = ""; 
$payinfo['cash_tr_code'] = "";
$payinfo['cash_id_info'] = "";

// 변경 제한 영역: 2012년 8월 18일 전자상거래법 개정 (0:일회성 1:기간설정(ex 1:2012010120120131))
$payinfo['good_expr'] = "";

// 4. 옵션 정보: 결제에 필요한 추가 옵션 정보를 입력 및 설정합니다.
$default_options = array(
    "used_card_YN" => "Y",               // 사용카드 설정 여부 파라미터
    "used_card" => "CCBC:CCKM:CCSS",     // 사용카드 설정 파라미터
    "used_card_CCXX" => "Y",             // 해외카드 구분 파라미터 ((해외비자, 해외마스터, 해외JCB)
    "save_ocb" => "Y",                   // 신용카드 결제시 OK캐쉬백 적립 여부
    "fix_inst" => "07",                  // 고정 할부 개월 수 선택
    "kcp_noint" => "",                   // 설정할부: '', 일반할부: 'N', 무이자할부: 'Y'
    "kcp_noint_quota" => "CCBC-02:03:06,CCKM-03:06,CCSS-03:06:09",
        // 전 카드 2,3,6개월 무이자(국민,비씨,엘지,삼성,신한,현대,롯데,외환) : ALL-02:03:04
        // BC 2,3,6개월, 국민 3,6개월, 삼성 6,9개월 무이자 : CCBC-02:03:06,CCKM-03:06,CCSS-03:06:04
    "wish_vbank_list" => "05:03:04:07:11:23:26:32:34:81:71", // 가상계좌 은행 선택 파라미터 (은행코드는 매뉴얼을 참조)
    "vcnt_expire_term" => "3",           // 가상계좌 입금 기한 설정하는 파라미터 - 발급일 + 3일
    "vcnt_expire_term_time" => "120000", // 가상계좌 입금 시간 설정하는 파라미터 (HHMMSS 형식, 기본값은 23시59분59초)
    "complex_pnt_yn" => "N",             // 포인트 결제시 복합 결제(신용카드+포인트) 여부를 결정
    "disp_tax_yn" => "Y",                // 현금영수증 등록 창을 출력 여부를 설정하는 파라미터
    "site_logo" => "",                   // 사이트 로고, 로고 용량이 150 X 50 이상일 경우 site_name 값이 표시
    "eng_flag" => "",                    // 결제창 영문 표시 파라미터, 사용 시 'Y'로 설정
    "tax_flag" => "TG03",                // 변경불가: 과세품목코드
    "comm_tax_mny" => "",                // 과세금액
    "comm_vat_mny" => "",                // 부가세
    "comm_free_mny" => "",               // 비과세금액
    "skin_indx" => "1",                  // 스킨 변경 파라미터. 7개 (1~7) 지원
    "good_cd" => "",                     // 상품코드 설정 파라미터
    "shop_user_id" => "",                // 가맹점에서 관리하는 고객 아이디, 상품권 결제 시 반드시 입력
    "pt_memcorp_cd" => ""                // 복지포인트 결제시 가맹점에 할당되어진 코드 값을 입력
);
foreach($default_options as $k=>$v) {
    $req_value = get_requested_value($k);
    if(!empty($req_value)) {
        $payinfo[$k] = ($req_value === "_DEFAULT_") ? $v : $req_value;
    }
}

// 설정 불러오기
$payinfo['g_conf_site_cd'] = $pgkcp_config['g_conf_site_cd'];
$payinfo['g_conf_site_name'] = $pgkcp_config['g_conf_site_name'];
$payinfo['module_type'] = $pgkcp_config['module_type'];

// 결제 정보 업데이트
$data['payinfo'] = $payinfo;

// 스크립트 설정
$jsloader = new JSLoader();
$jsloader->add_scripts(base_url() . "view/public/js/route/orderpay.pgkcp.1.js");
$jsloader->add_scripts($g_conf_js_url);
$jsloader->add_scripts(base_url() . "view/public/js/route/orderpay.pgkcp.2.js");
$jsoutput = $jsloader->get_output();
$data['jsoutput'] = $jsoutput;

// 결제 진행 URL
$data['pgkcp_action_url'] = base_url();

// 디버그 시
if($debug == "true") {
    $auto_fills = array(
        "good_name" => "테스트 상품",
        "good_mny" => "1",
        "buyr_name" => "홍길동",
        "buyr_mail" => "webmaster@example.org",
        "buyr_tel1" => "01000000000"
    );
    foreach($auto_fills as $k=>$v) {
        if(array_key_empty($k, $payinfo)) {
            $payinfo[$k] = $v;
        }
    }
    $data['payinfo'] = $payinfo;
}

// 결제창 불러오기 
renderView("view_orderpay.pgkcp", $data);
