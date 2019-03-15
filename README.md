# ReasonableFramework
- ReasonableFramework is `RVHM` structured PHP Web Framework, Securely, Compatibility.
- Old version name: Very Simple PHP Framework (VSPF)
 
## Specification
- Database connection (via PDO, MySQLi (MySQL Improved), MySQL Tranditional, Oracle(OCI))
- RVHM Structure: `R` is Route (like as `controller`), `V` is View, `H` is Helper (like as `import` on Python/Go/NodeJS), `M` is Model and implemented with `KV bind`(like as `Map structure`), Modal is optional.
- WebApp Sandbox: You can use legacy apps without modifying the source code.

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

## How to use CLI
```
$ php cli.php --route [route name]
```

## 한국어(Korean)
- Resonable PHP Framework(이유있는 PHP 프레임워크)는 한국의 웹 개발 환경에 적합한 PHP 프레임워크입니다.
- Composer를 포함한 별도의 개발 보조 도구, PHP 플러그인, PHP 프레임워크가 사용 불가능한 환경에 적합합니다.
- 개발 팀원을 대상으로 객체지향(OOP) 교육이 이루어지지 않아도, 그에 준하는 생존주기(Life cycle)를 보장합니다.
- Resonable PHP Framework는 CSRF, XSS, SQL Injection 보안 조치를 기본적으로 가지고 있습니다.
- 한국에서 사용되는 각종 CMS와 API와 연동되어 한국 환경에서 사용 빈도가 높은 구현 유형을 작성하는데 적합합니다.
- RVHM 구조는 MVC 구조와 함께 사용하실 수 있으며, 기존 개발 스킬로도 사용할 수 있도록 더 유연한 구조를 가지고 있습니다.
