<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateTestUsers extends Command
{
    protected $signature = 'create:test-users';
    protected $description = 'Create test users for the application';

    public function handle()
    {
        $this->info('Creating test users...');

        // Get or create department
        $department = Department::firstOrCreate([
            'name' => 'Physiology',
            'code' => 'PHYS'
        ]);

        // Create student user
        $student = User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Student',
                'email' => 'student@example.com',
                'password' => Hash::make('password'),
                'department_id' => $department->id,
                'current_year' => 1,
                'email_verified_at' => now(),
            ]
        );

        // Create faculty user
        $faculty = User::updateOrCreate(
            ['email' => 'faculty@example.com'],
            [
                'first_name' => 'Dr. Sarah',
                'last_name' => 'Professor',
                'email' => 'faculty@example.com',
                'password' => Hash::make('password'),
                'department_id' => $department->id,
                'email_verified_at' => now(),
            ]
        );

        // Assign roles
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $facultyRole = Role::firstOrCreate(['name' => 'faculty']);

        $student->syncRoles([$studentRole]);
        $faculty->syncRoles([$facultyRole]);

        $this->info('Test users created successfully!');
        $this->line('Student: student@example.com / password');
        $this->line('Faculty: faculty@example.com / password');

        return 0;
    }
}
