<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users');
            $table->json('answers'); // JSON or array of typed text / S3 file URLs
            $table->decimal('score', 6, 2)->nullable(); // nullable until graded
            $table->foreignId('graded_by')->nullable()->constrained('users'); // faculty
            $table->timestamp('graded_at')->nullable();
            $table->text('feedback')->nullable(); // optional
            $table->timestamps();
            
            // Add constraints and indexes
            $table->unique(['assessment_id', 'student_id']);
            $table->index('assessment_id');
            $table->index('student_id');
            $table->index('graded_by');
            $table->index('graded_at');
        });
    }
    public function down(): void {
        Schema::dropIfExists('submissions');
    }
};