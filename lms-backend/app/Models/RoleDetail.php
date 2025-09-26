<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'role_id', 'display_name', 'description'
    ];

    public function role() 
    { 
        return $this->belongsTo(Role::class); 
    }
}