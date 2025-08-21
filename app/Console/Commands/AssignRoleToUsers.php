<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AssignRoleToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-role-to-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign User Role To Users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
          $userIds = [5, 6];

        // Hardcoded role
        $role = 'User';

        $users = User::whereIn('id', $userIds)->get();

        if ($users->isEmpty()) {
            $this->error('No users found with the given IDs.');
            return;
        }

        foreach ($users as $user) {
            $user->assignRole($role);
            $this->info("Assigned role '{$role}' to user ID {$user->id}");
        }

        $this->info('Static role assignment completed successfully.');
    }
}
