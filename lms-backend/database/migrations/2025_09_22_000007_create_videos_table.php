<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects');
            $table->string('title');
            $table->string('master_file_url'); // original file
            $table->string('hls_manifest_url')->nullable(); // for streaming
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->integer('duration')->nullable(); // seconds
            $table->string('thumbnail_url')->nullable();
            $table->timestamps();
            
            // Add indexes
            $table->index('subject_id');
            $table->index('uploaded_by');
            $table->index('uploaded_at');
        });
    }
    public function down(): void {
        Schema::dropIfExists('videos');
    }
};