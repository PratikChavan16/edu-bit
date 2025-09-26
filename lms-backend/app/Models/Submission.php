<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;
    protected $fillable = [
        'assessment_id','student_id','answers','submitted_at','score','auto_graded','manual_feedback','graded_by'
    ];
    protected $casts = [
        'answers' => 'array',
        'submitted_at' => 'datetime'
    ];

    public function assessment() { return $this->belongsTo(Assessment::class); }
    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function grader() { return $this->belongsTo(User::class, 'graded_by'); }
}