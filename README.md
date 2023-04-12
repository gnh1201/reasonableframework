# ReasonableFramework
![License LGPLv3](https://img.shields.io/github/license/gnh1201/reasonableframework.svg)
![Registered KCC C-2020-018490](https://img.shields.io/static/v1?label=registered&message=KCC%20C-2020-018490&color=orange)

- ReasonableFramework is `RVHM` structured PHP framework with common security
- Prefix code: `RSF` (ReasonableFramework)
- Old prefix code: `VSPF` (Very Simple PHP Framework)

![This project open source licensed under LGPL version 3](https://github.com/gnh1201/reasonableframework/raw/master/lgplv3-147x51.png)

Note: This project has been assigned a registration number by the Korea Copyright Commission Software Copyright Registration System. It was registered on June 3, 2020, with registration number `C-2020-018490` for version `v1.6.5.1-kcc`. This registration does not affect the open source license applied to this project.

## Security policy
- [Security policy and techincal support](SECURITY.md)

## Specification
- Database connection (via PDO, MySQLi (MySQL Improved), MySQL Tranditional, MySQL CLI, Oracle(OCI))
- RVHM structure: `R` is Route (like as `controller`), `V` is View, `H` is Helper (like as `import` on Python/Go/NodeJS), `M` is Model and implemented with `KV bind`(like as `Map` data structure), Model is not required.
- Controllable shared variables: Minimize abuse of global variables (Inspired by the `scope` of AngularJS, and `SharedPreferences` of Android Framework)

## Compatible
- Tested in PHP 5.3.3
- Tested in PHP 7.x

## How to use
- Extract or clone this project to your (restrictive) shared web hosting.
- You can intergrate all of PHP projects (linear, modular (ex. `autoloader`), or others) without complicated extensions.
- You can write your code and rewrite by `route` parameter without heavy framework. (like as `controller`)
- You can add your custom `ini.php` configuration file in `config` directory.
- Enjoy it!

## Map of structure
![Map of structure](https://github.com/gnh1201/reasonableframework/raw/master/assets/img/reasonableframework.jpg)

## Quick Start
1. git clone https://github.com/gnh1201/reasonableframework.git
2. edit database configuration: `/storage/config/database.ini.php`
3. create new file: `/route/example.php`
4. go to `http://:base_url/?route=example` or `http://:base_url/example/`(if set `.htaccess`) in the web browser.
5. code it.

## Use cases
- [Send severities from Zabbix to Grafana](https://gist.github.com/gnh1201/792964e9719d2f62157cf46e394888f5)
- [REST API Integration (Papago Translation REST API)](https://gist.github.com/gnh1201/081484e6f5e10bd3be819093ba5f49c8)
- [Payment Gateway Integration](https://github.com/gnh1201/reasonableframework/blob/master/route/orderpay.pgkcp.php)

## Remote logging
- Remote logging feature based on [RFC3164(The BSD Syslog Protocol)](https://tools.ietf.org/html/rfc3164)

## Compatible of free web hostings

| Provider               | Pass?  | Tested version | Note
| ---------------------- | ------ | -------------- | -------------------------- |
| [vultr.com](https://catswords.re.kr/go/vultr) (Vultr Holdings Co.)       | :heavy_check_mark: Passed | v1.6.5.2       | Paid, Pre-configured LAMP server |
| cafe24.com (Cafe24 Inc.)      | :heavy_check_mark: Passed | v1.6.2         | Paid                           |
| woobi.co.kr (MyCGI)            | :heavy_check_mark: Passed | v1.6.2         |                            |
| dothome.co.kr (Anysecure Inc.)         | :heavy_check_mark: Passed | v1.5           |                            |
| ivyro.net (Smileserv Inc.)            | :heavy_check_mark: Passed | v1.5           |                            |
| 000webhost.com         | :warning: Warn   | v1.5           | Advertising logo           |
| freewebhostingarea.com | :heavy_check_mark: Passed | v1.5           |                            |
| infinityfree.net       | :warning: Warn   | v1.5           | Anti-crawling              |
| freehosting.io         | :heavy_check_mark: Passed | v1.5           |                            |
| freehostingeu.com      | :warning: Warn   | v1.5           | CURL blocked               |
| freehostingnoads.net   | :warning: Warn   | v1.5           | CURL blocked               |
| awardspace.com         | :warning: Warn   | v1.5           | CURL blocked               |

## How to use CLI (Command-line interface)
```
$ php cli.php --route :route --session-id :session_id
```

## Comment about PSR standards
Many people are saying that this project seems to be distant from the PSR standards, and that claim is correct.

The coding convention of this project is similar to the CGI style that was widely used in the early 2000s. Moreover, this style is still observed in solutions written in PHP that are sold in markets such as [WordPress](https://wordpress.org/) plugins, [a local-optimized CMS](https://github.com/gnuboard/gnuboard5), or [Codecanyon](https://codecanyon.net/) in 2023.

Although this project hardly uses object-oriented concepts and does not use package managers like Composer much, it still incorporates concepts such as Model, View, Controller, Router, and Helper that are proposed in modern frameworks, and we have made efforts to provide a similar experience as much as possible.

We made efforts to address common security vulnerabilities (XSS, CSRF, SQL injection) in web applications, and included many code snippets that were designed to minimize reliance on specific DBMS or communication drivers

The specifications that this project offers are still in demand in enterprise environments, so it can be a useful solution if you happen to be in such a situation.

If you want to comply with the PSR standards and your colleagues are also ready to learn them humbly, we recommend that you consider [Codeigniter](https://github.com/bcit-ci/CodeIgniter) (which has a similar structure to this project) or [Silm Framework](https://github.com/slimphp/Slim).

## Contact us
- abuse@catswords.net
