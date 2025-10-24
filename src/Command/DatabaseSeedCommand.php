<?php

namespace App\Command;

use App\Entity\Domain;
use App\Entity\PageStats;
use App\Entity\ReferrerStats;
use App\Entity\SiteStats;
use App\Repository\UserRepository;
use App\Repository\DomainRepository;
use App\Repository\StatRepository;
use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:database:seed', description: 'Populates the database with some sample data')]
class DatabaseSeedCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected StatRepository $statRepository,
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
        $time_start = microtime(true);
        $months = (int) $input->getOption('months');
        $domain_name = $input->getOption('domain');
        $date_start = new \DateTimeImmutable("-{$months} months", new \DateTimeZone('UTC'));
        $date_now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        // TODO: Empty database first? If so, we need to ask for confirmation first.

        $user = $this->seedUsers();
        $domain = $this->seedDomain($user, $domain_name);

        $this->seedSiteStats($domain, $date_start, $date_now);
        $this->seedPageStats($domain, $date_start, $date_now);
        $this->seedReferrerStats($domain, $date_start, $date_now);

        $time_elapsed = round((microtime(true) - $time_start) * 1000, 2);
        $output->writeln("Created {$months} months of sample data in {$time_elapsed} ms");
        return Command::SUCCESS;
    }

    private function seedDomain(User $user, string $name): Domain
    {
        if ($domain = $this->domainRepository->getByName($name)) {
            return $domain;
        }

        $domain = new Domain();
        $domain->user_id = $user->getId();
        $domain->name = $name;
        $this->domainRepository->insert($domain);
        $this->statRepository->createTables($domain);
        return $domain;
    }

    private function seedUsers(): User
    {
        if ($user = $this->userRepository->getByEmail('test@kokoanalytics.com')) {
            return $user;
        }

        $user = new User();
        $user->setEmail('test@kokoanalytics.com');
        $user->setPassword(password_hash('password', PASSWORD_DEFAULT));
        $this->userRepository->save($user);
        return $user;
    }

    private function seedSiteStats(Domain $domain, \DateTimeImmutable $date_start, \DateTimeImmutable $date_now): void
    {
        $date_cur = $date_start;

        // populate site stats
        while ($date_cur < $date_now) {
            $s = new SiteStats();
            $s->visitors = random_int(10, 100);
            $s->pageviews = $s->visitors + random_int(10, 100);
            $s->date = $date_cur;
            $this->statRepository->upsertSiteStats($domain, $s);
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
        $stats = [];
        $date_cur = $date_start;

        while ($date_cur < $date_now) {
            foreach ($page_urls as $url) {
                $s = new PageStats();
                $s->url = $url;
                $s->date = $date_cur;
                $s->visitors = random_int(5, 50);
                $s->pageviews = $s->visitors + random_int(5, 50);
                $stats[] = $s;
            }

            $date_cur = $date_cur->modify('+1 day');
        }

        $this->statRepository->upsertManyPageStats($domain, $stats);
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
        $stats = [];

        $date_cur = $date_start;
        while ($date_cur < $date_now) {
            foreach ($referrer_urls as $url) {
                $s = new ReferrerStats();
                $s->url = $url;
                $s->date = $date_cur;
                $s->visitors = random_int(5, 50);
                $s->pageviews = $s->visitors + random_int(5, 50);
                $stats[] = $s;
            }

            $date_cur = $date_cur->modify('+1 day');
        }

        $this->statRepository->upsertManyReferrerStats($domain, $stats);
    }
}
