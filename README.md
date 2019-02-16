# ReasonableFramework
- ReasonableFramework is PHP framework for make solid and secure web development.
- Old version name: Very Simple PHP Framework (VSPF)
 
## Specification
- Database connection (via PDO, MySQLi (MySQL Improved), MySQL Tranditional, Oracle(OCI))
- Route (Controller), Helper (like as `import` on Python or Go), View Structrue (Model is optional)

## Compatible
- Tested in PHP 5.3.3
- Tested in PHP 7.x

## How to use
- Extract or clone this project to your (restrictive) shared web hosting.
- You can intergrate all of PHP projects (linear, modular, or others) without complicated extensions.
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
2. set up database configuration: storage/config/database.ini
3. make route/example.php
4. go to [base_url]/?route=example
5. enjoy it.

##  Website example
```
<?php
if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");

loadHelper("string.utl");

$copyright = read_storage_file("copyright.txt", array(
    "storage_type" => "terms"
));
$lines = explode_by_line($copyright);
foreach($lines as $line) {
    $copyright .= "<p>" . $line . "</p>";
}

$data = array(
    "copyright" => $copyright
);

renderView('templates/default/header');
renderView('view_copyright', $data);
renderView('templates/default/footer');
?>
```

## CLI mode example
```
$ php cli.php --route [route name]
```

## Korean
- Resonable PHP Framework(이유있는 PHP 프레임워크)는 한국의 웹 개발 환경에 적합한 PHP 프레임워크입니다.
- Composer를 포함한 별도의 개발 보조 도구, PHP 플러그인, PHP 프레임워크가 사용 불가능한 환경에 적합합니다.
- 개발 팀원을 대상으로 객체지향(OOP) 교육이 이루어지지 않아도, 그에 준하는 생존주기(Life cycle)를 보장합니다.
- Resonable PHP Framework는 CSRF, XSS, SQL Injection 보안 조치를 기본적으로 가지고 있습니다.
- 한국에서 사용되는 각종 CMS와 API와 연동되어 한국 환경에서 사용 빈도가 높은 구현 유형을 작성하는데 적합합니다.
- MVC 모델과 유사하지만 기존 개발 스킬로도 사용할 수 있도록 더 유연한 모델을 가지고 있습니다.
