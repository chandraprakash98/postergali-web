<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TruncateAppTablesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_defaults_to_dry_run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'hashed-password',
        ]);

        $this->artisan('db:truncate-app')
            ->expectsOutputToContain('Dry run only')
            ->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => 'admin@example.com']);
    }

    public function test_command_truncates_application_tables_but_preserves_system_tables(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'hashed-password',
        ]);
        DB::table('cache')->insert([
            'key' => 'test-cache-entry',
            'value' => 'cached',
            'expiration' => time() + 3600,
        ]);
        $migrationCount = DB::table('migrations')->count();

        $this->artisan('db:truncate-app', ['--execute' => true, '--force' => true])
            ->expectsOutputToContain('Application tables truncated successfully.')
            ->assertSuccessful();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseHas('cache', ['key' => 'test-cache-entry']);
        $this->assertDatabaseCount('migrations', $migrationCount);
    }
}
