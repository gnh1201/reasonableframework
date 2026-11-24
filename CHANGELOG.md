# Changelog

All notable changes to ReasonableFramework are documented in this file.

The version numbers match the Git tags. A heading drops a leading `v`. Tags that were published without that prefix keep their original spelling. The latest tagged release is v1.6.9-hotfix1.

Each section is the difference from the previous tag in this list. v1.4 does not descend from v1.3, so that section is the difference between the two trees.

## [Unreleased]

## [1.6.9-hotfix1] - 2026-11-24

First hotfix of v1.6.9.

### Security

- Work around [CVE-2026-128064](https://github.com/php/php-src/security/advisories/GHSA-f6gg-rchx-xw8j). PHP's HTTP and FTP stream wrappers accept a scheme without regard to case, but they turn TLS on only when that scheme contains a lowercase `s`. An `HTTPS://` or `FTPS://` URL, including one received in a redirect `Location`, was therefore sent in plaintext.
- Add `file_get_contents_tls_scheme()`. It rewrites the `http`, `https`, `ftp`, and `ftps` schemes to lowercase before calling `file_get_contents()`, and it follows redirects itself so PHP does not apply the same check to a later `Location`.
- `get_web_fgc()`, `file_get_html()`, and `simple_html_dom::load_file()` now read remote URLs through that function.
- Replace curly-brace string offsets in `get_xor_text()` with square brackets so `system/security.php` loads on PHP 8. The same code still runs on PHP 5.2 and later. PHP versions before 5.3.4 ignore the `follow_location` context option; `max_redirects` set to `0` still stops the wrapper from following redirects there.
- Limit `route/webproxy.php` to `http` and `https` URLs whose host is listed in `webproxy_hosts`. The default host is `code.jquery.com`. The route refuses an IP address, a name that resolves to a private or reserved address, a non-default port, a redirect that leaves the list, and a redirect from `https` to `http`. It rebuilds the request from the scheme, host, path, and query, ignores an environment HTTP proxy, chooses the response content type from the final file extension, and checks the TLS certificate for that request.
- Keep route, view, helper, module, and vendor includes inside their own directories. `get_safe_path()` rejects a parent segment, a leading slash, a drive prefix, and a null byte. Storage reads and writes, uploaded names, config includes, vendor autoload, `route/webapp.php`, and zip extraction stay under the directory they were given. Names such as `errors/404` and `api.mailgun` still match.

### Changed

- State in the README that, as of 2025, development has stopped and maintenance is limited to security patches for existing users.
- Point vulnerability reports to oss@catswords.re.kr and [GitHub Security Advisories](https://github.com/gnh1201/reasonableframework/security).
- Point the community link to [Catswords OSS on Discord](https://discord.gg/FGMb9VnVFC).
- Publish the catswords-oss.rdbl.io notes in English and Korean under `docs/`. The text comes from the Internet Archive snapshots of 2025-08-12, and the samples now follow the functions in this repository. `README.md` and `SECURITY.md` link the English pages.
- Use oss@catswords.re.kr for contact addresses, including `SECURITY.md`, the payment notice, and the default User-Agent.
- Use gnh1201@catswords.re.kr as the author email for Go Namhyeon in file headers and `composer.json`.
- Document the PHP runtime upgrade for the engine defects announced on 21 October 2026 and 19 November 2026. The short list is at the top of `README.md`. The steps are in `docs/php-runtime.md` and `docs/php-runtime.ko.md`.
- Explain [CVE-2026-128064](https://github.com/php/php-src/security/advisories/GHSA-f6gg-rchx-xw8j) at the top of `README.md`. The detail is in `docs/cve-2026-128064.md` and `docs/cve-2026-128064.ko.md`.

### Fixed

- Keep an empty requested string in `get_requested_value()`. `get_final_link()` builds `q=` for that value and for boolean `false`. A requested `0` stays `0`.
- Delete aged files from `remove_volatile_files()` one at a time while the directory is read. A large cache no longer builds a list of every name.
- Limit webpage cache files in `get_web_cache()`. Files older than `cache_max_age` are removed, and a new file is stored only while fewer than `cache_max_files` files remain. The default is 256. Set `cache_max_files` to `0` for no file limit.

## [1.6.9] - 2024-11-11

Documentation release. No runtime code changed between v1.6.8 and v1.6.9.

### Added

- Add `CITATION.cff` and a Zenodo DOI badge to the README. The citation record is [10.5281/zenodo.11392417](https://doi.org/10.5281/zenodo.11392417).
- Move the funding file to `.github/FUNDING.yml`, with the GitHub sponsor `gnh1201` and `https://gnh1201.link`.
- Document the EOL page for older branches in `SECURITY.md`: [EOL (catswords-oss.rdbl.io)](https://catswords-oss.rdbl.io/7839294550/3090512080).
- Add the Catswords Microsoft Teams community to the security contact list.

### Changed

- Move the use-case notes to catswords-oss.rdbl.io. The README now links the REST client, the Zabbix-to-Grafana notes, the Papago REST example, the KCP payment route, RFC 3164 remote logging, and static-link rewriting.
- Host the structure-map image at `https://ics.catswords.net/reasonableframework.jpg`.
- Mark 1.5.x and every release before 1.5 as legacy in `SECURITY.md`. 1.5.x reached EOL on 2020-08-25. The supported line remains 1.6.x.
- Update the README contact block with GitHub Security Advisories, ActivityPub, XMPP, and Microsoft Teams.

### Removed

- Remove the root `FUNDING.yml` after the move into `.github/`.
- Remove `KOREAN.MD`.
- Remove the images previously stored under `assets/img/`.

## [1.6.8] - 2024-05-30

Documentation and bootstrap cleanup. No PHP functions were added or removed.

### Changed

- Change `bootstrap.sh` so it sets permissions with `chmod -R u+rwX,go+rX,go-w` and still copies `storage/example/_.htaccess` to `.htaccess`.
- Update `KOREAN.MD`.

### Removed

- Remove `bootstrap.707.sh`, `fixindent.sh`, and the LGPL badge image `lgplv3-147x51.png`.

## [1.6.7] - 2024-04-29

### Changed

- Rewrite the README specification block. It describes RVHM, shared variables, CGI-style use beside object-oriented code, and the database drivers (PDO, MySQLi, legacy MySQL, MySQL over the shell, Oracle OCI). The hosting table heading becomes "Tested on free web hostings".
- Record the public name as ReasonableFramework, also known as RSF, VSPF, and copyright registration C-2020-018490.
- Add an ActivityPub contact, `@catswords_oss@catswords.social`, to the README and to `SECURITY.md`.
- Fill `FUNDING.yml` with GitHub sponsor `gnh1201`, Open Collective `welsonjs`, Liberapay `catswords`, Ko-fi `catswords`, and the custom links `https://www.buymeacoffee.com/catswords` and `https://toss.me/catswords`.
- Stop calling `get_network_event()` from `read_route()`.

### Removed

- Remove the "Paid security services" section from `SECURITY.md`.
- Remove the KCC badge and the LGPL badge image from the top of the README.

## [1.6.7-preview1] - 2023-04-14

Seventy-seven files changed, mostly file headers. The author contact on those headers is `abuse@catswords.net`.

### Added

- Add `detect_perf_agent()`. It matches the user agent against Pingdom Page Speed and GTmetrix.
- Treat a `.crypto` suffix on `X-Forwarded-Host` as a hidden-service signal, next to `.onion` and `.i2p`.

### Changed

- Update `KOREAN.MD` and the README.

### Removed

- Remove `VERSION.MD` and `CDN_HOWTO.md`.

## [1.6.6] - 2022-01-17

### Added

- Add `helper/coupang.api.php` with `coupang_get_signature()` and `coupang_search_items()`.
- Add `helper/hiddentool.php`. `detect_hidden_service()` scores `X-Forwarded-Host` when the host ends in `onion` or `i2p`.
- Add `hmacsha256_sign_message()` (`hash_hmac` with SHA-256), `get_header_value()`, and `test_user_agent()`.
- Add `storage/config/l10n.ini.php`. Localization is off by default, and the default language is `ko`.
- Document remote logging over RFC 3164, a free-hosting test table, and the KCC registration badge `C-2020-018490` in the README.

### Changed

- Rename `storage/config/log.ini.php` to `storage/config/logger.ini.php`.
- Update the logger, storage, URI, database, security configuration, `exectool.php`, and `webpagetool.php`.
- Expand the Korean introduction in the README.

## [1.6.5.2-kcc] - 2020-06-03

README only.

### Added

- Record the Korea Copyright Commission software registration `C-2020-018490` for v1.6.5.1-kcc, approved on 2020-06-03. The README states that the open source license stays as it is.

## [1.6.5.1-kcc] - 2020-05-29

### Added

- Add `get_csv_from_array()` in `helper/string.utils.php`.
- Publish `SECURITY.md` with a supported-version table: 1.6.x supported, 1.5.x supported until 2020-08-25, and releases before 1.5 unsupported.
- Describe a paid sponsor tier for extra security work, and link the README to `SECURITY.md`.

### Changed

- Update `helper/webpagetool.php`.

## [1.6.5] - 2020-05-21

### Added

- Parse a YAML request body into `$requests['_YAML']` when `read_requests()` is called with `yaml` set and `yaml_parse()` exists.

### Changed

- Update the README.
- Apply `addslashes()` and `htmlspecialchars()` to the whole string value in `get_requested_value()`, after the earlier per-branch nesting.

### Removed

- Remove `get_requested_jsondata()`. Its body called itself.

## [1.6.4] - 2020-05-04

### Added

- Add `allocate_uploaded_files()` for stored uploads.
- Add `get_db_timediff()`.

### Changed

- Update the logger, the Zabbix helper, and storage.

### Removed

- Remove `get_timediff_on_query()`.
- Remove `move_uploaded_file_to_stroage()`.

## [1.6.3] - 2020-04-20

### Added

- Add `helper/SSL.class.php` with `getCertificateDetails()`, `getSSLinfo()`, `dateFormat()`, `dateFormatMonth()`, and `diffDate()`.
- Add `instantiate()` on the UUID class.

### Changed

- Update `CDN_HOWTO.md`, the README, `cli.php`, `helper/webpagetool.php`, `helper/zabbix.api.php`, and `system/database.php`.

## [1.6.2] - 2020-04-12

### Added

- Add `CDN_HOWTO.md`.
- Add `base_route` to `storage/config/uri.ini.php`. `read_route()` takes no argument and takes the first path segment after `base_route`, skipping `index.php`. An empty match still falls through to the `welcome` route.

### Changed

- Rename the URI config keys to `base_url`, `base_cdn_url`, and `base_vod_url` with spaces around `=`.

## [1.6.1] - 2020-04-12

### Added

- Add `storage/config/deprecated.ini.php`. The first deprecated list is `check_invalid_function`, `check_function_exists`, `array_multikey_empty`, `split_by_line`, `startsWith`, and `endsWith`. `is_deprecated_fn()` reads that list.
- Add `helper/migrate.1.5.php`.
- Add `get_cdn_link()` and `get_vod_link()`, which prefix a path with `base_cdn_url` and `base_vod_url`.
- Add `eregi()` and `eregi_replace()` as the public names of the older compatibility helpers.
- Add `is_prefix()`, `is_suffix()`, and `strlike()`.
- Bring back `get_scope()`, `set_scope()`, and `check_function_exists()`.
- Add `array_key_unset()`, `get_key_in_array()`, `get_seconds()`, `explode_by_line()`, `explode_storage_file_by_line()`, `get_bind_to_sql_past()`, `get_bind_to_sql_last()`, `get_cpu_idle2()`, `set_cpu_usage_limit()`, `set_min_cpu_idle()`, and `zabbix_get_hostgroups()`.

### Changed

- Update the welcome view, URI config, performance helper, Colona format, string helpers, and the webpage tool.

### Removed

- Remove `startsWith()`, `endsWith()`, `eregi_compatible()`, `eregi_replace_compatible()`, `get_bind_to_sql_past_minutes()`, `multi_str_split()`, and `read_storage_file_by_line()`.

## [1.6-pre20200220-1] - 2020-02-20

### Added

- Add `helper/perftool.php` with `get_cpu_idle()`, `set_max_execution_time()`, `set_memory_limit()`, `set_post_max_size()`, and `set_upload_max_filesize()`.

### Fixed

- Correct typos in `system/database.php` and `helper/colona.v1.format.php`.

## [1.6-pre20200217-2] - 2020-02-17

### Added

- Add bulk database writes: `exec_db_bulk_start()`, `exec_db_bulk_push()`, and `exec_db_bulk_end()`.

### Changed

- Update `composer.json`.

## [1.6-pre20200217-1] - 2020-02-17

### Added

- Add `is_fn()` and `is_not_fn()`, and switch the helper guards over to them.
- Add webpage fetchers `get_web_aria()` (aria2c), `get_web_httpie()` (the `http` command), and `get_web_async()` (returns the process id from `get_web_cmd()`).
- Add `get_bind_to_sql_fields()`.
- Add the LGPL badge image `lgplv3-147x51.png`.

### Changed

- Update the README and `storage/config/system.ini.php`.

### Removed

- Remove `check_function_exists()` and `check_invalid_function()` from this tag. v1.6.1 brings `check_function_exists()` back.

## [1.6-pre20200204-1] - 2020-02-04

### Added

- Replace the scope helpers with shared variables: `get_shared_var()` and `set_shared_var()` store values in `$shared_vars`.
- Add `exec_db_temp_clear()`, `get_uncompressed_text()`, and `json_decode_assoc()`.
- Add Zabbix helpers `zabbix_get_alerts()`, `zabbix_get_hosts()`, and `zabbix_get_records()`.

### Changed

- Replace `do_error()` with a `trigger_error($msg, $code)` wrapper that records the message and prints collected errors. The usual `check_function_exists("trigger_error")` guard sees PHP's built-in `trigger_error()`, so this tag does not define that wrapper.
- Update the README, CLI, security layer, MySQL and Oracle drivers, the obfuscator, and JSON helpers.

### Removed

- Remove `get_scope()`, `set_scope()`, `do_error()`, and `json_decode_to_assoc()`.

## [1.6-pre20200125-1] - 2020-01-25

### Added

- Add `helper/rfc3164.proto.php` with `rfc3164_get_config()` and `rfc3164_send_message()`.
- Add `storage/config/log.ini.php`.
- Add `append_log_to_file()` and `get_int()`.

### Changed

- Update the logger, URI router, KCP order routes and views, string helpers, `composer.json`, `index.php`, and the README.

### Removed

- Remove `write_log_to_file()`.

## [1.6-pre200117-1] - 2020-01-16

### Changed

- Rename insert options `setduplicate` and `setignore` to `setkeys` and `setignores` in the SQL insert builder.
- Read the KCP gateway settings in `helper/pgkcp.lnk.php` as object properties from `json_decode()`.
- Send array POST bodies from `get_web_curl()` through `http_build_query()`. A string body is sent as given.
- Decode JSON in `json_decode_ex()` with the associative flag taken from the `assoc` option before the call.

## [1.6-pre200109-2] - 2020-01-09

### Added

- Add `exec_db_table_insert()` and `get_db_zero()`.

### Changed

- Update `storage/config/database.ini.php`.

## [1.6-pre200109-1] - 2020-01-09

### Fixed

- Load `string.utils` from `route/orderpay.step2.pgkcp.php`. The previous name was `string.urils`.

## [1.6-pre200108-1] - 2020-01-08

`system/database.php` only.

### Added

- Add `check_table_is_separated()`, `get_db_tablenames()`, `exec_db_table_drop()`, and `exec_db_table_update()`.

## [1.6-pre200102-1] - 2020-01-02

### Added

- Add `db_separated_tables` to `storage/config/database.ini.php`. When the requested table name is in that comma-separated list, a SELECT is built as a `UNION` of the names stored in `` `<table>.tables` ``.

### Changed

- Quote table and column names with backticks in the SELECT, DELETE, and CREATE builders.
- Look up an existing table in `information_schema.tables` with `TABLE_SCHEMA` and `TABLE_NAME`.
- Create unique indexes from a `setunique` option after `exec_db_table_create()`.
- Create a temporary table with `` create temporary table if not exists `<name>` <statement> ``.
- Default the third fragment of a WHERE builder to `1`.

## [1.6-pre191230-1] - 2019-12-30

Includes the auto-partitioning work from pull request 68.

### Added

- Add `get_bind_to_sql_create()` and `exec_db_table_create()`.
- Add `get_old_version()`. The 2019-11-17 pre-release already calls it from the database layer and stores `old_version=0` in `storage/config/system.ini.php`.
- Add `write_debug_log()` and `write_log_to_file()`.

### Changed

- Update the KCP order widget, `helper/json.format.php`, and `system/config.php`.

## [1.6-pre191117-1] - 2019-11-17

### Added

- Add `old_version=0` to `storage/config/system.ini.php`.

### Changed

- When `get_old_version()` is `1.4`, a fetch-all response includes `length` as well as `cnt` and `data`. When it is `1.5`, the WHERE and UPDATE builders use the 1.5 compatibility path.
- Pass the storage type through `remove_volatile_files()`.

### Fixed

- Correct the opening tag of `storage/config/lguplus.ini.php` from `<?php1` to `<?php`.

## [1.6-pre191115-2] - 2019-11-15

### Added

- Add `remove_volatile_files()`.

### Changed

- Update the README, `index.php`, and `storage/config/system.ini.php`.

## [1.6-pre191115-1] - 2019-11-15

### Added

- Add `remove_storage_files()`.

### Changed

- Update `index.php` and `system/storage.php`.

## [1.6-pre191114-2] - 2019-11-14

### Fixed

- Give `get_current_timestamp()` its opening brace.
- Measure storage cache age from `filemtime()`, and cast `max_age` to an integer. A `max_age` of `0` still means the file does not expire.
- Keep the value returned by an included config file in `read_config()`.

### Changed

- Keep mode `fake` as the storage write that does not store a body. Mode `w` writes the file.
- Log webpage cache hits and misses from `get_web_cache()`.

## [1.6-pre191114-1] - 2019-11-14

### Added

- Add `zabbix_get_problems()` and `zabbix_get_triggers()`.

### Changed

- Update `helper/webpagetool.php`, `system/database.php`, `system/storage.php`, and `storage/config/system.ini.php`.

## [1.6-pre191108-1] - 2019-10-25

The tag object is dated 2019-10-08. The tagged commit is 2019-10-25.

### Added

- Add `helper/thumbnail.youtube.php` with `get_youtube_thumbnail()`.
- Add `exec_db_temp_create()`, `get_current_timestamp()`, and `get_web_binded_url()`.

### Changed

- Update the KCP order and completion routes, Payman script, security configuration, JSON helper, Twilio helper, string helpers, and the webpage tool.

## [1.6-pre191013-1] - 2019-10-13

### Added

- Add `helper/json.format.php` with `json_decode_ex()` and `json_encode_ex()`.
- Add `helper/compress.zip.php` with `unzip()`.
- Add `helper/pgkcp.install.php` (`install_pgkcp()`, `get_pgkcp_dir()`) and `helper/lguplus.install.php` (`lguplus_install()`).
- Add `expire_isolated_session()`.
- Add `storage/sandbox/logs/index.php`.

### Changed

- Update KCP order form view, webpage tool, database, config, URI, storage, and security.

### Removed

- Remove `end_isolated_session()`.

## [1.6-pre191011-3] - 2019-10-11

`system/storage.php` only.

### Added

- Return a public URL from `write_storage_file()` when the `url` option is true: the upload base URL plus `get_safe_path()` of the file name.

## [1.6-pre191011-2] - 2019-10-11

### Added

- Add `start_isolated_session()` and `end_isolated_session()`.
- Add `storage/sandbox/sessions/index.php`.

### Changed

- Update `index.php` and `system/security.php`.

## [1.6-pre191011-1] - 2019-10-11

### Changed

- Read the LG U+ SMS switch from the config key `enabled`. Sending proceeds when that value is `1`.
- Accept `now` and `adjust` on `get_current_datetime()`. `now` is parsed with `DateTime::createFromFormat()`, then `strtotime()` if that throws. `adjust` is applied with `strtotime()`.
- Default the bind argument of `exec_db_temp_start()` to an empty array.

## [1.6-pre191008-1] - 2019-10-08

### Added

- Add `helper/lguplus.api.php` and `storage/config/lguplus.ini.php` for LG U+ SMS (`lguplus_get_config()`, `lguplus_send_message()`).
- Add `helper/colona.v1.format.php` with `decode_colona_format()`.
- Add `get_web_cspt()`, which downloads a page and runs `catsplit_decode()` on the body.
- Add MySQL-over-shell fetch and connect helpers `exec_db_mysql_cmd_fetch_all()` and `get_db_mysql_cmd_connect()`, plus `exec_db_temp_start()` and `exec_db_temp_end()`.
- Add `get_compressed_text()`, `get_splitted_strings()`, `get_string_length()`, `remove_utf8_bom()`, and `twilio_parse_messages()`.
- Add webpage-tool diagrams `assets/img/webpagetool-1.png` and `assets/img/webpagetool-2.png`.

### Changed

- Update the Twilio route and helper, KCP payment steps, and `storage/config/3rdparty.ini.php`.

## [1.5] - 2019-07-16

### Added

- Vendor PureCSS under `vendor/_dist/purecss`.
- Add `casplit_escape()`, `catsplit_unescape()`, `do_error()`, and `get_floating_percentage()`.

### Changed

- Name the upload helper `move_uploaded_file_to_stroage()` again.
- Update CatSplit, the spreadsheet reader, string helpers, the webpage tool, security, storage, URI, the database layer, the README, and `SECURITY.md`.

### Removed

- Remove `helper/archivetool.php`, `helper/pChart2.lnk.php`, and the security-vendor links added in v1.4: `php-sec-fw.lnk.php`, `php-sec-ddos.lnk.php`, `php-sec-shell.lnk.php`, and `php-sec-scc.lnk.php`.

## [1.4] - 2019-06-05

This tag does not descend from v1.3. The notes are the difference between the two trees.

### Added

- Add `SECURITY.md` and a root `FUNDING.yml`.
- Add `helper/catsplit.format.php` with `catsplit_encode()` and `catsplit_decode()`.
- Add `helper/api.whois.kr.php` with `get_whois_kr()`.
- Add security-vendor links `helper/php-sec-fw.lnk.php`, `helper/php-sec-ddos.lnk.php`, `helper/php-sec-shell.lnk.php`, and `helper/php-sec-scc.lnk.php`.
- Add `check_is_empty()`.
- Vendor the Cascade Framework under `vendor/_dist/cascadeframework`, including jQuery 1.11.1 and 2.1.1.
- Add `fixindent.sh`.
- Rename the upload helper to `move_uploaded_file_to_storage()`.

### Changed

- Rename `helper/jcryption.lnk.php` to `helper/jCryption.lnk.php` and expose `jCryption_get()`.
- Move `vendor/PHP-MetaParser/MetaParser.class.php` to `vendor/_dist/PHP-MetaParser/MetaParser.class.php`.
- Update the KCP routes and views, web proxy, web app route, WordPress post route, logger, storage, and security configuration.

### Removed

- Remove `get_db_binded_param()`.

## [1.3] - 2019-05-13

### Added

- Add database drivers `helper/database.mysql.cmd.php` (MySQL over the shell) and `helper/database.mssql.pdo.php` (MSSQL through PDO), with `exec_db_mysql_cmd_query()`, `get_db_mssql_pdo_connect()`, `exec_db_mssql_pdo_query()`, and `exec_db_mssql_pdo_fetch_all()`.
- Add `helper/zabbix.api.php` and `route/api.zabbix.php`: `get_zabbix_config()`, `zabbix_get_api_url()`, `zabbix_authenticate()`, `zabbix_retrieve_hosts()`, `zabbix_get_items()`, and `zabbix_get_id()`.
- Add `helper/twilio.api.php` and `route/api.twilio.php`: `twilio_get_config()`, `twilio_send_message()`, and `twilio_send_voice()`.
- Add `helper/mailgun.api.php` and `route/api.mailgun.php`: `mailgun_get_config()` and `mailgun_send_message()`.
- Add `helper/webhooktool.php` (`send_web_hook()`), `helper/mobiletool.php` (`detect_mobile()`), `route/mobileswitcher.php`, and `view/view_mobileswitcher.php`.
- Add `helper/pChart2.lnk.php` and `helper/country.kr.php`, with Korean given-name and family-name lists under `storage/country/`.
- Add `get_safe_path()`, `get_xor_text()`, `get_tokenized_text()`, `append_storage_file()`, `retrieve_storage_dir()`, `get_requested_jsondata()`, `get_hash_algos()`, `check_hash_algo()`, `check_db_oracle_installed()`, and `check_invalid_function()`.
- Add `bootstrap.sh` and `bootstrap.707.sh`.
- Add `storage/config/3rdparty.ini.php` and `storage/config/social.ini.php`.
- Add an example `.htaccess` at `storage/example/_.htaccess`, and move the Oracle `tnsname.orax` example under `storage/example/`.

### Changed

- Rename `helper/string.utl.php` to `helper/string.utils.php`.
- Rename `helper/socialhub.utl.php` to `helper/socialtool.php`, and `route/api.socialhub.php` to `route/api.social.php`. The social helpers are `social_get_object()`, `social_get_object_facebook()`, `social_parse_object_id()`, and `social_send_message()`.
- Rename `route/api.newuuid.php` to `route/api.uuid.php`.
- Replace direct `function_exists()` checks with `check_function_exists()`.
- Accept more than one source in `get_requested_value()`.
- Update Oracle, MySQL, HybridAuth, spreadsheet, pagination, Gnuboard, and Zeroboard helpers.
- Tighten log file permissions.

### Removed

- Remove `helper/naturename.kr.php`, `storage/config/sns.ini.php`, and `storage/config/vworld.ini.php`.
- Remove `get_scope_all()`, `xor_this()`, `hash_algo_exists()`, and `read_route_all()`.

## [1.2] - 2019-02-24

### Added

- Add `route/webapp.php` and `webapp/webapp.md`.
- Add `route/ping.php`.
- Add `get_db_driver()`, `check_db_driver()`, `get_requested_json_value()`, `get_web_user_agent()`, `read_route()`, and `read_route_all()`.
- Describe RVH in the README: Route, View, and Helper, with Model optional. The README links a Papago REST example, the KCP payment route, and the Gnuboard route, and it documents `cli.php`.

### Changed

- Update the database layer, URI router, webpage tool, alternate database helper, `index.php`, and `cli.php`.

### Removed

- Remove `helper/allreporting.php`.

## [1.1] - 2019-02-13

First tagged snapshot. The history leading to this tag is 891 commits. This section describes the tree at that tag.

### Added

- Add the RVH core under `system/`: `base.php`, `config.php`, `database.php`, `logger.php`, `security.php`, `storage.php`, and `uri.php`. Entry points are `index.php` and `cli.php`, plus `welcome.sh` and `welcome.bat`.
- Read configuration from `storage/config/` (`database.ini.php`, `security.ini.php`, `system.ini.php`, `uri.ini.php`, `country.ini.php`, HybridAuth, SNS, and VWorld).
- Route requests through `route/`, including `welcome`, `webproxy`, `wppost`, Gnuboard, UUID, obfuscation, `api.usage`, the KCP order and payment flow, a Payman example, and a 404 page. Views live under `view/`, with the `default` and `frame100` templates.
- Ship helpers for fetching pages (`webpagetool.php`, `simple_html_dom.php`), WordPress REST, Gnuboard, Zeroboard 4, and Kimsboard, HybridAuth, MySQL (Improved and the old extension), Oracle, spreadsheets, obfuscation, encryption, jCryption, VWorld geocoding, pagination, archives, strings, network addresses, shell commands, MIME types, and UUIDs.
- Accept JSON, raw, and PHP-serialized request bodies. Clean request text for XSS. Provide `eregi_compatible()` and `eregi_replace_compatible()` for code that still calls the old `eregi` functions.
- Add `KOREAN.MD`, `VERSION.MD`, `LICENSE`, `composer.json`, and the structure-map image under `assets/img/`.
