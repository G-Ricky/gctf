<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Silber\Bouncer\Bouncer;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('listRoles');

        return view('admin.role.index');
    }

    public function list(Bouncer $bouncer)
    {
        $this->authorize('listRoles');

        $paginate = $bouncer
            ->role()
            ->paginate(15, ['*'], 'p')
            ->jsonSerialize();

        $roles = $paginate['data'];

        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $roles,
            'paginate' => $paginate
        ];
    }

    public function add(Request $request, Bouncer $bouncer)
    {
        $this->authorize('addRole');

        $data = $this->validate($request, [
            'name'  => 'bail|required|string|alpha_dash|unique:roles|max:100',
            'title' => 'bail|string|max:200',
        ]);

        $role = $bouncer->role()->create($data);

        return [
            'status'  => 200,
            'success' => !!$role
        ];
    }

    public function edit(Request $request, Bouncer $bouncer)
    {
        $this->authorize('editRole');

        $data = $this->validate($request, [
            'id'    => 'bail|required|integer',
            'name'  => 'bail|required|string|alpha_dash|max:100',
            'title' => 'bail|string|max:200',
        ]);

        $affectedRow = $bouncer->role()
            ->where('id', '=',
                $data['id']
            )
            ->update($data);

        return [
            'status'  => 200,
            'success' => !!$affectedRow
        ];
    }

    public function delete(Request $request, Bouncer $bouncer)
    {
        $this->authorize('deleteRole');

        $data = $this->validate($request, [
            'id' => 'bail|required|integer',
        ]);

        $success = $bouncer->role()
            ->where('id', '=', $data['id'])
            ->delete();

        return [
            'status'  => 200,
            'success' => $success
        ];
    }
}
