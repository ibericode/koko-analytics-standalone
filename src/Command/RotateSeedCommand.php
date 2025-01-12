<?php

namespace App\Command;

use App\Aggregator;
use App\Repository\DomainRepository;
use App\SessionManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:rotate-seed', description: 'Rotates the daily seed used in determining the uniqueness of a request.')]
class RotateSeedCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sessionManager = new SessionManager();
        $sessionManager->rotateSeed();
        $output->writeln("Written new seed to {$sessionManager->getSeedFilename()}.");
        return Command::SUCCESS;
    }
}
