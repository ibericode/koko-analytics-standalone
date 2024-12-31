<?php

namespace App\Command;

use App\Aggregator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:aggregate', description: 'Aggregates any temporary data in the buffer file to persistent database storage.')]
class AggregateCommand extends Command
{
    public function __construct(protected Aggregator $aggregator) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $time_start = microtime(true);
        $this->aggregator->run();
        $time_elapsed = round((microtime(true) - $time_start) * 1000, 2); // in ms
        $output->writeln("Done! Aggregation ran for {$time_elapsed} ms.");
        return Command::SUCCESS;
    }
}
