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

    public function hints() {

    }

    public function list($page_size)
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
            ->where('is_hidden', '=', false)
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
            ->where('id', '=', $id)
            ->where('is_hidden', '=', false)
            ->with([
               'tags' => function($query) {
                   return $query->select('challenge', 'name');
               }
            ])
            ->get();
    }

    public function saveWithTags(array $data)
    {
        foreach($data['tags'] as $value) {
            $tags[] = new Tag(['name' => $value]);
        }
        unset($data['tags']);
        //Create a record of new challenge and create records of tag related to the same challenge
        $success = $this->create($data)->tags()->saveMany($tags);
        return $success;
    }
}
