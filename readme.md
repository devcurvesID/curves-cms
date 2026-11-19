<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## Running Curves locally

This guide explains how to run the Curves CMS on Windows, macOS, or Linux. The project uses Laravel 9 and PostgreSQL. Run commands from a terminal in the project root unless instructed otherwise.

### 1. Install the prerequisites

Install Git, PHP, Composer, and PostgreSQL. You can use pgAdmin to create and restore databases through a graphical interface.

- PHP 8.1 or later, as specified in `composer.json`. The project's local environment uses PHP 8.2.
- Composer to install dependencies from `composer.lock`.
- PostgreSQL for the local database.
- PHP extensions `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `dom`, `xml`, `zip`, and `gd`. Composer checks additional extension requirements from the dependencies.

Check your installation from the terminal. On Windows, add the executable locations to `PATH` so the commands are available.

```bash
git --version
php -v
composer --version
psql --version
php -m
```

### 2. Clone the repository

Replace `<REPOSITORY_URL>` and `<BRANCH_NAME>` with the repository URL and branch you want to use. Use a Git account with access to the repository.

```bash
git clone <REPOSITORY_URL> curves
cd curves
git checkout <BRANCH_NAME>
```

### 3. Configure the local environment

Create a `.env` file in the project root using your editor. The repository reviewed for this guide does not include `.env.example`. Start with the configuration below and update the PostgreSQL username and password to match your local installation.

```dotenv
APP_NAME=Curves
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=curves_local
DB_USERNAME=postgres
DB_PASSWORD=your_local_postgres_password

SESSION_DRIVER=file
SESSION_SECURE_COOKIE=false
CACHE_DRIVER=file
QUEUE_DRIVER=sync
BROADCAST_DRIVER=log
FILESYSTEM_DRIVER=local

MAIL_MAILER=log
MAIL_DRIVER=log

CURVES_BACKEND_API_ENABLED=false
CURVES_BACKEND_API_URL=
```

This configuration uses a local database and disables outgoing mobile-curves requests, including EZYPAY. Keep `.env` as your computer's private configuration. Obtain a development database backup from the project maintainer for the restore steps below.

### 4. Install PHP dependencies

```bash
composer install --prefer-dist
composer check-platform-reqs
php artisan key:generate
```

`key:generate` sets `APP_KEY` for the local installation. If Composer reports a missing extension, enable it in your PHP CLI configuration and run the dependency installation again.

### 5. Create the PostgreSQL database

Make sure PostgreSQL is running. Use the pgAdmin Query Tool or `psql` to run the following SQL while connected to the `postgres` database.

```sql
CREATE DATABASE curves_local;
```

Check that `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env` match your local PostgreSQL database and account.

### 6. Restore the project database

Use a development backup containing the schema, master data, views, login accounts, and migration records. The application uses views such as `usermobilecurves_v` and master data for clubs, payment methods, and accounting accounts.

For a plain SQL backup, run the following command and adjust the file path.

```bash
psql -h 127.0.0.1 -U postgres -d curves_local -f backup.sql
```

For a PostgreSQL custom-format backup, use the following command.

```bash
pg_restore --no-owner --no-privileges -h 127.0.0.1 -U postgres -d curves_local backup.dump
```

In pgAdmin, select the `curves_local` database, open **Restore**, and choose the custom-format backup. Plain SQL files can be run through the Query Tool. Wait for the restore to finish and review any errors before continuing.

### 7. Apply pending migrations

After restoring the database, apply migrations that are missing from the backup.

```bash
php artisan optimize:clear
php artisan migrate
php artisan migrate:status
```

Check that the latest migrations show `Ran`, including the migrations for installment checkout columns and the EZYPAY renewal context. If an undefined-column error occurs, check the migration status and confirm the application is connected to `curves_local`.

### 8. Set up file storage

```bash
php artisan storage:link
```

Make sure the PHP process can write to `storage` and `bootstrap/cache`. On Windows, creating symbolic links may require Developer Mode or a terminal with administrator privileges.

To display existing member photos, product images, or receipts, copy the required uploaded files from the development environment into the corresponding storage folders. The database contains references to these files.

### 9. Start the application and log in

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Open the [local Curves dashboard](http://127.0.0.1:8000/cf-lite/dashboard). Log in using a development account from the restored database. Keep the server terminal running while using the application.

### 10. Inspect application logs

On macOS or Linux, open another terminal in the project folder.

```bash
tail -n 100 -F storage/logs/laravel.log
```

On Windows PowerShell, use the following command.

```powershell
Get-Content storage/logs/laravel.log -Tail 100 -Wait
```

If login repeatedly returns to the login page, check `APP_URL`, session folder permissions, and `SESSION_SECURE_COOKIE=false` for local HTTP access. After editing `.env` during development, reload the configuration with the following command.

```bash
php artisan optimize:clear
```

### Build frontend assets

When rebuilding JavaScript or CSS, first check Node.js compatibility with the project's dependencies. `package.json` uses Laravel Mix 1 and older frontend dependencies. The build workflow on a fresh installation has not been verified.

The repository provides the following build commands.

```bash
npm install
npm run dev
```

### Enable mobile-curves and EZYPAY for testing

For integration testing, set the mobile-curves development or sandbox server URL and enable the following settings in `.env`.

```dotenv
CURVES_BACKEND_API_ENABLED=true
CURVES_BACKEND_API_URL=https://mobile-curves-development.example.com
```

Then run `php artisan optimize:clear`. When enabled, the application sends requests to the configured server. When set to `false`, synchronization is skipped and EZYPAY is filtered out of payment dropdowns. Skipped synchronization must be run again if you want to send that data later.

This guide is based on the repository configuration. Verify cloning, database restoration, and login on each new local environment.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb combination of simplicity, elegance, and innovation give you tools you need to build any application with which you are tasked.

## Learning Laravel

Laravel has the most extensive and thorough documentation and video tutorial library of any modern web application framework. The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it a breeze to get started learning the framework.

If you're not in the mood to read, [Laracasts](https://laracasts.com) contains over 900 video tutorials on a range of topics including Laravel, modern PHP, unit testing, JavaScript, and more. Boost the skill level of yourself and your entire team by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for helping fund on-going Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](http://patreon.com/taylorotwell):

- **[Vehikl](http://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[British Software Development](https://www.britishsoftware.co)**
- **[Styde](https://styde.net)**
- [Fragrantica](https://www.fragrantica.com)
- [SOFTonSOFA](https://softonsofa.com/)

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
