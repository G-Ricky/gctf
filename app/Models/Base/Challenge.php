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

    public function list($id, $page_size)
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
            ->where('id', '=', $id)
            ->whereNotNull('flag')
            ->orderBy('updated_at', 'desc')
            ->with('tags:challenge,name')
            ->paginate($page_size)
            ->jsonSerialize();
    }

    public function info($id)
    {
        return $this
            ->select(
                'id',
                'title',
                'description',
                'category',
                'poster',
                'points',
                'bank',
                'created_at',
                'updated_at'
            )
            ->where('id', $id)
            ->with([
               'tags' => function($query) {
                   return $query->select('challenge', 'name');
               }
            ])
            ->get();
    }
}
