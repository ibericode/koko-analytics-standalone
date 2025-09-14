<?php

namespace App;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Template
{
    public function __construct(
        protected ContainerInterface $container,
    ) {
    }

    public function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        \ob_start();
        $this->partial($view, $parameters);
        $content = \ob_get_clean();

        $response ??= new Response();
        $response->setContent($content);
        return $response;
    }

    protected function partial(string $view, array $parameters = []): void
    {
        \extract($parameters);
        require \dirname(__DIR__, 1) . "/templates/{$view}";
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @see UrlGeneratorInterface
     */
    protected function generateUrl(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    protected function getFlashMessages(): array
    {
        return $this->container->get('request_stack')->getSession()->getFlashBag()->all();
    }


    protected function e(string $value): void
    {
        if (str_starts_with($value, 'javascript:')) {
            $value = substr($value, strlen('javascript:'));
        }

        echo \htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
