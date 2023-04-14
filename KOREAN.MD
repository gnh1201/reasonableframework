# 본 웹 어플리케이션에 대하여
본 웹 어플리케이션은 ReasonableFramework 기반으로 작성되었습니다. https://github.com/gnh1201/reasonableframework

## 적재된 모듈 목록
* System: base (시스템 기반)
* System: config (시스템 설정)
* System: database (데이터베이스)
* System: logger (로그)
* System: uri (URI 및 URL 제어)
* System: security (보안)
* Helper: paginate (페이지 네비게이션)
* Helper: gnuboard, gnuboard.config, gnuboard.lib (그누보드 설정 및 라이브러리 연동)
* Helper: vworld (국토교통부 vworld 연동)
* Helper: wprest (워드프레스[wordpress] REST API 클라이언트)
* Hepler: webpagetool (Proxy 설정, UA 설정, JSON/DOM 파서를 지원하는 HTTP 클라이언트)
* Helper: noreporting, allreporting (오류 출력 범위를 설정)
* Helper: isemail.lnk (is_email 지원 -> /vendor/isemail)
* Helper: metaparser.lnk (HTML META 데이터 파싱 지원 -> /vendor/PHP-MetaParser)
* Helper: simple_html_dom (HTML DOM 처리기)
* Helper: oracle (오라클 데이터베이스 접속 및 쿼리)
* Helper: exectool (PHP 명령행 실행 지원)
* 그 외 다양한 사양 지원을 위한 모듈들

## 기술지원
* Website: https://catswords.social/@catswords_oss
* E-mail: abuse@catswords.net

## 저작권
* LGPL 3.0

## 최근변경
* 2023-04-14

# 정적 파일 호스팅 관련 가이드 (카페24 CDN/Amazon S3)
이 가이드는 ReasonableFramework 버전 v1.6.2를 기준으로 합니다. https://github.com/gnh1201/reasonableframework

- 카페24 ([10G 광아우토반 Full SSD](https://www.cafe24.com/?controller=product_page&type=basic&page=autoban) 웹 호스팅 상품 기준)
- Amazon S3 또는 타사 정적 파일 호스팅/Object Storage 사용자
- 문의사항

## 카페24 ([10G 광아우토반 Full SSD](https://www.cafe24.com/?controller=product_page&type=basic&page=autoban) 웹 호스팅 상품 기준)

1. 카페24(cafe24.com) 관리자 페이지에 로그인하여, CDN/스트리밍 도메인 정보를 얻는다.
   1. 메인 웹 사이트 로그인 -> `나의서비스관리` 클릭
   2. 좌측 메뉴에서 `서비스 접속관리` -> `서비스 접속 정보` 클릭
   3. CDN/스트리밍 정보 확인
   
       ```
       *** CDN ***
       CDN 하드 용량: 200M
       CDN 트래픽 용량: 500M
       FTP 주소: iup.cdn2.cafe24.com
       FTP 포트: 21
       FTP 아이디: [사용자 아이디]
       
       *** 스트리밍 ***
       스트리밍 하드 용량: 200M
       스트리밍 트래픽 용량: 500M
       FTP 주소: wm-004.cafe24.com
       FTP 포트: 5565
       FTP 아이디: [사용자 아이디]
       ```
   4. CDN의 경우, `https://[사용자 아이디].cdn2.cafe24.com`(예시)가 접속 주소가 된다.
   5. 스트리밍의 경우, `mms://wm-004.cafe24.com/[사용자 아이디]/abc.mp3`(예시)가 접속 주소가 된다.

2. ResonableFramework v1.6.2 설치 및 CDN/스트리밍 설정
   1. https://github.com/gnh1201/reasonableframework 접속 후 `Release` 탭을 누르고 `v1.6.2` 또는 최신 버전을 내려받는다.
   2. `storage/config/uri.ini.php` 파일의 CDN/스트리밍 관련 부분을 아래와 같이 수정한다.
   
       ```
       base_cdn_url = https://[사용자 아이디].cdn2.cafe24.com
       base_vod_url = mms://wm-004.cafe24.com/[사용자 아이디]
       ```
       
   3. `route` 폴더 밑에 `cdntest.php` 이름의 빈 파일을 만들고, 아래와 같이 입력 후 저장한다.
   
       ```
       <?php
       $data = array(
           "imageurl" => get_cdn_link("/picture.jpg");
       );
       renderView("view_cdntest", $data);
       ```
   
   4. `view` 폴더 밑에 `view_cdntest.php` 이름의 빈 파일을 만들고, 아래와 같이 입력 후 저장한다.
   
       ```
       <img src="<?php echo $imageurl; ?>" alt="this is cdn test">
       ```

   5. 사용하고 있는 웹 호스팅에 reasonableframework-master 폴더 아래의 모든 파일을 업로드한다.
   
   6. 1번에서 얻은 FTP 정보를 이용하여, CDN 서버에 `picture.jpg` 이름으로 임의의 JPG 그림 파일을 업로드한다.
   
   7. 웹 브라우저를 열고 `http://[웹 호스팅 주소]/?route=cdntest`에 접속하여 그림이 잘 뜨는지 확인한다.

## Amazon S3 또는 타사 정적 파일 호스팅/Object Storage 사용자
  - 정적 파일 호스팅을 지원하는 서비스의 경우, `base_cdn_url`의 주소만 바꾸어주면 동일한 방법으로 사용이 가능하다.

## 문의사항
   - abuse@catswords.net
