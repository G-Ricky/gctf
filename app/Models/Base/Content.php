<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use SoftDeletes;

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
        return $this->belongsTo('App\\Models\\Admin\\User', 'modifier', 'id');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->diffForHumans();
    }
}
