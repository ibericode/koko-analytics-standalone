<?php

use Symfony\Component\Dotenv\Dotenv;

$GLOBALS['time_app_start'] = microtime(true);

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

// create buffer file for default domain
// so that requests to /collect are accepted
touch(dirname(__DIR__) . "/var/buffer-website.com");

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}
