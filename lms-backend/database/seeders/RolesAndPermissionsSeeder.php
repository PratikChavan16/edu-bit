<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Department management
            'view departments',
            'create departments',
            'edit departments',
            'delete departments',
            
            // Course management
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            
            // Subject management
            'view subjects',
            'create subjects',
            'edit subjects',
            'delete subjects',
            
            // Assessment management
            'view assessments',
            'create assessments',
            'edit assessments',
            'delete assessments',
            'grade assessments',
            
            // Medical cases
            'view medical cases',
            'create medical cases',
            'edit medical cases',
            'delete medical cases',
            
            // Clinical rotations
            'view clinical rotations',
            'create clinical rotations',
            'edit clinical rotations',
            'delete clinical rotations',
            'supervise rotations',
            
            // Competencies
            'view competencies',
            'create competencies',
            'edit competencies',
            'delete competencies',
            'assess competencies',
            
            // Announcements
            'view announcements',
            'create announcements',
            'edit announcements',
            'delete announcements',
            
            // Reports and analytics
            'view reports',
            'generate reports',
            'view analytics',
            
            // System administration
            'manage system',
            'view logs',
            'backup system',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Student role
        $studentRole = Role::firstOrCreate(['name' => 'student']);
        $studentRole->syncPermissions([
            'view courses',
            'view subjects',
            'view assessments',
            'view medical cases',
            'view clinical rotations',
            'view announcements',
        ]);

        // Faculty role
        $facultyRole = Role::firstOrCreate(['name' => 'faculty']);
        $facultyRole->syncPermissions([
            'view users',
            'view courses',
            'view subjects',
            'create subjects',
            'edit subjects',
            'view assessments',
            'create assessments',
            'edit assessments',
            'grade assessments',
            'view medical cases',
            'create medical cases',
            'edit medical cases',
            'view clinical rotations',
            'supervise rotations',
            'view competencies',
            'assess competencies',
            'view announcements',
            'create announcements',
            'view reports',
        ]);

        // HOD role (Head of Department)
        $hodRole = Role::firstOrCreate(['name' => 'hod']);
        $hodRole->syncPermissions([
            'view users',
            'create users',
            'edit users',
            'view departments',
            'edit departments',
            'view courses',
            'create courses',
            'edit courses',
            'view subjects',
            'create subjects',
            'edit subjects',
            'delete subjects',
            'view assessments',
            'create assessments',
            'edit assessments',
            'delete assessments',
            'grade assessments',
            'view medical cases',
            'create medical cases',
            'edit medical cases',
            'delete medical cases',
            'view clinical rotations',
            'create clinical rotations',
            'edit clinical rotations',
            'supervise rotations',
            'view competencies',
            'create competencies',
            'edit competencies',
            'assess competencies',
            'view announcements',
            'create announcements',
            'edit announcements',
            'view reports',
            'generate reports',
            'view analytics',
        ]);

        // Principal role
        $principalRole = Role::firstOrCreate(['name' => 'principal']);
        $principalRole->syncPermissions([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view departments',
            'create departments',
            'edit departments',
            'delete departments',
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'view subjects',
            'create subjects',
            'edit subjects',
            'delete subjects',
            'view assessments',
            'create assessments',
            'edit assessments',
            'delete assessments',
            'grade assessments',
            'view medical cases',
            'create medical cases',
            'edit medical cases',
            'delete medical cases',
            'view clinical rotations',
            'create clinical rotations',
            'edit clinical rotations',
            'delete clinical rotations',
            'supervise rotations',
            'view competencies',
            'create competencies',
            'edit competencies',
            'delete competencies',
            'assess competencies',
            'view announcements',
            'create announcements',
            'edit announcements',
            'delete announcements',
            'view reports',
            'generate reports',
            'view analytics',
        ]);

        // Admin role (Super Administrator)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // Add role details
        $this->seedRoleDetails();
    }

    private function seedRoleDetails()
    {
        $roles = Role::all();
        
        $roleDetails = [
            'student' => [
                'display_name' => 'Student',
                'description' => 'MBBS Students enrolled in various academic years',
            ],
            'faculty' => [
                'display_name' => 'Faculty',
                'description' => 'Teaching faculty members across different departments',
            ],
            'hod' => [
                'display_name' => 'Head of Department',
                'description' => 'Department heads with administrative responsibilities',
            ],
            'principal' => [
                'display_name' => 'Principal',
                'description' => 'College principal with institutional oversight',
            ],
            'admin' => [
                'display_name' => 'System Administrator',
                'description' => 'Technical administrators with full system access',
            ],
        ];

        foreach ($roles as $role) {
            if (isset($roleDetails[$role->name])) {
                // Check if role detail already exists
                $existingDetail = DB::table('role_details')->where('role_id', $role->id)->first();
                if (!$existingDetail) {
                    DB::table('role_details')->insert([
                        'role_id' => $role->id,
                        'display_name' => $roleDetails[$role->name]['display_name'],
                        'description' => $roleDetails[$role->name]['description'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}