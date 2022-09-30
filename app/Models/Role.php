<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid', 'name', 'description', 'meta', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];
    
    // id = uuid
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'uuid';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'meta' => 'array'
    ];

    /**
     * get the permissions of the role
     * @return Permission[]
     */
    public function permissions()
    {
        // return $this->belongsToMany(Permission::class);
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role', 'permission');
    }
}
