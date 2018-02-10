## About Dienstplan

Dienstplan is a web application for first responder groups that allows them to
manage and share on-call duties. It's features comprise:

- Responsive mobile-first web design
- Straightforward shift-based duty selection (no fiddling with dates and times
  required)
- Announcement system to keep your colleagues up-to-date
- User-friendly invite-based registration system
- Overview of all members' phone numbers in addition to a configurable set of
  important ones
- Configurability regarding e.g. time spans to create, modify and view duties
  as a normal user
- ...

All of this is built on a solid selection of up-to-date web technology:
- [Laravel](https://laravel.com) is used as the server-side framework
- [jQuery](https://jquery.com) drives the client-side JavaScript
- [jQuery UI](https://jqueryui.com/) renders the datepicker
- [Pure CSS](https://purecss.io) provides beautiful responsive styles
- [Font Awesome](https://fontawesome.com/v4.7.0/) enriches the UI with
  meaningful icons
- [Carbon](http://carbon.nesbot.com/) makes handling dates and times fun again
 
## Installation

Deploying Dienstplan is essentially no different from any other Laravel
application. This section will describe a basic installation procedure.

All the available possibilities and background information can be found from
Laravel's documentation. Foremost relevant are:
 - [General Installation](https://laravel.com/docs/5.5/installation)
 - [Configuration](https://laravel.com/docs/5.5/configuration)
 - [Compiling Assets through Laravel Mix](https://laravel.com/docs/5.5/mix)
 - [Production Deployment](https://laravel.com/docs/5.5/installation)

 ### Server Requirements
 
 - some webserver like Apache or nginx pointed to the `public` directory
    - `mod_rewrite` with Apache for pretty URLs
    - `mod_expires` with Apache for long-term caching
 - PHP >= 7.0.0 with
    - OpenSSL PHP Extension
    - PDO PHP Extension
    - Mbstring PHP Extension
    - Tokenizer PHP Extension
    - XML PHP Extension
- some database like mysql or mariadb (or possibly any other database supported
  by Laravel such as sqlite)
- permission to set-up cron jobs
- permission to start a long-running worker process
 
### Pre-Installation

*If you downloaded a release bundle instead of a source checkout, you can skip
this section.*

1. `npm install`
2. `npm run prod` (or for development: `npm run dev`)
3. *(for development)* possibly set `ExpiresActive = off` in `public/.htaccess`

### Installation

1. `composer install`
2. Save `.env.example`  as `.env` and adapt it to your environment
3. `php artisan migrate:fresh` (or to keep existing tables: `php artisan migrate`)
4. *optional:* `php artisan db:seed` (or for even more example content:
   `php artisan seed --class=DevDatabaseSeeder`)

### Production Deployment

1. `composer install --optimize-autoloader`
2. `php artisan config:cache`
3. `php artisan route:cache`

### Backgrounds tasks

1. Set up a cronjob that runs at least daily: `php artisan schedule:run`
2. Start one queue worker using: `php artisan queue:work`
   (optimally have it monitored and automatically restarted using
   [supervisord](http://supervisord.org/))
   
## Deployment to AWS Elastic Beanstalk

The project contains in the `.ebextensions` directory configuration to easily
deploy the Dienstplan application to AWS Elastic Beanstalk with HTTPS support using
a [Let's Encrypt](https://letsencrypt.org/) certificate and `mod_pagespeed`
enabled on the Apache webserver. When creating a Elastic Beanstalk environment
following instructions from the
[AWS Elastic Beanstalk documentation](https://docs.aws.amazon.com/elasticbeanstalk/latest/dg/GettingStarted.html),
you need to additionally:

1. set the *Document root* to `/public`
2. set the environment property `APP_ENV = aws`
3. configure at least the following environment properties (cmp.
   [.env.aws](.env.aws) and [config](config)):
    - `APP_KEY`
    - `APP_URL`
    - `CERT_DOMAINS`
    - `CERT_EMAIL`
    - `MAIL_DRIVER` (+ further properties depending on the driver, cmp. [Mail config](config/mail.php))
    - `MAIL_FROM_ADDRESS`
    - `MAIL_FROM_NAME`
4. configure the following properties if your database is not part of your
   Elastic Beanstalk environment:
    - `RDS_DB_NAME`
    - `RDS_HOSTNAME`
    - `RDS_PASSWORD`
    - `RDS_PORT`
    - `RDS_USERNAME`
5. upload and deploy a release bundle built with `release.sh <commit>`

## Bugs and Feedback

Please send bug reports and feedback to `futur~DOT~andy~AT~googlemail~DOT~com`
or use the [issue tracker on GitHub](https://github.com/Phoosha/dienstplan/issues).

## License

The Dienstplan application is open-sourced software licensed under the
[GNU Affero General Public License 3.0](LICENSE).

### Incorporated works

The project layout is based on the
[Laravel project v5.5.0](https://github.com/laravel/laravel/tree/v5.5.0),
which is licensed under the [MIT license](http://opensource.org/licenses/MIT).

From the Pure CSS [Responsive Side Menu layout example](https://purecss.io/layouts/)
licensed under the [Yahoo! BSD license](https://github.com/pure-css/pure-site/blob/master/LICENSE.md)
the following files were incorporated:
- `resources/assets/sass/main.scss` originally based on `css/layouts/side-menu.css`
- `resources/assets/js/menu.js` copied from `js/ui.js`