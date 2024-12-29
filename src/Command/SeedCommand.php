<?php

namespace App\Command;

use App\Database;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:seed', description: 'Populates the database with some sample data')]
class SeedCommand extends Command
{
    public function __construct(protected Database $db) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date_start = new \DateTimeImmutable('-730 days');
        $date_now = new \DateTimeImmutable('now');
        $date_cur = $date_start;

        // populate site stats
        $stmt = $this->db->prepare('INSERT INTO koko_analytics_site_stats (date, visitors, pageviews) VALUES (:date, :visitors, :pageviews);');
        while ($date_cur < $date_now) {
            $visitors = random_int(10, 100);
            $pageviews = $visitors + random_int(10, 100);
            $stmt->execute([
                'date' => $date_cur->format('Y-m-d'),
                'visitors' => $visitors,
                'pageviews' => $pageviews,
            ]);
            $date_cur = $date_cur->modify('+1 day');
        }

        // create page URL's
        $page_urls = [
            '/',
            '/contact',
            '/about',
            '/team/danny',
            '/team/john',
            '/team/sarah',
            '/blog',
        ];
        $page_url_ids = [];
        $stmt = $this->db->prepare('INSERT INTO koko_analytics_page_urls (url) VALUES (:url);');
        foreach ($page_urls as $url) {
            $stmt->execute([ 'url' => $url ]);
            $page_url_ids[$url] = $this->db->lastInsertId();
        }

        $date_cur = $date_start;
        $stmt = $this->db->prepare('INSERT INTO koko_analytics_page_stats (date, id, visitors, pageviews) VALUES (:date, :id, :visitors, :pageviews);');
        while ($date_cur < $date_now) {
            foreach ($page_url_ids as $url => $id) {
                $visitors = random_int(5, 50);
                $pageviews = $visitors + random_int(10, 100);
                $stmt->execute([
                    'date' => $date_cur->format('Y-m-d'),
                    'id' => $id,
                    'visitors' => $visitors,
                    'pageviews' => $pageviews,
                ]);
            }

            $date_cur = $date_cur->modify('+1 day');
        }

        return Command::SUCCESS;
    }
}
