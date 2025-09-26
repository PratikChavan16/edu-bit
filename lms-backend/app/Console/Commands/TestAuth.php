<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\AuthService;

class TestAuth extends Command
{
    protected $signature = 'test:auth';
    protected $description = 'Test the authentication system';

    public function handle()
    {
        $this->info('Testing Authentication System');
        $this->info('============================');
        $this->newLine();

        // Test 1: Check users
        $this->info('1. Checking users in database...');
        $userCount = User::count();
        $this->info("Total users: {$userCount}");

        // Find admin user
        $adminUsers = User::where('email', 'like', '%admin%')->get();
        if ($adminUsers->count() > 0) {
            $this->info('Admin users found:');
            foreach ($adminUsers as $admin) {
                $this->line("  - {$admin->email} ({$admin->first_name} {$admin->last_name})");
            }
        } else {
            $this->warn('No admin users found');
        }

        // Test specific admin
        $admin = User::where('email', 'admin@medical.edu')->first();
        if ($admin) {
            $this->info("\n2. Testing admin user:");
            $this->line("  Email: {$admin->email}");
            $this->line("  Name: {$admin->first_name} {$admin->last_name}");
            
            $role = $admin->roles()->first();
            if ($role) {
                $this->line("  Role: {$role->name}");
            } else {
                $this->warn("  No role assigned");
            }

            // Test login
            $this->info("\n3. Testing login...");
            $authService = new AuthService();
            try {
                $result = $authService->login([
                    'email' => 'admin@medical.edu',
                    'password' => 'admin123'
                ]);
                $this->info('✓ Login successful!');
                $this->line("  Token length: " . strlen($result['access_token']));
                $this->line("  User: {$result['user']['first_name']} {$result['user']['last_name']}");
                $this->line("  Role: {$result['user']['role']['display_name']}");
                $this->line("  Department: " . ($result['user']['department']['name'] ?? 'None'));
                $this->line("  Permissions count: " . count($result['user']['permissions']));
                
                // Test a student user too
                $this->info("\n4. Testing student login...");
                $student = User::whereHas('roles', function($q) {
                    $q->where('name', 'student');
                })->first();
                
                if ($student) {
                    $studentResult = $authService->login([
                        'email' => $student->email,
                        'password' => 'student123'
                    ]);
                    $this->info("✓ Student login successful!");
                    $this->line("  Student: {$studentResult['user']['first_name']} {$studentResult['user']['last_name']}");
                    $this->line("  Department: " . ($studentResult['user']['department']['name'] ?? 'None'));
                    $this->line("  Year: " . ($studentResult['user']['current_year'] ?? 'N/A'));
                }
            } catch (\Exception $e) {
                $this->error("✗ Login failed: " . $e->getMessage());
            }
        } else {
            $this->error("Admin user not found!");
        }

        $this->newLine();
        $this->info('Test completed!');
    }
}
