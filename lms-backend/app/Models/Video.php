<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'file_path',
        'file_size',
        'duration',
        'hls_path',
        'thumbnail_path',
        'processing_status',
        'uploaded_by',
        'is_active',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'duration' => 'integer', // in seconds
        'is_active' => 'boolean',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file size in human readable format
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get duration in human readable format (HH:MM:SS)
     */
    public function getDurationHumanAttribute(): string
    {
        if (!$this->duration) {
            return '00:00:00';
        }

        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Scope for active videos only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for completed processing status
     */
    public function scopeProcessed($query)
    {
        return $query->where('processing_status', 'completed');
    }

    /**
     * Check if video is ready for streaming
     */
    public function isReadyForStreaming(): bool
    {
        return $this->processing_status === 'completed' && $this->hls_path;
    }
}