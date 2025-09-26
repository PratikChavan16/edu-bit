<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $principalRole = Role::where('name', 'principal')->first();
        $hodRole = Role::where('name', 'hod')->first();
        $facultyRole = Role::where('name', 'faculty')->first();
        $studentRole = Role::where('name', 'student')->first();
        
        $anatomyDept = Department::where('code', 'ANAT')->first();
        $physiologyDept = Department::where('code', 'PHYS')->first();
        $biochemistryDept = Department::where('code', 'BIOC')->first();

        // 1 Admin User
        $admin = User::create([
            'department_id' => null, // admins don't need departments
            'email' => 'admin@medical.edu',
            'password' => Hash::make('admin123'),
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'phone' => '+91-9876543210',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($adminRole);

        // Principal
        $principal = User::create([
            'department_id' => null,
            'email' => 'principal@medical.edu',
            'password' => Hash::make('principal123'),
            'first_name' => 'Dr. Rajesh',
            'last_name' => 'Kumar',
            'phone' => '+91-9876543211',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $principal->assignRole($principalRole);

        // HODs for each department
        $hod = User::create([
            'department_id' => $anatomyDept->id,
            'email' => 'hod.anatomy@medical.edu',
            'password' => Hash::make('hod123'),
            'first_name' => 'Dr. Priya',
            'last_name' => 'Sharma',
            'phone' => '+91-9876543212',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $hod->assignRole($hodRole);

        // 2-3 Faculty per department
        $facultyData = [
            // Anatomy Faculty
            [
                'department_id' => $anatomyDept->id,
                'email' => 'faculty1.anatomy@medical.edu',
                'first_name' => 'Dr. Arun',
                'last_name' => 'Patel',
                'phone' => '+91-9876543213',
            ],
            [
                'department_id' => $anatomyDept->id,
                'email' => 'faculty2.anatomy@medical.edu',
                'first_name' => 'Dr. Sunita',
                'last_name' => 'Verma',
                'phone' => '+91-9876543214',
            ],
            
            // Physiology Faculty
            [
                'department_id' => $physiologyDept->id,
                'email' => 'faculty1.physiology@medical.edu',
                'first_name' => 'Dr. Ramesh',
                'last_name' => 'Gupta',
                'phone' => '+91-9876543215',
            ],
            [
                'department_id' => $physiologyDept->id,
                'email' => 'faculty2.physiology@medical.edu',
                'first_name' => 'Dr. Meera',
                'last_name' => 'Nair',
                'phone' => '+91-9876543216',
            ],
            
            // Biochemistry Faculty
            [
                'department_id' => $biochemistryDept->id,
                'email' => 'faculty1.biochemistry@medical.edu',
                'first_name' => 'Dr. Vikram',
                'last_name' => 'Singh',
                'phone' => '+91-9876543217',
            ],
        ];

        foreach ($facultyData as $faculty) {
            $user = User::create([
                'department_id' => $faculty['department_id'],
                'email' => $faculty['email'],
                'password' => Hash::make('faculty123'),
                'first_name' => $faculty['first_name'],
                'last_name' => $faculty['last_name'],
                'phone' => $faculty['phone'],
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $user->assignRole($facultyRole);
        }

        // 5-10 Students per year (Years 1-4)
        $studentCount = 0;
        for ($year = 1; $year <= 4; $year++) {
            for ($i = 1; $i <= 8; $i++) { // 8 students per year
                $studentCount++;
                $student = User::create([
                    'department_id' => collect([$anatomyDept->id, $physiologyDept->id, $biochemistryDept->id])->random(), // assign random department
                    'email' => "student{$studentCount}@medical.edu",
                    'password' => Hash::make('student123'),
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'phone' => '+91-' . fake()->numerify('##########'),
                    'current_year' => $year,
                    'enrollment_number' => 'MED2024' . str_pad($studentCount, 3, '0', STR_PAD_LEFT),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
                $student->assignRole($studentRole);
            }
        }
    }
}