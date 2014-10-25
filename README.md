# The Datar-Gionis-Indyk-Motwani algorithm
Given a stream of bits and a window size `N`, we want be able to answer to the question

How many 1s appeared in the last `k` bits? 


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