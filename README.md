# The /v/idya Gaem Awards

This repository contains all the files and database schema used to run the [2012 /v/GAs](https://2012.vidyagaemawards.com).

**We strongly recommend not using the site as-is in production. Good practices are not adhered to at all. You should instead take bits and pieces of the code as needed.** You've been warned.

## What's the deal

We planned to clean up the code before open sourcing it, but that didn't happen and we figured something is better than nothing. So here it is! Much of it was hacked together quickly during late night coding binges, although some of it is actually quite presentable.

There is essentially no support for this software and this page is the only documentation. It's currently being improved for the 2013 /v/GAs, so things may or may not get better over time.

## Things you need to change

 * There's a file called **config.php.example** in the **includes** directory. You'll want to copy this to **config.php** and fill it in.
 * You'll likely want to make changes to the domains in **script/numbers.php**, since they are specific to the /v/GAs.

## Things you need to know

 * **database_schema.sql** contains the MySQL table definitions. **database_data.sql** contains some sample data.
 * We use the deprecated MySQL extension in some places, but the new MySQLi library in others.
 * bTemplate is literally over 10 years old, so I highly recommend tearing it out and replacing it with Twig or something.

## Licensing

In the spirit of /v/, you can pretty much do whatever you want with what we made.

We'd appreciate it if you at least made an effort to make the frontend look a bit different, but if you don't, whatever.

 * All PHP and Javascript files are licensed under the [MIT License](https://opensource.org/licenses/MIT).
 * All the HTML and images are licensed under [Creative Commons Attribution 3.0](https://creativecommons.org/licenses/by/3.0/deed.en_GB).

Because we're lazy/efficient we also used a bunch of code that other people had already written, which are listed here:

 * We use [Bootstrap](https://getbootstrap.com/) which uses the [Apache License](https://github.com/twbs/bootstrap/blob/master/LICENSE).
 * We use [jQuery](https://jquery.org/), [bTemplate](http://www.massassi.com/bTemplate/) and [this thing](http://forums.steampowered.com/forums/showthread.php?t=1430511) which use the [MIT License](https://opensource.org/licenses/MIT).

There are a bunch of images and fonts with uncertain licensing lying around the directory structure. You probably shouldn't use these if you want to be fully legit.
