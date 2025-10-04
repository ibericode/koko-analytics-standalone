<?php

function bench(array $functions, int $n = 100000)
{
    $results = [];

    foreach ($functions as $name => $fn) {
        $time_start = microtime(true);
        for ($i = 0; $i < $n; $i++) {
            $result = $fn();
        }
        $time = (microtime(true) - $time_start) / $n;

        $results[] = [$name, $time * 1e6];
    }

    usort($results, function ($a, $b) {
        return $a[1] > $b[1];
    });

    foreach ($results as [$name, $time]) {
        echo sprintf("%-16s\t%.2f μs / it\n", $name, $time);
    }
}

$first_match = '.';
$last_match = 'seed.txt';
$no_match = bin2hex(random_bytes(16));

bench([
    'in_array-first' => function () use ($first_match, $last_match, $no_match) {
        return in_array($first_match, ['.', '..', 'seed.txt']);
    },
    'in_array-last' => function () use ($first_match, $last_match, $no_match) {
        return in_array($last_match, ['.', '..', 'seed.txt']);
    },
    'in_array-no' => function () use ($first_match, $last_match, $no_match) {
        return in_array($no_match, ['.', '..', 'seed.txt']);
    },
    'multiple-if-first' => function () use ($first_match, $last_match, $no_match) {
        return $first_match == '.' || $first_match == '..' || $first_match == 'seed.txt';
    },
    'multiple-if-last' => function () use ($first_match, $last_match, $no_match) {
        return $last_match == '.' || $last_match == '..' || $last_match == 'seed.txt';
    },
    'multiple-if-no' => function () use ($first_match, $last_match, $no_match) {
        return $no_match == '.' || $no_match == '..' || $no_match == 'seed.txt';
    },
]);
