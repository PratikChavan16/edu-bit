<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Course;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $anatomyCourse = Course::whereHas('department', function($query) {
            $query->where('code', 'ANAT');
        })->first();
        
        $physiologyCourse = Course::whereHas('department', function($query) {
            $query->where('code', 'PHYS');
        })->first();
        
        $biochemistryCourse = Course::whereHas('department', function($query) {
            $query->where('code', 'BIOC');
        })->first();

        $subjects = [
            // Anatomy Course Subjects (3-4 subjects)
            [
                'course_id' => $anatomyCourse->id,
                'code' => 'ANAT101',
                'title' => 'General Anatomy',
                'credit_hours' => 4,
            ],
            [
                'course_id' => $anatomyCourse->id,
                'code' => 'ANAT102',
                'title' => 'Systemic Anatomy',
                'credit_hours' => 6,
            ],
            [
                'course_id' => $anatomyCourse->id,
                'code' => 'ANAT103',
                'title' => 'Embryology',
                'credit_hours' => 3,
            ],
            [
                'course_id' => $anatomyCourse->id,
                'code' => 'ANAT104',
                'title' => 'Histology',
                'credit_hours' => 3,
            ],
            
            // Physiology Course Subjects (3-4 subjects)
            [
                'course_id' => $physiologyCourse->id,
                'code' => 'PHYS101',
                'title' => 'General Physiology',
                'credit_hours' => 4,
            ],
            [
                'course_id' => $physiologyCourse->id,
                'code' => 'PHYS102',
                'title' => 'Cardiovascular Physiology',
                'credit_hours' => 4,
            ],
            [
                'course_id' => $physiologyCourse->id,
                'code' => 'PHYS103',
                'title' => 'Respiratory Physiology',
                'credit_hours' => 3,
            ],
            
            // Biochemistry Course Subjects (3-4 subjects)
            [
                'course_id' => $biochemistryCourse->id,
                'code' => 'BIOC201',
                'title' => 'Protein Biochemistry',
                'credit_hours' => 4,
            ],
            [
                'course_id' => $biochemistryCourse->id,
                'code' => 'BIOC202',
                'title' => 'Enzyme Kinetics',
                'credit_hours' => 3,
            ],
            [
                'course_id' => $biochemistryCourse->id,
                'code' => 'BIOC203',
                'title' => 'Metabolic Pathways',
                'credit_hours' => 5,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}