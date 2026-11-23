# 사용 사례

[English](use-cases.md)

## 자빅스 심각도를 Grafana로 보내기

v1.6.2 기준입니다. 이전 기록은 [gist](https://gist.github.com/gnh1201/792964e9719d2f62157cf46e394888f5)에 있습니다.

1. [ReasonableFramework](https://github.com/gnh1201/reasonableframework)를 설치합니다.

2. `storage/config/3rdparty.ini.php`의 `[3rdparty]` 절에 자빅스 계정을 적습니다. 그 파일의 다른 키는 그대로 둡니다. `zabbix_host`는 호스트 이름만 적습니다. `helper/zabbix.api.php`가 이 값으로 `http://<host>/zabbix/api_jsonrpc.php`를 만듭니다.

```
zabbix_host=127.0.0.1
zabbix_username=Admin
zabbix_password=your-password
zabbix_protocol=http
```

3. `route/zabbix.status.json.php`를 추가합니다. 호스트마다 Grafana JSON 데이터 포인트 하나를 돌려줍니다. 값은 그 호스트에서 가장 높은 트리거 우선순위입니다.

```php
<?php
loadHelper("zabbix.api");

zabbix_authenticate();

$hosts = zabbix_get_hosts();
if (!is_array($hosts)) {
    $hosts = array();
}

$data = array();
foreach ($hosts as $host) {
    $severity = 0;
    $triggers = zabbix_get_triggers($host->hostid);
    if (!is_array($triggers)) {
        $triggers = array();
    }
    foreach ($triggers as $trigger) {
        $_severity = intval($trigger->priority);
        if ($_severity > $severity) {
            $severity = $_severity;
        }
    }

    $data[] = array(
        "target" => $host->host,
        "datapoints" => array(
            array($severity, get_current_timestamp())
        )
    );
}

header("Content-Type: application/json");
echo json_encode($data);
```

4. `/zabbix.status.json` 같은 경로를 쓰려면 `storage/example/_.htaccess`를 `/.htaccess`로 복사합니다.

```
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php/$1 [L]
```

재작성 규칙을 끄면 같은 라우트는 `http://localhost/?route=zabbix.status.json`입니다.

5. Grafana에 [simpod-json-datasource](https://grafana.com/grafana/plugins/simpod-json-datasource)를 설치합니다. 이름이 `Zabbix Status API`인 JSON 데이터 소스를 추가하고, URL을 `https://localhost/zabbix.status.json`으로 둔 뒤 Save & test를 누릅니다.

6. `Zabbix Status API` 데이터 소스를 쓰는 패널을 추가합니다.

## 네이버 파파고 번역 API 호출

파파고 `n2mt`는 POST 본문을 받습니다. `post.cache`는 그 본문을 보내고, `storage/config/system.ini.php`에서 `cache_enabled`가 켜져 있으면 응답을 저장합니다. 이전 기록은 [gist](https://gist.github.com/gnh1201/081484e6f5e10bd3be819093ba5f49c8)에 있습니다.

1. [네이버 개발자 센터](https://developers.naver.com/)에서 클라이언트 ID와 Secret을 만듭니다.

2. ReasonableFramework를 설치합니다.

3. `helper/papago.api.php`를 추가하고, 두 헤더 값에 클라이언트 ID와 Secret을 넣습니다.

```php
<?php
if (!is_fn("get_translated_text_by_papago")) {
    function get_translated_text_by_papago($text, $source = "ko", $target = "en") {
        loadHelper("webpagetool");
        $response = get_web_json("https://openapi.naver.com/v1/papago/n2mt", "post.cache", array(
            "headers" => array(
                "Content-Type" => "application/x-www-form-urlencoded; charset=UTF-8",
                "X-Naver-Client-Id" => "<your client ID>",
                "X-Naver-Client-Secret" => "<your client secret>",
            ),
            "data" => array(
                "source" => $source,
                "target" => $target,
                "text" => $text,
            ),
        ));

        return $response->message->result->translatedText;
    }
}
```

4. `route/papago.php`를 추가합니다. `renderView()`가 `$data`를 뷰 변수로 풀어서 넣습니다.

```php
<?php
loadHelper("papago.api");
$text = get_requested_value("text");
$translated_text = get_translated_text_by_papago($text);
renderView("view_papago", array(
    "translated_text" => $translated_text,
));
```

5. `view/view_papago.php`를 추가합니다.

```php
<?php
echo $translated_text;
```

6. `http://localhost/?route=papago&text=사랑해`를 엽니다. `localhost`는 실제로 서비스하는 호스트로 바꿉니다.

## 결제 대행사 연동

KCP 결제 단계는 [`route/orderpay.pgkcp.php`](../route/orderpay.pgkcp.php)에 있습니다.

## 같이 보기

- [연동](integration.ko.md)
- [지원 종료](eol.ko.md)

## 출처

인터넷 아카이브 스냅샷 [20250812115021](https://web.archive.org/web/20250812115021/https://catswords-oss.rdbl.io/7839294550/2189219652).
