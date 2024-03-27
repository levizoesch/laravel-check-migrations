<?php
namespace levizoesch\checkmigrations;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;


class CheckMigrations extends Command
{
    // skip flag is redundant as you would just run native
    // migrate command, but kept for possible use for pipelines/automation
    protected $signature = 'check-migrations
                            {--skip : Skip confirmation and run migrations directly}
                            {--ignore= : Specify migration name to ignore from running, and prompting for confirmation}';

    protected $description = 'Check for unrun migrations';

    public function handle(): void
    {
        $this->showPendingMigrations();
    }

    // Function to display pending migrations and optionally run them
    private function showPendingMigrations(): void
    {
        $pendingMigrations = $this->getPendingMigrations();

        foreach ($pendingMigrations as $migrationName) {
            $this->info("Pending migration found: $migrationName");

            if ($this->shouldRunMigration($migrationName)) {
                $this->runMigration($migrationName);
            } else {
                $this->comment("The migration $migrationName was not run.");
                $this->line("");
            }
        }
    }

    // Function to retrieve a list of pending migrations
    private function getPendingMigrations(): array
    {
        Artisan::call('migrate:status');

        $output = trim(Artisan::output());
        $lines = explode(PHP_EOL, $output);
        $pendingMigrations = [];

        foreach ($lines as $line) {
            if (str_contains($line, 'Pending')) {
                preg_match('/(\d{4}_\d{2}_\d{2}_\d{6}_\w+)/', $line, $matches);

                if (!empty($matches[1])) {
                    $pendingMigrations[] = $matches[1];
                } else {
                    $this->error("Unable to extract migration name from line: $line");
                }
            }
        }

        return $pendingMigrations;
    }

    // Function to determine whether to run a migration based on user confirmation or pipeline option
    private function shouldRunMigration($migrationName): bool
    {
        $ignoreMigration = $this->option('ignore');

        if ($ignoreMigration && $ignoreMigration === $migrationName) {
            return false;
        }

        if ($this->option('skip')) {
            return false;
        }

        return $this->confirm("Are you sure you want to run the migration $migrationName?");
    }

    // Function to run a migration
    private function runMigration($migrationName): void
    {
        $migrationPath = $this->findMigrationPath($migrationName);

        if ($migrationPath) {
            Artisan::call('migrate', ['--path' => $migrationPath]);
            $this->line(Artisan::output());
        } else {
            $this->error("Migration file not found for $migrationName");
        }
    }

    // Function to find the path of a migration file
    private function findMigrationPath($migrationName)
    {
        $migrationFiles = $this->getMigrationFiles('database/migrations');

        foreach ($migrationFiles as $file) {
            if (strpos($file, $migrationName) !== false) {
                return $file;
            }
        }

        return null;
    }

    // Function to retrieve migration files within a directory
    private function getMigrationFiles($directory): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}