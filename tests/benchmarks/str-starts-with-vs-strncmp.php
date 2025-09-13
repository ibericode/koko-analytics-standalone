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

bench([
    'str_starts_with' => function () {
        return str_starts_with("App\\Controllers\\ApiController", "App\\");
    },
    'strncmp' => function () {
        return strncmp("App\\Controllers\\ApiController", "App\\", strlen("App\\")) === 0;
    },
    'substr' => function () {
        return substr("App\\Controllers\\ApiController", 0, strlen("App\\")) === "App\\";
    },
    'strpos' => function () {
        return strpos("App\\Controllers\\ApiController", "App\\") === 0;
    },
]);
