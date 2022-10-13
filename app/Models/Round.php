<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Round extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'program', 'schema', 'form', 'user_group', 'name',
        'description', 'meta', 'active', 'testing_instructions', 'start_date', 'end_date',
        'created_at', 'updated_at'
    ];
}
