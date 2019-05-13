@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    @canany(['addUser', 'editUser'])
    <link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/wu-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/iconfont.css') }}" rel="stylesheet">
    @endcanany
    <style>
        #container-users {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        #container-users>#table-users {
            flex: 1;
        }
    </style>
@endpush

@section('content')
    <div class="ui container" id="container-users"></div>

    @canany(['addUser', 'editUser'])
    <!-- modal -->
    <div class="ui tiny basic flat modal" id="user-save">
        <i class="close icon"></i>
        <div class="header" id="modal-user-title">
            {{ __('user.view.admin.modal.title.edit') }}
        </div>
        <div class="scrolling content">
            <div class="description">
                <form class="ui form" id="form-user" name="user" action="{{ url('api/user') }}" method="post">
                    @csrf
                    <input id="user-id" name="id" type="hidden" value="">
                    <input id="form-method" name="_method" type="hidden" value="">

                    <div class="field">
                        <label for="user-nickname">{{ __('user.view.admin.modal.label.nickname') }}</label>
                        <input id="user-nickname" name="nickname" type="text" value="" maxlength="16">
                    </div>

                    <div class="field">
                        <label for="user-name">{{ __('user.view.admin.modal.label.name') }}</label>
                        <input id="user-name" name="name" type="text" value="" required maxlength="16">
                    </div>

                    <div class="field">
                        <label for="user-sid">{{ __('user.view.admin.modal.label.sid') }}</label>
                        <input id="user-sid" name="sid" type="text" value="" maxlength="10">
                    </div>

                    <div class="field">
                        <label for="user-email">{{ __('user.view.admin.modal.label.email') }}</label>
                        <input id="user-email" name="email" type="text" value="" maxlength="100">
                    </div>

                    <div class="field">
                        <label for="user-password">{{ __('user.view.admin.modal.label.password') }}</label>
                        <input id="user-password" name="password" type="password" value="" maxlength="16">
                    </div>

                    <div class="field">
                        <label for="user-gender">{{ __('user.view.admin.modal.label.gender') }}</label>
                        <select id="user-gender" name="gender">
                            <option value="UNKNOWN">UNKNOWN</option>
                            <option value="MALE">MALE</option>
                            <option value="FEMALE">FEMALE</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" form="form-user" type="submit" value="{{ __('user.view.admin.modal.button.save') }}">
        </div>
    </div>
    <!-- end modal-->
    @endcanany

    @can('changeRelation')
    <!-- modal -->
    <div class="ui tiny basic flat modal" id="user-relation">
        <i class="close icon"></i>
        <div class="header">
            {{ __('user.view.admin.assignRoleModal.title') }}
        </div>
        <div class="scrolling content">
            <div class="ui dimmer" id="loading-relation">
                <div class="ui text loader">加载</div>
            </div>
            <div class="description">
                <form class="ui form" id="form-relation" name="relation" action="{{ url('api/relation') }}" method="post">
                    @csrf
                    <input id="relation-user-id" name="id" type="hidden" value="">
                    <input id="relation-form-method" name="_method" type="hidden" value="PUT">

                    <div class="field">
                        <label for="user-roles">{{ __('user.view.admin.assignRoleModal.label.roles') }}</label>
                        <select class="ui dropdown" id="select-roles" name="roles" multiple data-origin="[]"></select>
                    </div>

                    <div style="height: 120px;text-align: center;font-size: 90px;"><i class="angellist icon"></i><!-- 占位 --></div>
                </form>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" form="form-relation" type="submit" value="{{ __('user.view.admin.assignRoleModal.button.save') }}">
        </div>
    </div>
    <!-- end modal-->
    @endcan

    <!-- template -->
    <script id="tpl-container-users" type="text/html">
        <div class="ui basic vertical segment" id="table-users">
            <table class="ui single line compact table">
                <thead>
                <tr>
                    <th>{{ __('user.view.admin.table.id') }}</th>
                    <th>{{ __('user.view.admin.table.username') }}</th>
                    <th>{{ __('user.view.admin.table.nickname') }}</th>
                    <th>{{ __('user.view.admin.table.name') }}</th>
                    <th>{{ __('user.view.admin.table.sid') }}</th>
                    <th>{{ __('user.view.admin.table.email') }}</th>
                    <th>{{ __('user.view.admin.table.roles') }}</th>
                    <th>{{ __('user.view.admin.table.operation') }}</th>
                </tr>
                </thead>
                <tbody>
                @{{each users user index}}
                <tr>
                    <td>@{{user.id}}</td>
                    <td>@{{user.username}}</td>
                    <td>@{{user.nickname}}</td>
                    <td>@{{user.name}}</td>
                    <td>@{{user.sid}}</td>
                    <td>@{{user.email}}</td>
                    <td>
                        @canany(['changeRelation'])
                        <a href="javascript:changeRelation('@{{user.id}}')">
                        @endcanany
                        @{{if user.roles && user.roles.length > 0}}
                        @{{each user.roles role index}}
                        @{{if index !== 0}},@{{/if}} @{{role.name}}
                        @{{/each}}
                        @{{else}}
                            None
                        @{{/if}}
                        @canany(['changeRelation'])
                        </a>
                        @endcanany
                    </td>
                    <td>
                        @can('editUser')
                        <button class="ui primary icon button" data-tooltip="{{ __('user.view.admin.table.row.tooltip.edit') }}" onclick="editUser('@{{user.id}}')">
                            <i class="edit icon"></i>
                        </button>
                        @endcan
                        @can('banUser')
                        @{{if user.is_ban}}
                        <button class="ui primary icon button" data-tooltip="{{ __('user.view.admin.table.row.tooltip.unban') }}" onclick="confirm('{{ __('user.view.admin.table.row.confirm.unban') }} @{{user.nickname || user.name}}?') &amp;&amp; unbanUser('@{{user.id}}')">
                            <i class="undo icon"></i>
                        </button>
                        @{{else}}
                        <button class="ui orange icon button" data-tooltip="{{ __('user.view.admin.table.row.tooltip.ban') }}" onclick="confirm('{{ __('user.view.admin.table.row.confirm.ban') }} @{{user.nickname || user.name}}?') &amp;&amp; banUser('@{{user.id}}')">
                            <i class="ban icon"></i>
                        </button>
                        @{{/if}}
                        @endcan
                        @can('deleteUser')
                        <button class="ui negative icon button" data-tooltip="{{ __('user.view.admin.table.row.tooltip.delete') }}" onclick="confirm('{{ __('user.view.admin.table.row.confirm.delete') }} @{{user.nickname || user.name}}?') &amp;&amp; deleteUser('@{{user.id}}')">
                            <i class="trash icon"></i>
                        </button>
                        @endcan
                    </td>
                </tr>
                @{{/each}}
                </tbody>
            </table>
        </div>
        @{{if paginate.last_page > 1}}
        <div class="ui vertical clearing segment">
            <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadUsers('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
            <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadUsers('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
        </div>
        @{{/if}}
    </script>
    <!-- end template-->

    <!-- template -->
    <script id="tpl-select-roles" type="text/html">
        @{{each roles role index}}
        <option value="@{{role.id}}">@{{role.name}} （@{{role.title}}）</option>
        @{{/each}}
    </script>
    <!-- end template -->
@endsection

@push('scripts')
    @canany(['addUser', 'editUser'])
    <script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
    <script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
    <script src="{{ asset('js/common/tip.js') }}"></script>
    <script src="{{ asset('js/common/error.js') }}"></script>
    @endcanany
    <script>
        let usersDict = {};
        function loadUsers(url) {
            if(url == null) {
                url = "{{ url('api/users') }}";
            }
            $.ajax({
                "url": url,
                "type": "GET",
                "success": function(response, status) {
                    if(response && response.success) {
                        let users = response.data;
                        for(let i = 0;i < users.length;++i) {
                            usersDict[users[i].id] = users[i];
                        }
                        $("#container-users").html(
                            template("tpl-container-users", {
                                "success": true,
                                "users": response.data,
                                "paginate": response.paginate
                            })
                        );
                    } else {
                        tip.error(response.message || "{{ __('global.fail') }}")
                    }
                },
                "error": handleError
            });
        }
        @can('listRoles')
        function loadRoles() {
            $.ajax({
                "url": "{{ url('api/roles/all') }}",
                "success": function(response, status) {
                    if(response && response.success) {
                        $("#select-roles").html(
                            template('tpl-select-roles', {
                                "roles": response.data
                            })
                        );
                    }else{
                        alert("加载角色失败");
                    }
                },
                "error": handleError,
                "complete": function() {
                    $("#loading-relation").removeClass("active");
                }
            });
        }
        @endcan
        @can('editUser')
        function editUser(id) {
            let user = usersDict[id];
            $("#form-method").val("PUT");
            $("#modal-user-title").text("{{ __('user.view.admin.modal.title.edit') }}");
            $("#user-id").val(id);
            $("#user-sid").val(user.sid);
            $("#user-name").val(user.name);
            $("#user-nickname").val(user.nickname);
            $("#user-gender").dropdown("set selected", user.gender);
            $("#user-email").val(user.email);
            $("#user-save").modal('show');
        }
        @endcan
        @can('banUser')
        function banUser(id) {
            $.ajax({
                "url": "{{ url('api/user/ban') }}",
                "type": "POST",
                "data": {
                    "id": id,
                    "_method": "PUT"
                },
                "success": function(response, status) {
                    if(response && response.success) {
                        loadUsers();
                        tip.success("{{ __('global.success') }}");
                    } else {
                        tip.error(response.message || "{{ __('global.fail') }}");
                    }
                },
                "error": handleError
            });
        }
        function unbanUser(id) {
            $.ajax({
                "url": "{{ url('api/user/unban') }}",
                "type": "POST",
                "data": {
                    "id": id,
                    "_method": "PUT"
                },
                "success": function(response, status) {
                    if(response && response.success) {
                        loadUsers();
                        tip.success("{{ __('global.success') }}");
                    } else {
                        tip.error(response.message || "{{ __('global.fail') }}");
                    }
                },
                "error": handleError
            });
        }
        @endcan
        @can('deleteUser')
        function deleteUser(id) {
            $.ajax({
                "url": "{{ url('api/user') }}",
                "type": "POST",
                "data": {
                    "id": id,
                    "_method": "DELETE"
                },
                "success": function(response, status) {
                    if(response && response.success) {
                        loadUsers();
                        tip.success("{{ __('global.success') }}");
                    } else {
                        tip.error(response.message || "{{ __('global.fail') }}");
                    }
                },
                "error": handleError
            });
        }
        @endcan
        @can('changeRelation')
        function changeRelation(id) {
            let user = usersDict[id];
            $("#user-relation").modal('show');
            $("#relation-user-id").val(id);
            $("#select-roles").dropdown("clear");
            $("#select-roles").data("origin", JSON.stringify(user.roles));
            let roleIds = [];
            for(let i = 0;i < user.roles.length;++i) {
                roleIds.push(user.roles[i].id.toString());
            }
            $("#select-roles").dropdown("set selected", roleIds);
        }
        @endcan
        @canany(['addUser', 'editUser'])
        function requiredIfAdd() {
            return $("#user-id").val().length === 0
        }
        @endcanany
        $(document).ready(function() {
            @canany(['addUser', 'editUser'])
            $("#user-save").modal();
            @endcanany
            loadUsers();
            loadRoles();
            @canany(['addUser', 'editUser'])
            $("#user-gender").dropdown();
            @endcanany
            @canany(['addUser', 'editUser'])
            $("#form-user").validate({
                "submitHandler": function(form) {
                    $(form).ajaxSubmit({
                        "success": function(response, status) {
                            if(response && response.success) {
                                $("#user-save").modal('hide');
                                tip.success("{{ __('global.success') }}");
                                loadUsers();
                            } else {
                                tip.error(response.message || "{{ __('global.fail') }}");
                            }
                        },
                        "error": handleError,
                        "complete": function(jqXHR, textStatus) {

                        }
                    });
                    return false;
                },
                "rules": {
                    "sid": {
                        "required": false,
                        "maxlength": 10
                    },
                    "name": {
                        "required": false,
                        "maxlength": 16
                    },
                    "nickname": {
                        "maxlength": 16
                    },
                    "email": {
                        "required": false,
                        "maxlength": 100,
                        "email": true
                    },
                    "password": {
                        "required": requiredIfAdd,
                        "minlength": 6,
                        "maxlength": 16
                    }
                }
            });
            @endcanany
            @can('changeRelation')
            $("#form-relation").validate({
                "submitHandler": function (form) {
                    $("[name='assigns[]'], [name='retracts[]']").each(function() {
                        this.remove();
                    });

                    let origins = JSON.parse($("#select-roles").data("origin"));
                    let originIds = [];
                    let roleIds = $("#select-roles").dropdown("get value");
                    let assigns = [], retracts = [];

                    for(let i = 0;i < origins.length;++i) {
                        originIds.push(String(origins[i].id));
                    }
                    //差集
                    for(let i = 0;i < roleIds.length;++i) {
                        if(originIds.indexOf(roleIds[i]) === -1) {
                            assigns.push(roleIds[i]);
                        }
                    }

                    for(let i = 0;i < originIds.length;++i) {
                        if(roleIds.indexOf(originIds[i]) === -1) {
                            retracts.push(originIds[i]);
                        }
                    }

                    if(assigns.length !== 0 || retracts.length !== 0) {
                        $.each(assigns, function(i, value) {
                            $("#relation-user-id").after('<input type="hidden" name="assigns[]" value="' + value + '">');
                        });
                        $.each(retracts, function(i, value) {
                            $("#relation-user-id").after('<input type="hidden" name="retracts[]" value="' + value + '">');
                        });

                        $(form).ajaxSubmit({
                            "success": function(response, status) {
                                if(response.success) {
                                    $("#user-relation").modal('hide');
                                    tip.success("{{ __('global.success') }}");
                                    loadUsers();
                                } else {
                                    tip.error(response.message || "{{ __('global.error') }}")
                                }
                            },
                            "error": handleError
                        });
                    }else{
                        $("#user-relation").modal('hide');
                    }
                }
            });
            @endcan
        })
    </script>
@endpush