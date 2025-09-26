<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // First, let's modify existing tables to be MBBS-specific
        
        // Academic Years Table
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 1st Year, 2nd Year, 3rd Year, 4th Year, Internship
            $table->string('code'); // MBBS1, MBBS2, MBBS3, MBBS4, INT
            $table->text('description')->nullable();
            $table->integer('duration_months'); // 12 for each year
            $table->json('semester_structure')->nullable(); // semester breakdown
            $table->boolean('is_active')->default(true);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // Update departments table for MBBS departments
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->enum('department_type', ['preclinical', 'paraclinical', 'clinical'])->default('preclinical');
                $table->boolean('has_clinical_postings')->default(false);
                $table->json('competencies')->nullable(); // learning competencies
            });
        }

        // Update courses table for MBBS structure
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignId('academic_year_id')->nullable()->constrained('academic_years');
                $table->enum('course_type', ['theory', 'practical', 'clinical', 'integrated'])->default('theory');
                $table->integer('credit_hours')->default(0);
                $table->json('learning_outcomes')->nullable();
            });
        }

        // Update subjects table for MBBS subjects
        if (Schema::hasTable('subjects')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->enum('subject_type', ['core', 'elective', 'rotation'])->default('core');
                $table->integer('theory_hours')->default(0);
                $table->integer('practical_hours')->default(0);
                $table->integer('clinical_hours')->default(0);
                $table->json('prerequisites')->nullable(); // required previous subjects
                $table->json('learning_objectives')->nullable();
                $table->boolean('has_practicals')->default(false);
                $table->boolean('has_clinical_postings')->default(false);
            });
        }

        // Clinical Rotations Table
        Schema::create('clinical_rotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('rotation_name');
            $table->text('description')->nullable();
            $table->integer('duration_weeks');
            $table->integer('max_students')->default(10);
            $table->json('learning_objectives')->nullable();
            $table->json('assessment_criteria')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();
        });

        // Student Clinical Postings Table
        Schema::create('student_clinical_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rotation_id')->constrained('clinical_rotations')->onDelete('cascade');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['scheduled', 'active', 'completed', 'cancelled'])->default('scheduled');
            $table->text('objectives')->nullable();
            $table->json('daily_logs')->nullable();
            $table->text('final_feedback')->nullable();
            $table->integer('attendance_percentage')->nullable();
            $table->enum('performance_rating', ['excellent', 'good', 'satisfactory', 'needs_improvement'])->nullable();
            $table->timestamps();
        });

        // Medical Cases Table
        Schema::create('medical_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('case_title');
            $table->text('patient_history');
            $table->text('chief_complaint');
            $table->text('history_of_present_illness');
            $table->text('past_medical_history')->nullable();
            $table->text('family_history')->nullable();
            $table->text('social_history')->nullable();
            $table->json('physical_examination')->nullable();
            $table->json('investigation_results')->nullable();
            $table->json('differential_diagnosis')->nullable();
            $table->text('final_diagnosis')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->text('learning_points')->nullable();
            $table->json('images')->nullable(); // medical images, X-rays, etc.
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('intermediate');
            $table->enum('case_type', ['real', 'simulated', 'modified'])->default('simulated');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // Case Study Attempts Table
        Schema::create('case_study_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_case_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->json('student_diagnosis')->nullable();
            $table->json('student_investigation_orders')->nullable();
            $table->json('student_treatment_plan')->nullable();
            $table->text('reasoning')->nullable();
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'reviewed'])->default('in_progress');
            $table->timestamps();
        });

        // Competencies Table
        Schema::create('competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('competency_code'); // MCI competency codes
            $table->string('competency_title');
            $table->text('description');
            $table->enum('domain', ['knowledge', 'skills', 'attitude', 'communication'])->default('knowledge');
            $table->enum('level', ['knows', 'knows_how', 'shows_how', 'does'])->default('knows'); // Miller's pyramid
            $table->json('assessment_methods')->nullable();
            $table->boolean('is_core')->default(true);
            $table->timestamps();
        });

        // Student Competency Assessment Table
        Schema::create('student_competency_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('competency_id')->constrained()->onDelete('cascade');
            $table->foreignId('assessor_id')->constrained('users')->onDelete('cascade');
            $table->enum('achievement_level', ['not_achieved', 'developing', 'achieved', 'mastered'])->default('not_achieved');
            $table->text('evidence')->nullable();
            $table->text('feedback')->nullable();
            $table->date('assessment_date');
            $table->json('assessment_context')->nullable(); // where/how assessed
            $table->timestamps();
            
            $table->unique(['student_id', 'competency_id', 'assessment_date']);
        });

        // Practical Skills Table
        Schema::create('practical_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('skill_name');
            $table->text('description');
            $table->text('procedure_steps');
            $table->json('equipment_required')->nullable();
            $table->json('safety_considerations')->nullable();
            $table->json('assessment_criteria')->nullable();
            $table->string('video_url')->nullable();
            $table->json('reference_images')->nullable();
            $table->enum('skill_type', ['basic', 'intermediate', 'advanced'])->default('basic');
            $table->boolean('requires_supervision')->default(true);
            $table->timestamps();
        });

        // Student Skill Assessments Table
        Schema::create('student_skill_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('practical_skills')->onDelete('cascade');
            $table->foreignId('assessor_id')->constrained('users')->onDelete('cascade');
            $table->integer('attempts')->default(1);
            $table->json('checklist_scores')->nullable(); // detailed scoring
            $table->integer('total_score')->nullable();
            $table->integer('max_score')->nullable();
            $table->enum('competency_level', ['not_competent', 'developing', 'competent', 'proficient'])->default('not_competent');
            $table->text('feedback')->nullable();
            $table->date('assessment_date');
            $table->timestamps();
        });

        // Viva Examinations Table
        Schema::create('viva_examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('examiner_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('exam_date');
            $table->time('start_time');
            $table->integer('duration_minutes');
            $table->integer('max_marks');
            $table->json('topics_covered')->nullable();
            $table->enum('viva_type', ['internal', 'external', 'grand_viva'])->default('internal');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });

        // Student Viva Performance Table
        Schema::create('student_viva_performances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viva_id')->constrained('viva_examinations')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->json('questions_asked')->nullable();
            $table->json('responses_quality')->nullable();
            $table->integer('communication_score')->nullable();
            $table->integer('knowledge_score')->nullable();
            $table->integer('clinical_reasoning_score')->nullable();
            $table->integer('total_marks')->nullable();
            $table->text('examiner_comments')->nullable();
            $table->enum('overall_performance', ['excellent', 'good', 'satisfactory', 'poor'])->nullable();
            $table->timestamps();
        });

        // Medical References Table
        Schema::create('medical_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('authors');
            $table->string('publication')->nullable();
            $table->string('edition')->nullable();
            $table->year('publication_year')->nullable();
            $table->string('isbn')->nullable();
            $table->text('description')->nullable();
            $table->enum('reference_type', ['textbook', 'journal', 'guideline', 'protocol', 'atlas'])->default('textbook');
            $table->string('url')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_mandatory')->default(false);
            $table->timestamps();
        });

        // Student Attendance Table
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->date('attendance_date');
            $table->enum('session_type', ['lecture', 'practical', 'clinical', 'tutorial', 'seminar'])->default('lecture');
            $table->string('session_topic')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->text('remarks')->nullable();
            $table->foreignId('marked_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['student_id', 'subject_id', 'attendance_date', 'session_type']);
        });

        // Continuous Assessment Table
        Schema::create('continuous_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('assessment_type', ['quiz', 'assignment', 'presentation', 'log_book', 'portfolio'])->default('quiz');
            $table->integer('max_marks');
            $table->decimal('weightage', 5, 2)->default(10.00); // percentage weightage in final grade
            $table->date('due_date');
            $table->boolean('is_mandatory')->default(true);
            $table->json('rubric')->nullable(); // assessment rubric
            $table->timestamps();
        });

        // Student Continuous Assessment Scores Table
        Schema::create('student_continuous_assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('continuous_assessments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('marks_obtained')->nullable();
            $table->text('feedback')->nullable();
            $table->date('submission_date')->nullable();
            $table->enum('status', ['not_submitted', 'submitted', 'graded', 'resubmission_required'])->default('not_submitted');
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();
            
            $table->unique(['assessment_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_continuous_assessment_scores');
        Schema::dropIfExists('continuous_assessments');
        Schema::dropIfExists('student_attendance');
        Schema::dropIfExists('medical_references');
        Schema::dropIfExists('student_viva_performances');
        Schema::dropIfExists('viva_examinations');
        Schema::dropIfExists('student_skill_assessments');
        Schema::dropIfExists('practical_skills');
        Schema::dropIfExists('student_competency_assessments');
        Schema::dropIfExists('competencies');
        Schema::dropIfExists('case_study_attempts');
        Schema::dropIfExists('medical_cases');
        Schema::dropIfExists('student_clinical_postings');
        Schema::dropIfExists('clinical_rotations');
        Schema::dropIfExists('academic_years');
        
        // Remove added columns
        if (Schema::hasTable('subjects')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropColumn(['subject_type', 'theory_hours', 'practical_hours', 'clinical_hours', 'prerequisites', 'learning_objectives', 'has_practicals', 'has_clinical_postings']);
            });
        }
        
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn(['academic_year_id', 'course_type', 'credit_hours', 'learning_outcomes']);
            });
        }
        
        if (Schema::hasTable('departments')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropColumn(['department_type', 'has_clinical_postings', 'competencies']);
            });
        }
    }
};