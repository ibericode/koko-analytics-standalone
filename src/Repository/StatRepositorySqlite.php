<?php

namespace App\Repository;

use App\Entity\Domain;
use App\Entity\SiteStats;

class StatRepositorySqlite extends StatRepository
{
    public function createTables(Domain $domain): void
    {
        $id = $domain->getId();
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS koko_analytics_site_stats_{$id} (
              date DATE PRIMARY KEY NOT NULL,
              visitors SMALLINT UNSIGNED NOT NULL,
              pageviews SMALLINT UNSIGNED NOT NULL
            )"
        );
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS koko_analytics_page_urls_{$id} (
              id INTEGER PRIMARY KEY,
              url VARCHAR(255) NOT NULL,
              UNIQUE (url)
            )"
        );
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS koko_analytics_page_stats_{$id} (
              date DATE NOT NULL,
              id INTEGER NOT NULL,
              visitors SMALLINT UNSIGNED NOT NULL,
              pageviews SMALLINT UNSIGNED NOT NULL,
              PRIMARY KEY (date, id)
            )"
        );
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS koko_analytics_referrer_urls_{$id} (
              id INTEGER PRIMARY KEY,
              url VARCHAR(255) NOT NULL,
              UNIQUE (url)
            )"
        );
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS koko_analytics_referrer_stats_{$id} (
              date DATE NOT NULL,
              id INTEGER NOT NULL,
              visitors SMALLINT UNSIGNED NOT NULL,
              pageviews SMALLINT UNSIGNED NOT NULL,
              PRIMARY KEY (date, id)
            )"
        );
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS koko_analytics_realtime_count_{$id} (
                timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                count SMALLINT UNSIGNED NOT NULL DEFAULT 0
            )"
        );
    }

    public function upsertSiteStats(Domain $domain, SiteStats $stats): void
    {
        $query = "INSERT INTO koko_analytics_site_stats_{$domain->getId()} (date, visitors, pageviews) VALUES (:date, :visitors, :pageviews) ON CONFLICT DO UPDATE SET visitors = visitors + excluded.visitors, pageviews = pageviews + excluded.pageviews";

        $this->db->prepare($query)
            ->execute([
                'date' => $stats->date->format('Y-m-d'),
                'visitors' => $stats->visitors,
                'pageviews' => $stats->pageviews,
            ]);
    }

    public function upsertManyPageStats(Domain $domain, array $stats): void
    {
        if (empty($stats)) {
            return;
        }

        // insert all page urls
        $urls = \array_map(function ($s) {
            return $s->url;
        }, $stats);
        $placeholders = \rtrim(\str_repeat('(?),', \count($urls)), ',');
        $query = "INSERT OR IGNORE INTO koko_analytics_page_urls_{$domain->getId()} (url) VALUES {$placeholders}";
        $this->db->prepare($query)->execute($urls);

        // select and map page url to id
        $placeholders = \rtrim(\str_repeat('?,', count($urls)), ',');
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_page_urls_{$domain->getId()} WHERE url IN ({$placeholders})");
        $stmt->execute($urls);
        $page_url_ids = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $page_url_ids[$row['url']] = $row['id'];
        }

        // build final upsert query for page stats
        $values = [];
        foreach ($stats as $s) {
            \array_push($values, $s->date->format('Y-m-d'), $page_url_ids[$s->url], $s->visitors, $s->pageviews);
        }
        $column_count = 4;
        $placeholders = \rtrim(\str_repeat('?,', $column_count), ',');
        $placeholders = \rtrim(\str_repeat("($placeholders),", \count($values) / $column_count), ',');

        $query = "INSERT INTO koko_analytics_page_stats_{$domain->getId()} (date, id, visitors, pageviews) VALUES {$placeholders} ON CONFLICT DO UPDATE SET visitors = visitors + excluded.visitors, pageviews = pageviews + excluded.pageviews";
        $this->db->prepare($query)->execute($values);
    }

    public function upsertManyReferrerStats(Domain $domain, array $stats): void
    {
        if (empty($stats)) {
            return;
        }

        // insert all page urls
        $urls = \array_map(function ($s) {
            return $s->url;
        }, $stats);
        $placeholders = \rtrim(\str_repeat('(?),', \count($urls)), ',');
        $query = "INSERT OR IGNORE INTO koko_analytics_referrer_urls_{$domain->getId()} (url) VALUES {$placeholders}";

        $this->db->prepare($query)->execute($urls);

        // select and map page url to id
        $placeholders = \rtrim(\str_repeat('?,', \count($urls)), ',');
        $stmt = $this->db->prepare("SELECT * FROM koko_analytics_referrer_urls_{$domain->getId()} WHERE url IN ({$placeholders})");
        $stmt->execute($urls);
        $url_ids = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $url_ids[$row['url']] = $row['id'];
        }

        // build final upsert query for page stats
        $values = [];
        foreach ($stats as $s) {
            \array_push($values, $s->date->format('Y-m-d'), $url_ids[$s->url], $s->visitors, $s->pageviews);
        }
        $column_count = 4;
        $placeholders = \rtrim(\str_repeat('?,', $column_count), ',');
        $placeholders = \rtrim(\str_repeat("($placeholders),", \count($values) / $column_count), ',');
        $query = "INSERT INTO koko_analytics_referrer_stats_{$domain->getId()} (date, id, visitors, pageviews) VALUES {$placeholders} ON CONFLICT DO UPDATE SET visitors = visitors + excluded.visitors, pageviews = pageviews + excluded.pageviews";
        $this->db->prepare($query)->execute($values);
    }
}
