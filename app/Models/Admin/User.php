<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function roles()
    {
        return $this->belongsToMany('App\\Models\\Admin\\Role', 'assigned_roles', 'entity_id', 'role_id');
    }

    public function users()
    {
        return $this
            ->select([
                'id', 'nickname', 'email', 'sid'
            ])
            ->with('roles:id,name,title')
            ->paginate(20, ['*'], 'p')
            ->jsonSerialize();
    }
}
