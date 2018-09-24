<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\BouncerFacade as Bouncer;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('listRoles');

        return view('admin.role.index');
    }

    public function list()
    {
        $this->authorize('listRoles');

        $paginate = Bouncer
            ::role()
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

    public function listAll()
    {
        $roles = Bouncer
            ::role()
            ->select(['id', 'name', 'title'])
            ->get()
            ->toArray();

        return [
            'status'  => 200,
            'success' => true,
            'data'    => $roles
        ];
    }

    public function add(Request $request)
    {
        $this->authorize('addRole');

        $data = $this->validate($request, [
            'name'  => 'required|string|alpha_dash|unique:roles|max:100',
            'title' => 'nullable|string|max:200',
        ]);

        $role = Bouncer::role()->create($data);

        return [
            'status'  => 200,
            'success' => !!$role
        ];
    }

    public function edit(Request $request)
    {
        $this->authorize('editRole');

        $data = $this->validate($request, [
            'id'    => 'required|integer',
            'name'  => 'required|string|alpha_dash|max:100',
            'title' => 'nullable|string|max:200',
        ]);

        $affectedRow = Bouncer::role()
            ->where('id', '=',
                $data['id']
            )
            ->update($data);

        return [
            'status'  => 200,
            'success' => !!$affectedRow
        ];
    }

    public function delete(Request $request)
    {
        $this->authorize('deleteRole');

        $data = $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $success = Bouncer::role()
            ->where('id', '=', $data['id'])
            ->delete();

        return [
            'status'  => 200,
            'success' => $success
        ];
    }

    public function change(Request $request)
    {
        $this->authorize('changeRelation');

        $data = $this->validate($request, [
            'id'       => 'required|integer|exists:users',
            'assigns'  => 'required_without:retracts|array',
            'retracts' => 'required_without:assigns|array',
        ]);

        DB::transaction(function() use($data) {
            $user = User::where('id', '=', $data['id'])->firstOrFail();

            if(isset($data['assigns'])) {
                foreach($data['assigns'] as $roleId) {
                    $role = Bouncer::role()->where('id', '=', $roleId)->firstOrFail();
                    Bouncer::assign($role)->to($user);
                }
            }

            if(isset($data['retracts'])) {
                foreach($data['retracts'] as $roleId) {
                    $role = Bouncer::role()->where('id', '=', $roleId)->firstOrFail();
                    Bouncer::retract($role)->from($user);
                }
            }
        });

        return [
            'statue'  => 200,
            'success' => true
        ];
    }
}
