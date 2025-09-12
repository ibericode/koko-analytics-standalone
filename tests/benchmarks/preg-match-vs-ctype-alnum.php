<?php

$string = "website-url.com";

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

        $results[] = [$name, $time * 1e6]; // = [$name, $time * 1e6];
    }

    usort($results, function ($a, $b) {
        return $a[1] > $b[1];
    });

    foreach ($results as [$name, $time]) {
        echo sprintf("%-16s\t%.2f μs / it\n", $name, $time);
    }
}

bench([
    "preg_replace" => function () use ($string) {
        return ! preg_match('/[^a-zA-Z0-9\.\-]/', $string);
    },
    "strtr + ctype_alnum" => function () use ($string) {
        return ctype_alnum(strtr($string, ["-" => "0", "." => "0"]));
    },
    "strspn" => function () use ($string) {
        return strspn($string, "abcdefghijklmnopqrstuvwxyz0123456789-.") == strlen($string);
    }
]);
