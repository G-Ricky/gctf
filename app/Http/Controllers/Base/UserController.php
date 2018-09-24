<?php

namespace App\Http\Controllers\Base;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('base.user.index');
    }

    public function info()
    {
        $user = User
            ::where('id', '=', Auth::id())
            ->firstOrFail([
                'id', 'sid', 'username', 'nickname', 'name', 'gender', 'email'
            ])
            ->toArray();

        return [
            'status'  => 200,
            'success' => true,
            'data'    => $user
        ];
    }

    public function edit(Request $request, User $user)
    {
        $data = $this->validate($request, [
            'sid'      => [
                'required', 'string', 'max:10',
                Rule::unique('users')->ignore(Auth::id())
            ],
            'nickname' => [
                'nullable', 'string', 'max:16',
                Rule::unique('users')->ignore(Auth::id())
            ],
            'name'     => 'nullable|string|max:16',
            'gender'   => 'required|string|in:UNKNOWN,MALE,FEMALE',
            'email'    => [
                'required', 'string', 'email',
                Rule::unique('users')->ignore(Auth::id())
            ]
        ]);

        $affectedRows = User
            ::where('id', '=', Auth::id())
            ->update($data);

        return [
            'status' => 200,
            'success' => !!$affectedRows,
            'message' => $affectedRows ? '修改成功' : '修改失败'
        ];
    }
}
