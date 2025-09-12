<?php

namespace App\Command;

use App\Aggregator;
use App\ReferrerBlocklist;
use App\Repository\DomainRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:aggregate', description: 'Aggregates any temporary data in the buffer file to persistent database storage.')]
class AggregateCommand extends Command
{
    public function __construct(
        protected Aggregator $aggregator,
        protected DomainRepository $domainRepository,
        protected LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // aggregate statistics for every domain
        $domains = $this->domainRepository->getAll();
        foreach ($domains as $domain) {
            $time_start = microtime(true);
            $this->aggregator->run($domain);
            $time_elapsed = round((microtime(true) - $time_start) * 1000, 2); // in ms
            $output->writeln("{$domain->getName()}: aggregation completed in {$time_elapsed} ms.");
        }

        // (maybe) update referrer blocklist
        if ((new ReferrerBlocklist())->update()) {
            $output->writeln("global: referrer blocklist updated");
        }

        return Command::SUCCESS;
    }
}
