<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete(); // 1 assessment -> many questions
            $table->enum('type', ['mcq', 'saq', 'laq']);
            $table->text('content');
            $table->json('choices')->nullable(); // JSON for MCQ
            $table->json('correct_choices')->nullable(); // JSON for MCQ
            $table->decimal('marks', 5, 2)->default(1.0);
            $table->integer('order_index')->default(0);
            $table->timestamps();
            
            // Add indexes
            $table->index('assessment_id');
            $table->index('type');
            $table->index('order_index');
        });
    }
    public function down(): void {
        Schema::dropIfExists('questions');
    }
};