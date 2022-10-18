<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form_response extends Model
{
    use HasFactory, SoftDeletes;

    // TODO: add round field - DONE

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'form_section', 'form_submission', 'form_field', 'value', 'meta', 'created_at', 'updated_at'
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

    public function form_submission()
    {
        return $this->belongsTo('App\Models\Form_Submission', 'form_submission', 'uuid');
    }

    public function form_field()
    {
        return $this->belongsTo('App\Models\Form_field', 'form_field', 'uuid');
    }
}
