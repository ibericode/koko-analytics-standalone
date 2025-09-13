<?php


function bench(array $functions, int $n = 1000)
{
    $results = [];

    foreach ($functions as $name => $fn) {
        $time_start = microtime(true);
        for ($i = 0; $i < $n; $i++) {
            $result = $fn();
        }
        $time = (microtime(true) - $time_start) / $n;
        $result or throw new Exception("Incorrect result");

        $results[] = [$name, $time * 1e6];
    }

    usort($results, function ($a, $b) {
        return $a[1] > $b[1];
    });

    foreach ($results as [$name, $time]) {
        echo sprintf("%-16s\t%.2f μs / it\n", $name, $time);
    }
}


$path = '/contact/?p=100&utm_source=foobar%20barfoo+hello';
// [a-zA-Z0-9-\/\#\&\?\=\%]
bench([
    '[a-zA-Z0-9\-\/\#\&\?\=\%]+' => function () use ($path) {
        return 1 === preg_match("/[a-zA-Z0-9\-\/\#\&\?\=\%]+/", $path);
    },
    '[^a-zA-Z0-9\-\/\#\&\?\=\%]' => function () use ($path) {
        return 0 === preg_match("/[^a-zA-Z0-9\-\/\#\&\?\=\%\_\+]/", $path);
    },
]);
