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

    protected $datas = [
        'deleted_at'
    ];

    public function bank()
    {
        return $this->belongsTo('App\\Models\\Base\\Bank', 'bank');
    }

    public function tags()
    {
        return $this->hasMany('App\\Models\\Base\\Tag', 'challenge');
    }

    public function hints()
    {

    }

    public function list($bank, $page, $page_size = 20)
    {
        return $this
            ->select(
                'id',
                'title',
                'description',
                'category',
                'points',
                'created_at',
                'updated_at'
            )
            ->where('is_hidden', '=', false)
            ->where('bank', '=', $bank)
            ->whereNotNull('flag')
            ->orderBy('updated_at', 'desc')
            ->with('tags:challenge,name')
            ->paginate($page_size, '*', 'page', $page)
            ->jsonSerialize();
    }

    public function search()
    {

    }

    /**
     * @param integer $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function info($id)
    {
        return $this
            ->select(
                'id',
                'title',
                'description',
                'flag',
                'category',
                'poster',
                'points',
                'bank',
                'created_at',
                'updated_at'
            )
            ->where('id', '=', $id)
            ->where('is_hidden', '=', false)
            ->with('tags:challenge,name')
            ->limit(1)
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

    public function remove($id)
    {
        return $this
            ->where('id', '=', $id)
            ->delete();
    }
}
