Feed Reader
===========

A simple PHP RSS/Atom reader. For now it only shows last twenty articles.

Installation / first run
------------------------

```
composer install
php app.php
```

Also make sure your cURL cacert.pem is reasonably updated.
(Some feeds will not be downloaded otherwise.)

See:
https://stackoverflow.com/questions/24611640/curl-60-ssl-certificate-unable-to-get-local-issuer-certificate

Configuration
-------------

To configure Feed Reader with your own feeds:

 * Copy config/feeds.json.dist to config/feeds.json
 * Change / add your feeds
 * Run Feed Reader with config option:

 `php app.php --config config/feeds.json`
