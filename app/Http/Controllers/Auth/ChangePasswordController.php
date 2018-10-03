<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function index()
    {
        return view('auth.passwords.change');
    }

    public function change(Request $request)
    {
        $data = $this->validate($request, [
            'old_password' => 'required|string|min:6|max:16',
            'password'     => 'required|string|min:6|max:16|confirmed',
        ]);

        if(!Hash::check($data['old_password'], Auth::user()->getAuthPassword())) {
            return $this->fail('Password don\'t match!', 200);
        }

        if($affectedRows = Auth::user()->update([
            'password' => Hash::make($data['password'])
        ])) {
            Auth::logout();
        };

        return [
            'status'  => 200,
            'success' => !!$affectedRows
        ];
    }
}
