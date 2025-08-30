<?php

use Symfony\Component\Dotenv\Dotenv;

$GLOBALS['time_app_start'] = microtime(true);

require dirname(__DIR__) . '/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

touch(dirname(__DIR__) . "/var/buffer-website.com");

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}
