<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Content extends Model
{
    protected $fillable = [];

    protected $hidden = [
        'id',
        'type',
        'deleted_at',
        'created_at',
    ];

    public function poster()
    {
        return $this->belongsTo('App\\Models\\Admin\\User', 'poster', 'id');
    }

    public function modifier()
    {
        return $this->belongsTo('App\\Models\\Admin\\User', 'mender', 'id');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->diffForHumans();
    }
}
