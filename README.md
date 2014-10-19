# Php library template

This is the basic skeleton I use each time I create a new php library. It includes basic phpunit, travisci and composer configuration,
and a basic folder structure.

There are few template variables I substitute with a "find and replace" each time I initialize the library. This variables are
- DGIM The name of the library
- dgim The sluggified version of library name, used in composer configuration
- Implementation of the DGIM algorithm to count ones in a window A brief descrption of what the library does
- NicMart\DGIM The main library namespace. I usually set this equal to the camelized library name

Other values like library author name and email are hardcoded in the files.

What follows is the skeleton README.md file.

# DGIM
[![Build Status](https://travis-ci.org/nicmart/DGIM.png?branch=master)](https://travis-ci.org/nicmart/DGIM)
[![Coverage Status](https://coveralls.io/repos/nicmart/DGIM/badge.png?branch=master)](https://coveralls.io/r/nicmart/DGIM?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/nicmart/DGIM/badges/quality-score.png?s=e06818508807c109a8c9354a73fc1a5227426c09)](https://scrutinizer-ci.com/g/nicmart/StringTemplate/)

Implementation of the DGIM algorithm to count ones in a window.

## Install

The best way to install DGIM is [through composer](http://getcomposer.org).

Just create a composer.json file for your project:

```JSON
{
    "require": {
        "nicmart/dgim": "~0.1"
    }
}
```

Then you can run these two commands to install it:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install

or simply run `composer install` if you have have already [installed the composer globally](http://getcomposer.org/doc/00-intro.md#globally).

Then you can include the autoloader, and you will have access to the library classes:

```php
<?php
require 'vendor/autoload.php';
```