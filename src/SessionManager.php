<?php

namespace App;

use App\Entity\Domain;

/**
 * @package koko-analytics
 *
 * SessionCleaner removes all files from /var/sessions which are older than 6 hours.
 *
 * This ensures that a visitor that did not visit any new paths within this 6 hour window is treated as a new visitor and/or unique pageview.
 *
 * TODO: Each domain should get its own directory, because each domain can have a different timezone.
 */
class SessionManager
{
    public function generateId(string $domain, string $user_agent, string $ip_address): string
    {
        $seed = $this->getSeed();
        return \hash("xxh64", "{$seed}-{$domain}-{$user_agent}-{$ip_address}", false);
    }

    public function purge(Domain $domain): void
    {
        $session_directory = $this->getStorageDirectory();
        $midnight = (new \DateTimeImmutable('today, midnight', new \DateTimeZone($domain->getTimezone())))->getTimestamp();

        // clean all session files older than 6 hours
        $files = \scandir("{$session_directory}", SCANDIR_SORT_NONE);
        $ignored_files = [".", "..", "seed.txt"];
        foreach ($files as $filename) {
            if (in_array($filename, $ignored_files)) {
                continue;
            }

            $filename = "{$session_directory}/$filename";
            if (\filemtime($filename) < $midnight) {
                \unlink($filename);
            }
        }

        // rotate seed for hashing every night at midnight
        $seed_filename = $this->getSeedFilename();
        if (!\is_file($seed_filename) || \filemtime($seed_filename) < $midnight) {
            $this->rotateSeed();
        }
    }

    public function getStorageDirectory(): string
    {
        $session_directory = \dirname(__DIR__, 1) . '/var/sessions';
        if (!\is_dir($session_directory)) {
            \mkdir($session_directory, 0755);
        }

        return $session_directory;
    }

    public function getSeed(): string
    {
        $filename = $this->getSeedFilename();
        if (!\is_file($filename)) {
            $this->rotateSeed();
        }

        return \file_get_contents($filename);
    }

    public function getSeedFilename(): string
    {
        $session_directory = $this->getStorageDirectory();
        return "{$session_directory}/seed.txt";
    }

    public function rotateSeed(): void
    {
        $seed = \bin2hex(\random_bytes(16));
        \file_put_contents($this->getSeedFilename(), $seed);
    }

    public function getVisitedPages(string $id): array
    {
        $session_filename = "{$this->getStorageDirectory()}/$id";
        if (! \is_file($session_filename)) {
            return [];
        }

        if (\filemtime($session_filename) < \time() - 6 * 3600) {
            \unlink($session_filename);
            return [];
        }

        return \file($session_filename, FILE_IGNORE_NEW_LINES);
    }

    public function addVisitedPage(string $id, string $page): void
    {
        $session_filename = "{$this->getStorageDirectory()}/$id";
        \file_put_contents($session_filename, $page . PHP_EOL, FILE_APPEND);
    }
}
