<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Silber\Bouncer\Bouncer;

class UserController extends Controller
{
    private function doHide($userId, $isHidden)
    {
        return !!User
            ::where('id', '=', $userId)
            ->update([
                'is_hidden' => $isHidden
            ]);
    }

    public function index()
    {
        $this->authorize('listUsers');

        return view('admin.user.index');
    }

    public function list()
    {
        $this->authorize('listUsers');

        $paginate = User
            ::select([
                'id', 'username', 'nickname', 'name', 'email', 'sid', 'is_hidden'
            ])
            ->with('roles')
            ->paginate(20, ['*'], 'p')
            ->jsonSerialize();

        $users = $paginate['data'];

        foreach($users as $i => $user) {
            $roles = [];
            $isBan = false;
            foreach($users[$i]['roles'] as $role) {
                $roles[] = array_only($role, [
                    'id', 'name', 'title'
                ]);
                if($role['name'] === 'banned') {
                    $isBan = true;
                }
            }
            $users[$i]['roles'] = $roles;
            $users[$i]['is_ban'] = $isBan;
        }

        unset($paginate['data']);

        return [
            'status'   => 200,
            'success'  => true,
            'data'     => $users,
            'paginate' => $paginate
        ];
    }

    public function add(Request $request)
    {
        $this->authorize('addUser');

        $data = $this->validate($request, [
            'sid'      => 'required|string|max:10|unique:users',
            'name'     => 'required|string|max:255|unique:users',
            'nickname' => 'nullable|string|max:16',
            'gender'   => 'required|string|in:UNKNOWN,MALE,FEMALE',
            'email'    => 'nullable|string|email|unique:users',
            'password' => 'required|string|min:6|max:16',
        ]);

        $user = User::create($data);

        return [
            'status'  => 200,
            'success' => !!$user,
        ];
    }

    public function edit(Request $request)
    {
        $this->authorize('editUser');

        $id = $request->get('id');

        $data = $this->validate($request, [
            'id'       => 'required|integer|exists:users',
            'sid'      => [
                'nullable', 'string', 'max:10',
                Rule::unique('users')->ignore($id)
            ],
            'name'     => [
                'nullable', 'string', 'max:16',
                Rule::unique('users')->ignore($id)
            ],
            'nickname' => 'nullable|string|max:16',
            'gender'   => 'nullable|string|in:UNKNOWN,MALE,FEMALE',
            'email'    => [
                'nullable', 'string', 'email',
                Rule::unique('users')->ignore($id)
            ],
            'password' => 'nullable|string|min:6|max:16',
        ]);

        foreach($data as $key => $value) {
            if($value === '' || is_null($data[$key])) {
                unset($data[$key]);
            }
        }

        $affectedRows = User
            ::where('id', '=', $data['id'])
            ->update($data);

        return [
            'status'  => 200,
            'success' => !!$affectedRows
        ];
    }

    public function delete(Request $request)
    {
        $this->authorize('deleteUser');

        $data = $this->validate($request, [
            'id' => 'bail|required|integer',
        ]);

        $success = User
            ::where('id', '=', $data['id'])
            ->delete();

        return [
            'status'  => 200,
            'success' => $success
        ];
    }

    public function hide(Request $request)
    {
        $this->authorize('hideUser');

        $data = $this->validate($request, [
            'id' => 'bail|required|integer',
        ]);

        return [
            'status'  => 200,
            'success' => $this->doHide($data['id'], true)
        ];
    }

    public function unhide(Request $request)
    {
        $this->authorize('hideUser');

        $data = $this->validate($request, [
            'id' => 'bail|required|integer',
        ]);

        return [
            'status'  => 200,
            'success' => $this->doHide($data['id'], false)
        ];
    }

    public function ban(Request $request, Bouncer $bouncer)
    {
        $this->authorize('banUser');

        $data = $this->validate($request, [
            'id' => 'bail|required|integer',
        ]);

        $user = User::findOrFail($data['id']);

        $success = $bouncer->assign('banned')->to($user);

        return [
            'status'  => 200,
            'success' => !!$success
        ];
    }

    public function unban(Request $request, Bouncer $bouncer)
    {
        $this->authorize('banUser');

        $data = $this->validate($request, [
            'id' => 'bail|required|integer',
        ]);

        $user = User::findOrFail($data['id']);

        $bouncer->retract('banned')->from($user);

        return [
            'status'  => 200,
            'success' => true
        ];
    }
}
