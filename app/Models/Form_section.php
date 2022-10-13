<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form_section extends Model
{
    use HasFactory;

      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid','form','name', 'description', 'meta', 'actions', 'created_at', 'updated_at', 'index', /*'next', 'next_condition',*/ 'disabled'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
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
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'meta' => 'array'
    ];

    public function form()
    {
        return $this->belongsTo('App\Models\Form');
    }

    // fields
    public function form_fields()
    {
        return $this->hasMany('App\Models\Form_field', 'form_section', 'uuid');
    }

    // cascade delete
    public static function boot() {
        parent::boot();

        static::deleting(function($form_section) { // before delete() method call this
             $form_section->form_fields()->delete();
             // do the rest of the cleanup...
        });
    }

}
