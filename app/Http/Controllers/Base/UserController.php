<?php

namespace App\Http\Controllers\Base;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
