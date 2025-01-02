<?php

namespace App\Command;

use App\Entity\Domain;
use App\Repository\DomainRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:domain:create', description: 'Registers a new domain to track analytics for.')]
class DomainCreateCommand extends Command
{
    public function __construct(protected DomainRepository $domainRepository) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the domain (without protocol)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $domain = new Domain;
        $domain->setName($name);
        $this->domainRepository->save($domain);
        return Command::SUCCESS;
    }
}
