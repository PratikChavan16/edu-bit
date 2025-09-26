<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckUserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:user-data {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user data for debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found");
            return;
        }
        
        $this->info("User: {$user->first_name} {$user->last_name}");
        $this->info("Email: {$user->email}");
        $this->info("Department ID: " . ($user->department_id ?? 'NULL'));
        $this->info("Roles: " . $user->getRoleNames()->implode(', '));
        
        if ($user->department) {
            $this->info("Department: {$user->department->name}");
            
            // Check subjects for this department
            $subjects = \App\Models\Subject::whereHas('course', function($q) use ($user) {
                $q->where('department_id', $user->department_id);
            })->get();
            
            $this->info("Subjects in department: " . $subjects->count());
            foreach ($subjects as $subject) {
                $this->line("  - {$subject->code}: {$subject->title}");
            }
        } else {
            $this->warn("No department assigned!");
        }
    }
}
