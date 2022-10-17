<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Round_Forms extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'round', 'form', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    // id = uuid;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'uuid';

    public function round()
    {
        return $this->belongsTo('App\Models\Round', 'round', 'uuid');
    }

    public function form()
    {
        return $this->belongsTo('App\Models\Form', 'form', 'uuid');
    }
}
