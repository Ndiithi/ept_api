<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'description',
        'program',
        'meta',
        'actions',
        'target_type',
        'created_at',
        'updated_at', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
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
        'actions' => 'array'
    ];

    // id = uuid
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'uuid';


    // sections
    public function sections()
    {
        return $this->hasMany('App\Models\Form_section', 'form', 'uuid');
    }
    
    // cascade delete
    public static function boot() {
        parent::boot();
        self::deleting(function($form) {
            foreach ($form->sections()->get() as $section) {
                $section->delete();
            }
        });
    }
}
