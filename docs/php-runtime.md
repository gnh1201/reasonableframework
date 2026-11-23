# PHP runtime security

[한국어](php-runtime.ko.md)

The defects announced on 21 October 2026 and 19 November 2026 are in the PHP engine. ReasonableFramework has no patch for them. The work is on the server that runs PHP.

## Names

PHP2Shell is CVE-2026-109618 and CVE-2026-109619. The PHP project described them on [21 October 2026](https://www.php.net/archive/2026.php#2026-10-21-5).

- [CVE-2026-109618](https://github.com/php/php-src/security/advisories/GHSA-w8hw-w9fx-j79w) is in out-of-memory handling. Cleanup can treat half-initialized request data as valid.
- [CVE-2026-109619](https://github.com/php/php-src/security/advisories/GHSA-6wmf-4rv5-p3rp) leaves internal state pointing at memory from an earlier request.

phpAfterShell is [CVE-2026-126714](https://github.com/php/php-src/security/advisories/GHSA-fm2m-fjf5-v3cp). The default file session handler can keep part of an old session when a write stops midway. There is no CVE named PHP4Shell. That nickname has been used for this November follow-up and, earlier, for PHP2Shell.

The 19 November releases also finish the October fix:

- [CVE-2026-129481](https://github.com/php/php-src/security/advisories/GHSA-g3vh-838c-65v7) on PHP 8.3 and later. PHP describes it as an incomplete fix for CVE-2026-109619.
- [CVE-2026-129182](https://github.com/php/php-src/security/advisories/GHSA-2fjv-p558-g82m) on PHP 8.5, in fatal-error backtraces.

[CVE-2026-115560](https://github.com/php/php-src/security/advisories/GHSA-658j-r8rw-q6mx) needs `session.serialize_handler` set to `php_binary` or `php_serialize`, then switched to `php` with `ini_set()` before `session_start()`, with session upload progress still enabled. This repository does not switch that handler.

## Versions to install

| Branch | Install |
| --- | --- |
| 8.6 | 8.6.0 |
| 8.5 | 8.5.13 |
| 8.4 | 8.4.28 |
| 8.3 | 8.3.37 |
| 8.2 | 8.2.36 |

The releases of 21 October 2026 (8.2.35, 8.3.36, 8.4.27, 8.5.12) are not enough. PHP 8.2 reaches end of life on 31 December 2026.

Check the binary that serves the site, then restart that service:

```
php -v
php-fpm -v
```

The service name depends on the host. Examples are `systemctl restart php8.3-fpm`, `systemctl restart php-fpm`, `systemctl restart apache2`, and `systemctl restart httpd`. Restart the master and the workers so old processes exit.

## Settings that must apply before the request

`index.php` calls `set_memory_limit()`, `set_post_max_size()`, and `set_upload_max_filesize()` after PHP has accepted the request. `storage/config/system.ini.php` ships these values:

```
memory_limit=0
upload_max_filesize=0
post_max_size=0
```

`0` is greater than `-1`, so those calls pass `0` to `ini_set()`. PHP says `ini_set()` and `.user.ini` are too late for session upload progress, and too late for the memory-limit control that has to be in place before request parsing.

Put the upload-progress switch in `php.ini`, or lock it in the FPM pool, then restart every worker:

```
session.upload_progress.enabled = 0
```

FPM pool:

```
php_admin_value[session.upload_progress.enabled] = 0
php_admin_value[memory_limit] = -1
```

`php_admin_value` keeps `index.php` from replacing the value. `memory_limit=-1` removes the controllable PHP memory limit. PHP's October notice says an allocation failure can still reach the same path, so the version upgrade remains required. Do not use `RLIMIT_AS` or `RLIMIT_DATA` in its place. A cgroup limit that stops only the affected worker is an availability boundary.

On PHP 8.5 before 8.5.13, set `fatal_error_backtraces=0` in the same early configuration.

Confirm the value with the CLI that loads the same `php.ini`. A script that calls `ini_set()` prints a later value.

```
php -i
```

Find `session.upload_progress.enabled` and check that it is `0`.

## Request size

Put the body limit on the web server. The framework's `ini_set()` runs too late, and the shipped sizes are `0`.

For a deployment that does not accept uploads, use `post_max_size=8M`. For a deployment that accepts uploads, use `post_max_size=32M` and keep `upload_max_filesize` below it. ReasonableFramework has upload and payment routes, so choose the cap from what that server actually receives.

nginx:

```
client_max_body_size 8m;
fastcgi_keep_conn off;
```

Use `32m` when the deployment accepts uploads. Apache:

```
LimitRequestBody 8388608
```

Use `33554432` when the deployment accepts uploads. For `mod_proxy_fcgi`, leave `enablereuse` off.

`fastcgi_keep_conn` and `enablereuse` belong to [CVE-2026-129289](https://github.com/php/php-src/security/advisories/GHSA-qc49-hpvq-g2w6), fixed in the same 19 November releases. A front end that closes the FastCGI connection after each request is outside the path PHP demonstrated. `read_requests()` in this repository reads `php://input` to the end on the normal path. PHP says that read is not a complete substitute for the upgrade or for closing the connection.

## Which SAPI

Every SAPI that serves web requests is in scope, including PHP-FPM, Apache `mod_php`, `php-cgi`, FrankenPHP, and LiteSpeed. PHP rates `mod_php` highest for the October defects because a request can reach the engine before `index.php` runs. Run a patched PHP-FPM build when you can choose the SAPI.

## PHP 5.3 and 7.x

This repository is tested on PHP 5.3.3 and PHP 7.x. Those lines have no package from the PHP project for these defects.

The PHP project published minimal source backports of the October pair only:

- [PHP 7.4](https://github.com/php/php-src/tree/PHP-7.4-CVE-2026-109618-109619)
- [PHP 8.0](https://github.com/php/php-src/tree/PHP-8.0-CVE-2026-109618-109619)
- [PHP 8.1](https://github.com/php/php-src/tree/PHP-8.1-CVE-2026-109618-109619)

Those branches do not include the 19 November session fix. A host that stays on PHP 5.3 or 7.x needs a vendor build that names the CVEs it fixes, or a move to a version in the table above. The `session.upload_progress.enabled=0` setting still belongs in `php.ini` on those hosts.

## If the server was reachable before the upgrade

CVE-2026-109618 and CVE-2026-109619 are in CISA's Known Exploited Vulnerabilities catalog. After the upgrade, look for PHP files and for child processes of `php-fpm` or `httpd` that this deployment did not create.

## What this repository does not change

Sessions use PHP's default files handler while `sandboxdisabled=1` in `storage/config/security.ini.php`. That is the handler CVE-2026-126714 affects. The PHP release truncates the session file before it writes the replacement. This repository does not replace that handler.
