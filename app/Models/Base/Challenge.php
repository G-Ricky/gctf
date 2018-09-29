<?php

namespace App\Models\Base;

use App\Plugins\Points\Points;
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

    public function submissions()
    {
        return $this->hasMany('App\\Models\\Base\\Submission', 'challenge');
    }

    public function submitters()
    {
        return $this->belongsToMany('App\\Models\\Base\\User', 'submissions', 'challenge', 'submitter');
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

    public function detail($id)
    {
        return $this
            ->select(
                'id',
                'title',
                'description',
                'category',
                'points'
            )
            ->where('id', '=', $id)
            ->where('is_hidden', '=', false)
            ->with('tags:challenge,name')
            ->limit(1)
            ->get();
    }

    public function remove($id)
    {
        return $this
            ->where('id', '=', $id)
            ->delete();
    }
}
