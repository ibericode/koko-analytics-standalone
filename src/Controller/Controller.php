<?php

namespace App\Controller;

use App\Security\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

abstract class Controller extends AbstractController {
    protected function render(string $template, array $parameters = [], ?Response $response = null): Response {

        $templateDir = $this->container->get('parameter_bag')->get('template_dir');
        extract($parameters);
        require __DIR__ . '/../template-functions.php';

        ob_start();
        require $templateDir . $template;
        $content = ob_get_clean();

        $response ??= new Response();
        $response->setContent($content);
        return $response;
    }
}
