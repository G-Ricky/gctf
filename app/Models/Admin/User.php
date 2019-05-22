<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sid',
        'name',
        'nickname',
        'password',
        'email',
        'gender',
    ];

    public function roles()
    {
        return $this->belongsToMany('App\\Models\\Admin\\Role', 'assigned_roles', 'entity_id', 'role_id');
    }

    public function submissions()
    {
        return $this->hasMany('App\\Models\\Base\\Submission', 'submitter', 'id');
    }
}
