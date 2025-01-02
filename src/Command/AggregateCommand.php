<?php

namespace App\Command;

use App\Aggregator;
use App\Repository\DomainRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:aggregate', description: 'Aggregates any temporary data in the buffer file to persistent database storage.')]
class AggregateCommand extends Command
{
    public function __construct(
        protected Aggregator $aggregator,
        protected DomainRepository $domainRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $domains = $this->domainRepository->getAll();

        foreach ($domains as $domain) {
            $time_start = microtime(true);
            $this->aggregator->run($domain);
            $time_elapsed = round((microtime(true) - $time_start) * 1000, 2); // in ms
            $output->writeln("{$domain->domain}: aggregation completed in {$time_elapsed} ms.");
        }
        return Command::SUCCESS;
    }
}
