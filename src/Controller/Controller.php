<?php

namespace App\Controller;

use App\Security\User;
use App\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

abstract class Controller extends AbstractController
{
    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        return new Template($this->container)->render($view, $parameters, $response);
    }
}
