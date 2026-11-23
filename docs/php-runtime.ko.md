# PHP 런타임 보안

[English](php-runtime.md)

2026년 10월 21일과 11월 19일에 발표된 결함은 PHP 엔진에 있습니다. ReasonableFramework에는 이에 대한 패치가 없습니다. 작업은 PHP를 실행하는 서버에서 합니다.

## 이름

PHP2Shell은 CVE-2026-109618과 CVE-2026-109619입니다. PHP 프로젝트가 [2026년 10월 21일](https://www.php.net/archive/2026.php#2026-10-21-5)에 설명했습니다.

- [CVE-2026-109618](https://github.com/php/php-src/security/advisories/GHSA-w8hw-w9fx-j79w)은 메모리 한도 처리에 있습니다. 정리가 절반만 초기화된 요청 데이터를 유효한 것으로 다룰 수 있습니다.
- [CVE-2026-109619](https://github.com/php/php-src/security/advisories/GHSA-6wmf-4rv5-p3rp)은 이전 요청의 메모리를 가리키는 내부 상태가 남을 수 있습니다.

phpAfterShell은 [CVE-2026-126714](https://github.com/php/php-src/security/advisories/GHSA-fm2m-fjf5-v3cp)입니다. 기본 파일 세션 핸들러는 기록이 중간에 멈추면 이전 세션의 일부를 남길 수 있습니다. PHP4Shell이라는 CVE는 없습니다. 그 별칭은 이번 11월 후속 조치와, 그 이전에는 PHP2Shell에 쓰였습니다.

11월 19일 릴리스는 10월 수정을 마칩니다.

- [CVE-2026-129481](https://github.com/php/php-src/security/advisories/GHSA-g3vh-838c-65v7)은 PHP 8.3 이상입니다. PHP는 이것을 CVE-2026-109619 수정의 미완으로 설명합니다.
- [CVE-2026-129182](https://github.com/php/php-src/security/advisories/GHSA-2fjv-p558-g82m)은 PHP 8.5의 치명 오류 역추적입니다.

[CVE-2026-115560](https://github.com/php/php-src/security/advisories/GHSA-658j-r8rw-q6mx)은 `session.serialize_handler`가 `php_binary` 또는 `php_serialize`인 상태에서, `session_start()` 전에 `ini_set()`으로 `php`로 바꾸고, 세션 업로드 진행 표시가 켜져 있을 때 해당합니다. 이 저장소는 그 핸들러를 바꾸지 않습니다.

## 설치할 버전

| 브랜치 | 설치 |
| --- | --- |
| 8.6 | 8.6.0 |
| 8.5 | 8.5.13 |
| 8.4 | 8.4.28 |
| 8.3 | 8.3.37 |
| 8.2 | 8.2.36 |

2026년 10월 21일 릴리스(8.2.35, 8.3.36, 8.4.27, 8.5.12)로는 부족합니다. PHP 8.2는 2026년 12월 31일에 지원이 끝납니다.

사이트를 서비스하는 바이너리를 확인한 뒤 그 서비스를 재시작합니다.

```
php -v
php-fpm -v
```

서비스 이름은 호스트마다 다릅니다. 예는 `systemctl restart php8.3-fpm`, `systemctl restart php-fpm`, `systemctl restart apache2`, `systemctl restart httpd`입니다. 마스터와 워커를 재시작해서 이전 프로세스를 종료합니다.

## 요청보다 먼저 적용할 설정

`index.php`는 PHP가 요청을 받은 뒤에 `set_memory_limit()`, `set_post_max_size()`, `set_upload_max_filesize()`를 호출합니다. `storage/config/system.ini.php`에 배포된 값은 다음과 같습니다.

```
memory_limit=0
upload_max_filesize=0
post_max_size=0
```

`0`은 `-1`보다 크므로 이 호출은 `ini_set()`에 `0`을 넘깁니다. PHP는 세션 업로드 진행 표시와, 요청을 파싱하기 전에 있어야 하는 메모리 한도에 대해 `ini_set()`과 `.user.ini`가 늦다고 적습니다.

업로드 진행 표시는 `php.ini`에 두거나 FPM 풀에서 잠근 뒤 모든 워커를 재시작합니다.

```
session.upload_progress.enabled = 0
```

FPM 풀:

```
php_admin_value[session.upload_progress.enabled] = 0
php_admin_value[memory_limit] = -1
```

`php_admin_value`는 `index.php`가 그 값을 바꾸지 못하게 합니다. `memory_limit=-1`은 PHP가 제어하는 메모리 한도를 없앱니다. PHP의 10월 안내는 할당 실패가 같은 경로에 여전히 닿을 수 있다고 하므로, 버전 업그레이드는 그대로 필요합니다. 그 자리에 `RLIMIT_AS`나 `RLIMIT_DATA`를 쓰지 않습니다. 해당 워커만 멈추는 cgroup 제한은 가용성 경계입니다.

8.5.13 미만의 PHP 8.5에서는 같은 초기 설정에 `fatal_error_backtraces=0`을 둡니다.

같은 `php.ini`를 읽는 CLI로 값을 확인합니다. `ini_set()`을 호출하는 스크립트가 찍는 값은 그 이후의 값입니다.

```
php -i
```

`session.upload_progress.enabled`가 `0`인지 봅니다.

## 요청 크기

본문 제한은 웹 서버에 둡니다. 프레임워크의 `ini_set()`은 늦고, 배포된 크기는 `0`입니다.

업로드를 받지 않는 배포는 `post_max_size=8M`을 씁니다. 업로드를 받는 배포는 `post_max_size=32M`으로 두고 `upload_max_filesize`를 그보다 작게 둡니다. ReasonableFramework에는 업로드와 결제 라우트가 있으므로, 그 서버가 실제로 받는 요청에 맞춰 한도를 정합니다.

nginx:

```
client_max_body_size 8m;
fastcgi_keep_conn off;
```

업로드를 받는 배포는 `32m`을 씁니다. Apache:

```
LimitRequestBody 8388608
```

업로드를 받는 배포는 `33554432`를 씁니다. `mod_proxy_fcgi`에서는 `enablereuse`를 끄고 둡니다.

`fastcgi_keep_conn`과 `enablereuse`는 같은 11월 19일 릴리스에서 고쳐진 [CVE-2026-129289](https://github.com/php/php-src/security/advisories/GHSA-qc49-hpvq-g2w6)에 해당합니다. 요청마다 FastCGI 연결을 닫는 프런트엔드는 PHP가 시연한 경로 밖에 있습니다. 이 저장소의 `read_requests()`는 정상 경로에서 `php://input`을 끝까지 읽습니다. PHP는 그 읽기가 업그레이드나 연결 종료를 대신하지 못한다고 적습니다.

## 어떤 SAPI인가

웹 요청을 받는 PHP-FPM, Apache `mod_php`, `php-cgi`, FrankenPHP, LiteSpeed가 범위에 들어갑니다. PHP는 10월 결함에 대해 `mod_php`의 위험이 가장 크다고 봅니다. 요청이 `index.php`보다 먼저 엔진에 닿을 수 있기 때문입니다. SAPI를 고를 수 있으면 패치된 PHP-FPM을 씁니다.

## PHP 5.3과 7.x

이 저장소는 PHP 5.3.3과 PHP 7.x에서 테스트됩니다. 그 줄에는 이 결함의 PHP 프로젝트 패키지가 없습니다.

PHP 프로젝트는 10월 두 건에 대해서만 최소 소스 백포트를 공개했습니다.

- [PHP 7.4](https://github.com/php/php-src/tree/PHP-7.4-CVE-2026-109618-109619)
- [PHP 8.0](https://github.com/php/php-src/tree/PHP-8.0-CVE-2026-109618-109619)
- [PHP 8.1](https://github.com/php/php-src/tree/PHP-8.1-CVE-2026-109618-109619)

그 브랜치에는 11월 19일 세션 수정이 없습니다. PHP 5.3이나 7.x에 남는 호스트는 고친 CVE를 명시한 벤더 빌드를 쓰거나, 위 표의 버전으로 옮깁니다. `session.upload_progress.enabled=0`은 그 호스트의 `php.ini`에도 둡니다.

## 업그레이드 전에 서버가 도달 가능했던 경우

CVE-2026-109618과 CVE-2026-109619은 CISA Known Exploited Vulnerabilities 목록에 있습니다. 업그레이드 뒤에, 이 배포가 만들지 않은 PHP 파일과 `php-fpm` 또는 `httpd`의 자식 프로세스를 확인합니다.

## 이 저장소가 바꾸지 않는 것

`storage/config/security.ini.php`의 `sandboxdisabled=1`인 동안 세션은 PHP 기본 파일 핸들러를 씁니다. CVE-2026-126714가 해당하는 핸들러입니다. PHP 릴리스가 세션 파일을 다시 쓰기 전에 비웁니다. 이 저장소는 그 핸들러를 바꾸지 않습니다.
