<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

$GLOBALS['time_app_start'] = microtime(true);

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
