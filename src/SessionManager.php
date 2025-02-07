<?php

namespace App;

/**
 * @package koko-analytics
 *
 * SessionCleaner removes all files from /var/sessions which are older than 6 hours.
 *
 * This ensures that a visitor that did not visit any new paths within this 6 hour window is treated as a new visitor and/or unique pageview.
 */
class SessionManager
{
    public function generateId(string $user_agent, string $ip_address): string
    {
        $seed = $this->getSeed();
        return \hash("xxh64", "{$seed}-{$user_agent}-{$ip_address}", false);
    }

    public function purge(): void
    {
        $session_directory = $this->getStorageDirectory();

        // clean all session files older than 6 hours
        $files = \glob("{$session_directory}/*");
        $cutoff_time =  \time() - (6 * 3600);
        foreach ($files as $filename) {
            if ($filename === "seed.txt") {
                continue;
            }

            if (\filemtime($filename) < $cutoff_time) {
                \unlink($filename);
            }
        }

        // rotate seed for hashing every 24 hours
        $seed_filename = $this->getSeedFilename();
        if (!\is_file($seed_filename) || \filemtime($seed_filename) < (\time() - (24 * 3600))) {
            $this->rotateSeed();
        }
    }

    public function getStorageDirectory(): string
    {
        $session_directory = \dirname(__DIR__, 1) . '/var/sessions';
        if (!\is_dir($session_directory)) {
            \mkdir($session_directory, 0770);
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
