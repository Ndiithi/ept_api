<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'email', 'meta','password', 'created_at', 'updated_at', 'role_id'
    ];

    // user permission check
    public function hasPermission(Permission $permission)
    {
        return $this->role->permissions->contains($permission);
    }
}
