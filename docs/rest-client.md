# REST client (webpagetool)

[한국어](rest-client.ko.md)

`helper/webpagetool.php` is the HTTP client. Load it before the calls below.

```php
loadHelper("webpagetool");
```

The first public note is from September 2019. It was moved in November 2024. The samples here follow the functions in this repository.

`get_web_json($url, $method, $data)` returns a decoded JSON object. The second argument has to be a string. `get_web_page()` returns the response array, including `content`, `size`, and `status`. `get_web_dom()`, `get_web_meta()`, and `get_web_xml()` parse HTML, meta tags, and XML.

## Methods

- `get` appends `data` to the query string.
- `post` sends an `application/x-www-form-urlencoded` body. A value that starts with `@` is uploaded as a file.
- `jsondata` sends a JSON body with POST. `jsonrpc` and `jsonrpc2` do the same and set `jsonrpc` to `2.0`. `jsonrpc.compatible` sets it to `1.1`.
- `rawdata` sends the body as bytes.
- A dotted suffix selects the transport. `.cache` stores the response when `cache_enabled` is on in `storage/config/system.ini.php`. `cache_max_age` is the lifetime in seconds. `.cmd` uses the `curl` program. The other suffixes are `async`, `fgc`, `sock`, `wget`, `aria`, and `httpie`.

Basic authentication uses the header key `Authentication` or `Authorization` with `array("Basic", $user, $secret)`. The helper passes that pair to cURL as the user and password. A string value is sent as a normal header, which is how a Bearer token is passed.

## Examples

### 1. URL-encoded POST with Basic authentication

Twilio. `twilio_sid`, `twilio_token`, and `twilio_from` live in `storage/config/3rdparty.ini.php`. `helper/twilio.api.php` calls this with the method `post.cmd`.

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

### 2. URL-encoded POST for Mailgun

Pass `post` as the method. `helper/mailgun.api.php` calls `get_web_json()` without a method string, and `get_web_page()` then stops with `method must be string`.

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

`jsondata` posts the array as JSON. `helper/zabbix.api.php` uses `jsonrpc2.cache`, which adds `"jsonrpc": "2.0"` and caches the response.

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

### 4. OAuth 2.0 access token

This is the authorization-code grant used by `helper/wprest.php`. `jsondata` sends the fields as a JSON body.

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

### 5. Bearer token

`get_web_build_qs()` appends the query string. `helper/wprest.php` uses it for `/wp/v2/posts`.

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

### 6. Cached GET

`get.cache` reads the cache and, on a miss, performs a GET. `get_web_page()` returns the response array, so the body is in `content`.

```php
loadHelper("webpagetool");
$remote_host = "http://" . ($protocol == "ipv6" ? "ipv6." : "") . "icanhazip.com";
$response = get_web_page($remote_host, "get.cache");
$addr = get_value_in_array("content", $response, "");
```

## See also

- [Important updates](important-updates.md)
- [Integration](integration.md)

## Source

Internet Archive snapshot [20250812123738](https://web.archive.org/web/20250812123738/https://catswords-oss.rdbl.io/7839294550/2212849588).
