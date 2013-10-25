# The /v/idya Gaem Awards

This repository contains all the files and database schema used to run the [2012 /v/GAs](http://2012.vidyagaemawards.com).

**We strongly recommend not using the site as-is in production. Good practices are not adhered to at all. You should instead take bits and pieces of the code as needed.** You've been warned.

## What's the deal

We planned to clean up the code before open sourcing it, but that didn't happen and we figured something is better than nothing. So here it is! Much of it was hacked together quickly during late night coding binges, although some of it is actually quite presentable.

There is essentially no support for this software and this page is the only documentation, so you're on your own. Don't bother submitting issues, it's unlikely they'll ever be fixed. If you want help with something, email [clamburger@vidyagaemawards.com](mailto:clamburger@vidyagaemawards.com) and you *might* get a response.

## Things you need to change

 * You need to add database information in **includes/php.php** (lines 14-20). Each of the files in the **scripts** folder also requires database information (location varies, but all within lines 10-12).
 * While you're in **php.php**, you also need to add your domain to line 27 and your Steam API key to line 25.
 * You should change the email addresses in **controllers/volunteer-submission.php**.
 * If you use **scripts/numbers.php**, you'll likely want to change the domain in line 55.
 * You'll also want to change the domain in **index.php** on line 36 for cookies to work.
 * The domain is mentioned tons of times in the template files.

## Things you need to know

 * **database_schema.sql** contains the MySQL table definitions. **database_data.sql** contains some sample data.
 * We use the deprecated MySQL extension in some places, but the new MySQLi library in others.
 * bTemplate is literally over 10 years old, so I highly recommend tearing it out and replacing it with Twig or something.

## Licensing

In the spirit of /v/, you can pretty much do whatever you want with what we made.

We'd appreciate it if you at least made an effort to make the frontend look a bit different, but if you don't, whatever.

 * All PHP and Javascript files are licensed under the [MIT License](http://opensource.org/licenses/MIT).
 * All the .tpl files and images are licensed under [Creative Commons Attribution 3.0](http://creativecommons.org/licenses/by/3.0/deed.en_GB).

Because we're lazy/efficient we also used a bunch of code that other people had already written, which are listed here:

 * We use [Bootstrap](http://getbootstrap.com/) which uses the [Apache License](https://github.com/twbs/bootstrap/blob/master/LICENSE).
 * We use [jQuery](https://jquery.org/), [bTemplate](http://www.massassi.com/bTemplate/) and [this thing](http://forums.steampowered.com/forums/showthread.php?t=1430511) which use the [MIT License](http://opensource.org/licenses/MIT).
 * We use [Highcharts](http://www.highcharts.com/products/highcharts) which is licensed under [Creative Commons Attribution-NonCommercial 3.0](http://shop.highsoft.com/highcharts.html)

There are a bunch of images and fonts with uncertain licensing lying around the directory structure. You probably shouldn't use these if you want to be totes-legit.