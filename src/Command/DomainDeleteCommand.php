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

#[AsCommand(name: 'app:domain:delete', description: 'Deletes a domain')]
class DomainDeleteCommand extends Command
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
        $domain = $this->domainRepository->getByName($name);
        if (!$domain) {
            $output->writeln("No domain with name {$name}");
            return Command::FAILURE;
        }

        $this->statRepository->reset($domain);
        $this->domainRepository->delete($domain);
        return Command::SUCCESS;
    }
}
