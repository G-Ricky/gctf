<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'sid',
        'name',
        'nickname',
        'password',
        'email',
        'gender',
        'is_hidden',
    ];

    public function roles()
    {
        return $this->belongsToMany('App\\Models\\Admin\\Role', 'assigned_roles', 'entity_id', 'role_id');
    }
}
