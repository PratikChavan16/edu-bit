<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'code',
        'title',
        'description',
        'credit_hours',
        'is_active',
    ];

    protected $casts = [
        'credit_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Scope for active subjects only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full subject name with course and department
     */
    public function getFullNameAttribute(): string
    {
        return $this->course->department->code . ' - ' . $this->code . ': ' . $this->title;
    }
}