<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects');
            $table->string('title');
            $table->string('file_url'); // S3 path
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->integer('version')->default(1); // for updated notes
            $table->timestamps();
            
            // Add indexes for search and performance
            $table->index('subject_id');
            $table->index('uploaded_by');
            $table->index('title'); // for search
            $table->index('uploaded_at');
        });
    }
    public function down(): void {
        Schema::dropIfExists('notes');
    }
};