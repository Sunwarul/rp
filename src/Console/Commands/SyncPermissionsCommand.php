<?php

namespace NuxtIt\RP\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SyncPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rp:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions from PermissionsEnum and assign them to admin role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting permission synchronization...');

        // Check if PermissionsEnum exists
        if (!class_exists(\App\Enums\PermissionsEnum::class)) {
            $this->error('PermissionsEnum class not found. Please ensure it exists at App\Enums\PermissionsEnum');
            return Command::FAILURE;
        }

        // Get all permissions from enum
        $permissions = \App\Enums\PermissionsEnum::all();

        if (empty($permissions)) {
            $this->warn('No permissions found in PermissionsEnum.');
            return Command::FAILURE;
        }

        $this->info('Found ' . count($permissions) . ' permissions in enum.');

        // Sync permissions (create if not exists)
        $created = 0;
        $existing = 0;
        $bar = $this->output->createProgressBar(count($permissions));
        $bar->start();

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            if ($permission->wasRecentlyCreated) {
                $created++;
            } else {
                $existing++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Permissions: {$created} created, {$existing} already existed.");

        // Create or get admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        if ($adminRole->wasRecentlyCreated) {
            $this->info("✓ Created admin role.");
        } else {
            $this->info("✓ Admin role already exists.");
        }

        // Sync all permissions to admin role
        $adminRole->syncPermissions($permissions);
        $this->info("✓ Assigned all " . count($permissions) . " permissions to admin role.");

        $this->newLine();
        $this->info('Permission synchronization completed successfully!');

        return Command::SUCCESS;
    }
}

