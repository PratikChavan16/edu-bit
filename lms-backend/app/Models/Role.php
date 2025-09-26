<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\RoleDetail;

class Role extends SpatieRole
{
    public function details()
    {
        return $this->hasOne(RoleDetail::class);
    }
    
    public function getDisplayNameAttribute()
    {
        return $this->details?->display_name ?? $this->name;
    }
    
    public function getDescriptionAttribute()
    {
        return $this->details?->description;
    }
}