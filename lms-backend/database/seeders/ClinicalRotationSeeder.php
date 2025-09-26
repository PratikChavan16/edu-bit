<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClinicalRotationSeeder extends Seeder
{
    public function run()
    {
        // Get departments that have clinical postings
        $clinicalDepts = DB::table('departments')
            ->where('has_clinical_postings', true)
            ->get();
        
        // Get 3rd year, 4th year, and internship academic years
        $clinicalYears = DB::table('academic_years')
            ->whereIn('code', ['MBBS3', 'MBBS4', 'INT'])
            ->get();
        
        // Get some sample students
        $students = DB::table('users')
            ->whereIn('current_year', [3, 4, 5]) // 3rd year, 4th year, internship
            ->take(10)
            ->get();
        
        // Get faculty supervisors from clinical departments
        $supervisors = DB::table('users')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->where('departments.department_type', 'clinical')
            ->select('users.*')
            ->get();
        
        foreach ($students as $student) {
            $this->createRotationsForStudent($student, $clinicalDepts, $clinicalYears, $supervisors);
        }
    }
    
    private function createRotationsForStudent($student, $clinicalDepts, $clinicalYears, $supervisors)
    {
        // Determine which year this student is in
        $currentYear = $clinicalYears->where('order_index', $student->current_year)->first();
        
        if (!$currentYear) return;
        
        // Create rotations based on year
        if ($currentYear->code === 'MBBS3') {
            $this->create3rdYearRotations($student, $clinicalDepts, $supervisors);
        } elseif ($currentYear->code === 'MBBS4') {
            $this->create4thYearRotations($student, $clinicalDepts, $supervisors);
        } elseif ($currentYear->code === 'INT') {
            $this->createInternshipRotations($student, $clinicalDepts, $supervisors);
        }
    }
    
    private function create3rdYearRotations($student, $clinicalDepts, $supervisors)
    {
        $rotationDepts = ['MED', 'SURG', 'OBGY', 'PEDI'];
        $startDate = now()->startOfYear();
        
        foreach ($rotationDepts as $index => $deptCode) {
            $dept = $clinicalDepts->where('code', $deptCode)->first();
            $supervisor = $supervisors->where('department_id', $dept->id)->first();
            
            if ($dept && $supervisor) {
                $rotationStart = $startDate->copy()->addMonths($index * 3);
                $rotationEnd = $rotationStart->copy()->addMonths(3)->subDay();
                
                DB::table('clinical_rotations')->insert([
                    'student_id' => $student->id,
                    'department_id' => $dept->id,
                    'supervisor_id' => $supervisor->id,
                    'rotation_name' => $dept->name . ' Rotation',
                    'start_date' => $rotationStart,
                    'end_date' => $rotationEnd,
                    'duration_weeks' => 12,
                    'objectives' => json_encode([
                        'Clinical exposure to ' . $dept->name,
                        'Patient interaction skills',
                        'Basic diagnostic procedures',
                        'Case history taking'
                    ]),
                    'learning_activities' => json_encode([
                        'Ward rounds',
                        'OPD sessions',
                        'Case presentations',
                        'Skill demonstrations'
                    ]),
                    'assessment_methods' => json_encode([
                        'Clinical logbook',
                        'Case presentations',
                        'Supervisor evaluation',
                        'Written assessment'
                    ]),
                    'expected_competencies' => json_encode($this->getCompetenciesForDepartment($deptCode)),
                    'status' => $this->getRotationStatus($rotationStart, $rotationEnd),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
    
    private function create4thYearRotations($student, $clinicalDepts, $supervisors)
    {
        $rotationDepts = ['ENT', 'OPHT', 'DERM', 'PSYC', 'RADI', 'ANES'];
        $startDate = now()->startOfYear();
        
        foreach ($rotationDepts as $index => $deptCode) {
            $dept = $clinicalDepts->where('code', $deptCode)->first();
            $supervisor = $supervisors->where('department_id', $dept->id)->first();
            
            if ($dept && $supervisor) {
                $rotationStart = $startDate->copy()->addMonths($index * 2);
                $rotationEnd = $rotationStart->copy()->addMonths(2)->subDay();
                
                DB::table('clinical_rotations')->insert([
                    'student_id' => $student->id,
                    'department_id' => $dept->id,
                    'supervisor_id' => $supervisor->id,
                    'rotation_name' => $dept->name . ' Rotation',
                    'start_date' => $rotationStart,
                    'end_date' => $rotationEnd,
                    'duration_weeks' => 8,
                    'objectives' => json_encode([
                        'Specialized knowledge in ' . $dept->name,
                        'Diagnostic skills',
                        'Treatment modalities',
                        'Professional communication'
                    ]),
                    'learning_activities' => json_encode([
                        'Specialist clinics',
                        'Procedure observations',
                        'Case discussions',
                        'Research activities'
                    ]),
                    'assessment_methods' => json_encode([
                        'Specialty logbook',
                        'Mini-CEX',
                        'Portfolio assessment',
                        'Supervisor feedback'
                    ]),
                    'expected_competencies' => json_encode($this->getCompetenciesForDepartment($deptCode)),
                    'status' => $this->getRotationStatus($rotationStart, $rotationEnd),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
    
    private function createInternshipRotations($student, $clinicalDepts, $supervisors)
    {
        $rotationDepts = ['MED', 'SURG', 'OBGY', 'PEDI', 'COMM', 'EMER'];
        $startDate = now()->startOfYear();
        
        foreach ($rotationDepts as $index => $deptCode) {
            $dept = $clinicalDepts->where('code', $deptCode)->first();
            $supervisor = $supervisors->where('department_id', $dept->id)->first();
            
            if ($dept && $supervisor) {
                $rotationStart = $startDate->copy()->addMonths($index * 2);
                $rotationEnd = $rotationStart->copy()->addMonths(2)->subDay();
                
                DB::table('clinical_rotations')->insert([
                    'student_id' => $student->id,
                    'department_id' => $dept->id,
                    'supervisor_id' => $supervisor->id,
                    'rotation_name' => $dept->name . ' Internship',
                    'start_date' => $rotationStart,
                    'end_date' => $rotationEnd,
                    'duration_weeks' => 8,
                    'objectives' => json_encode([
                        'Independent patient care',
                        'Clinical decision making',
                        'Emergency management',
                        'Professional responsibility'
                    ]),
                    'learning_activities' => json_encode([
                        'Independent patient care',
                        'Emergency duties',
                        'Procedure assistance',
                        'Multidisciplinary meetings'
                    ]),
                    'assessment_methods' => json_encode([
                        'Internship logbook',
                        'Supervisor assessment',
                        'Peer evaluation',
                        'Patient feedback'
                    ]),
                    'expected_competencies' => json_encode($this->getCompetenciesForDepartment($deptCode)),
                    'status' => $this->getRotationStatus($rotationStart, $rotationEnd),
                    'is_internship' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
    
    private function getCompetenciesForDepartment($deptCode)
    {
        $competencies = [
            'MED' => [
                'History taking and clinical examination',
                'Interpretation of investigations',
                'Diagnosis and management of common medical conditions',
                'Emergency medicine protocols'
            ],
            'SURG' => [
                'Surgical anatomy and procedures',
                'Pre and post-operative care',
                'Wound management',
                'Surgical emergency management'
            ],
            'OBGY' => [
                'Antenatal care and delivery',
                'Gynecological examination',
                'Family planning counseling',
                'Emergency obstetric care'
            ],
            'PEDI' => [
                'Pediatric examination techniques',
                'Growth and development assessment',
                'Childhood immunizations',
                'Pediatric emergency care'
            ],
            'ENT' => [
                'ENT examination procedures',
                'Audiometry and hearing assessment',
                'Common ENT procedures',
                'ENT emergency management'
            ],
            'OPHT' => [
                'Ophthalmological examination',
                'Visual acuity testing',
                'Common eye conditions',
                'Emergency eye care'
            ],
            'DERM' => [
                'Dermatological examination',
                'Skin lesion identification',
                'Dermatological procedures',
                'Cosmetic dermatology basics'
            ],
            'PSYC' => [
                'Mental status examination',
                'Psychiatric interview techniques',
                'Common psychiatric conditions',
                'Crisis intervention'
            ],
            'RADI' => [
                'Radiological image interpretation',
                'Radiation safety',
                'Common imaging modalities',
                'Emergency radiology'
            ],
            'ANES' => [
                'Anesthesia principles',
                'Perioperative care',
                'Pain management',
                'Critical care basics'
            ],
            'COMM' => [
                'Epidemiological methods',
                'Health promotion strategies',
                'Community health assessment',
                'Public health programs'
            ],
            'EMER' => [
                'Emergency assessment protocols',
                'Life support procedures',
                'Trauma management',
                'Disaster medicine'
            ]
        ];
        
        return $competencies[$deptCode] ?? ['General clinical competencies'];
    }
    
    private function getRotationStatus($startDate, $endDate)
    {
        $now = now();
        
        if ($now < $startDate) {
            return 'upcoming';
        } elseif ($now >= $startDate && $now <= $endDate) {
            return 'active';
        } else {
            return 'completed';
        }
    }
}