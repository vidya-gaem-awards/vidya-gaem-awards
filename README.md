# The /v/idya Gaem Awards

This repository contains all the files and database schema used to run the [Vidya Gaem Awards](https://vidyagaemawards.com) website.

## What's the deal

The primary goal in open-sourcing the website is to provide some transparency into how the /v/GAs are run.

## Using this software

Although there's nothing stopping you from reusing this code for an award show of your own
(the codebase is licensed under the [MIT License](https://opensource.org/licenses/MIT)),
it's been designed specifically for the /v/GAs and is likely more effort than it's worth to
use anywhere else.

In the extremely unlikely event that you decide to use it anyway, here's what you need:

### Requirements

 * PHP 7
 * A MySQL or MariaDB database
 * [Composer](https://getcomposer.org/)

### Installation

 * Clone the repo to a server of your choice and run `composer install`.
 * There's a file called `config.php.example`. You'll want to copy this to `config.php` and adjust as needed.
 * Once you've got a database created, run `vendor/bin/doctrine orm:schema-tool:update` to create the tables.    
