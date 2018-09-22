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
        $paginate = Ability
            ::select('id', 'name', 'title')
            ->paginate(15, ['*'], 'p')
            ->jsonSerialize();

        $abilities = $paginate['data'];

        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $abilities,
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
}
