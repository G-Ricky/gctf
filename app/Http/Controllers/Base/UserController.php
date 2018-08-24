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

    public function info(User $user)
    {
        $data = $user->info(Auth::user()->id);
        if($data) {
            $response = [
                'status'  => 200,
                'success' => true,
                'data'    => $data[0]
            ];
        }else{
            $response = [
                'status'  => 200,
                'success' => false
            ];
        }
        return $response;
    }

    public function edit(Request $request, User $user)
    {
        $this->validate($request, [
            'sid'    => [
                'required', 'string', 'max:10',
                Rule::unique('users')->ignore(Auth::user()->id)
            ],
            'gender' => 'required|string|in:UNKNOWN,MALE,FEMALE',
            'email'  => [
                'required', 'string', 'email',
                Rule::unique('users')->ignore(Auth::user()->id)
            ]
        ]);

        $data = $request->all();
        $data['id'] = Auth::user()->id;

        $success = $user->edit($data);

        return [
            'status' => 200,
            'success' => $success,
            'message' => $success ? '修改成功' : '修改失败'
        ];
    }
}
