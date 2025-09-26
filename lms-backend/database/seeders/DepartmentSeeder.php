<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['name' => 'Anatomy', 'code' => 'ANAT', 'description' => 'Human anatomy and structure'],
            ['name' => 'Physiology', 'code' => 'PHYS', 'description' => 'Human physiology and functions'],
            ['name' => 'Biochemistry', 'code' => 'BIOC', 'description' => 'Medical biochemistry and molecular biology'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}