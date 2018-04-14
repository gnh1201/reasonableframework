# Reasonable PHP Framework
- Reasonable Framework is PHP framework for critical legacy web environments. 
- Old version name: Very Simple PHP Framework (VSPF)
 
## Supported feature
- Database connection (via PDO)
- URL Route, Route Controller
- Sperated View
- Model, or somethings is your freedom!

## Compatible
- Tested in PHP 5.3.3
- Tested in PHP 7.x

## How to use
- Extract or clone this project to your shared web hosting.
- You can use and intergrate all of PHP packages without Composer and Additional PHP Extensions! (supported autoloader)
- You can use and rewrite by route feature! without heavy frameworks!
- You can write your back-end code in route. (same as controller)
- You can config database if you add your custom ini file in config directory.
- Enjoy it!

## Roadmap: Support legacy
- Support critical lagacy web server (old: PHP 4.x ~ modern: 7.x)
- Support critical old browser (old: IE 6 ~ modern: IE 11)

## Map of structure
![Map of ResaonableFramework structure](https://github.com/gnh1201/reasonableframework/raw/master/assets/img/reasonableframework.jpg)


## Contact me
- Go Namhyeon <gnh1201@gmail.com>
- Website: https://exts.kr/go/home

## Example
```
<?php
loadHelper("allreporting");

$copyright = "";
$lines = read_file_by_line("./storage/copyright.txt");
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

## Korean
- Resonable PHP Framework(이유있는 PHP 프레임워크)는 한국의 웹 개발 환경에 적합한 PHP 프레임워크입니다.
- Composer를 포함한 개발 보조 도구와, 별도의 플러그인 설치가 제한되어 주류 PHP 프레임워크가 사용 불가능한 환경에 적합합니다.
- Resonable PHP Framework는 CSRF, XSS, SQL Injection 보안 조치를 기본적으로 가지고 있습니다.
- 한국에서 사용되는 각종 CMS와 API와 연동되어 한국 환경에서 사용 빈도가 높은 구현 유형을 작성하는데 적합합니다.
- MVC 모델과 유사하지만 기존 개발 스킬로도 사용할 수 있도록 더 유연한 모델을 가지고 있습니다.
