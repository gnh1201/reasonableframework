# 변경 기록

ReasonableFramework의 주요 변경을 이 파일에 적습니다.

버전 번호는 Git 태그와 같습니다. 제목에서는 앞의 `v`를 뺍니다. 처음부터 `v` 없이 올린 태그는 그 철자를 유지합니다. 태그된 최신 릴리즈는 v1.6.9입니다.

각 절은 이 목록에서 바로 앞 태그와의 차이입니다. v1.4는 v1.3에서 이어진 커밋이 아니라서, 그 절은 두 트리의 차이를 적습니다.

## [미발표]

### 보안

- [CVE-2026-128064](https://github.com/php/php-src/security/advisories/GHSA-f6gg-rchx-xw8j)에 대한 우회를 넣었습니다. PHP의 HTTP와 FTP 스트림 래퍼는 스키마의 대소문자를 구분하지 않고 받지만, 스키마에 소문자 `s`가 있을 때만 TLS를 켭니다. 그래서 `HTTPS://`나 `FTPS://` 주소, 그리고 리다이렉트 `Location`으로 받은 같은 형태의 주소가 평문으로 나갔습니다.
- `file_get_contents_tls_scheme()`을 추가했습니다. `file_get_contents()`를 호출하기 전에 `http`, `https`, `ftp`, `ftps` 스키마를 소문자로 바꿉니다. 리다이렉트도 이 함수가 따라가서, 다음 `Location`에 PHP가 같은 판정을 다시 적용하지 않습니다.
- `get_web_fgc()`, `file_get_html()`, `simple_html_dom::load_file()`이 원격 URL을 이 함수로 읽습니다.
- `get_xor_text()`의 중괄호 문자열 오프셋을 대괄호로 바꿔, `system/security.php`가 PHP 8에서 로드되게 했습니다. 같은 코드는 PHP 5.2 이후에서도 실행됩니다. PHP 5.3.4 미만은 `follow_location` 컨텍스트 옵션을 무시하며, 그 버전에서는 `max_redirects`를 `0`으로 두어 래퍼가 리다이렉트를 따라가지 않게 합니다.

### 변경

- README에 2025년부터 기능 개발을 멈추고, 기존 사용자를 위한 보안 패치만 유지한다고 적었습니다.
- 취약점 제보 창구를 oss@catswords.re.kr과 [GitHub Security Advisories](https://github.com/gnh1201/reasonableframework/security)로 바꿨습니다.
- 커뮤니티 링크를 [Catswords OSS Discord](https://discord.gg/FGMb9VnVFC)로 바꿨습니다.
- catswords-oss.rdbl.io 문서를 영어와 한국어로 `docs/`에 실었습니다. 본문은 2025-08-12 인터넷 아카이브 스냅샷이고, 예제는 이 저장소의 함수를 따릅니다. `README.md`와 `SECURITY.md`는 영어 페이지를 가리킵니다.
- 연락 주소를 oss@catswords.re.kr로 맞췄습니다. `SECURITY.md`, 결제 안내, 기본 User-Agent가 여기 포함됩니다.
- Go Namhyeon의 작성자 이메일을 파일 헤더와 `composer.json`에서 gnh1201@catswords.re.kr로 바꿨습니다.

## [1.6.9] - 2024-11-11

문서 릴리즈입니다. v1.6.8과 v1.6.9 사이에는 실행 코드 변경이 없습니다.

### 추가

- `CITATION.cff`와 README의 Zenodo DOI 배지를 추가했습니다. 인용 기록은 [10.5281/zenodo.11392417](https://doi.org/10.5281/zenodo.11392417)입니다.
- 후원 설정을 `.github/FUNDING.yml`로 옮겼습니다. GitHub 스폰서는 `gnh1201`이고, 추가 주소는 `https://gnh1201.link`입니다.
- `SECURITY.md`에 이전 브랜치의 EOL 문서를 연결했습니다. [EOL (catswords-oss.rdbl.io)](https://catswords-oss.rdbl.io/7839294550/3090512080)
- 보안 연락처에 Catswords Microsoft Teams 커뮤니티를 넣었습니다.

### 변경

- 사용 사례 문서를 catswords-oss.rdbl.io로 옮겼습니다. README는 REST 클라이언트, Zabbix에서 Grafana로 보내는 기록, Papago REST 예제, KCP 결제 라우트, RFC 3164 원격 로깅, 정적 링크 재작성을 가리킵니다.
- 구조도 이미지를 `https://ics.catswords.net/reasonableframework.jpg`에서 제공합니다.
- `SECURITY.md`에서 1.5.x와 1.5 이전 릴리즈를 레거시로 표시했습니다. 1.5.x의 EOL은 2020-08-25입니다. 보안 업데이트를 받는 줄은 1.6.x입니다.
- README 연락처에 GitHub Security Advisories, ActivityPub, XMPP, Microsoft Teams를 반영했습니다.

### 제거

- 루트의 `FUNDING.yml`을 지우고 `.github/`로 옮겼습니다.
- `KOREAN.MD`를 삭제했습니다.
- `assets/img/`에 두었던 이미지를 삭제했습니다.

## [1.6.8] - 2024-05-30

문서와 부트스트랩 정리입니다. 추가되거나 삭제된 PHP 함수는 없습니다.

### 변경

- `bootstrap.sh`가 `chmod -R u+rwX,go+rX,go-w`로 권한을 맞추고, `storage/example/_.htaccess`를 `.htaccess`로 복사합니다.
- `KOREAN.MD`를 고쳤습니다.

### 제거

- `bootstrap.707.sh`, `fixindent.sh`, LGPL 배지 이미지 `lgplv3-147x51.png`를 삭제했습니다.

## [1.6.7] - 2024-04-29

### 변경

- README 사양 설명을 다시 썼습니다. RVHM, 공유 변수, 객체 지향 코드와 함께 쓰는 CGI 스타일, 데이터베이스 드라이버(PDO, MySQLi, 구형 MySQL, 셸을 통한 MySQL, Oracle OCI)를 적습니다. 호스팅 표의 제목은 "Tested on free web hostings"입니다.
- 공개 이름을 ReasonableFramework로 적고, RSF, VSPF, 저작권 등록 C-2020-018490도 함께 적었습니다.
- README와 `SECURITY.md` 연락처에 ActivityPub `@catswords_oss@catswords.social`을 넣었습니다.
- `FUNDING.yml`에 GitHub 스폰서 `gnh1201`, Open Collective `welsonjs`, Liberapay `catswords`, Ko-fi `catswords`, 사용자 지정 주소 `https://www.buymeacoffee.com/catswords`와 `https://toss.me/catswords`를 넣었습니다.
- `read_route()`에서 `get_network_event()` 호출을 뺐습니다.

### 제거

- `SECURITY.md`의 "Paid security services" 절을 뺐습니다.
- README 상단의 KCC 배지와 LGPL 배지 이미지를 뺐습니다.

## [1.6.7-preview1] - 2023-04-14

77개 파일이 바뀌었고, 대부분은 파일 헤더입니다. 그 헤더의 작성자 연락처는 `abuse@catswords.net`입니다.

### 추가

- `detect_perf_agent()`를 추가했습니다. 사용자 에이전트에서 Pingdom Page Speed와 GTmetrix를 찾습니다.
- `X-Forwarded-Host`가 `.crypto`로 끝나면 `.onion`, `.i2p`와 같이 히든 서비스 신호로 셉니다.

### 변경

- `KOREAN.MD`와 README를 고쳤습니다.

### 제거

- `VERSION.MD`와 `CDN_HOWTO.md`를 삭제했습니다.

## [1.6.6] - 2022-01-17

### 추가

- `helper/coupang.api.php`를 추가했습니다. `coupang_get_signature()`, `coupang_search_items()`가 있습니다.
- `helper/hiddentool.php`를 추가했습니다. `detect_hidden_service()`는 `X-Forwarded-Host`가 `onion` 또는 `i2p`로 끝나면 점수를 올립니다.
- `hmacsha256_sign_message()`(SHA-256 `hash_hmac`), `get_header_value()`, `test_user_agent()`를 추가했습니다.
- `storage/config/l10n.ini.php`를 추가했습니다. 지역화는 기본적으로 꺼져 있고, 기본 언어는 `ko`입니다.
- README에 RFC 3164 원격 로깅, 무료 호스팅 시험 표, KCC 등록 배지 `C-2020-018490`을 적었습니다.

### 변경

- `storage/config/log.ini.php`를 `storage/config/logger.ini.php`로 바꿨습니다.
- 로거, 스토리지, URI, 데이터베이스, 보안 설정, `exectool.php`, `webpagetool.php`를 고쳤습니다.
- README의 한국어 소개를 늘렸습니다.

## [1.6.5.2-kcc] - 2020-06-03

README만 바뀌었습니다.

### 추가

- 한국저작권위원회 소프트웨어 등록 `C-2020-018490`을 적었습니다. 대상은 v1.6.5.1-kcc이고, 승인일은 2020-06-03입니다. README는 오픈 소스 라이선스가 그대로라고 적습니다.

## [1.6.5.1-kcc] - 2020-05-29

### 추가

- `helper/string.utils.php`에 `get_csv_from_array()`를 추가했습니다.
- `SECURITY.md`에 지원 버전 표를 넣었습니다. 1.6.x는 지원, 1.5.x는 2020-08-25까지 지원, 1.5 이전은 미지원입니다.
- 추가 보안 작업을 위한 유료 스폰서 등급을 적고, README에서 `SECURITY.md`로 연결했습니다.

### 변경

- `helper/webpagetool.php`를 고쳤습니다.

## [1.6.5] - 2020-05-21

### 추가

- `read_requests()`에 `yaml` 옵션이 있고 `yaml_parse()`가 있으면, YAML 본문을 `$requests['_YAML']`에 넣습니다.

### 변경

- README를 고쳤습니다.
- `get_requested_value()`가 문자열 값 전체에 `addslashes()`와 `htmlspecialchars()`를 적용합니다.

### 제거

- `get_requested_jsondata()`를 뺐습니다. 함수 본문이 자기 자신을 호출했습니다.

## [1.6.4] - 2020-05-04

### 추가

- 업로드 파일을 스토리지에 두는 `allocate_uploaded_files()`를 추가했습니다.
- `get_db_timediff()`를 추가했습니다.

### 변경

- 로거, Zabbix 헬퍼, 스토리지를 고쳤습니다.

### 제거

- `get_timediff_on_query()`를 뺐습니다.
- `move_uploaded_file_to_stroage()`를 뺐습니다.

## [1.6.3] - 2020-04-20

### 추가

- `helper/SSL.class.php`를 추가했습니다. `getCertificateDetails()`, `getSSLinfo()`, `dateFormat()`, `dateFormatMonth()`, `diffDate()`가 있습니다.
- UUID 클래스에 `instantiate()`를 추가했습니다.

### 변경

- `CDN_HOWTO.md`, README, `cli.php`, `helper/webpagetool.php`, `helper/zabbix.api.php`, `system/database.php`를 고쳤습니다.

## [1.6.2] - 2020-04-12

### 추가

- `CDN_HOWTO.md`를 추가했습니다.
- `storage/config/uri.ini.php`에 `base_route`를 넣었습니다. `read_route()`는 인자를 받지 않고, `base_route` 다음의 첫 경로 조각을 고릅니다. `index.php`는 건너뜁니다. 맞는 조각이 없으면 `welcome` 라우트로 갑니다.

### 변경

- URI 설정 키를 `base_url`, `base_cdn_url`, `base_vod_url`로 두고 `=` 양쪽에 공백을 넣었습니다.

## [1.6.1] - 2020-04-12

### 추가

- `storage/config/deprecated.ini.php`를 추가했습니다. 처음 폐기 목록은 `check_invalid_function`, `check_function_exists`, `array_multikey_empty`, `split_by_line`, `startsWith`, `endsWith`입니다. `is_deprecated_fn()`이 이 목록을 읽습니다.
- `helper/migrate.1.5.php`를 추가했습니다.
- `get_cdn_link()`와 `get_vod_link()`를 추가했습니다. 경로 앞에 `base_cdn_url`, `base_vod_url`을 붙입니다.
- 예전 호환 함수의 공개 이름으로 `eregi()`와 `eregi_replace()`를 추가했습니다.
- `is_prefix()`, `is_suffix()`, `strlike()`를 추가했습니다.
- `get_scope()`, `set_scope()`, `check_function_exists()`를 다시 넣었습니다.
- `array_key_unset()`, `get_key_in_array()`, `get_seconds()`, `explode_by_line()`, `explode_storage_file_by_line()`, `get_bind_to_sql_past()`, `get_bind_to_sql_last()`, `get_cpu_idle2()`, `set_cpu_usage_limit()`, `set_min_cpu_idle()`, `zabbix_get_hostgroups()`를 추가했습니다.

### 변경

- 환영 화면, URI 설정, 성능 헬퍼, Colona 포맷, 문자열 헬퍼, 웹 페이지 도구를 고쳤습니다.

### 제거

- `startsWith()`, `endsWith()`, `eregi_compatible()`, `eregi_replace_compatible()`, `get_bind_to_sql_past_minutes()`, `multi_str_split()`, `read_storage_file_by_line()`을 뺐습니다.

## [1.6-pre20200220-1] - 2020-02-20

### 추가

- `helper/perftool.php`를 추가했습니다. `get_cpu_idle()`, `set_max_execution_time()`, `set_memory_limit()`, `set_post_max_size()`, `set_upload_max_filesize()`가 있습니다.

### 수정

- `system/database.php`와 `helper/colona.v1.format.php`의 오타를 고쳤습니다.

## [1.6-pre20200217-2] - 2020-02-17

### 추가

- 일괄 데이터베이스 쓰기를 추가했습니다. `exec_db_bulk_start()`, `exec_db_bulk_push()`, `exec_db_bulk_end()`입니다.

### 변경

- `composer.json`을 고쳤습니다.

## [1.6-pre20200217-1] - 2020-02-17

### 추가

- `is_fn()`과 `is_not_fn()`을 추가하고, 헬퍼의 함수 존재 검사를 이 함수로 바꿨습니다.
- 웹 페이지 수신 함수 `get_web_aria()`(aria2c), `get_web_httpie()`(`http` 명령), `get_web_async()`(`get_web_cmd()`의 프로세스 ID를 반환)를 추가했습니다.
- `get_bind_to_sql_fields()`를 추가했습니다.
- LGPL 배지 이미지 `lgplv3-147x51.png`를 추가했습니다.

### 변경

- README와 `storage/config/system.ini.php`를 고쳤습니다.

### 제거

- 이 태그에서 `check_function_exists()`와 `check_invalid_function()`을 뺐습니다. v1.6.1에서 `check_function_exists()`가 다시 들어옵니다.

## [1.6-pre20200204-1] - 2020-02-04

### 추가

- 스코프 헬퍼를 공유 변수로 바꿨습니다. `get_shared_var()`와 `set_shared_var()`가 `$shared_vars`에 값을 둡니다.
- `exec_db_temp_clear()`, `get_uncompressed_text()`, `json_decode_assoc()`을 추가했습니다.
- Zabbix 헬퍼 `zabbix_get_alerts()`, `zabbix_get_hosts()`, `zabbix_get_records()`를 추가했습니다.

### 변경

- `do_error()` 자리에 `trigger_error($msg, $code)` 래퍼를 두었습니다. 이 래퍼는 메시지를 기록하고 모은 오류를 출력합니다. `check_function_exists("trigger_error")` 가드는 PHP에 이미 있는 `trigger_error()`를 보므로, 이 태그는 그 래퍼를 정의하지 않습니다.
- README, CLI, 보안 계층, MySQL과 Oracle 드라이버, 난독화 도구, JSON 헬퍼를 고쳤습니다.

### 제거

- `get_scope()`, `set_scope()`, `do_error()`, `json_decode_to_assoc()`을 뺐습니다.

## [1.6-pre20200125-1] - 2020-01-25

### 추가

- `helper/rfc3164.proto.php`를 추가했습니다. `rfc3164_get_config()`, `rfc3164_send_message()`가 있습니다.
- `storage/config/log.ini.php`를 추가했습니다.
- `append_log_to_file()`과 `get_int()`를 추가했습니다.

### 변경

- 로거, URI 라우터, KCP 주문 라우트와 뷰, 문자열 헬퍼, `composer.json`, `index.php`, README를 고쳤습니다.

### 제거

- `write_log_to_file()`을 뺐습니다.

## [1.6-pre200117-1] - 2020-01-16

### 변경

- SQL INSERT 빌더의 옵션 `setduplicate`, `setignore`를 `setkeys`, `setignores`로 바꿨습니다.
- `helper/pgkcp.lnk.php`가 KCP 게이트웨이 설정을 `json_decode()` 객체의 속성으로 읽습니다.
- `get_web_curl()`은 배열 POST 본문을 `http_build_query()`로 보냅니다. 문자열 본문은 그대로 보냅니다.
- `json_decode_ex()`는 호출 전에 `assoc` 옵션으로 연관 배열 여부를 정합니다.

## [1.6-pre200109-2] - 2020-01-09

### 추가

- `exec_db_table_insert()`와 `get_db_zero()`를 추가했습니다.

### 변경

- `storage/config/database.ini.php`를 고쳤습니다.

## [1.6-pre200109-1] - 2020-01-09

### 수정

- `route/orderpay.step2.pgkcp.php`가 `string.utils`를 읽게 했습니다. 이전 이름은 `string.urils`였습니다.

## [1.6-pre200108-1] - 2020-01-08

`system/database.php`만 바뀌었습니다.

### 추가

- `check_table_is_separated()`, `get_db_tablenames()`, `exec_db_table_drop()`, `exec_db_table_update()`를 추가했습니다.

## [1.6-pre200102-1] - 2020-01-02

### 추가

- `storage/config/database.ini.php`에 `db_separated_tables`를 넣었습니다. 요청한 테이블 이름이 이 쉼표 목록에 있으면, SELECT는 `` `<table>.tables` ``에 들어 있는 이름을 `UNION`으로 묶습니다.

### 변경

- SELECT, DELETE, CREATE 빌더가 테이블과 컬럼 이름을 백틱으로 감쌉니다.
- 기존 테이블 확인은 `information_schema.tables`의 `TABLE_SCHEMA`, `TABLE_NAME`으로 합니다.
- `exec_db_table_create()` 뒤에 `setunique` 옵션으로 유니크 인덱스를 만듭니다.
- 임시 테이블은 `` create temporary table if not exists `<name>` <statement> ``로 만듭니다.
- WHERE 빌더의 세 번째 조각 기본값을 `1`로 두었습니다.

## [1.6-pre191230-1] - 2019-12-30

풀 리퀘스트 68의 자동 파티셔닝 작업이 들어 있습니다.

### 추가

- `get_bind_to_sql_create()`와 `exec_db_table_create()`를 추가했습니다.
- `get_old_version()`을 추가했습니다. 2019-11-17 프리릴리즈의 데이터베이스 계층이 이미 이 함수를 호출하고, `storage/config/system.ini.php`에 `old_version=0`을 둡니다.
- `write_debug_log()`와 `write_log_to_file()`을 추가했습니다.

### 변경

- KCP 주문 위젯, `helper/json.format.php`, `system/config.php`를 고쳤습니다.

## [1.6-pre191117-1] - 2019-11-17

### 추가

- `storage/config/system.ini.php`에 `old_version=0`을 넣었습니다.

### 변경

- `get_old_version()`이 `1.4`이면 fetch-all 응답에 `cnt`, `data`와 함께 `length`가 들어갑니다. `1.5`이면 WHERE와 UPDATE 빌더가 1.5 호환 경로를 탑니다.
- `remove_volatile_files()`에 스토리지 종류를 넘깁니다.

### 수정

- `storage/config/lguplus.ini.php`의 여는 태그를 `<?php1`에서 `<?php`로 고쳤습니다.

## [1.6-pre191115-2] - 2019-11-15

### 추가

- `remove_volatile_files()`를 추가했습니다.

### 변경

- README, `index.php`, `storage/config/system.ini.php`를 고쳤습니다.

## [1.6-pre191115-1] - 2019-11-15

### 추가

- `remove_storage_files()`를 추가했습니다.

### 변경

- `index.php`와 `system/storage.php`를 고쳤습니다.

## [1.6-pre191114-2] - 2019-11-14

### 수정

- `get_current_timestamp()`에 여는 중괄호를 넣었습니다.
- 스토리지 캐시 나이를 `filemtime()`으로 재고, `max_age`를 정수로 변환합니다. `max_age`가 `0`이면 파일은 만료되지 않습니다.
- `read_config()`가 include한 설정 파일의 반환값을 유지합니다.

### 변경

- 본문을 저장하지 않는 스토리지 쓰기는 `fake` 모드입니다. `w` 모드는 파일을 씁니다.
- `get_web_cache()`가 웹 페이지 캐시 적중과 미스를 로그에 남깁니다.

## [1.6-pre191114-1] - 2019-11-14

### 추가

- `zabbix_get_problems()`와 `zabbix_get_triggers()`를 추가했습니다.

### 변경

- `helper/webpagetool.php`, `system/database.php`, `system/storage.php`, `storage/config/system.ini.php`를 고쳤습니다.

## [1.6-pre191108-1] - 2019-10-25

태그 객체 날짜는 2019-10-08입니다. 태그가 가리키는 커밋 날짜는 2019-10-25입니다.

### 추가

- `helper/thumbnail.youtube.php`와 `get_youtube_thumbnail()`을 추가했습니다.
- `exec_db_temp_create()`, `get_current_timestamp()`, `get_web_binded_url()`을 추가했습니다.

### 변경

- KCP 주문과 완료 라우트, Payman 스크립트, 보안 설정, JSON 헬퍼, Twilio 헬퍼, 문자열 헬퍼, 웹 페이지 도구를 고쳤습니다.

## [1.6-pre191013-1] - 2019-10-13

### 추가

- `helper/json.format.php`를 추가했습니다. `json_decode_ex()`, `json_encode_ex()`가 있습니다.
- `helper/compress.zip.php`와 `unzip()`을 추가했습니다.
- `helper/pgkcp.install.php`(`install_pgkcp()`, `get_pgkcp_dir()`)와 `helper/lguplus.install.php`(`lguplus_install()`)를 추가했습니다.
- `expire_isolated_session()`을 추가했습니다.
- `storage/sandbox/logs/index.php`를 추가했습니다.

### 변경

- KCP 주문서 뷰, 웹 페이지 도구, 데이터베이스, 설정, URI, 스토리지, 보안을 고쳤습니다.

### 제거

- `end_isolated_session()`을 뺐습니다.

## [1.6-pre191011-3] - 2019-10-11

`system/storage.php`만 바뀌었습니다.

### 추가

- `write_storage_file()`은 `url` 옵션이 참이면 공개 URL을 반환합니다. 업로드 기준 URL에 파일 이름의 `get_safe_path()` 결과를 붙입니다.

## [1.6-pre191011-2] - 2019-10-11

### 추가

- `start_isolated_session()`과 `end_isolated_session()`을 추가했습니다.
- `storage/sandbox/sessions/index.php`를 추가했습니다.

### 변경

- `index.php`와 `system/security.php`를 고쳤습니다.

## [1.6-pre191011-1] - 2019-10-11

### 변경

- LG U+ SMS 스위치를 설정 키 `enabled`에서 읽습니다. 값이 `1`일 때 발송합니다.
- `get_current_datetime()`이 `now`와 `adjust`를 받습니다. `now`는 `DateTime::createFromFormat()`으로 파싱하고, 예외가 나면 `strtotime()`을 씁니다. `adjust`는 `strtotime()`으로 적용합니다.
- `exec_db_temp_start()`의 바인드 인자 기본값을 빈 배열로 두었습니다.

## [1.6-pre191008-1] - 2019-10-08

### 추가

- LG U+ SMS용 `helper/lguplus.api.php`와 `storage/config/lguplus.ini.php`를 추가했습니다. `lguplus_get_config()`, `lguplus_send_message()`가 있습니다.
- `helper/colona.v1.format.php`와 `decode_colona_format()`을 추가했습니다.
- `get_web_cspt()`를 추가했습니다. 페이지를 받은 뒤 본문에 `catsplit_decode()`를 적용합니다.
- 셸 MySQL용 `exec_db_mysql_cmd_fetch_all()`, `get_db_mysql_cmd_connect()`와, `exec_db_temp_start()`, `exec_db_temp_end()`를 추가했습니다.
- `get_compressed_text()`, `get_splitted_strings()`, `get_string_length()`, `remove_utf8_bom()`, `twilio_parse_messages()`를 추가했습니다.
- 웹 페이지 도구 그림 `assets/img/webpagetool-1.png`, `assets/img/webpagetool-2.png`를 추가했습니다.

### 변경

- Twilio 라우트와 헬퍼, KCP 결제 단계, `storage/config/3rdparty.ini.php`를 고쳤습니다.

## [1.5] - 2019-07-16

### 추가

- PureCSS를 `vendor/_dist/purecss`에 넣었습니다.
- `casplit_escape()`, `catsplit_unescape()`, `do_error()`, `get_floating_percentage()`를 추가했습니다.

### 변경

- 업로드 헬퍼 이름을 다시 `move_uploaded_file_to_stroage()`로 두었습니다.
- CatSplit, 스프레드시트 리더, 문자열 헬퍼, 웹 페이지 도구, 보안, 스토리지, URI, 데이터베이스, README, `SECURITY.md`를 고쳤습니다.

### 제거

- `helper/archivetool.php`, `helper/pChart2.lnk.php`, v1.4에서 넣은 보안 벤더 링크 `php-sec-fw.lnk.php`, `php-sec-ddos.lnk.php`, `php-sec-shell.lnk.php`, `php-sec-scc.lnk.php`를 뺐습니다.

## [1.4] - 2019-06-05

이 태그는 v1.3에서 이어진 커밋이 아닙니다. 아래는 두 트리의 차이입니다.

### 추가

- `SECURITY.md`와 루트 `FUNDING.yml`을 추가했습니다.
- `helper/catsplit.format.php`를 추가했습니다. `catsplit_encode()`, `catsplit_decode()`가 있습니다.
- `helper/api.whois.kr.php`와 `get_whois_kr()`을 추가했습니다.
- 보안 벤더 링크 `helper/php-sec-fw.lnk.php`, `helper/php-sec-ddos.lnk.php`, `helper/php-sec-shell.lnk.php`, `helper/php-sec-scc.lnk.php`를 추가했습니다.
- `check_is_empty()`를 추가했습니다.
- Cascade Framework를 `vendor/_dist/cascadeframework`에 넣었습니다. jQuery 1.11.1과 2.1.1이 들어 있습니다.
- `fixindent.sh`를 추가했습니다.
- 업로드 헬퍼 이름을 `move_uploaded_file_to_storage()`로 바꿨습니다.

### 변경

- `helper/jcryption.lnk.php`를 `helper/jCryption.lnk.php`로 바꾸고 `jCryption_get()`을 노출합니다.
- `vendor/PHP-MetaParser/MetaParser.class.php`를 `vendor/_dist/PHP-MetaParser/MetaParser.class.php`로 옮겼습니다.
- KCP 라우트와 뷰, 웹 프록시, 웹 앱 라우트, 워드프레스 글 라우트, 로거, 스토리지, 보안 설정을 고쳤습니다.

### 제거

- `get_db_binded_param()`을 뺐습니다.

## [1.3] - 2019-05-13

### 추가

- 데이터베이스 드라이버 `helper/database.mysql.cmd.php`(셸을 통한 MySQL)와 `helper/database.mssql.pdo.php`(PDO를 통한 MSSQL)를 추가했습니다. `exec_db_mysql_cmd_query()`, `get_db_mssql_pdo_connect()`, `exec_db_mssql_pdo_query()`, `exec_db_mssql_pdo_fetch_all()`이 있습니다.
- `helper/zabbix.api.php`와 `route/api.zabbix.php`를 추가했습니다. `get_zabbix_config()`, `zabbix_get_api_url()`, `zabbix_authenticate()`, `zabbix_retrieve_hosts()`, `zabbix_get_items()`, `zabbix_get_id()`가 있습니다.
- `helper/twilio.api.php`와 `route/api.twilio.php`를 추가했습니다. `twilio_get_config()`, `twilio_send_message()`, `twilio_send_voice()`가 있습니다.
- `helper/mailgun.api.php`와 `route/api.mailgun.php`를 추가했습니다. `mailgun_get_config()`, `mailgun_send_message()`가 있습니다.
- `helper/webhooktool.php`(`send_web_hook()`), `helper/mobiletool.php`(`detect_mobile()`), `route/mobileswitcher.php`, `view/view_mobileswitcher.php`를 추가했습니다.
- `helper/pChart2.lnk.php`와 `helper/country.kr.php`를 추가했습니다. 한국어 이름과 성 목록은 `storage/country/`에 있습니다.
- `get_safe_path()`, `get_xor_text()`, `get_tokenized_text()`, `append_storage_file()`, `retrieve_storage_dir()`, `get_requested_jsondata()`, `get_hash_algos()`, `check_hash_algo()`, `check_db_oracle_installed()`, `check_invalid_function()`을 추가했습니다.
- `bootstrap.sh`와 `bootstrap.707.sh`를 추가했습니다.
- `storage/config/3rdparty.ini.php`와 `storage/config/social.ini.php`를 추가했습니다.
- 예제 `.htaccess`를 `storage/example/_.htaccess`에 두고, Oracle `tnsname.orax` 예제를 `storage/example/`로 옮겼습니다.

### 변경

- `helper/string.utl.php`를 `helper/string.utils.php`로 바꿨습니다.
- `helper/socialhub.utl.php`를 `helper/socialtool.php`로, `route/api.socialhub.php`를 `route/api.social.php`로 바꿨습니다. 소셜 헬퍼는 `social_get_object()`, `social_get_object_facebook()`, `social_parse_object_id()`, `social_send_message()`입니다.
- `route/api.newuuid.php`를 `route/api.uuid.php`로 바꿨습니다.
- `function_exists()` 직접 호출을 `check_function_exists()`로 바꿨습니다.
- `get_requested_value()`가 둘 이상의 출처를 받습니다.
- Oracle, MySQL, HybridAuth, 스프레드시트, 페이지 나누기, 그누보드, 제로보드 헬퍼를 고쳤습니다.
- 로그 파일 권한을 조였습니다.

### 제거

- `helper/naturename.kr.php`, `storage/config/sns.ini.php`, `storage/config/vworld.ini.php`를 뺐습니다.
- `get_scope_all()`, `xor_this()`, `hash_algo_exists()`, `read_route_all()`을 뺐습니다.

## [1.2] - 2019-02-24

### 추가

- `route/webapp.php`와 `webapp/webapp.md`를 추가했습니다.
- `route/ping.php`를 추가했습니다.
- `get_db_driver()`, `check_db_driver()`, `get_requested_json_value()`, `get_web_user_agent()`, `read_route()`, `read_route_all()`을 추가했습니다.
- README에 RVH를 적었습니다. Route, View, Helper이고 Model은 선택입니다. Papago REST 예제, KCP 결제 라우트, 그누보드 라우트, `cli.php` 사용법을 연결합니다.

### 변경

- 데이터베이스, URI 라우터, 웹 페이지 도구, 대체 데이터베이스 헬퍼, `index.php`, `cli.php`를 고쳤습니다.

### 제거

- `helper/allreporting.php`를 삭제했습니다.

## [1.1] - 2019-02-13

처음 태그된 스냅샷입니다. 이 태그까지 이어진 이력은 891개 커밋입니다. 이 절은 그 태그의 트리를 설명합니다.

### 추가

- `system/`에 RVH 핵심을 두었습니다. `base.php`, `config.php`, `database.php`, `logger.php`, `security.php`, `storage.php`, `uri.php`입니다. 진입점은 `index.php`와 `cli.php`이고, `welcome.sh`와 `welcome.bat`이 있습니다.
- 설정은 `storage/config/`에서 읽습니다. `database.ini.php`, `security.ini.php`, `system.ini.php`, `uri.ini.php`, `country.ini.php`, HybridAuth, SNS, VWorld가 있습니다.
- 요청은 `route/`로 갑니다. `welcome`, `webproxy`, `wppost`, 그누보드, UUID, 난독화, `api.usage`, KCP 주문과 결제, Payman 예제, 404 페이지가 있습니다. 뷰는 `view/`에 있고 `default`와 `frame100` 템플릿이 있습니다.
- 페이지를 가져오는 헬퍼(`webpagetool.php`, `simple_html_dom.php`), 워드프레스 REST, 그누보드, 제로보드 4, 킴스보드, HybridAuth, MySQL(Improved와 구형 확장), Oracle, 스프레드시트, 난독화, 암호화, jCryption, VWorld 지오코딩, 페이지 나누기, 압축, 문자열, 네트워크 주소, 셸 명령, MIME, UUID 헬퍼가 들어 있습니다.
- JSON, 원문, PHP 직렬화 요청 본문을 받습니다. 요청 문자를 XSS 기준으로 정리합니다. 옛 `eregi` 함수를 쓰는 코드를 위해 `eregi_compatible()`과 `eregi_replace_compatible()`을 둡니다.
- `KOREAN.MD`, `VERSION.MD`, `LICENSE`, `composer.json`, `assets/img/`의 구조도 이미지를 넣었습니다.
