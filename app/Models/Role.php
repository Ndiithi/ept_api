<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'uuid',
        'permissions',
        'name',
        'description',
        'meta',
    ];
    protected $table = 'roles';

    // get all permissions for this role
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
