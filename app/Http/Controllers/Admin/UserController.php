<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.user.index');
    }

    public function list(User $userModel)
    {
        $paginate = $userModel->users();

        $users = $paginate['data'];

        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $users,
            'paginate' => $paginate
        ];
    }

    public function add()
    {

    }

    public function edit()
    {

    }

    public function delete()
    {

    }

    public function hide()
    {

    }

    public function ban()
    {

    }
}
