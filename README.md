# The Datar-Gionis-Indyk-Motwani algorithm
Given a stream of bits and a window size `N`, we want be able to answer to the question

*How many 1s appeared in the last `k` bits?*

Of course the answer is very simple if we are able to store in memory all last N bits.
If it's not the case, we have to use a smarter way to store the data.

The DGIM algorithm allows us to answer the question with a logarithmic amount
of memory, and with tunable precision.

More precisely, for a given precision 1/m, the needed amount of memory is O(m log(N)²).

In this library you can find a PHP implementation of the algorithm, together with the generalization
to streams of non negative integers. Yeah, PHP is not the proper tool for memory-intensive tasks,
 I've written this library mainly to experiment a bit with the concepts in DGIM.

## Counting 1s



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
