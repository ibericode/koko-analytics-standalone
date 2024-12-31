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
        $new_visitor = $request->query->get('v');
        $unique_pageview = $request->query->get('pv');
        $referrer = $request->query->getString('r', '');

        // do nothing if required param is missing
        if ($path === null || $new_visitor === null || $unique_pageview === null) {
            return new Response('Bad request', 400, $headers);
        }

        // validate params
        $new_visitor = \filter_var($new_visitor, FILTER_VALIDATE_INT);
        $unique_pageview = \filter_var($unique_pageview, FILTER_VALIDATE_INT);
        $referrer = $referrer === '' ? '' : \filter_var($referrer, FILTER_VALIDATE_URL);
        if ($new_visitor === false || $unique_pageview === false || $referrer === false) {
            return new Response('Bad request', 400, $headers);
        }

        // limit path and referrer URL to a maximum of 255 chars
        $path = strtolower(substr($path, 0, 255));
        $referrer = strtolower(substr($referrer, 0, 255));

        // write to buffer file
        // TODO: Get projectRootDir() from Kernel instead of using a relative path here
        \file_put_contents(__DIR__ . '/../../var/buffer.json', json_encode([$path, $new_visitor, $unique_pageview, $referrer]) . PHP_EOL, FILE_APPEND);

        return new Response('', 200, $headers);
    }
}
