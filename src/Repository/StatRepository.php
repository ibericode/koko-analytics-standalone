<?php

namespace App\Repository;

use App\Database;
use App\Entity\Domain;
use App\Entity\PageStats;
use App\Entity\ReferrerStats;
use App\Entity\SiteStats;
use PDOException;

abstract class StatRepository
{
    public function __construct(
        protected Database $db
    ) {
    }

    public static function create(Database $db): static
    {
        if ($db->getDriverName() === Database::DRIVER_SQLITE) {
            return new StatRepositorySqlite($db);
        } else {
            return new StatRepositoryMysql($db);
        }
    }

    public function getTotalsBetween(Domain $domain, string $path, \DateTimeInterface $start, \DateTimeInterface $end): SiteStats
    {
        if ($path) {
            $stmt = $this->db->prepare("
                SELECT
                    SUM(visitors) AS visitors,
                    SUM(pageviews) AS pageviews
                FROM koko_analytics_page_stats_{$domain->id} s
                JOIN koko_analytics_page_urls_{$domain->id} p ON p.id = s.id
                WHERE date BETWEEN :start AND :end AND p.url = :path
            ");
            $stmt->execute([
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'path' => $path,
            ]);
        } else {
            $stmt = $this->db->prepare("
                SELECT
                    SUM(visitors) as visitors,
                    SUM(pageviews) as pageviews
                FROM koko_analytics_site_stats_{$domain->id}
                WHERE date BETWEEN :start and :end
            ");
            $stmt->execute([
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ]);
        }

        return SiteStats::fromArray($stmt->fetch(\PDO::FETCH_ASSOC) ?: []);
    }

    /**
     * @return SiteStats[]
     */
    public function getGroupedTotalsBetween(Domain $domain, string $path, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        if ($path) {
            $stmt = $this->db->prepare("
                SELECT
                    date,
                    SUM(visitors) AS visitors,
                    SUM(pageviews) AS pageviews
                FROM koko_analytics_page_stats_{$domain->id} s
                JOIN koko_analytics_page_urls_{$domain->id} p ON p.id = s.id
                WHERE date BETWEEN :start AND :end AND p.url = :path
                GROUP BY date
            ");
            $stmt->execute([
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'path' => $path,
            ]);
        } else {
            $stmt = $this->db->prepare("
                SELECT
                    date,
                    SUM(visitors) as visitors,
                    SUM(pageviews) as pageviews
                FROM koko_analytics_site_stats_{$domain->id}
                WHERE date BETWEEN :start and :end
                GROUP BY date;
            ");
            $stmt->execute([
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
            ]);
        }
        return \array_map([SiteStats::class, 'fromArray'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * @return PageStats[]
     */
    public function getPageStatsBetween(Domain $domain, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $stmt = $this->db->prepare("
            SELECT
                p.url as url,
                SUM(s.visitors) as visitors,
                SUM(s.pageviews) as pageviews
            FROM koko_analytics_page_stats_{$domain->id} s
            JOIN koko_analytics_page_urls_{$domain->id} p ON p.id = s.id
            WHERE s.date BETWEEN :start and :end
            GROUP BY s.id
            ORDER BY pageviews DESC, visitors DESC, s.id ASC
            LIMIT 0, 20
        ");
        $stmt->execute([
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ]);

        return \array_map([PageStats::class, 'fromArray'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * @return ReferrerStats[]
     */
    public function getReferrerStatsBetween(Domain $domain, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $stmt = $this->db->prepare("
            SELECT
                r.url as url,
                SUM(s.visitors) as visitors,
                SUM(s.pageviews) as pageviews
            FROM koko_analytics_referrer_stats_{$domain->id} s
            JOIN koko_analytics_referrer_urls_{$domain->id} r ON r.id = s.id
            WHERE s.date BETWEEN :start and :end
            GROUP BY s.id
            ORDER BY pageviews DESC, visitors DESC, s.id ASC
            LIMIT 0, 20
        ");
        $stmt->execute([
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ]);

        return \array_map([ReferrerStats::class, 'fromArray'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function getRealtimeCount(Domain $domain,): int
    {
        $stmt = $this->db->prepare("
            SELECT SUM(count)
            FROM koko_analytics_realtime_count_{$domain->id}
            WHERE timestamp >= ?");
        $stmt->execute([(new \DateTimeImmutable('-1 hour', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')]);
        return (int) $stmt->fetchColumn();
    }

    public function deleteAllBeforeDate(Domain $domain, \DateTimeInterface $dt): void
    {
        $queries = [
            "DELETE FROM koko_analytics_site_stats_{$domain->id} WHERE date < ?",
            "DELETE FROM koko_analytics_page_stats_{$domain->id} WHERE date < ?",
            "DELETE FROM koko_analytics_referrer_stats_{$domain->id} WHERE date < ?"
        ];
        foreach ($queries as $query) {
            $this->db
                ->prepare($query)
                ->execute([$dt->format('Y-m-d')]);
        }

        // remove orphaned page urls
        $this->db
            ->prepare("DELETE FROM koko_analytics_page_urls_{$domain->id} WHERE id NOT IN (SELECT DISTINCT(id) FROM koko_analytics_page_stats_{$domain->id})")
            ->execute();

        // remove orphaned referrer url's
        $this->db
            ->prepare("DELETE FROM koko_analytics_referrer_urls_{$domain->id} WHERE id NOT IN (SELECT DISTINCT(id) FROM koko_analytics_referrer_stats_{$domain->id})")
            ->execute();
    }

    public function insertRealtimePageviewsCount(Domain $domain, int $count): void
    {
        // insert pageviews since last aggregation run
        $this->db
            ->prepare("INSERT INTO koko_analytics_realtime_count_{$domain->id}(timestamp, count) VALUES(?, ?)")
            ->execute([(new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s') , $count]);

        // remove pageviews older than 3 hours
        $this->db
            ->prepare("DELETE FROM koko_analytics_realtime_count_{$domain->id} WHERE timestamp < ?")
            ->execute([ (new \DateTimeImmutable('-3 hours', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')]);
    }

    public function reset(Domain $domain): void
    {
        $this->db->exec("DROP TABLE if EXISTS koko_analytics_site_stats_{$domain->id}");
        $this->db->exec("DROP TABLE if EXISTS koko_analytics_page_stats_{$domain->id}");
        $this->db->exec("DROP TABLE if EXISTS koko_analytics_page_urls_{$domain->id}");
        $this->db->exec("DROP TABLE if EXISTS koko_analytics_referrer_stats_{$domain->id}");
        $this->db->exec("DROP TABLE if EXISTS koko_analytics_referrer_urls_{$domain->id}");
        $this->db->exec("DROP TABLE if EXISTS koko_analytics_realtime_count_{$domain->id}");
    }

    // The methods below have a database specific implementation
    // @see StatRepositorySqlite
    // @see StatRepositoryMysql
    // @see config/services.php

    abstract public function createTables(Domain $domain): void;
    abstract public function upsertSiteStats(Domain $domain, SiteStats $stats): void;

    /** @param PageStats[] $stats */
    abstract public function upsertManyPageStats(Domain $domain, array $stats): void;

    /** @param ReferrerStats[] $stats */
    abstract public function upsertManyReferrerStats(Domain $domain, array $stats): void;
}
