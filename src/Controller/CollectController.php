<?php

namespace App\Controller;

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
        // TODO: We can get rid of this check by creating dir separately (ie during app seed)
        $session_directory = \dirname(__DIR__, 2) . '/var/sessions';
        if (!\is_dir($session_directory)) {
            \mkdir($session_directory, 0755);
        }

        $user_agent = $request->headers->get('User-Agent', '');
        $ip_address = $request->getClientIp();
        $seed = @file_get_contents("$session_directory/seed.txt") ?: '';
        $id = \hash("xxh64", "{$seed}-{$user_agent}-{$ip_address}", false);

        $session_filename = "{$session_directory}/{$id}";

        // if file does not yet exist or is old, treat as new visitor and unique pageview
        // we only have to write path to the file (making sure not to append)
        if (! \is_file($session_filename) || \filemtime($session_filename) < \time() - 6*3600) {
            \file_put_contents($session_filename, $path . PHP_EOL);
            return [true, true];
        }

        $pages_visited = \file($session_filename, FILE_IGNORE_NEW_LINES);
        $new_visitor = \count($pages_visited) === 0 ? 1 : 0;
        $unique_pageview = \in_array($path, $pages_visited, true) ? 0 : 1;

        // write path to session file
        if ($unique_pageview) {
            \file_put_contents($session_filename, $path . PHP_EOL, FILE_APPEND);
        }

        return [$new_visitor, $unique_pageview];
    }

}
