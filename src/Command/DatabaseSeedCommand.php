<?php

namespace App\Command;

use App\Database;
use App\Entity\Domain;
use App\Repository\UserRepository;
use App\Repository\DomainRepository;
use App\Security\User;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:database:seed', description: 'Populates the database with some sample data')]
class DatabaseSeedCommand extends Command
{
    public function __construct(
        protected Database $db,
        protected UserRepository $userRepository,
        protected DomainRepository $domainRepository,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->addOption('domain', 'd', InputOption::VALUE_REQUIRED, 'Domain to create sample data in', 'website.com')
            ->addOption('months', 'm', InputOption::VALUE_REQUIRED, 'Amount of months to create sample data for', '13');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $months = (int) $input->getOption('months');
        $domain_name = $input->getOption('domain');
        $date_start = new \DateTimeImmutable("-{$months} months", new \DateTimeZone('UTC'));
        $date_now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $domain = $this->domainRepository->getByDomain($domain_name);
        if (!$domain) {
            $output->writeln("No such domain: {$domain_name}");
            return Command::FAILURE;
        }

        $this->seedUsers();
        $this->seedSiteStats($domain, $date_start, $date_now);
        $this->seedPageStats($domain, $date_start, $date_now);
        $this->seedReferrerStats($domain, $date_start, $date_now);
        return Command::SUCCESS;
    }

    private function seedUsers(): void
    {
        $user = new User();
        $user->setEmail('test@kokoanalytics.com');
        $user->setPassword(password_hash('password', PASSWORD_DEFAULT));
        $this->userRepository->save($user);
    }

    private function seedSiteStats(Domain $domain, \DateTimeImmutable $date_start, \DateTimeImmutable $date_now): void
    {
        $date_cur = $date_start;

        // populate site stats
        $stmt = $this->db->prepare("INSERT INTO koko_analytics_site_stats_{$domain->id} (date, visitors, pageviews) VALUES (:date, :visitors, :pageviews);");
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
    }

    private function seedPageStats(Domain $domain, \DateTimeImmutable $date_start, \DateTimeImmutable $date_now): void
    {
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
        $stmt = $this->db->prepare("INSERT INTO koko_analytics_page_urls_{$domain->id} (url) VALUES (:url);");
        foreach ($page_urls as $url) {
            $stmt->execute([ 'url' => $url ]);
            $page_url_ids[$url] = $this->db->lastInsertId();
        }

        $date_cur = $date_start;
        $stmt = $this->db->prepare("INSERT INTO koko_analytics_page_stats_{$domain->id} (date, id, visitors, pageviews) VALUES (:date, :id, :visitors, :pageviews);");
        while ($date_cur < $date_now) {
            foreach ($page_url_ids as $url => $id) {
                $visitors = random_int(5, 50);
                $pageviews = $visitors + random_int(5, 50);
                $stmt->execute([
                    'date' => $date_cur->format('Y-m-d'),
                    'id' => $id,
                    'visitors' => $visitors,
                    'pageviews' => $pageviews,
                ]);
            }

            $date_cur = $date_cur->modify('+1 day');
        }
    }

    private function seedReferrerStats(Domain $domain, \DateTimeImmutable $date_start, \DateTimeImmutable $date_now): void
    {
        // create referrer URL's
        $referrer_urls = [
            'https://www.kokoanalytics.com/',
            'https://www.github.com/',
            'https://sr.ht/',
            'https://mastodon.social/',
            'https://duckduckgo.com/',
            'https://www.dannyvankooten.com/',
            'https://www.bing.com/',
            'https://www.yahoo.com/',
        ];
        $referrer_url_ids = [];
        $stmt = $this->db->prepare("INSERT INTO koko_analytics_referrer_urls_{$domain->id} (url) VALUES (:url);");
        foreach ($referrer_urls as $url) {
            $stmt->execute([ 'url' => $url ]);
            $referrer_url_ids[$url] = $this->db->lastInsertId();
        }

        $date_cur = $date_start;
        $stmt = $this->db->prepare("INSERT INTO koko_analytics_referrer_stats_{$domain->id} (date, id, visitors, pageviews) VALUES (:date, :id, :visitors, :pageviews);");
        while ($date_cur < $date_now) {
            foreach ($referrer_url_ids as $url => $id) {
                $visitors = random_int(1, 10);
                $pageviews = $visitors + random_int(1, 10);
                $stmt->execute([
                    'date' => $date_cur->format('Y-m-d'),
                    'id' => $id,
                    'visitors' => $visitors,
                    'pageviews' => $pageviews,
                ]);
            }

            $date_cur = $date_cur->modify('+1 day');
        }
    }
}
