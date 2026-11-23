# 대체 데이터베이스 접속

[English](alternative-database-connector.md)

PHP 웹 호스팅은 MySQL에 닿는 방법이 하나 이상 필요한 경우가 있습니다. `storage/config/database.ini.php`의 `db_driver`로 고릅니다. 그 파일의 기본값은 `mysql`입니다.

`mysql`과 `mysql.pdo`는 `system/database.php` 안에서 PDO로 접속합니다. 그 밖의 값은 `helper/database.alt.php`가 읽고, 맞는 헬퍼를 불러옵니다.

| `db_driver` | 구현 | 역할 |
| --- | --- | --- |
| `mysql`, `mysql.pdo` | `system/database.php` | PDO를 통한 MySQL. 기본값입니다. |
| `mysql.imp` | `helper/database.mysql.imp.php` | MySQLi |
| `mysql.old` | `helper/database.mysql.old.php` | MySQLi가 없는 호스트용 예전 `mysql` 확장 |
| `mysql.cmd` | `helper/database.mysql.cmd.php` | `mysql` 명령행 클라이언트 |
| `oracle` | `helper/database.oracle.php` | OCI를 통한 오라클 |
| `mssql.pdo` | `helper/database.mssql.pdo.php` | PDO를 통한 Microsoft SQL Server |

## 같이 보기

- [호스팅 호환](provider-compatibility.ko.md)
- [SQLX (SQL Extendable)](sqlx.ko.md)

## 출처

인터넷 아카이브 스냅샷 [20250812112053](https://web.archive.org/web/20250812112053/https://catswords-oss.rdbl.io/7839294550/1303227424). `mssql.pdo` 행은 이 저장소에 들어 있는 드라이버입니다.
