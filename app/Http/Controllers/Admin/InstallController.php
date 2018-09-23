<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Silber\Bouncer\BouncerFacade as Bouncer;

class InstallController extends Controller
{
    private function roles()
    {
        return [
            [
                'name'  => 'super',
                'title' => '超级管理员',
            ],
            [
                'name'  => 'admin',
                'title' => '管理员',
            ],
            [
                'name'  => 'user',
                'title' => '普通用户',
            ],
            [
                'name'  => 'guest',
                'title' => '未注册用户',
            ],
            [
                'name'  => 'banned',
                'title' => '小黑屋用户',
            ]
        ];
    }

    private function abilities()
    {
        return [
            [
                'name'  => 'listChallenges',
                'title' => '查看题目列表',
            ],
            [
                'name'  => 'addChallenge',
                'title' => '添加题目',
            ],
            [
                'name'  => 'editChallenge',
                'title' => '编辑题目',
            ],
            [
                'name'  => 'deleteChallenge',
                'title' => '删除题目',
            ],
            [
                'name'  => 'viewFlag',
                'title' => '查看 flag',
            ],
            [
                'name'  => 'submitFlag',
                'title' => '提交 flag',
            ],
            [
                'name'  => 'listUsers',
                'title' => '查看注册用户列表',
            ],
            [
                'name'  => 'addUser',
                'title' => '添加用户',
            ],
            [
                'name'  => 'editUser',
                'title' => '编辑用户',
            ],
            [
                'name'  => 'hideUser',
                'title' => '隐藏用户',
            ],
            [
                'name'  => 'banUser',
                'title' => '封禁用户',
            ],
            [
                'name'  => 'listRoles',
                'title' => '查看角色列表',
            ],
            [
                'name'  => 'addRole',
                'title' => '添加角色',
            ],
            [
                'name'  => 'editRole',
                'title' => '编辑角色',
            ],
            [
                'name'  => 'deleteRole',
                'title' => '删除角色',
            ],
            [
                'name'  => 'listPrivileges',
                'title' => '查看权限列表',
            ],
            [
                'name'  => 'addPrivilege',
                'title' => '添加权限',
            ],
            [
                'name'  => 'editPrivilege',
                'title' => '编辑权限',
            ],
            [
                'name'  => 'deletePrivilege',
                'title' => '删除权限',
            ],
            [
                'name'  => 'listPermissions',
                'title' => '查看角色拥有的权限'
            ],
            [
                'name'  => 'grantPermission',
                'title' => '授予角色权限',
            ],
            [
                'name'  => 'revokePermission',
                'title' => '回收角色权限',
            ],
            [
                'name'  => 'modifyPermission',
                'title' => '修改角色权限',
            ],
            [
                'name'  => 'assignRole',
                'title' => '赋予用户角色',
            ],
            [
                'name'  => 'retractRole',
                'title' => '回收用户角色',
            ]
        ];
    }

    private function permissions()
    {
        return [
            [
                'role'      => 'admin',
                'abilities' => [
                    'listChallenges',
                    'addChallenge',
                    'editChallenge',
                    'deleteChallenge',
                    'viewFlag',
                    'listUsers',
                    'hideUser',
                    'banUser',
                    'listRoles',
                    'listPrivileges',
                    'listPermissions',
                ]
            ],
            [
                'role'      => 'user',
                'abilities' => [
                    'listChallenges',
                    'submitFlag',
                ]
            ],
            [
                'role'      => 'guest',
                'abilities' => [
                    'listChallenges',
                ]
            ]
        ];
    }

    private function permitEverything()
    {
        return [
            'super'
        ];
    }

    private function prohibitions()
    {
        return [];
    }

    private function prohibitEverything()
    {
        return [
            'banned'
        ];
    }

    private function assigedRoles()
    {
        return [
            'super' => [
                1
            ]
        ];
    }

    public function __construct()
    {
        $token = Request::get('token');
        $installToken = env('INSTALL_TOKEN', null);

        if(!isset($installToken) || $token !== $installToken) {
            abort(404);
        }
    }

    public function index()
    {

    }

    public function install()
    {
        if(
            Bouncer::role()->count() !== 0 ||
            Bouncer::ability()->count() !== 0
        ) {
            abort(500);
        }
        DB::transaction(function() {
            //Grant All Privileges For Roles
            foreach($this->permitEverything() as $roleName) {
                Bouncer::allow($roleName)->everything();
            }

            //Forbid All Privileges For Roles
            foreach($this->prohibitEverything() as $roleName) {
                Bouncer::forbid($roleName)->everything();
            }

            //Create Roles
            foreach($this->roles() as $role) {
                Bouncer::role()->create($role);
            }

            //Create Privileges
            foreach($this->abilities() as $ability) {
                Bouncer::ability()->create($ability);
            }

            //Grant Privileges
            foreach($this->permissions() as $permission) {
                $roleName = $permission['role'];
                foreach($permission['abilities'] as $abilityName) {
                    Bouncer::allow($roleName)->to($abilityName);
                }
            }

            //Forbid Privileges
            foreach($this->prohibitions() as $prohibition) {
                $roleName = $prohibition['role'];
                foreach($prohibition['abilities'] as $abilityName) {
                    Bouncer::forbid($roleName)->to($abilityName);
                }
            }

            //Assign Role to User
            $users = [];
            foreach($this->assigedRoles() as $roleName => $userIds) {
                foreach($userIds as $userId) {
                    if(!isset($users[$userId])) {
                        $users[$userId] = $user = User::find(1)->first();
                    }else{
                        $user = $users[$userId];
                    }
                }
                if($user) {
                    Bouncer::assign($roleName)->to($user);
                }
            }
        });

        return [
            'status'  => 200,
            'success' => true
        ];
    }
}
