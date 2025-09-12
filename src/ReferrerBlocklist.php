<?php

namespace App;

class ReferrerBlocklist
{
    public function getFilename(): string
    {
        return dirname(__DIR__) . '/var/blocklist.txt';
    }

    public function update(bool $force = false): bool
    {
        $filename = $this->getFilename();

        // only update once per day unless $force is true
        if (!$force && is_file($filename) && filemtime($filename) > time() - 24 * 60 * 60) {
            return false;
        }

        $blocklist = file_get_contents("https://raw.githubusercontent.com/matomo-org/referrer-spam-blacklist/master/spammers.txt");
        if (!$blocklist) {
            throw new \Exception("Error downloading blocklist");
        }

        if (!file_put_contents($this->getFilename(), $blocklist)) {
            throw new \Exception("Error writing blocklist to file");
        }

        return true;
    }

    public function read(): array
    {
        $filename = $this->getFilename();
        if (!is_file($filename)) {
            return [];
        }

        return \file($filename, FILE_IGNORE_NEW_LINES) ?: [];
    }
}
