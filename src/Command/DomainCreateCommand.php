<?php

namespace App\Command;

use App\Database;
use App\Entity\Domain;
use App\Repository\DomainRepository;
use App\Repository\StatRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:domain:create', description: 'Registers a new domain to track analytics for.')]
class DomainCreateCommand extends Command
{
    public function __construct(
        protected DomainRepository $domainRepository,
        protected StatRepository $statRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the domain (without protocol)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        if (strlen($name) < 3 || strlen($name) > 255) {
            $output->writeln("Name must be between 3 and 255 characters in length.");
            return Command::FAILURE;
        }

        if (\preg_match('/[^a-zA-Z0-9\.\-]/', $name)) {
            $output->writeln("Name of domain can only contain alphanumeric characters, hyphens and dots.");
            return Command::FAILURE;
        }

        $domain = new Domain();
        $domain->setName($name);
        $this->domainRepository->insert($domain);
        $this->statRepository->createTables($domain);
        return Command::SUCCESS;
    }
}
