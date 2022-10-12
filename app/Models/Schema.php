<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schema extends Model
{
    use HasFactory;

    protected $table = 'schemes';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'name', 'description', 'program', 'meta', 'scoringCriteria', 'created_at', 'updated_at'
    ];

}
