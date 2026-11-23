# 연동

[English](integration.md)

이 노트는 v1.6.8과 함께 적힌 모듈과, 정적 파일을 CDN이나 오브젝트 스토리지로 보내는 방법입니다. 프레임워크 저장소는 [gnh1201/reasonableframework](https://github.com/gnh1201/reasonableframework)입니다. 라이선스는 LGPL-3.0입니다. 아래 정적 호스팅 절차는 v1.6.8(2024-05-30) 기준입니다.

## v1.6.8에 적힌 모듈

- System: base (시스템 기반)
- System: config (시스템 설정)
- System: database (데이터베이스)
- System: logger (로그)
- System: uri (URI 및 URL 제어)
- System: security (보안)
- Helper: paginate (페이지 네비게이션)
- Helper: gnuboard, gnuboard.config, gnuboard.lib (그누보드 설정 및 라이브러리 연동)
- Helper: vworld (국토교통부 VWorld 연동)
- Helper: wprest (워드프레스 REST API 클라이언트)
- Helper: webpagetool (프록시, UA, JSON/DOM 파서를 지원하는 HTTP 클라이언트)
- Helper: noreporting, allreporting (오류 출력 범위)
- Helper: isemail.lnk (`is_email` 지원, `/vendor/isemail`)
- Helper: metaparser.lnk (HTML 메타 파싱, `/vendor/PHP-MetaParser`)
- Helper: simple_html_dom (HTML DOM)
- Helper: oracle (오라클 접속 및 쿼리)
- Helper: exectool (PHP에서 명령 실행)
- 같은 종류의 작업을 위한 헬퍼가 더 있습니다

## 정적 파일 호스팅 (카페24 CDN 또는 Amazon S3)

`get_cdn_link()`는 경로 앞에 `base_cdn_url`을 붙입니다. `get_vod_link()`는 경로 앞에 `base_vod_url`을 붙입니다. 두 키는 `storage/config/uri.ini.php`에 있습니다. 값이 비어 있으면 `base_url`을 씁니다.

이 절차가 대상으로 한 서비스는 다음과 같습니다.

- 카페24 [10G 광아우토반 FullSSD+](https://hosting.cafe24.com/?controller=new_product_page&page=autobahn) 상품
- Amazon S3
- 오브젝트 스토리지

### 카페24

1. 카페24 관리자에 로그인하고 CDN과 스트리밍 호스트를 확인합니다.
   1. 나의서비스관리를 엽니다.
   2. 서비스 접속관리에서 서비스 접속 정보를 엽니다.
   3. CDN과 스트리밍 정보를 읽습니다. 한 화면은 다음과 같았습니다.

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

   4. CDN 주소는 `https://[사용자 아이디].cdn2.cafe24.com` 형태입니다.
   5. 스트리밍 주소는 `mms://wm-004.cafe24.com/[사용자 아이디]/abc.mp3` 형태입니다.

2. [GitHub 릴리즈](https://github.com/gnh1201/reasonableframework/releases)에서 v1.6.8 또는 그 이후를 설치합니다.
   1. `storage/config/uri.ini.php`에 두 키를 넣습니다.

       ```
       base_cdn_url = https://[사용자 아이디].cdn2.cafe24.com
       base_vod_url = mms://wm-004.cafe24.com/[사용자 아이디]
       ```

   2. `route/cdntest.php`를 추가합니다.

       ```php
       <?php
       $data = array(
           "imageurl" => get_cdn_link("/picture.jpg")
       );
       renderView("view_cdntest", $data);
       ```

   3. `view/view_cdntest.php`를 추가합니다.

       ```php
       <img src="<?php echo $imageurl; ?>" alt="this is cdn test">
       ```

   4. 프로젝트 파일을 웹 호스팅에 올립니다.
   5. 1번에서 확인한 CDN FTP로 `picture.jpg`라는 JPEG를 올립니다.
   6. `http://[웹 호스팅 주소]/?route=cdntest`를 열어 그림이 보이는지 확인합니다.

### Amazon S3 또는 오브젝트 스토리지

업체가 안내하는 기본 URL을 `storage/config/uri.ini.php`의 `base_cdn_url`에 넣습니다.

## 기술 지원

- ActivityPub: https://catswords.social/@catswords_oss
- 이메일: [oss@catswords.re.kr](mailto:oss@catswords.re.kr)
- 취약점 제보: [GitHub Security Advisories](https://github.com/gnh1201/reasonableframework/security)

## 같이 보기

- [REST 클라이언트 (webpagetool)](rest-client.ko.md)
- [사용 사례](use-cases.ko.md)

## 출처

인터넷 아카이브 스냅샷 [20250812121351](https://web.archive.org/web/20250812121351/https://catswords-oss.rdbl.io/7839294550/3237877389). 아카이브 HTML은 이 페이지를 이스케이프된 마크다운으로 저장하고 있었습니다. 깨끗한 본문은 태그 v1.6.8의 `KOREAN.MD`이고, 이 페이지는 그 본문을 저장소에 맞게 고친 것입니다.
