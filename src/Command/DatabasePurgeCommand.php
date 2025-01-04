<?php

namespace App\Command;

use App\Database;
use App\Repository\DomainRepository;
use App\Repository\StatRepository;
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
    public function __construct(
        protected DomainRepository $domainRepository,
        protected StatRepository $statRepository,
    ) {
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

        $domains = $this->domainRepository->getAll();
        foreach ($domains as $domain) {
            $this->statRepository->deleteAllBeforeDate($domain, $cutoff_date);
        }

        return Command::SUCCESS;
    }
}
