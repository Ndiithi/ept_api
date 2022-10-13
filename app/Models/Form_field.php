<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form_field extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'form_section','name', 'description', 'meta', 'type', 'actions', 'created_at', 'updated_at', 'validation', 'disabled', 'options', 'index'
    ];


    // id = uuid
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'uuid';

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

    public function form_section()
    {
        return $this->belongsTo('App\Models\Form_section', 'form_section', 'uuid');
    }

}
