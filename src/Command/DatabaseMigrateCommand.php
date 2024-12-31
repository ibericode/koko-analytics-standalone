<?php

namespace App\Command;

use App\Database;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:database:migrate', description: 'Upgrades the database schema to the latest code version')]
class DatabaseMigrateCommand extends Command
{
    public function __construct(protected Database $db) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $driver = $this->db->getDriverName();
        $migration_files = glob("migrations/{$driver}/*-*.php");
        try {
            $version = $this->db->query('SELECT MAX(version) FROM koko_analytics_migrations')->fetchColumn(0);
        } catch (Exception $e) {
            $this->db->exec(
                "CREATE TABLE koko_analytics_migrations (
                      version INT UNSIGNED NOT NULL PRIMARY KEY,
                      timestamp DATETIME NOT NULL
                )"
           );

           $version = 0;
        }

        $stmt = $this->db->prepare("INSERT INTO koko_analytics_migrations (version, timestamp) VALUES (:version, :timestamp);");

        foreach ($migration_files as $migration_file) {
            // extract migration version from filename
            $migration_filename = basename($migration_file);
            $migration_version = (int) explode("-", $migration_filename)[0];

            // skip migration if already executed
            if ($migration_version <= $version) {
                continue;
            }

            // execute migration
            (require $migration_file)($this->db);

            // mark migration as completed
            $stmt->execute(["version" => $migration_version, "timestamp" => (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s')]);

            $output->writeln("Executed migration file '$migration_file'");
        }

        return Command::SUCCESS;
    }
}
