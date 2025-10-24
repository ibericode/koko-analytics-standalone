<?php

namespace App\Controller;

use App\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller extends AbstractController
{
    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        return new Template($this->container)->render($view, $parameters, $response);
    }
}
