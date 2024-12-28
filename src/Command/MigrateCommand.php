<?php

namespace App\Command;

use App\Database;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:migrate', description: 'Upgrades the database schema to the latest code version')]
class MigrateCommand extends Command
{
    public function __construct(protected Database $db) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migration_files = glob("migrations/*-*.php");
        try {
            $version = $this->db->query('SELECT MAX(version) FROM koko_analytics_migrations')->fetchColumn(0);
        } catch (Exception $e) {
            // in case table does not yet exist, we should start at earliest possible migration
            $version = 0;
        }

        $stmt = $this->db->prepare("INSERT INTO koko_analytics_migrations (version, timestamp) VALUES (:version, :timestamp);");

        foreach ($migration_files as $migration_file) {
            // extract migration version from filename
            $migration_filename = basename($migration_file);
            if (!str_contains($migration_filename, '-') || !is_numeric($migration_filename[0])) {
                throw new Exception("Migration filename '$migration_filename' does not match expected format: <version>-<description>.php.");
            }
            $migration_version = (int) explode("-", $migration_filename)[0];

            // skip migration if already executed
            if ($migration_version <= $version) {
                continue;
            }

            // execute migration
            $fn = require $migration_file;
            $fn($this->db);

            // mark migration as completed
            $stmt->execute(["version" => $migration_version, "timestamp" => (new \DateTimeImmutable())->format('Y-m-d H:i:s')]);

            $output->writeln("Executed migration file '$migration_file'");
        }

        return Command::SUCCESS;
    }
}
