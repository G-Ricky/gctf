<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_hidden'
    ];

    public function challenges()
    {
        return $this->hasMany('App\\Models\\Base\\Challenge', 'bank');
    }

    public function add($data)
    {
        $data = array_only($data, ['name', 'description', 'is_hidden']);
        return $this->create($data);
    }

    public function edit()
    {

    }

    public function list($page, $pageSize = 20)
    {
        return $this
            ->select('id', 'name', 'description')
            ->orderBy('created_at')
            ->paginate($pageSize, '*', 'page', $page)
            ->jsonSerialize();
    }

    public function remove()
    {

    }
}
