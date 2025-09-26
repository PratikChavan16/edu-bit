<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            // Remove the default name column since we'll have first_name and last_name
            $table->dropColumn('name');
            
            // Add new user fields
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('phone')->nullable()->unique()->after('email');
            $table->foreignId('department_id')->nullable()->constrained('departments')->after('password');
            $table->tinyInteger('current_year')->nullable()->after('department_id'); // year for students (1,2,3,4)
            $table->string('enrollment_number')->nullable()->unique()->after('current_year');
            $table->string('photo_url')->nullable()->after('enrollment_number'); // S3 link for profile image
            $table->boolean('is_active')->default(true)->after('photo_url');
            
            // Add indexes as specified (Spatie handles role relationships via pivot table)
            $table->index('email');
            $table->index('phone');
            $table->index('enrollment_number');
            $table->index('department_id');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['first_name','last_name','phone','department_id','current_year','enrollment_number','photo_url','is_active']);
            $table->string('name')->after('id');
        });
    }
};