<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sample extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'name', 'schema', 'description', 'round', 'meta', 'created_at', 'updated_at',
        'expected_outcome', 'expected_outcome_notes', 'expected_interpretation', 'expected_interpretation_notes'
    ];
}
