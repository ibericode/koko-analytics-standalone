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
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(name: 'app:database:reset', description: 'Resets database to an empty state')]
class DatabaseResetCommand extends Command
{
    public function __construct(
        protected Database $db,
        protected DomainRepository $domainRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper */
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Are you sure you want to reset your database? This will remove all data. (y/N)', false);
        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $domains = $this->domainRepository->getAll();
        foreach ($domains as $domain) {
            $this->db->exec("DROP TABLE koko_analytics_site_stats_{$domain->getId()}");
            $this->db->exec("DROP TABLE koko_analytics_page_stats_{$domain->getId()}");
            $this->db->exec("DROP TABLE koko_analytics_referrer_stats_{$domain->getId()}");
        }

        $this->db->exec("DELETE FROM koko_analytics_domains");
        $this->db->exec("DELETE FROM koko_analytics_users");
        $output->writeln("Database successfully emptied.");
        return Command::SUCCESS;
    }
}
