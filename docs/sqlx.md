# SQLX (SQL Extendable)

[한국어](sqlx.ko.md)

SQLX keeps a SQL statement in `storage/sqlx/` and binds named placeholders at runtime. `get_bind_to_sqlx()` reads that file. The name you pass is the file name without `.sqlx`.

## Example

1. Write `storage/sqlx/gentleman.sqlx`.

```sql
SELECT (a.value + b.value) AS gentleman
    FROM mother a, father b
    WHERE a.id = b.object_id AND a.name = :aname
```

2. Write `helper/gentleman.dbt.php`.

```php
<?php
function coolguy() {
    $bind = array("aname" => "coolguy");
    $sql = get_bind_to_sqlx("gentleman", $bind);
    $rows = exec_db_fetch_all($sql, $bind);
    return $rows;
}
```

3. Call it from `route/gentleman.php`.

```php
<?php
loadHelper("gentleman.dbt");
var_dump(coolguy());
```

4. Open `http://localhost/?route=gentleman`.

## See also

- [Alternative database connectors](alternative-database-connector.md)
- [Important updates](important-updates.md)

## Source

Internet Archive snapshot [20250812115915](https://web.archive.org/web/20250812115915/https://catswords-oss.rdbl.io/7839294550/8471730580).
