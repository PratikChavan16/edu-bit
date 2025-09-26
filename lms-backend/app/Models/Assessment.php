<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;
    protected $fillable = [
        'subject_id','title','type','competency_tags','author_id','duration_minutes','start_at','end_at'
    ];
    protected $casts = [
        'competency_tags' => 'array',
        'start_at' => 'datetime',
        'end_at' => 'datetime'
    ];

    public function subject() { return $this->belongsTo(Subject::class); }
    public function author() { return $this->belongsTo(User::class,'author_id'); }
    public function questions() { return $this->hasMany(Question::class); }
}