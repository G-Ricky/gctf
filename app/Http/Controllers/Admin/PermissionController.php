<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Ability;
use App\Models\Admin\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\Bouncer;

class PermissionController extends Controller
{
    public function index($roleId)
    {
        return view('admin.permission.index', [
            'roleId' => $roleId
        ]);
    }

    public function list($roleId, Bouncer $bouncer)
    {
        $role = $bouncer->role()->where('id', '=', $roleId)->firstOrFail();

        $abilities = $role->abilities()->get()->toArray();
        $abilities = array_column($abilities, 'id');

        return [
            'status'  => 200,
            'success' => true,
            'data'    => $abilities
        ];
    }

    public function modify($roleId, Request $request, Bouncer $bouncer)
    {
        $data = $this->validate($request, [
            'grants'  => 'bail|required_without:revokes|array',
            'revokes' => 'bail|required_without:grants|array',
        ]);

        DB::transaction(function() use ($roleId, $data, $bouncer) {
            $role = $bouncer->role()->where('id', '=', $roleId)->firstOrfail();

            if(!empty($data['grants'])) {
                $grantIds = array_column($data['grants'], 'id');
                $abilities = $bouncer->ability()->whereIn('id', $grantIds)->get();
                foreach ($abilities as $ability) {
                    $bouncer->allow($role)->to($ability);
                }
            }

            if(!empty($data['revokes'])) {
                $revokeIds = array_column($data['revokes'], 'id');
                $abilities = $bouncer->ability()->whereIn('id', $revokeIds)->get();
                foreach ($abilities as $ability) {
                    $bouncer->disallow($role)->to($ability);
                }
            }
        });

        return [
            'status'  => 200,
            'success' => true
        ];
    }
}
