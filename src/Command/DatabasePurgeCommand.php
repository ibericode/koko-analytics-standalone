<?php

namespace App\Command;

use App\Database;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:database:purge', description: 'Purges all data older than the specified number of months from the database')]
class DatabasePurgeCommand extends Command
{
    public function __construct(protected Database $db) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->addOption('months', 'm', InputOption::VALUE_REQUIRED, 'Purge data older than how many months?', '24');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $months = (int) $input->getOption('months');
        $cutoff_date = new DateTimeImmutable("-{$months} months", new DateTimeZone('UTC'));
        $queries = [
            "DELETE FROM koko_analytics_site_stats WHERE date < ?",
            "DELETE FROM koko_analytics_page_stats WHERE date < ?",
            "DELETE FROM koko_analytics_referrer_stats WHERE date < ?"
        ];
        foreach ($queries as $query) {
            $this->db
            ->prepare($query)
            ->execute([$cutoff_date->format('Y-m-d')]);
        }

        // TODO: Remove orphaned rows from koko_analytics_page_urls and koko_analytics_referrer_urls tables
        // ....

        return Command::SUCCESS;
    }
}
