<?php

namespace App\Controller;

use App\SessionManager;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides the /collect endpoint which ingests data into the temporary buffer file on disk.
 *
 * IMPORTANT: We do not want to connect and/or query the database in this endpoint.
 */
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

        $domain = $request->query->get('d');
        $path = $request->query->get('p');
        $referrer = $request->query->getString('r', '');

        // do nothing if required param is missing
        if ($domain === null || $path === null) {
            return new Response('Bad request', 400, $headers);
        }

        // validate params
        $referrer = $referrer === '' ? '' : \filter_var($referrer, FILTER_VALIDATE_URL);
        if ($referrer === false) {
            return new Response('Bad request', 400, $headers);
        }


        // limit string inputs to a maximum of 255 chars
        $path = \strtolower(\substr($path, 0, 255));
        $referrer = \strtolower(\substr($referrer, 0, 255));
        $domain = \substr($domain, 0, 255);

        // validate domain param
        if (\preg_match('/[^a-zA-Z0-9\.\-]/', $domain)) {
            return new Response('Bad request', 400, $headers);
        }

        $buffer_filename = \dirname(__DIR__, 2) . "/var/buffer-{$domain}";

        // if filename does not exist: domain is invalid
        if (!\is_file($buffer_filename)) {
            return new Response('Bad request', 400, $headers);
        }

        // determine uniqueness of request to this path
        [$new_visitor, $unique_pageview ] = $this->determineUniqueness($request, $path);

        // write to buffer file
        \file_put_contents($buffer_filename, \serialize([$path, $new_visitor, $unique_pageview, $referrer]) . PHP_EOL, FILE_APPEND);

        return new Response('', 200, $headers);
    }

    private function determineUniqueness(Request $request, string $path): array {
        $session_manager = new SessionManager;
        $user_agent = $request->headers->get('User-Agent', '');
        $ip_address = $request->getClientIp();

        $id = $session_manager->generateId($user_agent, $ip_address);
        $pages_visited = $session_manager->getVisitedPages($id);
        $new_visitor = \count($pages_visited) === 0 ? 1 : 0;
        $unique_pageview = \in_array($path, $pages_visited, true) ? 0 : 1;

        if ($unique_pageview) {
            $session_manager->addVisitedPage($id, $path);
        }

        return [$new_visitor, $unique_pageview];
    }

}
