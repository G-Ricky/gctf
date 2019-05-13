<?php

namespace App\Models\Admin;

use App\Models\Base\Tag;
use Carbon\Carbon;
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
        'basic_points',
        'flag',
        'bank',
    ];

    protected $hidden = [
        'delete_at'
    ];

    public function tags()
    {
        return $this->hasMany('App\\Models\\Base\\Tag', 'challenge');
    }

    public function getCreatedAtAttribute($createdAt)
    {
        return Carbon::parse($createdAt)->diffForHumans();
    }

    public function getUpdatedAtAttribute($updatedAt)
    {
        return Carbon::parse($updatedAt)->diffForHumans();
    }

    public function createWithTags(array $data)
    {
        foreach($data['tags'] as $i => $tag) {
            $data['tags'][$i] = new Tag(['name' => $tag]);
        }
        $tags = $data['tags'];
        unset($data['tags']);

        //Create a record of new challenge and create records of tag related to the same challenge
        $challenge = $this->create($data);
        if(count($tags) > 0) {
            $challenge = $challenge->tags()->saveMany($tags);
        }
        return $challenge;
    }

    public function updateWithTags(array $data)
    {
        foreach($data['tags'] as $i => $tag) {
            $data['tags'][$i] = new Tag([
                'name'      => $tag,
                'challenge' => $data['id'],
            ]);
        }
        $tags = $data['tags'];
        unset($data['tags']);

        $success = $this
            ->where('id', '=', $data['id'])
            ->update($data);

        if(!$success) {
            throw new \Exception('Update fail');
        }

        if(count($tags) > 0) {
            $this->setAttribute('id', $data['id']);
            $success = $this->tags()->saveMany($tags);
        }

        return !!$success;
    }
}
