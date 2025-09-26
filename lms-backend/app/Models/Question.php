<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'assessment_id','type','content','choices','correct_choices','marks','order_index'
    ];
    protected $casts = [
        'choices' => 'array',
        'correct_choices' => 'array'
    ];

    public function assessment() { return $this->belongsTo(Assessment::class); }
}