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
        'uuid',
        'program',
        'schema',
        // 'user_group',
        'name',
        'description',
        'meta', // e.g pre_response_approval, etc
        'active',
        'testing_instructions',
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
        // 'form',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'meta' => 'array',
        'forms' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'start_date',
        'end_date'
    ];

    // encode json fields
    protected $jsonEncode = [
        'meta',
        'forms'
    ];

    // id = uuid;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'uuid';

    // program
    public function program()
    {
        return $this->belongsTo('App\Models\Program', 'program', 'uuid');
    }

    // schema
    public function schema()
    {
        return $this->belongsTo('App\Models\Schema', 'schema', 'uuid');
    }
}
