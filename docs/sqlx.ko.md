# SQLX (SQL Extendable)

[English](sqlx.md)

SQLX는 SQL 문을 `storage/sqlx/`에 두고, 실행할 때 이름 있는 자리표시자를 바인드합니다. `get_bind_to_sqlx()`가 그 파일을 읽습니다. 넘기는 이름은 `.sqlx`를 뺀 파일 이름입니다.

## 예제

1. `storage/sqlx/gentleman.sqlx`를 작성합니다.

```sql
SELECT (a.value + b.value) AS gentleman
    FROM mother a, father b
    WHERE a.id = b.object_id AND a.name = :aname
```

2. `helper/gentleman.dbt.php`를 작성합니다.

```php
<?php
function coolguy() {
    $bind = array("aname" => "coolguy");
    $sql = get_bind_to_sqlx("gentleman", $bind);
    $rows = exec_db_fetch_all($sql, $bind);
    return $rows;
}
```

3. `route/gentleman.php`에서 호출합니다.

```php
<?php
loadHelper("gentleman.dbt");
var_dump(coolguy());
```

4. 브라우저에서 `http://localhost/?route=gentleman`를 엽니다.

## 같이 보기

- [대체 데이터베이스 접속](alternative-database-connector.ko.md)
- [중요 업데이트](important-updates.ko.md)

## 출처

인터넷 아카이브 스냅샷 [20250812115915](https://web.archive.org/web/20250812115915/https://catswords-oss.rdbl.io/7839294550/8471730580).
