<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [];

    protected $hidden = [
        'deleted_at'
    ];

    public function challenges()
    {
        return $this->hasMany('App\\Models\\Base\\Challenge', 'bank');
    }
}
