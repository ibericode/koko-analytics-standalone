<?php

namespace App\Command;

use App\Aggregator;
use App\Repository\DomainRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:rotate-seed', description: 'Rotates the daily seed used in determining the uniqueness of a request.')]
class RotateSeedCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $seed = bin2hex(random_bytes(16));
        $session_directory = dirname(__DIR__, 2) . "/var/sessions";
        $filename = "{$session_directory}/seed.txt";
        file_put_contents($filename, $seed);
        $output->writeln("Written new seed to {$filename}.");
        return Command::SUCCESS;
    }
}
