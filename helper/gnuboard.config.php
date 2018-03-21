<?php
/**
 * @file gnuboard.config.php
 * @date 2018-01-01
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Helper Library for Gnuboard CMS (4/5), Content Driver for VerySimplePHPFramework
 */
 
 <?php
// 자주 사용하는 값
// 서버의 시간과 실제 사용하는 시간이 틀린 경우 수정하세요.
// 하루는 86400 초입니다. 1시간은 3600초
// 6시간이 빠른 경우 time() + (3600 * 6);
// 6시간이 느린 경우 time() - (3600 * 6);
$g4['server_time'] = time();
$g4['time_ymd']    = date("Y-m-d", $g4['server_time']);
$g4['time_his']    = date("H:i:s", $g4['server_time']);
$g4['time_ymdhis'] = date("Y-m-d H:i:s", $g4['server_time']);

//
// 테이블 명
// (상수로 선언한것은 함수에서 global 선언을 하지 않아도 바로 사용할 수 있기 때문)
//
$g4['table_prefix']        = "g4_"; // 테이블명 접두사
$g4['write_prefix']        = $g4['table_prefix'] . "write_"; // 게시판 테이블명 접두사

$g4['auth_table']          = $g4['table_prefix'] . "auth";          // 관리권한 설정 테이블
$g4['config_table']        = $g4['table_prefix'] . "config";        // 기본환경 설정 테이블
$g4['group_table']         = $g4['table_prefix'] . "group";         // 게시판 그룹 테이블
$g4['group_member_table']  = $g4['table_prefix'] . "group_member";  // 게시판 그룹+회원 테이블
$g4['board_table']         = $g4['table_prefix'] . "board";         // 게시판 설정 테이블
$g4['board_file_table']    = $g4['table_prefix'] . "board_file";    // 게시판 첨부파일 테이블
$g4['board_good_table']    = $g4['table_prefix'] . "board_good";    // 게시물 추천,비추천 테이블
$g4['board_new_table']     = $g4['table_prefix'] . "board_new";     // 게시판 새글 테이블
$g4['login_table']         = $g4['table_prefix'] . "login";         // 로그인 테이블 (접속자수)
$g4['mail_table']          = $g4['table_prefix'] . "mail";          // 회원메일 테이블
$g4['member_table']        = $g4['table_prefix'] . "member";        // 회원 테이블
$g4['memo_table']          = $g4['table_prefix'] . "memo";          // 메모 테이블
$g4['poll_table']          = $g4['table_prefix'] . "poll";          // 투표 테이블
$g4['poll_etc_table']      = $g4['table_prefix'] . "poll_etc";      // 투표 기타의견 테이블
$g4['point_table']         = $g4['table_prefix'] . "point";         // 포인트 테이블
$g4['popular_table']       = $g4['table_prefix'] . "popular";       // 인기검색어 테이블
$g4['scrap_table']         = $g4['table_prefix'] . "scrap";         // 게시글 스크랩 테이블
$g4['visit_table']         = $g4['table_prefix'] . "visit";         // 방문자 테이블
$g4['visit_sum_table']     = $g4['table_prefix'] . "visit_sum";     // 방문자 합계 테이블
$g4['token_table']         = $g4['table_prefix'] . "token";         // 토큰 테이블

//$g4['cookie_domain'] = ".power.386.org";

$g4['link_count'] = 2;
$g4['charset'] = "utf-8";
$g4['token_time'] = 3;
$g4['url'] = "";
$g4['https_url'] = "";

define('G4_HTML_PURIFIER', false);

// get gnuboard config
$gb_result = sql_query(" select * from {$g4['config_table']} ");
$gb_config = $gb_result->fetch();
