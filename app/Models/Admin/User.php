<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function users()
    {
        return $this
            ->select([
                'id', 'nickname', 'email', 'sid'
            ])
            ->paginate(20, ['*'], 'p')
            ->jsonSerialize();
    }
}
