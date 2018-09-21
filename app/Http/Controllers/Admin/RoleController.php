<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.role.index');
    }

    public function list(Role $roleModel)
    {
        $paginate = $roleModel->roles();

        $roles = $paginate['data'];

        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $roles,
            'paginate' => $paginate
        ];
    }

    public function add()
    {

    }

    public function delete()
    {

    }
}
