<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Ability;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AbilityController extends Controller
{
    public function index()
    {
        return view('admin.ability.index');
    }

    public function list()
    {
        $abilities = Ability::select('id', 'name', 'title')->get()->toArray();

        return [
            'status'  => 200,
            'success' => true,
            'data'    => $abilities
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
}
