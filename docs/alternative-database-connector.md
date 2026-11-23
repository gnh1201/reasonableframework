# Alternative database connectors

[한국어](alternative-database-connector.ko.md)

Shared PHP hosting often needs more than one way to reach MySQL. Set `db_driver` in `storage/config/database.ini.php`. The default value in that file is `mysql`.

`mysql` and `mysql.pdo` connect with PDO inside `system/database.php`. Any other value loads `helper/database.alt.php`, which then loads the matching helper.

| `db_driver` | Implementation | Role |
| --- | --- | --- |
| `mysql`, `mysql.pdo` | `system/database.php` | MySQL through PDO. This is the default. |
| `mysql.imp` | `helper/database.mysql.imp.php` | MySQLi |
| `mysql.old` | `helper/database.mysql.old.php` | The old `mysql` extension, for hosts that have no MySQLi |
| `mysql.cmd` | `helper/database.mysql.cmd.php` | The `mysql` command-line client |
| `oracle` | `helper/database.oracle.php` | Oracle through OCI |
| `mssql.pdo` | `helper/database.mssql.pdo.php` | Microsoft SQL Server through PDO |

## See also

- [Provider compatibility](provider-compatibility.md)
- [SQLX (SQL Extendable)](sqlx.md)

## Source

Internet Archive snapshot [20250812112053](https://web.archive.org/web/20250812112053/https://catswords-oss.rdbl.io/7839294550/1303227424). The `mssql.pdo` row is the driver shipped in this repository.
