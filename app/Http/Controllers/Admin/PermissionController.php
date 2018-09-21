<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Ability;
use App\Models\Admin\Role;
use Illuminate\Http\Request;
use Silber\Bouncer\Bouncer;

class PermissionController extends Controller
{
    public function index($roleId)
    {
        return view('admin.permission.index', [
            'roleId' => $roleId
        ]);
    }

    public function list($roleId)
    {
        $roleModel = Role::where('id', '=', $roleId)->firstOrFail();

        $abilities = $roleModel->abilities()->get()->toArray();
        $abilities = array_column($abilities, 'id');

        return [
            'status'  => 200,
            'success' => true,
            'data'    => $abilities
        ];
    }
}
