<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\RoleDetail;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'student', 'display_name' => 'Student', 'description' => 'Medical student with learning access'],
            ['name' => 'faculty', 'display_name' => 'Faculty', 'description' => 'Teaching faculty with content management'],
            ['name' => 'hod', 'display_name' => 'Head of Department', 'description' => 'Department head with departmental management'],
            ['name' => 'principal', 'display_name' => 'Principal', 'description' => 'College principal with institutional oversight'],
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'System administrator with full access'],
        ];

        foreach ($roles as $roleData) {
            $role = Role::create([
                'name' => $roleData['name'],
                'guard_name' => 'web'
            ]);
            
            RoleDetail::create([
                'role_id' => $role->id,
                'display_name' => $roleData['display_name'],
                'description' => $roleData['description']
            ]);
        }
    }
}