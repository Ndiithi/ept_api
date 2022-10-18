<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form_Submission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'user',
        'form',
        'round',
        'meta',
        'created_at',
        'updated_at'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'meta' => 'array'
    ];

    // id = uuid;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'uuid';

    public function form()
    {
        return $this->belongsTo('App\Models\Form', 'form', 'uuid');
    }

    public function round()
    {
        return $this->belongsTo('App\Models\Round', 'round', 'uuid');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user', 'uuid');
    }

    // responses
    public function form_responses()
    {
        return $this->hasMany('App\Models\Form_response', 'form_submission', 'uuid');
    }
}
