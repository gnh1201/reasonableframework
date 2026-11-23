# Use cases

[한국어](use-cases.ko.md)

## Send Zabbix severities to Grafana

Tested against v1.6.2. An earlier write-up is the [gist](https://gist.github.com/gnh1201/792964e9719d2f62157cf46e394888f5).

1. Install [ReasonableFramework](https://github.com/gnh1201/reasonableframework).

2. Set the Zabbix account in the `[3rdparty]` section of `storage/config/3rdparty.ini.php`. Leave the other keys in that file as they are. `zabbix_host` is the host name only. `helper/zabbix.api.php` builds `http://<host>/zabbix/api_jsonrpc.php` from these values.

```
zabbix_host=127.0.0.1
zabbix_username=Admin
zabbix_password=your-password
zabbix_protocol=http
```

3. Add `route/zabbix.status.json.php`. The route returns one Grafana JSON datapoint per host. The value is the highest trigger priority on that host.

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

4. For a path such as `/zabbix.status.json`, copy `storage/example/_.htaccess` to `/.htaccess`.

```
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php/$1 [L]
```

The same route is `http://localhost/?route=zabbix.status.json` when rewrite rules are off.

5. In Grafana, install [simpod-json-datasource](https://grafana.com/grafana/plugins/simpod-json-datasource). Add a JSON data source named `Zabbix Status API`, set the URL to `https://localhost/zabbix.status.json`, and choose Save & test.

6. Add a panel that uses the `Zabbix Status API` data source.

## Call the Naver Papago translation API

Papago's `n2mt` endpoint expects a POST body. `post.cache` sends that body and stores the response when `cache_enabled` is on in `storage/config/system.ini.php`. An earlier write-up is the [gist](https://gist.github.com/gnh1201/081484e6f5e10bd3be819093ba5f49c8).

1. Create a client ID and secret at the [Naver Developers](https://developers.naver.com/) site.

2. Install ReasonableFramework.

3. Add `helper/papago.api.php` and put the client ID and secret in the two header values.

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

4. Add `route/papago.php`. `renderView()` extracts `$data` into the view.

```php
<?php
loadHelper("papago.api");
$text = get_requested_value("text");
$translated_text = get_translated_text_by_papago($text);
renderView("view_papago", array(
    "translated_text" => $translated_text,
));
```

5. Add `view/view_papago.php`.

```php
<?php
echo $translated_text;
```

6. Open `http://localhost/?route=papago&text=사랑해`. Replace `localhost` with the host you serve.

## Payment gateway

The KCP payment steps are in [`route/orderpay.pgkcp.php`](../route/orderpay.pgkcp.php).

## See also

- [Integration](integration.md)
- [End of life](eol.md)

## Source

Internet Archive snapshot [20250812115021](https://web.archive.org/web/20250812115021/https://catswords-oss.rdbl.io/7839294550/2189219652).
