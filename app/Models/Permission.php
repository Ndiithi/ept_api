<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'description',
        'meta',
    ];
    protected $table = 'permissions';
}
