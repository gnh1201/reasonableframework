# ReasonableFramework
![License LGPLv3](https://img.shields.io/github/license/gnh1201/reasonableframework.svg)
![KCC C-2020-018490](https://img.shields.io/static/v1?label=KCC&message=C-2020-018490&color=orange)

- ReasonableFramework is `RVHM` structured PHP framework with common security
- Prefix code: `RSF` (ReasonableFramework)
- Old prefix code: `VSPF` (Very Simple PHP Framework)

![This project open source licensed under LGPL version 3](https://github.com/gnh1201/reasonableframework/raw/master/lgplv3-147x51.png)

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

## Roadmap
- Support critial and special-purposed web environment (industry, scientific, legacy, or more)

## Quick Start
1. git clone https://github.com/gnh1201/reasonableframework.git
2. set up database configuration: `/storage/config/database.ini.php`
3. create new file: `/route/example.php`
4. go to `http://[base_url]/?route=example` or `http://[base_url]/example/`(if set `.htaccess`) in your web browser.
5. code it.

## Examples
- [Send severities from Zabbix to Grafana](https://gist.github.com/gnh1201/792964e9719d2f62157cf46e394888f5)
- [REST API Integration (Naver Papago Translation REST API)](https://gist.github.com/gnh1201/081484e6f5e10bd3be819093ba5f49c8)
- [Payment Gateway Integration (KCP)](https://github.com/gnh1201/reasonableframework/blob/master/route/orderpay.pgkcp.php)

## Advanced security (only for sponsors)
- CORS, CSRF, XSS, SQL-injection protection is common security, it is free and open-source for everyone.
- Firewall, DDoS protection, and more security tools are available only for sponsors. [more](https://github.com/gnh1201/reasonableframework/blob/master/SECURITY.md)

## Remote logger (free for all)
- Remote logger feature based on [RFC3164(The BSD Syslog Protocol)](https://catswords.re.kr/go/rfc3164), with [Papertrail](https://catswords.re.kr/go/papertrail)

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

## How to use CLI (Command line interface)
```
$ php cli.php --route [route name] --session-id [session ID]
```

## Administratives
- This project was registered to the Korea Copyright Commission's software copyright registration system. the registration number is `C-2020-018490`(version: v1.6.5.1-kcc) and approved in June 3, 2020.
- The open source license applied to this project will remain the same now and in the future.

## 한국어(Korean)
- **리즈너블 프레임워크**는 한국의 웹 개발 환경에 적합한 PHP 프레임워크입니다.
- 레거시 코드가 많거나 숙련되지 않은 개발자에 의해 프로그램이 개발되는 환경에서도 최대의 안정성과 보안을 제공합니다.
- 한국, 북미, 유럽권의 PHP를 기반으로 하는 무료 웹 호스팅에서도 안정적인 운영이 가능합니다.
- 객체지향, 모듈러(MVC), 시큐어 코딩 등 현대적인 웹 기술을 모르더라도 **더 견고한** 기준을 제공합니다.
- 리즈너블 프레임워크는 CSRF, XSS, SQL 인젝션 등 기초적인 **보안 위협에 사전 대응**하도록 설계되어 있습니다.
- PHP 버전 4 부터 버전 7까지 다양한 개인 및 기업 **적용 사례**를 보유하고 있습니다.
- [카카오톡 채팅방](https://catswords.re.kr/go/kakaotalk)을 통해 실시간 버그 및 보안 이슈 해결이 가능합니다.

## English
- **ReasonableFramework** is a PHP framework designed to the restrictive web environments.
- It provides maximum stability and security even in environments where programs are developed by inexperienced developers or based on lagacy codes.
- It also works smoothly on free web hosting (in South Korea, America, and Europe) based on PHP 
- It provides a **stronger standard** even if you don't know modern web technologies, such as object-oriented, modular (MVC), and secure coding.
- Resonable Framework is designed to **proactively respond to fundamental security threats** such as CSRF, XSS, and SQL injection.
- We have **various enterprise cases** from PHP version 4(legacy) to 7(modern).
- You can quickly resolve bugs and security issues in [our chatting room](https://catswords.re.kr/go/kakaotalk).

## Contact us
- gnh1201@gmail.com
- support@exts.kr
- catswords@protonmail.com (if you require confidential)
