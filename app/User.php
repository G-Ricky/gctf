<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sid',
        'name',
        'nickname',
        'password',
        'email',
        'gender',
        'role',
        'is_hidden'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function info($id)
    {
        return $this
            ->select('sid', 'name', 'nickname', 'gender', 'email')
            ->where('id', '=', $id)
            ->whereNull('deleted_at')
            ->offset(0)
            ->limit(1)
            ->get();
    }

    public function edit($data)
    {
        $data = array_only($data, ['id', 'name', 'gender', 'email']);
        return $this
            ->where('id', '=', $data['id'])
            ->update($data);
    }
}
