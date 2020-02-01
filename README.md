# The /v/idya Gaem Awards

This repository contains all the files and database schema used to run the [Vidya Gaem Awards](https://vidyagaemawards.com) website.

The /v/GAs are a community project, and in keeping with this spirit the codebase is available
under the [MIT License](https://opensource.org/licenses/MIT).

## Requirements

 * PHP 7.3
 * A MySQL or MariaDB database. (An SQLite database is sufficient for local development.) 
 * [Composer](https://getcomposer.org/)

## Getting started

 1. Clone the repo to a server of your choice.
 2. Copy `.env.dist` to `.env` and adjust as needed.
    * You will need a Steam Web API key for the login to work. You can get one from [here](https://steamcommunity.com/dev/apikey).
    * Leave the value of `DYNAMIC_TEMPLATES` as `false` until the database has been initalized.
 3. Run `composer install`.
 4. Run `bin/console app:init-db` to set up the database.

### Local development

While developing, you can use PHP's built in web server to serve the website by running the following:

`php -S localhost:8080 -t public`

This will allow you to access the website at http://localhost:8080.
