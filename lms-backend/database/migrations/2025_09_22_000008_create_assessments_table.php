<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects');
            $table->string('title');
            $table->enum('type', ['mcq', 'saq', 'laq']); // mcq, saq, laq
            $table->json('competency_tags')->nullable();
            $table->foreignId('author_id')->constrained('users');
            $table->integer('duration_minutes')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamps();
            
            // Add indexes
            $table->index('subject_id');
            $table->index('type');
            $table->index('author_id');
            $table->index(['start_at', 'end_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('assessments');
    }
};