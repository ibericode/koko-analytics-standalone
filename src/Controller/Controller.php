<?php

namespace App\Controller;

use App\Entity\User;
use App\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

abstract class Controller extends AbstractController
{
    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        return new Template($this->container)->render($view, $parameters, $response);
    }

    protected function getAuthenticatedUser(): ?User
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $session = $request->getSession();
        return $session->get('user');
    }
}
