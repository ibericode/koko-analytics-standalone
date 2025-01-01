<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CollectController
{
    #[Route('/collect', name: 'app_collect', methods: ['POST', 'GET'])]
    public function collect(Request $request): Response
    {
        $headers = [
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-cache, must-revalidate, max-age=0'
        ];

        // do nothing if empty user-agent or looks like bot/crawler/spider
        $user_agent = $request->headers->get('User-Agent', '');
        if (!$user_agent || \preg_match("/bot|crawl|spider|seo|lighthouse|facebookexternalhit|preview/", \strtolower($user_agent))) {
            return new Response('', 200, $headers);
        }

        $path = $request->query->get('p');
        $referrer = $request->query->getString('r', '');

        // do nothing if required param is missing
        if ($path === null) {
            return new Response('Bad request', 400, $headers);
        }

        // validate params
        $referrer = $referrer === '' ? '' : \filter_var($referrer, FILTER_VALIDATE_URL);
        if ($referrer === false) {
            return new Response('Bad request', 400, $headers);
        }

        // limit path and referrer URL to a maximum of 255 chars
        $path = \strtolower(\substr($path, 0, 255));
        $referrer = \strtolower(\substr($referrer, 0, 255));

        // determine uniqueness of request to this path
        [$new_visitor, $unique_pageview ] = $this->determineUniqueness($request, $path);

        // write to buffer file
        \file_put_contents(\dirname(__DIR__, 2) . '/var/buffer', \serialize([$path, $new_visitor, $unique_pageview, $referrer]) . PHP_EOL, FILE_APPEND);

        return new Response('', 200, $headers);
    }

    private function determineUniqueness(Request $request, string $path): array {
        $user_agent = $request->headers->get('User-Agent', '');
        $ip_address = $request->getClientIp();
        $id = \hash("xxh64", "{$user_agent}-{$ip_address}", false);
        $filename = \dirname(__DIR__, 2) . "/var/sessions/{$id}";

        // if file does not yet exist or is old, treat as new visitor and unique pageview
        // we only have to write path to the file (making sure not to append)
        if (! \is_file($filename) || \filemtime($filename) < \time() - 6*3600) {
            \file_put_contents($filename, $path . PHP_EOL);
            return [true, true];
        }

        $pages_visited = \file($filename, FILE_IGNORE_NEW_LINES);
        $new_visitor = count($pages_visited) === 0 ? 1 : 0;
        $unique_pageview = in_array($path, $pages_visited, true) ? 0 : 1;

        // write path to session file
        if ($unique_pageview) {
            \file_put_contents($filename, $path . PHP_EOL, FILE_APPEND);
        }

        return [$new_visitor, $unique_pageview];
    }

}
