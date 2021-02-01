geoCRE
======

<a href="https://geocre.hoschek.com/">geoCRE</a> is a collaborative research environment for geographic research purposes.

System requirements
-------------------

* Apache webserver with .htaccess file support
* PHP
* PostgreSQL
* PostGIS
* GDAL
* ZIP

Installation
------------

1. Create a PostgreSQL database with PostGIS extension
2. Run the database initialization script (config/sql/initial.sql)
3. Edit the file confifg/db_settings.conf.php
4. The application should be accessible under the server address now (e.g. http://127.0.0.1/geocre/)
5. Log in with the access data mail@example.org / foobar123
