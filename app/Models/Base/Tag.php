<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'challenge',
        'name'
    ];

    public function challenge()
    {
        return $this->belongsTo('App\\Models\\Base\\Challenge', 'challenge');
    }
}
