<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;

class RunAllMigrations extends Command
{
    protected $signature = 'migrate:all';
    protected $description = 'Migrate All Files skip errors';

    public function handle()
    {
        // Get all migration files (example logic, adjust as needed)
        $files = glob(database_path('migrations/*.php'));

        foreach ($files as $file) {
            $fileName = basename($file);
            $this->info("Attempting to migrate: {$fileName}");

            try {
                // Call the default migrate command for a specific path
                $this->call('migrate', [
                    '--path' => 'database/migrations/' . $fileName,
                    '--force' => true // Use --force to run without confirmation
                ]);
                $this->info("Successfully migrated: {$fileName}");

            } catch (Exception $e) {
                $this->error("Failed to migrate: {$fileName}. Error: " . $e->getMessage());
                // The catch block allows the loop to continue to the next file
            }
        }

        $this->info('Migration process finished, skipping files with errors.');
    }
}

