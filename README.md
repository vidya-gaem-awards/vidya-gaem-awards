# The /v/idya Gaem Awards

This repository contains all the files and database schema used to run the [Vidya Gaem Awards](https://vidyagaemawards.com) website.

The /v/GAs are a community project, and in keeping with this spirit the codebase is available
under the [MIT License](https://opensource.org/licenses/MIT).

## Requirements

 * PHP 8.1
 * A MySQL or MariaDB database. (An SQLite database is sufficient for local development.) 
 * [Composer](https://getcomposer.org/)
 * NodeJS
 * Yarn package manager

## Getting started

 1. Clone the repo to a server of your choice.
 2. Copy `.env` to `.env.local` and adjust as needed.
    * You will need a Steam Web API key for the login to work. You can get one from [here](https://steamcommunity.com/dev/apikey).
    * Update the value of `APP_SECRET` to a randomly generated string.
    * Leave the value of `DYNAMIC_TEMPLATES` as `false` until the database has been initalized.
 3. Run `composer install`.
 4. Run `php bin/console app:init-db` to set up the database.
 5. Run `yarn build` (for production) or `yarn watch` (for development).

## Local development

While developing, you can use PHP's built in web server to serve the website by running the following:

`php -S localhost:8080 -t public`

This will allow you to access the website at http://localhost:8080.

### Generating results

Running this command will generate the results for all awards:

`php bin/console app:results`

Keep in mind that only votes with a voting code are included in the final results. Go to `/vote/code` to get a valid
voting link.

In production, it's a good idea to add this command to a cron job or a scheduled task (depending on your operating
system). If you're running the site on a machine with `crontab` available, you can set `CRON_JOB_MANAGEMENT=true` in
`.env` which will allow you to control whether it's running or not from the frontend (`/config/cron`).
