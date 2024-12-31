<?php

namespace App\Repository;

use App\Database;
use App\Entity\PageStats;
use App\Entity\SiteStats;

class StatRepository {
    public function __construct(
        protected Database $db
    ) {}

    public function getTotalsBetween(\DateTimeInterface $start, \DateTimeInterface $end): SiteStats
    {
        $stmt = $this->db->prepare("
            SELECT
                SUM(visitors) AS visitors,
                SUM(pageviews) AS pageviews
            FROM koko_analytics_site_stats
            WHERE date BETWEEN :start AND :end
        ");
        $stmt->execute([
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ]);

        return SiteStats::fromArray($stmt->fetch(\PDO::FETCH_ASSOC) ?: []);
    }

    public function getGroupedTotalsBetween(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $stmt = $this->db->prepare("
            SELECT
                date,
                SUM(visitors) AS visitors,
                SUM(pageviews) AS pageviews
            FROM koko_analytics_site_stats
            WHERE date BETWEEN :start AND :end
            GROUP BY DATE_FORMAT(date, '%Y-%m-%d');
        ");
        $stmt->execute([
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ]);

        return array_map([SiteStats::class, 'fromArray'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function getPageStatsBetween(\DateTimeInterface $start, \DateTimeInterface $end)
    {
        $stmt = $this->db->prepare("
            SELECT
                p.url AS url,
                SUM(s.visitors) AS visitors,
                SUM(s.pageviews) AS pageviews
            FROM koko_analytics_page_stats s
            JOIN koko_analytics_page_urls p ON p.id = s.id
            WHERE s.date BETWEEN :start AND :end
            GROUP BY s.id
        ");
        $stmt->execute([
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ]);

        return array_map([PageStats::class, 'fromArray'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function getReferrerStatsBetween(\DateTimeInterface $start, \DateTimeInterface $end)
    {
        $stmt = $this->db->prepare("
            SELECT
                r.url AS url,
                SUM(s.visitors) AS visitors,
                SUM(s.pageviews) AS pageviews
            FROM koko_analytics_referrer_stats s
            JOIN koko_analytics_referrer_urls r ON r.id = s.id
            WHERE s.date BETWEEN :start AND :end
            GROUP BY s.id
        ");
        $stmt->execute([
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ]);

        return array_map([PageStats::class, 'fromArray'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function getRealtimeCount(): int
    {
        $stmt = $this->db->prepare("
            SELECT SUM(count)
            FROM koko_analytics_realtime_count
            WHERE timestamp >= ?");
        $stmt->execute([ (new \DateTimeImmutable('-1 hour', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s') ]);
        return (int) $stmt->fetchColumn();
    }
}
