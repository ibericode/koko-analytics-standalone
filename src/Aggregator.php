<?php

namespace App;

use App\Entity\Domain;
use App\Entity\PageStats;
use App\Entity\ReferrerStats;
use App\Entity\SiteStats;
use App\Repository\DomainRepository;
use App\Repository\StatRepository;
use DateTimeImmutable;
use Exception;

class Aggregator
{
    protected SiteStats $site_stats;

    /** @var PageStats[] $page_stats */
    protected array $page_stats = [];

    /** @var ReferrerStats[] $referrer_stats */
    protected array $referrer_stats = [];

    public function __construct(
        protected Database $db,
        protected StatRepository $statRepository,
    ) {
    }

    public function run(Domain $domain): void
    {
        $this->reset($domain);

        $filename = \dirname(__DIR__) . "/var/buffer-{$domain->getName()}";
        if (!\is_file($filename)) {
            // buffer file for this domain does not exist, meaning no new data since last aggregation
            // we still create the file, because we use this to validate domain on /collect requests
            \touch($filename);
            return;
        }

        // rename file to something temporary
        $tmp_filename = $filename . '-' . \time();
        $renamed = \rename($filename, $tmp_filename);
        if (!$renamed) {
            throw new Exception("Error renaming buffer file");
        }

        // put empty file into place
        \touch($filename);

        $fh = \fopen($tmp_filename, 'r');
        if (!$fh) {
            throw new Exception("Error opening buffer file for reading");
        }

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

        $this->commit($domain);
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
            $this->page_stats[$path] = new PageStats();
            $this->page_stats[$path]->date = $this->site_stats->date;
            $this->page_stats[$path]->url = $path;
        }
        $this->page_stats[$path]->pageviews++;
        $this->page_stats[$path]->visitors += $unique_pageview ? 1 : 0;

        // increment referrer stats
        if ($referrer_url !== '') {
            if (!isset($this->referrer_stats[$referrer_url])) {
                $this->referrer_stats[$referrer_url] = new ReferrerStats();
                $this->referrer_stats[$referrer_url]->url = $referrer_url;
                $this->referrer_stats[$referrer_url]->date = $this->site_stats->date;
            }
            $this->referrer_stats[$referrer_url]->pageviews++;
            $this->referrer_stats[$referrer_url]->visitors += $unique_pageview ? 1 : 0;
        }
    }

    private function commit(Domain $domain): void
    {
        // return early if no new data came in
        if ($this->site_stats->pageviews === 0) {
            return;
        }

        $this->statRepository->upsertSitestats($domain, $this->site_stats);
        $this->statRepository->upsertManyPageStats($domain, \array_values($this->page_stats));
        $this->statRepository->upsertManyReferrerStats($domain, \array_values($this->referrer_stats));
        $this->statRepository->insertRealtimePageviewsCount($domain, $this->site_stats->pageviews);

        // clean-up stale session files
        (new SessionManager())->purge($domain);

        // purge data older than specified treshold in domain settings
        if ($domain->getPurgeTreshold() > 0) {
            $datetime = new \DateTimeImmutable("-{$domain->getPurgeTreshold()} days", new \DateTimeZone($domain->getTimezone()));
            $this->statRepository->deleteAllBeforeDate($domain, $datetime);
        }
    }

    /**
     * Resets the object properties to their initial state.
     */
    private function reset(Domain $domain): void
    {
        $this->site_stats = new SiteStats();
        $this->site_stats->date = new \DateTimeImmutable('now', new \DateTimeZone($domain->getTimezone()));
        $this->page_stats = [];
        $this->referrer_stats = [];
    }

    private function isReferrerUrlOnBlocklist(string $url): bool
    {
        if ($url === '') {
            return false;
        }

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
