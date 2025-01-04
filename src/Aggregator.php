<?php

namespace App;

use App\Entity\Domain;
use App\Entity\PageStats;
use App\Entity\ReferrerStats;
use App\Entity\SiteStats;
use App\Repository\StatRepository;
use DateTimeImmutable;
use Exception;

class Aggregator {

    protected SiteStats $site_stats;

    /** @var PageStats[] $page_stats */
    protected array $page_stats = [];

    /** @var ReferrerStats[] $referrer_stats */
    protected array $referrer_stats = [];
    protected Domain $domain;

    public function __construct(
        protected Database $db,
        protected StatRepository $statRepository,
    ) {
        $this->site_stats = new SiteStats;
        $this->site_stats->date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function run(Domain $domain): void
    {
        $this->domain = $domain;

        $filename = \dirname(__DIR__) . "/var/buffer-{$this->domain->getName()}";
        if (!\is_file($filename)) {
            // buffer file for this domain does not exist, meaning no new data since last aggregation
            // we still create the file, because we use this to validate domain on /collect requests
            \touch($filename);
            return;
        }

        // rename file to something temporary
        $tmp_filename = $filename . '-' . \time();
        $renamed = \rename($filename, $tmp_filename);
        if (!$renamed) throw new Exception("Error renaming buffer file");

        // put empty file into place
        \touch($filename);

        $fh = \fopen($tmp_filename, 'r');
        if (!$fh) throw new Exception("Error opening buffer file for reading");

        // read file line by line
        while (($line = \fgets($fh, 1024)) !== false) {
            $line = \trim($line);

            // skip empty line
            if ($line === '') {
                continue;
            }

            $data = \unserialize($line);
            $this->addData($data);
        }

        // close file & remove it from filesystem
        \fclose($fh);
        \unlink($tmp_filename);

        $this->commit();
    }

    private function addData(array $data): void
    {
        [$path, $new_visitor, $unique_pageview, $referrer_url] = $data;

        // if referrer is on blocklist, ignore entire line
        if ($this->isReferrerUrlOnBlocklist($referrer_url)) {
            return;
        }

        // increment site stats
        $this->site_stats->pageviews++;
        $this->site_stats->visitors += $new_visitor ? 1 : 0;

        // increment page stats
        if (!isset($this->page_stats[$path])) {
            $this->page_stats[$path] = new PageStats;
            $this->page_stats[$path]->date = $this->site_stats->date;
            $this->page_stats[$path]->url = $path;
        }
        $this->page_stats[$path]->pageviews++;
        $this->page_stats[$path]->visitors += $unique_pageview ? 1 : 0;

        // increment referrer stats
        if ($referrer_url !== '') {
            if (!isset($this->referrer_stats[$referrer_url])) {
                $this->referrer_stats[$referrer_url] = new ReferrerStats;
                $this->referrer_stats[$referrer_url]->url = $referrer_url;
                $this->referrer_stats[$referrer_url]->date = $this->site_stats->date;
            }
            $this->referrer_stats[$referrer_url]->pageviews++;
            $this->referrer_stats[$referrer_url]->visitors += $unique_pageview ? 1 : 0;
        }
    }

    private function commit(): void
    {
        // return early if no new data came in
        if ($this->site_stats->pageviews === 0) return;

        $this->statRepository->upsertSitestats($this->domain, $this->site_stats);
        $this->statRepository->upsertManyPageStats($this->domain, $this->page_stats);
        $this->statRepository->upsertManyReferrerStats($this->domain, $this->referrer_stats);
        $this->statRepository->insertRealtimePageviewsCount($this->domain, $this->site_stats->pageviews);
        $this->reset();
        (new SessionCleaner)();
    }

    /**
     * Resets the object properties to their initial state.
     * This protects against calling run() twice on the same class instance, committing data twice.
     */
    private function reset(): void
    {
        $this->site_stats = new SiteStats;
        $this->site_stats->date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->page_stats = [];
        $this->referrer_stats = [];
    }

    private function isReferrerUrlOnBlocklist(string $url): bool
    {
        if ($url === '') return false;

        static $blocklist;
        if ($blocklist === null) {
            $blocklist_filename = \dirname(__DIR__) . '/var/blocklist.txt';
            if (\is_file($blocklist_filename)) {
                $blocklist = \file($blocklist_filename, FILE_IGNORE_NEW_LINES);
            }
            $blocklist = $blocklist ?: [];
        }

        foreach ($blocklist as $blocklisted_domain) {
            if (\str_contains($url, $blocklisted_domain)) {
                return true;
            }
        }

        return false;
    }
}
