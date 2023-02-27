# ReasonableFramework
![License LGPLv3](https://img.shields.io/github/license/gnh1201/reasonableframework.svg)
![Compliance KCC C-2020-018490](https://img.shields.io/static/v1?label=compliance&message=KCC%20C-2020-018490&color=orange)

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
- Support a mission critial and specialized web environment (For industry, For scientific, For legacy, or more)

## Quick Start
1. git clone https://github.com/gnh1201/reasonableframework.git
2. edit database configuration: `/storage/config/database.ini.php`
3. create new file: `/route/example.php`
4. go to `http://:base_url/?route=example` or `http://:base_url/example/`(if set `.htaccess`) in the web browser.
5. code it.

## Examples
- [Send severities from Zabbix to Grafana](https://gist.github.com/gnh1201/792964e9719d2f62157cf46e394888f5)
- [REST API Integration (Naver Papago Translation REST API)](https://gist.github.com/gnh1201/081484e6f5e10bd3be819093ba5f49c8)
- [Payment Gateway Integration (KCP)](https://github.com/gnh1201/reasonableframework/blob/master/route/orderpay.pgkcp.php)

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

## How to use CLI (Command line interface)
```
$ php cli.php --route :route --session-id :session_id
```

## Paid options
- Advanced security: Enhanced protection for CORS, CSRF, XSS, SQL-injection, DDoS attack, etc.

## Compliances
- This project was registered to the Korea Copyright Commission's software copyright registration system. the registration number is `C-2020-018490`(version: v1.6.5.1-kcc) and approved in June 3, 2020.
- The open source license applied to this project will remain the same now and in the future.

## 한국어(Korean)
- **리즈너블 프레임워크**는 한국의 웹 개발 환경에 적합한 PHP 프레임워크입니다.
- 레거시 코드에 기반하거나 숙련되지 않은 개발자가 프로그램을 개발하여도 최대의 안정성을 제공합니다.
- 한국, 아시아, 북미, 유럽권의 PHP를 기반으로 하는 무료 웹 호스팅에서도 안정적인 운영이 가능합니다.
- 객체지향(OOP), 모듈러(MVC), 시큐어 코딩 등 현대적인 웹 기술을 모르더라도 **편리한 기준**을 제공합니다.
    - Composer(패키지 관리자)를 포함한 별도의 개발 보조 도구, PHP 플러그인, PHP 프레임워크가 사용 불가능한 환경에 적합합니다.
    - 한국에서 사용되는 각종 CMS와 API와 연동되어 한국 환경에서 사용 빈도가 높은 구현 유형을 작성하는데 적합합니다.
    - RVHM 구조는 MVC 구조와 함께 사용하실 수 있으며, 기존 개발 스킬로도 사용할 수 있도록 더 유연한 구조를 가집니다.
- **리즈너블 프레임워크**는 CSRF, XSS, SQL 인젝션 등 기초적인 **보안 위협에 대응**하도록 설계되어 있습니다.
- **리즈너블 프레임워크**는 `Forwarded` 헤더를 자체적으로 지원하여 부하 분산이나 익명 웹사이트 구축(예를 들어, Tor)에 적합합니다.
- 이 프로젝트는 PHP 버전 4 부터 버전 7까지 다양한 **기업 수준의 적용 사례**를 포함하고 있습니다.
- 이 프로젝트는 [카카오톡 채팅방](https://catswords.re.kr/go/kakaotalk)에서 실시간으로 버그 및 보안 이슈를 제보받고 있습니다.

## English
* **ReasonableFramework** is a PHP framework designed for restrictive web environments.
* It provides maximum stability even if the program is developed by developers who are working with legacy code or who lack experience.
* It also runs smoothly on free web hosting services (in South Korea, Asia, America, and Europe) based on PHP 4 and 7.
* It provides a reliable standard even if you are not familiar with modern web technologies, such as object-oriented, modular (MVC), and secure coding.
  * It is ideal for environments where separate development tools, including Composer, PHP extensions, and famous PHP frameworks, are not available.
  * It is compatible with famous CMS and APIs used with REST APIs, making it suitable for creating implementations that are frequently used in various environments.
  * The RVHM structure can be used with the MVC structure and has a more flexible structure to use with existing development skills.
* **ReasonableFramework** is designed to address fundamental **security threats** such as CSRF, XSS, and SQL injection.
* **ReasonableFramework** natively supports 'Forwarded' headers, making it ideal for building load balancing or private services.
* This project includes a lot of enterprise-level use cases from PHP version 4 (legacy) to 7 (modern).
* This project receives real-time reports of bugs and security issues in the [official chat room](https://catswords.re.kr/go/kakaotalk).

## Contact us
- abuse@catswords.net
