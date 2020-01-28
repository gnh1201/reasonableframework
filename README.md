# ReasonableFramework
![Discord](https://img.shields.io/discord/359930650330923008.svg)
![View Licence](https://img.shields.io/github/license/gnh1201/reasonableframework.svg)
![Librapay](http://img.shields.io/liberapay/receives/catswords.svg?logo=liberapay)

- ReasonableFramework is `RVHM` structured PHP web framework, securely and compatibility
- Prefix code: `RSF` (ReasonableFramework)
- Old prefix code: `VSPF` (Very Simple PHP Framework)

## Donate us (or if you need technical support)
- https://www.patreon.com/posts/25380536
 
## Specification
- Database connection (via PDO, MySQLi (MySQL Improved), MySQL Tranditional, MySQL CLI, Oracle(OCI))
- RVHM Structure: `R` is Route (like as `controller`), `V` is View, `H` is Helper (like as `import` on Python/Go/NodeJS), `M` is Model and implemented with `KV bind`(like as `Map` data structure), Model is not required.
- AppJail(Experimental): Load a legacy PHP applications on the jailed area without modifying the source code.

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

## Roadmap: Support legacy
- Support critical legacy web server (old: PHP 4.x ~ modern: 7.x)
- Support critical old browser (old: IE 6 ~ modern: IE 11)
- Do Clean & Modern PHP without hard studies.

## Contact me
- Go Namhyeon <gnh1201@gmail.com>
- Website: https://exts.kr/go/home

## Quick Start
1. git clone https://github.com/gnh1201/reasonableframework.git
2. set up database configuration: `/storage/config/database.ini.php`
3. touch(make new file): `/route/example.php`
4. go to `http://[base_url]/?route=example` or `http://[base_url]/example/`(if set `.htaccess`) in your web browser.
5. enjoy it.

## Examples
- [REST API Integration (Naver Papago Translation REST API)](https://gist.github.com/gnh1201/081484e6f5e10bd3be819093ba5f49c8)
- [Payment Gateway Integration (KCP)](https://github.com/gnh1201/reasonableframework/blob/master/route/orderpay.pgkcp.php)
- [Gnuboard CMS Integration (version 4, version 5)](https://github.com/gnh1201/reasonableframework/blob/master/route/api.gnuboard.php)

## [NEW] Advanced security (only for sponsors)
- CORS, CSRF, XSS, SQL-injection protection is common security, it is free and open-source for everyone.
- Firewall, DDoS protection, and more tools are available only for sponsors. [see details](https://github.com/gnh1201/reasonableframework/blob/master/SECURITY.md)

## How to use CLI
```
$ php cli.php --route [route name]
```

## 한국어(Korean)
- **리즈너블(이유있는) 프레임워크**는 제약 사항이 많은 웹 개발 환경을 위해 설계된 PHP 프레임워크입니다.
- 유효기간 지난 자산 및 낮은 개발 능력으로 취약한 환경에 집중한 설계를 기반으로 높은 수준의 안정성과 보안을 제공합니다.
- PHP를 기반으로 하는 무료 웹 호스팅에서도 원활하게 돌아가도록 지원합니다.
- 개발 인력이 객체지향, 모듈러(MVC), 시큐어 코딩 등 현대적인 웹 기술을 모르더라도, **한단계 더 높은** 기술 수준을 지원합니다.
- 리즈너블 프레임워크는 CSRF, XSS, SQL 인젝션 등 기초적인 보안 위협에 사전 대응하도록 설계되어 있습니다.
- 현대적 웹 개발 기술과 환경을 함께 사용하실 수 있습니다.
- 공식 카카오톡 오픈채팅방을 통해 신속한 버그 및 보안이슈 해결이 가능합니다. https://open.kakao.com/o/g9spGfrb

## English
- **Reasonable Framework** is a PHP framework designed for highly constrained web development environments.
- Designed to be effective for vulnerable environments due to outdated assets and low development skills, we aim for the highest level of stability.
- It also works smoothly on free web hosting based on PHP.
- Even if the development staff doesn't know modern web techniques such as object-oriented and modular (MVC) and secure coding, we're support the **higher** technical level.
- Resonable Framework is designed to proactively respond to fundamental security threats such as CSRF, XSS, and SQL injection.
- Use with modern web development environments and techniques.
- You can quickly resolve bugs and security issues in our chatting room: https://open.kakao.com/o/g9spGfrb

## Contact us
- support@exts.kr
