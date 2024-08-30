# ReasonableFramework
![License LGPLv3](https://img.shields.io/github/license/gnh1201/reasonableframework.svg)
[![DOI](https://zenodo.org/badge/114566493.svg)](https://zenodo.org/doi/10.5281/zenodo.11392416)

ReasonableFramework is RVHM structured PHP framework. aka, RSF, VSPF, C-2020-018490

## Specifications
- Various types of database connection drivers. e.g, PHP PDO, MySQLi, Legacy MySQL, MySQL over the shell, Oracle(OCI)
- No OOP, Just `RVHM` structure
  - R is Route. like as `controller`
  - V is View
  - H is Helper. like a `import` on Python, Go, NodeJS
  - M is Model. it implemented with `KV bind`(like as `Map` data structure), Model is optional.
- Controllable shared variables: Minimize abuse of global variables. Inspired by the `scope` of AngularJS, and `SharedPreferences` of Android Framework
- CGI style compatibility prepared for industrial applications: This framework can utilize both the latest object-oriented style and the CGI style required in industrial applications.

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

## Tested on free web hostings

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
Many people are saying that this project seems to be distant from the [PSR](https://www.php-fig.org/psr/) standards, and that claim is correct.

The coding convention of this project is similar to the CGI style that was widely used in the early 2000s. Moreover, this style is still observed in solutions written in PHP that are sold in markets such as [WordPress](https://wordpress.org/) plugins, [a local-optimized CMS](https://github.com/gnuboard/gnuboard5), or [Codecanyon](https://codecanyon.net/) in 2023.

Although this project hardly uses object-oriented concepts and does not use package managers like Composer much, it still incorporates concepts such as Model, View, Controller, Router, and Helper that are proposed in modern frameworks, and we have made efforts to provide a similar experience as much as possible.

We made efforts to address common security vulnerabilities (XSS, CSRF, SQL injection) in web applications, and included many code snippets that were designed to minimize reliance on specific DBMS or communication drivers.

The specifications that this project offers are still in demand in enterprise environments, so it can be a useful solution if you happen to be in such a situation.

Whenever this project was introduced, I received a lot of questions about PSR, and I also made efforts to find customers who were willing to pay for a PSR version, such as holding conferences for existing customers. However, there is still no good news. Until good news comes, my plan is to mainly maintain this project.

If you want to comply with the PSR standards and your colleagues are also ready to learn them humbly, we recommend that you consider [Codeigniter](https://github.com/bcit-ci/CodeIgniter) (which has a similar structure to this project) or [Silm Framework](https://github.com/slimphp/Slim).

## Security Policy
- [Security policy](SECURITY.md)

## Contact us
- abuse@catswords.net
- ActivityPub [@catswords_oss@catswords.social](https://catswords.social/@catswords_oss)
- [Join Catswords on Microsoft Teams](https://teams.live.com/l/community/FEACHncAhq8ldnojAI)
