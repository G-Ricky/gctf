<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'poster',
        'modifier',
    ];

    public function poster()
    {
        return $this->belongsTo('App\\Models\\Admin\\User', 'poster', 'id');
    }

    public function modifier()
    {
        return $this->belongsTo('App\\Models\\Admin\\User', 'modifier', 'id');
    }
}
