<?php

namespace App\Models\Admin;

class Role extends \Silber\Bouncer\Database\Role
{
    public function roles()
    {
        return $this
            ->select([
                'id', 'name', 'title'
            ])
            ->paginate(20, ['*'], 'p')
            ->jsonSerialize();
    }
}
