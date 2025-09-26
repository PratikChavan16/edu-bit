<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MBBSDataSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks for SQLite
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        // Seed Academic Years
        $this->seedAcademicYears();
        
        // Seed MBBS Departments
        $this->seedMBBSDepartments();
        
        // Seed MBBS Courses
        $this->seedMBBSCourses();
        
        // Seed MBBS Subjects
        $this->seedMBBSSubjects();
        
        // Seed Users (Students, Faculty, Admin)
        $this->seedUsers();
        
        // Seed Medical Cases
        $this->seedMedicalCases();
        
        // Seed Competencies
        $this->seedCompetencies();
        
        // Seed Practical Skills
        $this->seedPracticalSkills();
        
        // Seed Assessment Types
        $this->seedAssessmentTypes();
        
        // Seed Sample Assessments
        $this->seedSampleAssessments();
        
        // Seed Medical References
        $this->seedMedicalReferences();
        
        // Re-enable foreign key checks
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
    
    private function seedAcademicYears()
    {
        $years = [
            [
                'name' => '1st Year MBBS',
                'code' => 'MBBS1',
                'description' => 'Foundation sciences and basic medical subjects',
                'duration_months' => 18,
                'semester_structure' => json_encode([
                    'semester_1' => ['duration' => 9, 'subjects' => ['Anatomy', 'Physiology', 'Biochemistry']],
                    'semester_2' => ['duration' => 9, 'subjects' => ['Anatomy', 'Physiology', 'Biochemistry', 'Forensic Medicine']]
                ]),
                'order_index' => 1
            ],
            [
                'name' => '2nd Year MBBS',
                'code' => 'MBBS2',
                'description' => 'Pathophysiology and pharmacology',
                'duration_months' => 12,
                'semester_structure' => json_encode([
                    'semester_1' => ['duration' => 6, 'subjects' => ['Pathology', 'Pharmacology', 'Microbiology']],
                    'semester_2' => ['duration' => 6, 'subjects' => ['Pathology', 'Pharmacology', 'Microbiology', 'Community Medicine']]
                ]),
                'order_index' => 2
            ],
            [
                'name' => '3rd Year MBBS',
                'code' => 'MBBS3',
                'description' => 'Clinical subjects and patient care',
                'duration_months' => 12,
                'semester_structure' => json_encode([
                    'semester_1' => ['duration' => 6, 'subjects' => ['Medicine', 'Surgery', 'Obstetrics & Gynecology']],
                    'semester_2' => ['duration' => 6, 'subjects' => ['Medicine', 'Surgery', 'Pediatrics']]
                ]),
                'order_index' => 3
            ],
            [
                'name' => '4th Year MBBS',
                'code' => 'MBBS4',
                'description' => 'Specialized clinical subjects',
                'duration_months' => 12,
                'semester_structure' => json_encode([
                    'semester_1' => ['duration' => 6, 'subjects' => ['ENT', 'Ophthalmology', 'Dermatology']],
                    'semester_2' => ['duration' => 6, 'subjects' => ['Psychiatry', 'Radiology', 'Anesthesiology']]
                ]),
                'order_index' => 4
            ],
            [
                'name' => 'Internship',
                'code' => 'INT',
                'description' => 'Rotational clinical postings',
                'duration_months' => 12,
                'semester_structure' => json_encode([
                    'rotations' => [
                        'Medicine' => 2, 'Surgery' => 2, 'Obstetrics & Gynecology' => 2,
                        'Pediatrics' => 2, 'Community Medicine' => 2, 'Emergency Medicine' => 2
                    ]
                ]),
                'order_index' => 5
            ]
        ];
        
        foreach ($years as $year) {
            DB::table('academic_years')->insert(array_merge($year, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
    
    private function seedMBBSDepartments()
    {
        $departments = [
            // Preclinical Departments
            ['name' => 'Anatomy', 'code' => 'ANAT', 'description' => 'Human anatomy and morphology'],
            ['name' => 'Physiology', 'code' => 'PHYS', 'description' => 'Human physiology and functions'],
            ['name' => 'Biochemistry', 'code' => 'BIOC', 'description' => 'Medical biochemistry and molecular biology'],
            ['name' => 'Pathology', 'code' => 'PATH', 'description' => 'Disease processes and diagnostics'],
            ['name' => 'Pharmacology', 'code' => 'PHAR', 'description' => 'Drug actions and therapeutics'],
            ['name' => 'Microbiology', 'code' => 'MICR', 'description' => 'Medical microbiology and immunology'],
            ['name' => 'Forensic Medicine', 'code' => 'FMED', 'description' => 'Legal medicine and toxicology'],
            
            // Paraclinical Departments
            ['name' => 'Community Medicine', 'code' => 'COMM', 'description' => 'Preventive and social medicine'],
            ['name' => 'Radiology', 'code' => 'RADI', 'description' => 'Medical imaging and diagnostics'],
            ['name' => 'Anesthesiology', 'code' => 'ANES', 'description' => 'Anesthesia and critical care'],
            
            // Clinical Departments
            ['name' => 'Medicine', 'code' => 'MED', 'description' => 'Internal medicine and subspecialties'],
            ['name' => 'Surgery', 'code' => 'SURG', 'description' => 'General surgery and subspecialties'],
            ['name' => 'Obstetrics & Gynecology', 'code' => 'OBGY', 'description' => 'Women\'s health and reproductive medicine'],
            ['name' => 'Pediatrics', 'code' => 'PEDI', 'description' => 'Child health and development'],
            ['name' => 'ENT', 'code' => 'ENT', 'description' => 'Ear, nose, and throat disorders'],
            ['name' => 'Ophthalmology', 'code' => 'OPHT', 'description' => 'Eye and vision disorders'],
            ['name' => 'Dermatology', 'code' => 'DERM', 'description' => 'Skin and related disorders'],
            ['name' => 'Psychiatry', 'code' => 'PSYC', 'description' => 'Mental health and behavioral disorders'],
            ['name' => 'Emergency Medicine', 'code' => 'EMER', 'description' => 'Emergency and trauma care'],
        ];
        
        foreach ($departments as $dept) {
            // Check if department already exists
            $existingDept = DB::table('departments')->where('code', $dept['code'])->first();
            if (!$existingDept) {
                DB::table('departments')->insert(array_merge($dept, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
            }
        }
    }
    
    private function seedMBBSCourses()
    {
        $academicYears = DB::table('academic_years')->get();
        $departments = DB::table('departments')->get();
        
        $courseMapping = [
            'MBBS1' => ['ANAT', 'PHYS', 'BIOC', 'FMED'],
            'MBBS2' => ['PATH', 'PHAR', 'MICR', 'COMM'],
            'MBBS3' => ['MED', 'SURG', 'OBGY', 'PEDI'],
            'MBBS4' => ['ENT', 'OPHT', 'DERM', 'PSYC', 'RADI', 'ANES'],
            'INT' => ['MED', 'SURG', 'OBGY', 'PEDI', 'COMM', 'EMER']
        ];
        
        foreach ($academicYears as $year) {
            $deptCodes = $courseMapping[$year->code] ?? [];
            
            foreach ($deptCodes as $deptCode) {
                $dept = $departments->where('code', $deptCode)->first();
                if ($dept) {
                    // Check if course already exists
                    $courseTitle = $dept->name . ' - ' . $year->name;
                    $existingCourse = DB::table('courses')
                        ->where('department_id', $dept->id)
                        ->where('year', $year->order_index)
                        ->first();
                    
                    if (!$existingCourse) {
                        DB::table('courses')->insert([
                            'department_id' => $dept->id,
                            'year' => $year->order_index,
                            'title' => $courseTitle,
                            'description' => $dept->description . ' for ' . $year->name,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }
    }
    
    private function seedMBBSSubjects()
    {
        $courses = DB::table('courses')
            ->join('departments', 'courses.department_id', '=', 'departments.id')
            ->select('courses.*', 'departments.code as dept_code', 'departments.name as dept_name')
            ->get();
        
        foreach ($courses as $course) {
            $subjects = $this->getSubjectsForDepartment($course->dept_code);
            
            foreach ($subjects as $subject) {
                // Check if subject already exists
                $existingSubject = DB::table('subjects')
                    ->where('course_id', $course->id)
                    ->where('code', $subject['code'])
                    ->first();
                
                if (!$existingSubject) {
                    DB::table('subjects')->insert([
                        'course_id' => $course->id,
                        'code' => $subject['code'],
                        'title' => $subject['title'],
                        'credit_hours' => $this->getCreditHours($course->dept_code, ''),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
    
    private function seedUsers()
    {
        // Create Admin User
        DB::table('users')->insert([
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@mbbslms.edu',
            'password' => Hash::make('admin123'),
            'department_id' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Create Faculty Users
        $departments = DB::table('departments')->get();
        foreach ($departments as $dept) {
            DB::table('users')->insert([
                'first_name' => 'Dr. ' . $dept->name,
                'last_name' => 'Professor',
                'email' => strtolower($dept->code) . '.prof@mbbslms.edu',
                'password' => Hash::make('faculty123'),
                'department_id' => $dept->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        // Create Student Users for each year
        $years = ['1st', '2nd', '3rd', '4th', 'Intern'];
        foreach ($years as $index => $year) {
            for ($i = 1; $i <= 5; $i++) {
                DB::table('users')->insert([
                    'first_name' => $year . ' Year Student',
                    'last_name' => $i,
                    'email' => strtolower($year) . '.student' . $i . '@mbbslms.edu',
                    'password' => Hash::make('student123'),
                    'department_id' => 1, // Default to Anatomy
                    'current_year' => $index + 1,
                    'enrollment_number' => 'MBBS' . date('Y') . str_pad(($index * 5) + $i, 3, '0', STR_PAD_LEFT),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        // Assign roles after users are created
        $this->assignRoles();
    }
    
    private function assignRoles()
    {
        $users = DB::table('users')->get();
        
        foreach ($users as $user) {
            if (strpos($user->email, 'admin') !== false) {
                DB::table('model_has_roles')->insert([
                    'role_id' => 1, // admin role
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id
                ]);
            } elseif (strpos($user->email, 'prof') !== false) {
                DB::table('model_has_roles')->insert([
                    'role_id' => 2, // faculty role
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id
                ]);
            } else {
                DB::table('model_has_roles')->insert([
                    'role_id' => 3, // student role
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $user->id
                ]);
            }
        }
    }
    
    private function seedMedicalCases()
    {
        $subjects = DB::table('subjects')->get();
        
        foreach ($subjects->take(5) as $subject) { // Sample cases for first 5 subjects
            DB::table('medical_cases')->insert([
                'subject_id' => $subject->id,
                'created_by' => 2, // First faculty member
                'case_title' => 'Case Study: ' . $subject->title,
                'patient_history' => 'A 45-year-old patient presents with relevant symptoms related to ' . $subject->title,
                'chief_complaint' => 'Main presenting complaint related to the subject matter',
                'history_of_present_illness' => 'Detailed history of the current illness',
                'past_medical_history' => 'Relevant past medical history',
                'family_history' => 'Family history details',
                'social_history' => 'Social and lifestyle factors',
                'physical_examination' => json_encode([
                    'general_examination' => 'Normal vital signs',
                    'systemic_examination' => 'Relevant findings'
                ]),
                'investigation_results' => json_encode([
                    'laboratory' => 'Relevant lab results',
                    'imaging' => 'Imaging findings'
                ]),
                'differential_diagnosis' => json_encode([
                    'Primary diagnosis',
                    'Alternative diagnosis'
                ]),
                'final_diagnosis' => 'Final confirmed diagnosis',
                'treatment_plan' => 'Comprehensive treatment approach',
                'learning_points' => 'Key learning objectives from this case',
                'difficulty_level' => 'intermediate',
                'case_type' => 'simulated',
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
    
    private function seedCompetencies()
    {
        $subjects = DB::table('subjects')->get();
        
        $competencyTemplates = [
            'knowledge' => [
                'Understands basic concepts',
                'Explains pathophysiology',
                'Describes clinical features',
                'Identifies diagnostic criteria'
            ],
            'skills' => [
                'Performs basic examination',
                'Demonstrates procedural skills',
                'Uses diagnostic tools',
                'Applies clinical reasoning'
            ],
            'attitude' => [
                'Shows professional behavior',
                'Demonstrates empathy',
                'Maintains patient confidentiality',
                'Works effectively in team'
            ]
        ];
        
        foreach ($subjects->take(10) as $subject) {
            foreach ($competencyTemplates as $domain => $templates) {
                foreach ($templates as $index => $template) {
                    DB::table('competencies')->insert([
                        'subject_id' => $subject->id,
                        'competency_code' => strtoupper($subject->code) . '-' . strtoupper($domain) . '-' . ($index + 1),
                        'competency_title' => $template . ' in ' . $subject->title,
                        'description' => 'Detailed description of the competency',
                        'domain' => $domain,
                        'level' => $domain === 'knowledge' ? 'knows' : ($domain === 'skills' ? 'shows_how' : 'does'),
                        'assessment_methods' => json_encode(['written_exam', 'practical_exam', 'viva']),
                        'is_core' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
    
    private function seedPracticalSkills()
    {
        $practicalSubjects = DB::table('subjects')
            ->whereIn('code', ['ANAT101', 'PHYS101', 'BIOC101', 'PATH101', 'MICR101'])
            ->get();
        
        $skillsData = [
            'ANAT101' => [
                ['skill_name' => 'Dissection Technique', 'description' => 'Basic dissection skills for anatomical study'],
                ['skill_name' => 'Histology Slide Reading', 'description' => 'Identification of tissue structures under microscope'],
            ],
            'PHYS101' => [
                ['skill_name' => 'Blood Pressure Measurement', 'description' => 'Accurate measurement of arterial blood pressure'],
                ['skill_name' => 'ECG Recording', 'description' => 'Recording and basic interpretation of electrocardiogram'],
            ],
            'BIOC101' => [
                ['skill_name' => 'Laboratory Techniques', 'description' => 'Basic biochemical laboratory procedures'],
                ['skill_name' => 'Enzyme Assays', 'description' => 'Performing and interpreting enzyme activity tests'],
            ]
        ];
        
        foreach ($practicalSubjects as $subject) {
            if (isset($skillsData[$subject->code])) {
                foreach ($skillsData[$subject->code] as $skill) {
                    DB::table('practical_skills')->insert(array_merge($skill, [
                        'subject_id' => $subject->id,
                        'procedure_steps' => 'Step-by-step procedure for ' . $skill['skill_name'],
                        'equipment_required' => json_encode(['Basic equipment list']),
                        'safety_considerations' => json_encode(['Safety protocols']),
                        'assessment_criteria' => json_encode(['Accuracy', 'Technique', 'Safety']),
                        'skill_type' => 'basic',
                        'requires_supervision' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]));
                }
            }
        }
    }
    
    private function seedAssessmentTypes()
    {
        $types = [
            ['name' => 'mcq', 'display_name' => 'Multiple Choice Questions', 'description' => 'Single best answer questions'],
            ['name' => 'saq', 'display_name' => 'Short Answer Questions', 'description' => 'Brief descriptive answers'],
            ['name' => 'laq', 'display_name' => 'Long Answer Questions', 'description' => 'Detailed essay-type answers'],
            ['name' => 'practical', 'display_name' => 'Practical Examination', 'description' => 'Hands-on skill assessment'],
            ['name' => 'viva', 'display_name' => 'Viva Voce', 'description' => 'Oral examination'],
            ['name' => 'case_study', 'display_name' => 'Case Study', 'description' => 'Clinical case analysis']
        ];
        
        foreach ($types as $type) {
            DB::table('assessment_types')->insert(array_merge($type, [
                'settings' => json_encode(['time_limit' => 60, 'max_attempts' => 3]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
    
    private function seedSampleAssessments()
    {
        $subjects = DB::table('subjects')->take(5)->get();
        $assessmentTypes = DB::table('assessment_types')->get();
        
        foreach ($subjects as $subject) {
            foreach ($assessmentTypes->take(3) as $type) {
                DB::table('assessments')->insert([
                    'subject_id' => $subject->id,
                    'title' => ucfirst($type->display_name) . ' - ' . $subject->title,
                    'type' => $type->name,
                    'competency_tags' => json_encode(['basic_knowledge', 'clinical_application']),
                    'author_id' => 2,
                    'duration_minutes' => $type->name === 'mcq' ? 60 : 120,
                    'start_at' => now()->addDays(rand(1, 30)),
                    'end_at' => now()->addDays(rand(31, 60)),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
    
    private function seedMedicalReferences()
    {
        $subjects = DB::table('subjects')->get();
        
        $referenceTemplates = [
            'textbook' => [
                'title' => 'Standard Textbook of {{subject}}',
                'authors' => 'Leading Authors in {{subject}}',
                'publication' => 'Medical Publishers',
                'edition' => '10th Edition'
            ],
            'atlas' => [
                'title' => 'Atlas of {{subject}}',
                'authors' => 'Visual Learning Authors',
                'publication' => 'Educational Press',
                'edition' => '5th Edition'
            ]
        ];
        
        foreach ($subjects->take(10) as $subject) {
            foreach ($referenceTemplates as $type => $template) {
                DB::table('medical_references')->insert([
                    'subject_id' => $subject->id,
                    'title' => str_replace('{{subject}}', $subject->title, $template['title']),
                    'authors' => str_replace('{{subject}}', $subject->title, $template['authors']),
                    'publication' => $template['publication'],
                    'edition' => $template['edition'],
                    'publication_year' => 2023,
                    'description' => 'Comprehensive reference for ' . $subject->title,
                    'reference_type' => $type,
                    'is_recommended' => true,
                    'is_mandatory' => $type === 'textbook',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
    
    // Helper methods for department-specific data
    private function getDepartmentCompetencies($deptCode)
    {
        $competencies = [
            'ANAT' => ['gross_anatomy', 'histology', 'embryology', 'neuroanatomy'],
            'PHYS' => ['cardiovascular', 'respiratory', 'nervous_system', 'endocrine'],
            'MED' => ['diagnosis', 'treatment', 'patient_care', 'clinical_reasoning'],
            'SURG' => ['surgical_skills', 'pre_operative_care', 'post_operative_care', 'emergency_procedures']
        ];
        
        return $competencies[$deptCode] ?? ['general_competency'];
    }
    
    private function getCourseType($deptCode)
    {
        $clinical = ['MED', 'SURG', 'OBGY', 'PEDI', 'ENT', 'OPHT', 'DERM', 'PSYC', 'EMER'];
        $practical = ['ANAT', 'PHYS', 'BIOC', 'PATH', 'MICR'];
        
        if (in_array($deptCode, $clinical)) return 'clinical';
        if (in_array($deptCode, $practical)) return 'practical';
        return 'theory';
    }
    
    private function getCreditHours($deptCode, $yearCode)
    {
        $creditMapping = [
            'MBBS1' => 6, 'MBBS2' => 5, 'MBBS3' => 8, 'MBBS4' => 4, 'INT' => 12
        ];
        
        return $creditMapping[$yearCode] ?? 4;
    }
    
    private function getLearningOutcomes($deptCode)
    {
        return [
            'Knowledge of basic concepts in ' . $deptCode,
            'Application of theoretical knowledge',
            'Clinical correlation and reasoning',
            'Professional skill development'
        ];
    }
    
    private function getSubjectsForDepartment($deptCode)
    {
        $subjectMapping = [
            'ANAT' => [
                ['code' => 'ANAT101', 'title' => 'General Anatomy'],
                ['code' => 'ANAT102', 'title' => 'Systemic Anatomy'],
                ['code' => 'ANAT103', 'title' => 'Neuroanatomy']
            ],
            'PHYS' => [
                ['code' => 'PHYS101', 'title' => 'General Physiology'],
                ['code' => 'PHYS102', 'title' => 'Cardiovascular Physiology'],
                ['code' => 'PHYS103', 'title' => 'Respiratory Physiology']
            ],
            'BIOC' => [
                ['code' => 'BIOC101', 'title' => 'General Biochemistry'],
                ['code' => 'BIOC102', 'title' => 'Clinical Biochemistry']
            ],
            'MED' => [
                ['code' => 'MED101', 'title' => 'Internal Medicine'],
                ['code' => 'MED102', 'title' => 'Cardiology'],
                ['code' => 'MED103', 'title' => 'Pulmonology']
            ]
        ];
        
        return $subjectMapping[$deptCode] ?? [
            ['code' => $deptCode . '101', 'title' => 'General ' . $deptCode]
        ];
    }
    
    private function getSubjectType($deptCode)
    {
        return in_array($deptCode, ['MED', 'SURG', 'OBGY', 'PEDI']) ? 'rotation' : 'core';
    }
    
    private function getTheoryHours($deptCode)
    {
        $mapping = ['ANAT' => 100, 'PHYS' => 80, 'MED' => 120, 'SURG' => 100];
        return $mapping[$deptCode] ?? 60;
    }
    
    private function getPracticalHours($deptCode)
    {
        $mapping = ['ANAT' => 80, 'PHYS' => 60, 'BIOC' => 40, 'PATH' => 50];
        return $mapping[$deptCode] ?? 20;
    }
    
    private function getClinicalHours($deptCode)
    {
        $mapping = ['MED' => 200, 'SURG' => 180, 'OBGY' => 100, 'PEDI' => 120];
        return $mapping[$deptCode] ?? 0;
    }
    
    private function getPrerequisites($deptCode)
    {
        $prerequisites = [
            'PHYS' => ['ANAT101'],
            'MED' => ['PHYS101', 'PATH101'],
            'SURG' => ['ANAT101', 'PHYS101']
        ];
        
        return $prerequisites[$deptCode] ?? [];
    }
    
    private function getLearningObjectives($deptCode)
    {
        return [
            'Understand fundamental concepts',
            'Apply knowledge in clinical context',
            'Develop practical skills',
            'Demonstrate professional competency'
        ];
    }
}