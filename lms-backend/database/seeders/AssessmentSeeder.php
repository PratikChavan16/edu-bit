<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentSeeder extends Seeder
{
    public function run()
    {
        // Seed Assessment Questions for different subjects
        $this->seedAssessmentQuestions();
        
        // Seed Viva Examinations
        $this->seedVivaExaminations();
        
        // Seed Student Assessments (sample attempts)
        $this->seedStudentAssessments();
        
        // Seed Continuous Assessments
        $this->seedContinuousAssessments();
    }
    
    private function seedAssessmentQuestions()
    {
        $assessments = DB::table('assessments')->get();
        
        foreach ($assessments as $assessment) {
            $questionCount = $assessment->type === 'mcq' ? 30 : ($assessment->type === 'saq' ? 10 : 5);
            
            for ($i = 1; $i <= $questionCount; $i++) {
                $questionData = $this->generateQuestionData($assessment->type, $i);
                
                DB::table('assessment_questions')->insert([
                    'assessment_id' => $assessment->id,
                    'question_text' => $questionData['question'],
                    'question_type' => $assessment->type,
                    'options' => $questionData['options'],
                    'correct_answer' => $questionData['correct_answer'],
                    'explanation' => $questionData['explanation'],
                    'marks' => $questionData['marks'],
                    'difficulty_level' => $questionData['difficulty'],
                    'competency_tags' => json_encode(['knowledge', 'application']),
                    'order_index' => $i,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
    
    private function generateQuestionData($type, $questionNumber)
    {
        switch ($type) {
            case 'mcq':
                return [
                    'question' => "Multiple choice question {$questionNumber}: Which of the following best describes the primary function of the given medical concept?",
                    'options' => json_encode([
                        'A' => 'Option A - Incorrect but plausible answer',
                        'B' => 'Option B - Correct answer with detailed explanation',
                        'C' => 'Option C - Common misconception',
                        'D' => 'Option D - Partially correct but incomplete'
                    ]),
                    'correct_answer' => 'B',
                    'explanation' => 'Option B is correct because it accurately describes the primary function with appropriate medical reasoning.',
                    'marks' => 1,
                    'difficulty' => 'medium'
                ];
                
            case 'saq':
                return [
                    'question' => "Short answer question {$questionNumber}: Briefly explain the pathophysiology of the given condition and list three main clinical features.",
                    'options' => null,
                    'correct_answer' => 'Expected answer includes: 1) Pathophysiology explanation, 2) Three clinical features, 3) Appropriate medical terminology',
                    'explanation' => 'Answer should demonstrate understanding of disease mechanisms and clinical presentation.',
                    'marks' => 5,
                    'difficulty' => 'medium'
                ];
                
            case 'laq':
                return [
                    'question' => "Long answer question {$questionNumber}: Discuss the comprehensive management approach for a complex medical condition including diagnosis, treatment, and follow-up care.",
                    'options' => null,
                    'correct_answer' => 'Comprehensive answer covering: 1) Diagnostic approach, 2) Treatment options, 3) Follow-up protocols, 4) Patient education',
                    'explanation' => 'Answer should demonstrate clinical reasoning and comprehensive patient care understanding.',
                    'marks' => 15,
                    'difficulty' => 'hard'
                ];
                
            case 'practical':
                return [
                    'question' => "Practical assessment {$questionNumber}: Demonstrate the procedure and explain the clinical significance.",
                    'options' => json_encode(['procedure_steps', 'safety_measures', 'interpretation']),
                    'correct_answer' => 'Correct demonstration with proper technique and accurate interpretation',
                    'explanation' => 'Assessment based on technique, safety, and clinical understanding.',
                    'marks' => 10,
                    'difficulty' => 'medium'
                ];
                
            default:
                return [
                    'question' => "General question {$questionNumber}: Demonstrate understanding of the topic.",
                    'options' => null,
                    'correct_answer' => 'Appropriate response demonstrating knowledge',
                    'explanation' => 'Answer should show understanding of the concept.',
                    'marks' => 5,
                    'difficulty' => 'medium'
                ];
        }
    }
    
    private function seedVivaExaminations()
    {
        $subjects = DB::table('subjects')->take(10)->get();
        $examiners = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', 2) // Faculty role
            ->select('users.*')
            ->take(5)
            ->get();
        
        foreach ($subjects as $subject) {
            foreach ($examiners->take(2) as $examiner) {
                DB::table('viva_examinations')->insert([
                    'subject_id' => $subject->id,
                    'examiner_id' => $examiner->id,
                    'viva_title' => 'Viva Examination - ' . $subject->title,
                    'examination_date' => now()->addDays(rand(1, 60)),
                    'duration_minutes' => 30,
                    'question_bank' => json_encode($this->generateVivaQuestions($subject->title)),
                    'evaluation_criteria' => json_encode([
                        'Knowledge and Understanding' => 30,
                        'Clinical Application' => 25,
                        'Communication Skills' => 20,
                        'Professional Attitude' => 15,
                        'Problem Solving' => 10
                    ]),
                    'maximum_marks' => 100,
                    'passing_marks' => 60,
                    'examination_type' => 'formative',
                    'instructions' => 'Students will be assessed on their understanding of core concepts and clinical applications.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
    
    private function generateVivaQuestions($subjectTitle)
    {
        return [
            'basic_concepts' => [
                "Define the basic concepts related to {$subjectTitle}",
                "Explain the fundamental principles underlying {$subjectTitle}",
                "What are the key components of {$subjectTitle}?"
            ],
            'clinical_application' => [
                "How would you apply {$subjectTitle} knowledge in clinical practice?",
                "Describe a clinical scenario where {$subjectTitle} is relevant",
                "What are the practical implications of {$subjectTitle}?"
            ],
            'problem_solving' => [
                "How would you approach a complex problem in {$subjectTitle}?",
                "Analyze this case study related to {$subjectTitle}",
                "What would be your differential diagnosis approach?"
            ],
            'recent_advances' => [
                "What are recent developments in {$subjectTitle}?",
                "How has {$subjectTitle} evolved in recent years?",
                "What are future directions in {$subjectTitle}?"
            ]
        ];
    }
    
    private function seedStudentAssessments()
    {
        $students = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', 3) // Student role
            ->select('users.*')
            ->get();
        
        $assessments = DB::table('assessments')->get();
        
        foreach ($students as $student) {
            foreach ($assessments->take(3) as $assessment) {
                // Create student assessment attempt
                $score = rand(60, 95); // Random score between 60-95%
                $totalMarks = 100;
                $obtainedMarks = ($score / 100) * $totalMarks;
                
                DB::table('student_assessments')->insert([
                    'student_id' => $student->id,
                    'assessment_id' => $assessment->id,
                    'start_time' => now()->subDays(rand(1, 30)),
                    'end_time' => now()->subDays(rand(1, 30))->addMinutes($assessment->duration_minutes),
                    'total_marks' => $totalMarks,
                    'obtained_marks' => $obtainedMarks,
                    'percentage' => $score,
                    'grade' => $this->calculateGrade($score),
                    'feedback' => $this->generateFeedback($score),
                    'answers_data' => json_encode($this->generateSampleAnswers()),
                    'status' => 'completed',
                    'attempt_number' => 1,
                    'time_taken_minutes' => $assessment->duration_minutes - rand(5, 15),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
    
    private function seedContinuousAssessments()
    {
        $students = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', 3) // Student role
            ->select('users.*')
            ->take(10)
            ->get();
        
        $subjects = DB::table('subjects')->take(5)->get();
        $assessors = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', 2) // Faculty role
            ->select('users.*')
            ->get();
        
        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                $assessor = $assessors->random();
                
                // Weekly assessments for the past 4 weeks
                for ($week = 1; $week <= 4; $week++) {
                    $scores = $this->generateContinuousAssessmentScores();
                    
                    DB::table('continuous_assessments')->insert([
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'assessor_id' => $assessor->id,
                        'assessment_date' => now()->subWeeks($week),
                        'assessment_type' => 'weekly',
                        'assessment_title' => "Week {$week} Assessment - {$subject->title}",
                        'attendance_score' => $scores['attendance'],
                        'participation_score' => $scores['participation'],
                        'assignment_score' => $scores['assignment'],
                        'quiz_score' => $scores['quiz'],
                        'practical_score' => $scores['practical'],
                        'total_score' => array_sum($scores),
                        'maximum_score' => 100,
                        'weightage' => 0.1, // Each weekly assessment worth 10%
                        'feedback' => $this->generateWeeklyFeedback($scores),
                        'improvement_areas' => json_encode($this->getImprovementAreas($scores)),
                        'strengths' => json_encode($this->getStrengths($scores)),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
    
    private function generateContinuousAssessmentScores()
    {
        return [
            'attendance' => rand(18, 20), // out of 20
            'participation' => rand(15, 20), // out of 20
            'assignment' => rand(16, 20), // out of 20
            'quiz' => rand(14, 20), // out of 20
            'practical' => rand(16, 20), // out of 20
        ];
    }
    
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 60) return 'B';
        if ($percentage >= 50) return 'C';
        return 'F';
    }
    
    private function generateFeedback($score)
    {
        if ($score >= 90) {
            return 'Excellent performance! Shows exceptional understanding of the subject matter.';
        } elseif ($score >= 80) {
            return 'Very good performance. Demonstrates strong grasp of concepts with minor areas for improvement.';
        } elseif ($score >= 70) {
            return 'Good performance. Shows understanding but could benefit from deeper study of certain topics.';
        } elseif ($score >= 60) {
            return 'Satisfactory performance. Meets basic requirements but needs improvement in several areas.';
        } else {
            return 'Needs significant improvement. Requires additional study and practice to meet learning objectives.';
        }
    }
    
    private function generateSampleAnswers()
    {
        return [
            'question_1' => ['answer' => 'B', 'correct' => true, 'time_taken' => 45],
            'question_2' => ['answer' => 'A', 'correct' => false, 'time_taken' => 60],
            'question_3' => ['answer' => 'C', 'correct' => true, 'time_taken' => 30],
        ];
    }
    
    private function generateWeeklyFeedback($scores)
    {
        $total = array_sum($scores);
        $feedback = "Weekly performance summary: Total score {$total}/100. ";
        
        if ($scores['attendance'] < 18) {
            $feedback .= "Attendance needs improvement. ";
        }
        if ($scores['participation'] < 16) {
            $feedback .= "More active participation encouraged. ";
        }
        if ($scores['practical'] < 16) {
            $feedback .= "Focus on practical skills development. ";
        }
        
        return $feedback;
    }
    
    private function getImprovementAreas($scores)
    {
        $areas = [];
        if ($scores['attendance'] < 18) $areas[] = 'Regular attendance';
        if ($scores['participation'] < 16) $areas[] = 'Class participation';
        if ($scores['assignment'] < 16) $areas[] = 'Assignment quality';
        if ($scores['quiz'] < 16) $areas[] = 'Quiz preparation';
        if ($scores['practical'] < 16) $areas[] = 'Practical skills';
        
        return $areas ?: ['Continue current performance'];
    }
    
    private function getStrengths($scores)
    {
        $strengths = [];
        if ($scores['attendance'] >= 19) $strengths[] = 'Excellent attendance';
        if ($scores['participation'] >= 18) $strengths[] = 'Active participation';
        if ($scores['assignment'] >= 18) $strengths[] = 'Quality assignments';
        if ($scores['quiz'] >= 18) $strengths[] = 'Good quiz performance';
        if ($scores['practical'] >= 18) $strengths[] = 'Strong practical skills';
        
        return $strengths ?: ['Consistent effort'];
    }
}