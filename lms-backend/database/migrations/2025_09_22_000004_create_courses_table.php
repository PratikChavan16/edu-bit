<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments'); // 1 department -> many courses
            $table->integer('year'); // year within the course
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Add indexes
            $table->index('department_id');
            $table->index('year');
        });
    }
    public function down(): void {
        Schema::dropIfExists('courses');
    }
};