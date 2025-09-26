<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses'); // 1 course -> many subjects
            $table->string('code'); // subject code, unique within course
            $table->string('title');
            $table->integer('credit_hours')->nullable();
            $table->timestamps();
            
            // Add unique constraint and indexes
            $table->unique(['course_id', 'code']); // unique within course
            $table->index('course_id');
            $table->index('code');
        });
    }
    public function down(): void {
        Schema::dropIfExists('subjects');
    }
};