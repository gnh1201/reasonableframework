# REST 클라이언트 (webpagetool)

[English](rest-client.md)

`helper/webpagetool.php`가 HTTP 클라이언트입니다. 아래 호출 전에 불러옵니다.

```php
loadHelper("webpagetool");
```

처음 공개된 노트는 2019년 9월입니다. 2024년 11월에 옮겨 적었습니다. 여기 예제는 이 저장소의 함수를 따릅니다.

`get_web_json($url, $method, $data)`는 디코드된 JSON 객체를 돌려줍니다. 두 번째 인자는 문자열이어야 합니다. `get_web_page()`는 `content`, `size`, `status`가 들어 있는 응답 배열을 돌려줍니다. `get_web_dom()`, `get_web_meta()`, `get_web_xml()`은 HTML, 메타 태그, XML을 파싱합니다.

## 메서드

- `get`은 `data`를 쿼리 문자열에 붙입니다.
- `post`는 `application/x-www-form-urlencoded` 본문을 보냅니다. `@`로 시작하는 값은 파일로 업로드합니다.
- `jsondata`는 JSON 본문을 POST로 보냅니다. `jsonrpc`와 `jsonrpc2`도 같고, `jsonrpc` 값을 `2.0`으로 넣습니다. `jsonrpc.compatible`는 `1.1`로 넣습니다.
- `rawdata`는 본문을 바이트로 보냅니다.
- 점 뒤의 접미사는 전송 방식을 고릅니다. `.cache`는 `storage/config/system.ini.php`에서 `cache_enabled`가 켜져 있으면 응답을 저장합니다. `cache_max_age`는 초 단위 수명입니다. 캐시가 빗나가면 그 수명보다 오래된 파일을 지웁니다. `cache_max_files`는 캐시 파일 개수 상한입니다. 기본값은 256이고, `0`이면 상한이 없습니다. `.cmd`는 `curl` 프로그램을 씁니다. 그 밖에는 `async`, `fgc`, `sock`, `wget`, `aria`, `httpie`가 있습니다.

Basic 인증은 헤더 키 `Authentication` 또는 `Authorization`에 `array("Basic", $user, $secret)`을 넣습니다. 헬퍼는 그 쌍을 cURL 사용자와 비밀번호로 넘깁니다. 문자열 값은 일반 헤더로 나가며, Bearer 토큰을 이렇게 보냅니다.

## 예제

### 1. Basic 인증이 있는 URL 인코드 POST

Twilio입니다. `twilio_sid`, `twilio_token`, `twilio_from`은 `storage/config/3rdparty.ini.php`에 있습니다. `helper/twilio.api.php`는 메서드 `post.cmd`로 이 호출을 합니다.

```php
$request_url = sprintf("https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json", $sid);

$response = get_web_json($request_url, "post", array(
    "headers" => array(
        "Content-Type" => "application/x-www-form-urlencoded",
        "Authentication" => array("Basic", $cnf['sid'], $cnf['token']),
    ),
    "data" => array(
        "Body" => $message,
        "From" => $cnf['from'],
        "To" => $to,
    ),
));
```

### 2. Mailgun용 URL 인코드 POST

메서드는 `post`로 넘깁니다. `helper/mailgun.api.php`는 메서드 문자열 없이 `get_web_json()`을 호출하고, `get_web_page()`는 `method must be string`에서 멈춥니다.

```php
$response = get_web_json(sprintf("https://api.mailgun.net/v3/%s/messages", $domain), "post", array(
    "headers" => array(
        "Content-Type" => "application/x-www-form-urlencoded",
        "Authentication" => array("Basic", "api", $cnf['apikey']),
    ),
    "data" => array(
        "from" => sprintf("%s <%s>", $cnf['name'], $cnf['from']),
        "to" => $to,
        "subject" => $subject,
        "text" => $content,
    ),
));
```

### 3. JSON-RPC

`jsondata`는 배열을 JSON으로 POST합니다. `helper/zabbix.api.php`는 `jsonrpc2.cache`를 쓰며, 이 메서드는 `"jsonrpc": "2.0"`을 넣고 응답을 캐시합니다.

```php
$response = get_web_json($zabbix_api_url, "jsondata", array(
    "data" => array(
        "jsonrpc" => "2.0",
        "method" => "host.get",
        "params" => array(
            "output" => array("hostid", "host"),
            "selectInterfaces" => array("interfaceid", "ip"),
        ),
        "id" => zabbix_get_id(),
        "auth" => $zabbix_auth,
    ),
));
```

### 4. OAuth 2.0 액세스 토큰

`helper/wprest.php`가 쓰는 인가 코드 허가입니다. `jsondata`는 필드를 JSON 본문으로 보냅니다.

```php
$response = get_web_json($wp_server_url . "/oauth/token/", "jsondata", array(
    "headers" => array(
        "Authorization" => sprintf("Basic %s", base64_encode($client_id . ":" . $client_secret)),
    ),
    "data" => array(
        "grant_type" => "authorization_code",
        "code" => $code,
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "redirect_uri" => get_route_link($route),
        "state" => $state,
    ),
));
```

### 5. Bearer 토큰

`get_web_build_qs()`가 쿼리 문자열을 붙입니다. `helper/wprest.php`가 `/wp/v2/posts`에 이 함수를 씁니다.

```php
$response = get_web_json(get_web_build_qs($wp_server_url, array(
    "rest_route" => "/wp/v2/posts"
)), "jsondata", array(
    "headers" => array(
        "Authorization" => "Bearer " . $access_token,
    ),
    "data" => $default_data,
));
```

### 6. 캐시하는 GET

`get.cache`는 캐시를 읽고, 없으면 GET을 합니다. `get_web_page()`는 응답 배열을 돌려주므로 본문은 `content`에 있습니다.

```php
loadHelper("webpagetool");
$remote_host = "http://" . ($protocol == "ipv6" ? "ipv6." : "") . "icanhazip.com";
$response = get_web_page($remote_host, "get.cache");
$addr = get_value_in_array("content", $response, "");
```

## 같이 보기

- [중요 업데이트](important-updates.ko.md)
- [연동](integration.ko.md)

## 출처

인터넷 아카이브 스냅샷 [20250812123738](https://web.archive.org/web/20250812123738/https://catswords-oss.rdbl.io/7839294550/2212849588).
