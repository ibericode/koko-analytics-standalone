<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

touch(dirname(__DIR__) . "/var/buffer-website.com");

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}
