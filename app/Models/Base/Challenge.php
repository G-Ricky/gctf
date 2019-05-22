<?php

namespace App\Models\Base;

use App\Plugins\Points\Points;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use SoftDeletes;

    protected $fillable = [];

    protected $dates = [
        'deleted_at'
    ];

    protected $hidden = [
        'poster',
        'flag',
        'deleted_at',
    ];

    public function bank()
    {
        return $this->belongsTo('App\\Models\\Base\\Bank', 'bank');
    }

    public function submissions()
    {
        return $this->hasMany('App\\Models\\Base\\Submission', 'challenge');
    }

    public function submitters()
    {
        return $this->belongsToMany('App\\Models\\Base\\User', 'submissions', 'challenge', 'submitter');
    }
}
