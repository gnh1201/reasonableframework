# Integration

[한국어](integration.ko.md)

These notes describe the modules listed with v1.6.8 and how to point static files at a CDN or object store. The framework repository is [gnh1201/reasonableframework](https://github.com/gnh1201/reasonableframework). The project is distributed under the LGPL-3.0 license. The static-hosting steps below match v1.6.8 (2024-05-30).

## Modules listed with v1.6.8

- System: base (framework base)
- System: config (configuration)
- System: database (database)
- System: logger (log)
- System: uri (URI and URL handling)
- System: security (security)
- Helper: paginate (page navigation)
- Helper: gnuboard, gnuboard.config, gnuboard.lib (Gnuboard settings and libraries)
- Helper: vworld (MOLIT VWorld)
- Helper: wprest (WordPress REST client)
- Helper: webpagetool (HTTP client with proxy, user agent, and JSON or DOM parsing)
- Helper: noreporting, allreporting (error display range)
- Helper: isemail.lnk (`is_email` through `/vendor/isemail`)
- Helper: metaparser.lnk (HTML meta parsing through `/vendor/PHP-MetaParser`)
- Helper: simple_html_dom (HTML DOM)
- Helper: oracle (Oracle connect and query)
- Helper: exectool (run a command from PHP)
- Further helpers cover more of the same kind of work

## Static file hosting (Cafe24 CDN or Amazon S3)

`get_cdn_link()` prefixes a path with `base_cdn_url`. `get_vod_link()` prefixes a path with `base_vod_url`. Both keys are in `storage/config/uri.ini.php`. An empty value falls back to `base_url`.

Services this was written for:

- Cafe24, on the [10G Gwang-Autobahn FullSSD+](https://hosting.cafe24.com/?controller=new_product_page&page=autobahn) plan
- Amazon S3
- Object storage

### Cafe24

1. Sign in to the Cafe24 console and read the CDN and streaming host.
   1. Open My Service Management.
   2. Open Service access, then Service access information.
   3. Read the CDN and streaming block. One panel looked like this:

       ```
       *** CDN ***
       CDN disk: 200M
       CDN traffic: 500M
       FTP host: iup.cdn2.cafe24.com
       FTP port: 21
       FTP user: [account id]

       *** Streaming ***
       Streaming disk: 200M
       Streaming traffic: 500M
       FTP host: wm-004.cafe24.com
       FTP port: 5565
       FTP user: [account id]
       ```

   4. A CDN URL then looks like `https://[account id].cdn2.cafe24.com`.
   5. A streaming URL then looks like `mms://wm-004.cafe24.com/[account id]/abc.mp3`.

2. Install v1.6.8 or a later release from the [GitHub releases](https://github.com/gnh1201/reasonableframework/releases).
   1. Set the two keys in `storage/config/uri.ini.php`.

       ```
       base_cdn_url = https://[account id].cdn2.cafe24.com
       base_vod_url = mms://wm-004.cafe24.com/[account id]
       ```

   2. Add `route/cdntest.php`.

       ```php
       <?php
       $data = array(
           "imageurl" => get_cdn_link("/picture.jpg")
       );
       renderView("view_cdntest", $data);
       ```

   3. Add `view/view_cdntest.php`.

       ```php
       <img src="<?php echo $imageurl; ?>" alt="this is cdn test">
       ```

   4. Upload the project files to the web hosting account.
   5. Upload a JPEG named `picture.jpg` to the CDN FTP account from step 1.
   6. Open `http://[web host]/?route=cdntest` and confirm the image.

### Amazon S3 or object storage

Put the provider's base URL in `base_cdn_url` in `storage/config/uri.ini.php`.

## Support

- ActivityPub: https://catswords.social/@catswords_oss
- Email: [oss@catswords.re.kr](mailto:oss@catswords.re.kr)
- Vulnerability reports: [GitHub Security Advisories](https://github.com/gnh1201/reasonableframework/security)

## See also

- [REST client (webpagetool)](rest-client.md)
- [Use cases](use-cases.md)

## Source

Internet Archive snapshot [20250812121351](https://web.archive.org/web/20250812121351/https://catswords-oss.rdbl.io/7839294550/3237877389). The archived HTML stored this page as escaped Markdown. The clean text is `KOREAN.MD` at tag v1.6.8, and this page is that text edited to match the repository.
