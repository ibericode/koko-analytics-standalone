<?php

namespace App\Command;

use App\Database;
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
    public function __construct(protected Database $db)
    {
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
        if (\preg_match('/[^a-zA-Z0-9\.\-]/', $name)) {
            $output->writeln("Name of domain can only contain alphanumeric characters, hyphens and dots.");
            return Command::FAILURE;
        }

        $this->db->prepare(
            "INSERT INTO koko_analytics_domains (name) VALUES (?);"
        )->execute([$name]);
        $id = $this->db->lastInsertId();

        // TODO: Abstract this away so we can just deal with a single class
        // TODO: Re-use code from migrations/003
        if ($this->db->getDriverName() === Database::DRIVER_SQLITE) {
            $this->createSqliteTables($id);
        } else {
            $this->createMysqlTables($id);
        }

        return Command::SUCCESS;
    }

    private function createSqliteTables(int $id): void
    {
        $this->db->exec(
            "CREATE TABLE koko_analytics_site_stats_{$id} (
              date DATE PRIMARY KEY NOT NULL,
              visitors SMALLINT UNSIGNED NOT NULL,
              pageviews SMALLINT UNSIGNED NOT NULL
            )"
        );
        $this->db->exec(
            "CREATE TABLE koko_analytics_page_urls_{$id} (
              id INTEGER PRIMARY KEY,
              url VARCHAR(255) NOT NULL,
              UNIQUE (url)
            )"
        );
        $this->db->exec(
            "CREATE TABLE koko_analytics_page_stats_{$id} (
              date DATE NOT NULL,
              id INTEGER NOT NULL,
              visitors SMALLINT UNSIGNED NOT NULL,
              pageviews SMALLINT UNSIGNED NOT NULL,
              PRIMARY KEY (date, id)
            )"
        );
        $this->db->exec(
            "CREATE TABLE koko_analytics_referrer_urls_{$id} (
              id INTEGER PRIMARY KEY,
              url VARCHAR(255) NOT NULL,
              UNIQUE (url)
            )"
        );
        $this->db->exec(
            "CREATE TABLE koko_analytics_referrer_stats_{$id} (
              date DATE NOT NULL,
              id INTEGER NOT NULL,
              visitors SMALLINT UNSIGNED NOT NULL,
              pageviews SMALLINT UNSIGNED NOT NULL,
              PRIMARY KEY (date, id)
            )"
        );
        $this->db->exec(
            "CREATE TABLE koko_analytics_realtime_count_{$id} (
                timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                count SMALLINT UNSIGNED NOT NULL DEFAULT 0
            )"
        );
    }

    private function createMysqlTables(int $id): void
    {
        $this->db->exec(
            "CREATE TABLE koko_analytics_site_stats_{$id} (
                  date DATE PRIMARY KEY NOT NULL,
                  visitors SMALLINT UNSIGNED NOT NULL,
                  pageviews SMALLINT UNSIGNED NOT NULL
            ) ENGINE=INNODB CHARACTER SET=ascii"
        );
        $this->db->exec(
            "CREATE TABLE koko_analytics_page_urls_{$id} (
              id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              url VARCHAR(255) NOT NULL,
              UNIQUE INDEX (url)
            ) ENGINE=INNODB CHARACTER SET=ascii"
        );
        $this->db->exec(
            "CREATE TABLE koko_analytics_page_stats_{$id} (
              date DATE NOT NULL,
              id INT UNSIGNED NOT NULL,
              visitors SMALLINT UNSIGNED NOT NULL,
              pageviews SMALLINT UNSIGNED NOT NULL,
              PRIMARY KEY (date, id)
            ) ENGINE=INNODB CHARACTER SET=ascii"
        );
        $this->db->exec(
            "CREATE TABLE koko_analytics_referrer_urls_{$id} (
              id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              url VARCHAR(255) NOT NULL,
              UNIQUE INDEX (url)
            ) ENGINE=INNODB CHARACTER SET=ascii"
        );
        $this->db->exec(
            "CREATE TABLE koko_analytics_referrer_stats_{$id} (
              date DATE NOT NULL,
              id INT UNSIGNED NOT NULL,
              visitors SMALLINT UNSIGNED NOT NULL,
              pageviews SMALLINT UNSIGNED NOT NULL,
              PRIMARY KEY (date, id)
            ) ENGINE=INNODB CHARACTER SET=ascii"
        );
        $this->db->exec(
            "CREATE TABLE koko_analytics_realtime_count_{$id} (
                timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                count SMALLINT UNSIGNED NOT NULL DEFAULT 0
            ) ENGINE=INNODB;"
        );
    }
}
