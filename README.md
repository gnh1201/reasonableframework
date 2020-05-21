# ReasonableFramework
![Discord](https://img.shields.io/discord/359930650330923008.svg)
![View Licence](https://img.shields.io/github/license/gnh1201/reasonableframework.svg)
![Librapay](http://img.shields.io/liberapay/receives/catswords.svg?logo=liberapay)

- ReasonableFramework is `RVHM` structured PHP framework with common security
- Prefix code: `RSF` (ReasonableFramework)
- Old prefix code: `VSPF` (Very Simple PHP Framework)

![This project open source licensed under LGPL version 3](https://github.com/gnh1201/reasonableframework/raw/master/lgplv3-147x51.png)

## technical support (donate us)
- [Technical support and improved web security for ReasonableFramework](https://catswords.re.kr/go/rsfsecurity) ($4/Monthly, pay on Patreon)

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

## Roadmap
- Support critial and special-purposed web environment (industry, scientific, legacy, or more)

## Quick Start
1. git clone https://github.com/gnh1201/reasonableframework.git
2. set up database configuration: `/storage/config/database.ini.php`
3. create new file: `/route/example.php`
4. go to `http://[base_url]/?route=example` or `http://[base_url]/example/`(if set `.htaccess`) in your web browser.
5. enjoy it.

## Examples
- [Send severities from Zabbix to Grafana](https://gist.github.com/gnh1201/792964e9719d2f62157cf46e394888f5)
- [REST API Integration (Naver Papago Translation REST API)](https://gist.github.com/gnh1201/081484e6f5e10bd3be819093ba5f49c8)
- [Payment Gateway Integration (KCP)](https://github.com/gnh1201/reasonableframework/blob/master/route/orderpay.pgkcp.php)

## [NEW] Advanced security (only for sponsors)
- CORS, CSRF, XSS, SQL-injection protection is common security, it is free and open-source for everyone.
- Firewall, DDoS protection, and more security tools are available only for sponsors. [more](https://github.com/gnh1201/reasonableframework/blob/master/SECURITY.md)

## [NEW] Remote Debugging (free for all)
- Remote debugging feature based on [RFC3164(The BSD Syslog Protocol)](https://catswords.re.kr/go/rfc3164), with [Papertrail](https://catswords.re.kr/go/papertrail)

## Compatible of free web hostings

| Provider               | Pass?  | Tested version | Note
| ---------------------- | ------ | -------------- | ------------- |
| cafe24.com (Paid)      | Passed | v1.6.2         |               |
| woobi.co.kr            | Passed | v1.6.2         |               |
| dothome.co.kr          | Passed | v1.5           |               |
| ivyro.net              | Passed | v1.5           |               |
| 000webhost.com         | Warn   | v1.5           | Ad logo       |
| freewebhostingarea.com | Passed | v1.5           |               |
| infinityfree.net       | Warn   | v1.5           | anti-crawling |
| freehosting.io         | Passed | v1.5           |               |
| freehostingeu.com      | Warn   | v1.5           | CURL blocked  |
| freehostingnoads.net   | Warn   | v1.5           | CURL blocked  |
| awardspace.com         | Warn   | v1.5           | CURL blocked  |

## How to use CLI (Command line interface)
```
$ php cli.php --route [route name] --session-id [session ID]
```

## 한국어(Korean)
- **리즈너블 프레임워크**는 필요 이상의 과한 `부작용`을 효과적으로 제어하고자 설계된 PHP 프레임워크입니다.
- `부작용` 출현 빈도가 높은 프로그래밍 환경(예. 레거시 방식의 개발)에서 최대의 안정성과 보안을 제공합니다.
- PHP를 기반으로 하는 무료 웹 호스팅에서도 원활하게 돌아가도록 지원합니다.
- 객체지향, 모듈러(MVC), 시큐어 코딩 등 현대적인 웹 기술을 모르더라도 **더 견고한** 기준을 제공합니다.
- 리즈너블 프레임워크는 CSRF, XSS, SQL 인젝션 등 기초적인 **보안 위협에 사전 대응**하도록 설계되어 있습니다.
- PHP 버전 4 부터 버전 7까지 다양한 개인 및 기업 **적용 사례**를 보유하고 있습니다.
- [카카오톡 채팅방](https://catswords.re.kr/go/kakaotalk)을 통해 실시간 버그 및 보안 이슈 해결이 가능합니다.

## English
- **ReasonableFramework** is a PHP framework designed to effectively control `side-effects` more than necessary.
- It provides maximum stability and security in a programming environment with high frequency of `side-effects` (eg legacy development).
- It also works smoothly on free web hosting based on PHP.
- It provides a **stronger standard** even if you don't know modern web technologies, such as object-oriented, modular (MVC), and secure coding.
- Resonable Framework is designed to **proactively respond to fundamental security threats** such as CSRF, XSS, and SQL injection.
- We have **various enterprise cases** from PHP version 4(legacy) to 7(modern).
- You can quickly resolve bugs and security issues in [our chatting room](https://catswords.re.kr/go/kakaotalk).

## Contact us
- gnh1201@gmail.com
- support@exts.kr
- catswords@protonmail (if you require confidential)
