<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Ability;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Silber\Bouncer\Bouncer;

class AbilityController extends Controller
{
    public function index()
    {
        $this->authorize('listPrivileges');

        return view('admin.ability.index');
    }

    public function list()
    {
        $this->authorize('listPrivileges');

        $paginate = Ability
            ::select('id', 'name', 'title')
            ->paginate(15, ['*'], 'p')
            ->jsonSerialize();

        $abilities = $paginate['data'];

        foreach($abilities as $i => $ability) {
            $abilities[$i] = array_only($ability, [
                'id', 'name', 'title'
            ]);
        }

        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $abilities,
            'paginate' => $paginate
        ];
    }

    public function listAll()
    {
        $this->authorize('listPrivileges');

        $abilities = Ability::select()->get()->toArray();

        foreach($abilities as $i => $ability) {
            $abilities[$i] = array_only($ability, [
                'id', 'name', 'title'
            ]);
        }

        return [
            'status'  => 200,
            'success' => true,
            'data'    => $abilities
        ];
    }

    public function add(Bouncer $bouncer, Request $request)
    {
        $this->authorize('addPrivilege');

        $data = $this->validate($request, [
            'name'  => 'bail|required|string|alpha_dash|unique:abilities|max:100',
            'title' => 'bail|string|max:200',
        ]);

        $ability = $bouncer->ability()->create($data);

        return [
            'status'  => 200,
            'success' => !!$ability
        ];
    }

    public function edit(Bouncer $bouncer, Request $request)
    {
        $this->authorize('editPrivilege');

        $data = $this->validate($request, [
            'id'    => 'bail|required|integer',
            'name'  => 'bail|required|string|alpha_dash|max:100',
            'title' => 'bail|string|max:200',
        ]);

        $affectedRow = $bouncer->ability()
            ->where('id', '=',
                $data['id']
            )
            ->update($data);

        return [
            'status'  => 200,
            'success' => !!$affectedRow
        ];
    }

    public function delete(Bouncer $bouncer, Request $request)
    {
        $this->authorize('deletePrivilege');

        $data = $this->validate($request, [
            'id' => 'bail|required|integer',
        ]);

        $success = $bouncer->ability()
            ->where('id', '=', $data['id'])
            ->delete();

        return [
            'status'  => 200,
            'success' => $success
        ];
    }
}
