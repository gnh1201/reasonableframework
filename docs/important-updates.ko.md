# 중요 업데이트

[English](important-updates.md)

## 2019년 10월 4일 (1.6-pre191004)

- `system/database`
  - 입력, 조회, 수정, 삭제의 `where` 조건 작성 방법을 하나로 맞췄습니다. [#57](https://github.com/gnh1201/reasonableframework/issues/57)
  - `or` / `and` 조건의 묶음과 혼합을 지원합니다. [#57](https://github.com/gnh1201/reasonableframework/issues/57)
  - 입력 시 `ignore` 조건으로 중복 행을 건너뜁니다. [#56](https://github.com/gnh1201/reasonableframework/issues/56)
  - 수정의 인자 구조를 입력, 삭제와 같게 맞췄습니다. [#59](https://github.com/gnh1201/reasonableframework/issues/59)
  - 시스템 레이어에서 임시 테이블을 지원합니다. [#60](https://github.com/gnh1201/reasonableframework/issues/60)
- `helper/database.mysql.cmd`
  - MySQL, MariaDB 명령행 드라이버를 다듬었습니다.
- `helper/colona.v1.format`
  - 레코드는 `\r\n`, 필드는 `:`로 나누는 줄 단위 형식을 추가했습니다.

## 같이 보기

- [SQLX (SQL Extendable)](sqlx.ko.md)
- [REST 클라이언트 (webpagetool)](rest-client.ko.md)

## 출처

인터넷 아카이브 스냅샷 [20250812113105](https://web.archive.org/web/20250812113105/https://catswords-oss.rdbl.io/7839294550/1838108760).
