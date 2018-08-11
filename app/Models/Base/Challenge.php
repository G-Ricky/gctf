<?php

namespace App\Models\Base;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Challenge extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category',
        'poster',
        'points',
        'flag',
        'bank'
    ];

    public function tags() {
        return $this->hasMany('App\\Models\\Base\\Tag', 'challenge');
    }

    public function list()
    {
        return $this
            ->select(
                'id',
                'title',
                'description',
                'category',
                'points',
                'updated_at'
            )
            ->whereNotNull('flag')
            ->orderBy('updated_at', 'desc')
            ->with('tags:challenge,name');
    }
}
