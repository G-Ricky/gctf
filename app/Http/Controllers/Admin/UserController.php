<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Silber\Bouncer\Bouncer;

class UserController extends Controller
{

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
                'id', 'username', 'nickname', 'name', 'email', 'sid'
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

        $data['password'] = Hash::make($data['password']);

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
            'name'     => 'nullable|string|max:16',
            'nickname' => [
                'nullable', 'string', 'max:16',
                Rule::unique('users')->ignore($id)
            ],
            'gender'   => 'required|string|in:UNKNOWN,MALE,FEMALE',
            'email'    => [
                'nullable', 'string', 'email',
                Rule::unique('users')->ignore($id)
            ],
            'password' => 'nullable|string|min:6|max:16',
        ]);

        $data['sid'] = $data['sid'] ?? '';
        $data['name'] = $data['name'] ?? '';
        $data['email'] = $data['email'] ?? '';
        $data['nickname'] = $data['nickname'] ?? '';


        if(isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else if(array_key_exists('password', $data)) {
            unset($data['password']);
        }

        $affectedRows = User
            ::findOrFail($data['id'])
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
            ::findOrFail($data['id'])
            ->delete();

        return [
            'status'  => 200,
            'success' => $success
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
