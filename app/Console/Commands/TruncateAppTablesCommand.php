<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class TruncateAppTablesCommand extends Command
{
    protected $signature = 'db:truncate-app
        {--execute : Truncate the application tables (otherwise only list them)}
        {--force : Skip the confirmation prompt; required in production}';

    protected $description = 'Safely truncate application tables while preserving Laravel system tables.';

    private const SYSTEM_TABLES = [
        'cache',
        'cache_locks',
        'failed_jobs',
        'job_batches',
        'migrations',
        'password_reset_tokens',
        'sessions',
    ];

    public function handle(): int
    {
        $tables = array_values(array_diff(
            Schema::getTableListing(schemaQualified: false),
            self::SYSTEM_TABLES,
        ));
        sort($tables);

        if ($tables === []) {
            $this->warn('No application tables were found.');

            return SymfonyCommand::SUCCESS;
        }

        $this->table(['Application tables'], array_map(
            static fn (string $table): array => [$table],
            $tables,
        ));

        if (! $this->option('execute')) {
            $this->comment('Dry run only. Re-run with --execute to truncate these tables.');

            return SymfonyCommand::SUCCESS;
        }

        if (app()->isProduction() && ! $this->option('force')) {
            $this->error('The --force option is required to truncate tables in production.');

            return SymfonyCommand::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('This permanently deletes all data in the listed tables. Continue?')) {
            $this->warn('Truncate cancelled.');

            return SymfonyCommand::SUCCESS;
        }

        Schema::withoutForeignKeyConstraints(function () use ($tables): void {
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }
        });

        $this->info('Application tables truncated successfully.');

        return SymfonyCommand::SUCCESS;
    }
}
