<?php

namespace App\Controller;

use App\Security\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

abstract class Controller extends AbstractController
{
    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        \extract($parameters);
        require_once \dirname(__DIR__) . '/template-functions.php';

        \ob_start();
        require \dirname(__DIR__, 2) . "/templates/{$view}";
        $content = \ob_get_clean();

        $response ??= new Response();
        $response->setContent($content);
        return $response;
    }
}
