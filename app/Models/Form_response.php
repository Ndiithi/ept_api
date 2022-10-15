<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form_response extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'form', 'form_section', 'form_field', 'value', 'meta', 'user', 'created_at', 'updated_at'
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
        'meta' => 'array'
    ];

    // id = uuid;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'uuid';

    public function form()
    {
        return $this->belongsTo('App\Models\Form');
    }

    public function form_section()
    {
        return $this->belongsTo('App\Models\Form_section');
    }

    public function form_field()
    {
        return $this->belongsTo('App\Models\Form_field');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
