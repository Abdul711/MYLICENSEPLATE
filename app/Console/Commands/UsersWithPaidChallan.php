<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ChallanStatusMail;
class UsersWithPaidChallan extends Command
{
    protected $signature = 'users:unpaid-challan';
    protected $description = 'List all users whose license plates have an unpaid challan';

    public function handle()
    {
        $users = User::whereHas('licensePlates.challan', function ($query) {
            $query->where('status', 'unpaid');
        })->with(['licensePlates.challan' => function ($query) {
            $query->where('status', 'unpaid');
        }])->get();

        if ($users->isEmpty()) {
            $this->info("No users found with unpaid challans.");
            return 0;
        }

        foreach ($users as $user) {
            // Filter plates with unpaid challans
            $unpaidPlates = $user->licensePlates->filter(function ($plate) {
                return $plate->challan && $plate->challan->status === 'unpaid';
            });

            $unpaidCount = $unpaidPlates->count();
             if ($unpaidPlates->isNotEmpty()) {
        Mail::to($user->email)->send(new ChallanStatusMail($unpaidPlates,$user));
    }

            $this->info("User: {$user->name} ({$user->email}) | Unpaid Plates: {$unpaidCount}");

            foreach ($unpaidPlates as $plate) {
                $dueDate = date('Y-m-d H:i:s', strtotime($plate->created_at . ' +2 months'));

                $this->info("  - Plate: {$plate->plate_number}");
                $this->info("    Status: Unpaid");
                $this->info("    Due Date: {$dueDate}");

            }

            $this->line(''); // empty line between users
        }
    }
}
