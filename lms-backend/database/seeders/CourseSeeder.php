<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Department;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $anatomyDept = Department::where('code', 'ANAT')->first();
        $physiologyDept = Department::where('code', 'PHYS')->first();
        $biochemistryDept = Department::where('code', 'BIOC')->first();

        $courses = [
            // Anatomy Department - Year 1 Course
            [
                'department_id' => $anatomyDept->id,
                'year' => 1,
                'title' => 'Basic Human Anatomy',
                'description' => 'Foundational course covering human anatomical structures and systems',
            ],
            
            // Physiology Department - Year 1 Course
            [
                'department_id' => $physiologyDept->id,
                'year' => 1,
                'title' => 'Human Physiology Fundamentals',
                'description' => 'Introduction to human physiological processes and mechanisms',
            ],
            
            // Biochemistry Department - Year 2 Course
            [
                'department_id' => $biochemistryDept->id,
                'year' => 2,
                'title' => 'Medical Biochemistry',
                'description' => 'Advanced biochemical processes relevant to medical practice',
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}