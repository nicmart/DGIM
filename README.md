# The Datar-Gionis-Indyk-Motwani algorithm

[![Build Status](https://travis-ci.org/nicmart/dgim.png?branch=master)](https://travis-ci.org/nicmart/dgim)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/nicmart/dgim/badges/quality-score.png?s=e06818508807c109a8c9354a73fc1a5227426c09)](https://scrutinizer-ci.com/g/nicmart/StringTemplate/)

Given a stream of bits and a window size N, we want be able to answer to the question

**How many 1s appeared in the last k bits?**

Of course the answer is very simple if we are able to store in memory all last N bits.
If it's not the case, we have to use a smarter way to store the data.

The DGIM algorithm allows us to answer the question with a **logarithmic amount
of memory**, and with tunable precision.

More precisely, for a given precision 1/m, the needed amount of memory is **O(m log(N)²)**. 

Just to outline, log(N)² (base 2) is a ridicously low number compared to N, for big N. For example, if N is 80 bilions,
log(N)² is 1311.

In this library you can find a PHP implementation of the algorithm, together with the generalization
to streams of non negative integers. Yeah, PHP is not the proper tool for memory-intensive tasks,
 I've written this library mainly to experiment a bit with the concepts in DGIM.

## Counting 1s
The only component the client need to use is the Counter class. This is the way to use it for counting ones with a max 1% error:
```php
use NicMart\DGIM\Counter;

$precision = 0.01;
$m = (int) (1/$precision) + 1;

$counter = new Counter($windowSize, 1, $m);

for ($i = 0; $i < 100000; $i++) {
    $counter->input(rand(0,1));
}

$onesInLast1000 = $counter->getCount(1000);
$onesInLast10000 = $counter->getCount(10000);
```

## Counting the sum of positive integers
If you are dealing with a stream of positive integers instead of a stream of bits, you can use the counter 
to get the approximate sum of the last $k ints. To do that you need to pass to the Counter the maximum of the integers
you will receive in the stream:

The only component the client need to use is the Counter class. This is the way to use it for counting ones with a max 1% error:
```php
$counter = new Counter($windowSize, $maxIntOfTheStream, $m);

for ($i = 0; $i < 100000; $i++) {
    $counter->input(rand(0,$maxIntOfTheStream));
}

$sumOfLast1000 = $counter->getCount(1000);
$sumOfLast10000 = $counter->getCount(10000);
```

## Command line example
You can test the precision of the library on randomly generated data using the `examples/example.php` file:
```
php example.php           
Test example to check the precision of the algorithm compared to real data.
Usage: php example.php windowsize maxint precision.
Example: php example.php 1000 100 0.1

php example.php 3000 10 0.01
...
N: 1599
Predicted: 7906
Real: 7919
Error: 0%
--------------------
N: 2398
Predicted: 11982
Real: 11988
Error: 0%
--------------------
Average Error: 0
Max Error: 0%
```

## References
You can find a detailed description of the algorithm in section 4.6.2 of the wonderful 
book [Mining of Massive Datasets](http://www.mmds.org), freely available in PDF format.

## Technical notes
Each bucket stores the timestamp and the exponent as php integers. The most memory efficient implementation
should only stores log(N) bits for the timestamp and log(log(N)) bits for the exponent.

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
