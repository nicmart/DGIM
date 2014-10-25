<?php
/**
 * This file is part of library-template
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Nicolò Martini <nicmartnic@gmail.com>
 */

use NicMart\DGIM\Counter;

include "../vendor/autoload.php";

if ($argc < 2) {
    echo "Test example to check the precision of the algorithm compared to real data." . PHP_EOL;
    echo "Usage: php example.php windowsize maxint precision." . PHP_EOL;
    echo "Example: php example.php 1000 100 0.1" . PHP_EOL;
    return;
}

$size = isset($argv[1]) ? $argv[1] : 1000;
$maxInt = isset($argv[2]) ? $argv[2] : 100;
$maxError = isset($argv[3]) ? $argv[3] : 0.5;
list($ary, $counter) = randomCounterAndAry($size, $maxInt, (int)(1/$maxError) + 1);

$errors = array();
for ($n = 1; $n < $size; $n = max($n+1, (int) ($n * 1.5))) {
    $expected = $counter->getCount($n);
    $real = array_sum(array_slice($ary, -$n));
    $error = $real ? round((abs($real - $expected) / $real) * 100) : 0;
    $errors[] = $error;
    // if ($error < 30) continue;
    echo "N: " . $n . PHP_EOL;
    echo "Predicted: " . $expected . PHP_EOL;
    echo "Real: " . $real . PHP_EOL;
    echo "Error: $error%" . PHP_EOL;
    echo str_repeat("-", 20) . PHP_EOL;
}
echo "Average Error: " . array_sum($errors) / count($errors) . PHP_EOL;
echo "Max Error: " . max($errors);


function randomCounterAndAry($windowSize, $maxInt, $maxForSameSize)
{
    $counter = new Counter($windowSize, $maxInt, $maxForSameSize);
    $ary = [];

    for ($i = 0; $i < $windowSize; $i++) {
        $rand = rand(0, $maxInt);
        $ary[] = $rand;
        $counter->input($rand);
    }

    return [$ary, $counter];
}