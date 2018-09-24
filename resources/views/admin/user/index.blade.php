@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    @canany(['addUser', 'editUser'])
    <link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
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
        <div class="header">
            {{ __('Add User') }}
        </div>
        <div class="scrolling content">
            <div class="description">
                <form class="ui form" id="form-user" name="user" action="{{ url('api/user') }}" method="post">
                    @csrf
                    <input id="user-id" name="id" type="hidden" value="">
                    <input id="form-method" name="_method" type="hidden" value="">
                    <div class="field">
                        <label for="user-name">{{ __('Name') }}</label>
                        <input id="user-name" name="name" type="text" value="" required maxlength="16">
                    </div>

                    <div class="field">
                        <label for="user-nickname">{{ __('Nickname') }}</label>
                        <input id="user-nickname" name="nickname" type="text" value="" maxlength="16">
                    </div>

                    <div class="field">
                        <label for="user-sid">{{ __('Student Number') }}</label>
                        <input id="user-sid" name="sid" type="text" value="" maxlength="10">
                    </div>

                    <div class="field">
                        <label for="user-email">{{ __('Email') }}</label>
                        <input id="user-email" name="email" type="text" value="" maxlength="100">
                    </div>

                    <div class="field">
                        <label for="user-password">{{ __('Password') }}</label>
                        <input id="user-password" name="password" type="password" value="" maxlength="16">
                    </div>

                    <div class="field">
                        <label for="user-gender">{{ __('Gender') }}</label>
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
            <input class="ui basic fluid button" form="form-user" type="submit" value="{{ __('Save') }}">
        </div>
    </div>
    <!-- end modal-->
    @endcanany

    @can('changeRelation')
    <!-- modal -->
    <div class="ui tiny basic flat modal" id="user-relation">
        <i class="close icon"></i>
        <div class="header">
            {{ __('Assign Role') }}
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
                        <label for="user-roles">{{ __('Roles') }}</label>
                        <select class="ui dropdown" id="select-roles" name="roles" multiple data-origin="[]"></select>
                    </div>

                    <div style="height: 120px;text-align: center;font-size: 90px;"><i class="angellist icon"></i><!-- 占位 --></div>
                </form>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" form="form-relation" type="submit" value="{{ __('Save') }}">
        </div>
    </div>
    <!-- end modal-->
    @endcanany

    <!-- template -->
    <script id="tpl-container-users" type="text/html">
        @can('addUser')
        <div class="ui basic vertical clearing segment">
            <button class="ui primary right floated button" onclick="addUser()"><i class="add circle icon"></i> {{ __('Add') }}</button>
        </div>
        @endcan
        <div class="ui basic vertical segment" id="table-users">
            <table class="ui single line compact table">
                <thead>
                <tr>
                    <th>{{ __('Nickname') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Student Number') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Roles') }}</th>
                    <th>{{ __('Operation') }}</th>
                </tr>
                </thead>
                <tbody>
                @{{each users user index}}
                <tr>
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
                        <button class="ui primary button" onclick="editUser('@{{user.id}}')"><i class="edit icon"></i>{{ __('Edit') }}</button>
                        @endcan
                        @can('hideUser')
                        @{{if user.is_hidden}}
                        <button class="ui primary button" onclick="confirm('{{ __('Are you sure to show') }} @{{user.name}}?') &amp;&amp; unhideUser('@{{user.id}}')"><i class="eye icon"></i>{{ __('Show') }}</button>
                        @{{else}}
                        <button class="ui orange button" onclick="confirm('{{ __('Are you sure to hide') }} @{{user.name}}?') &amp;&amp; hideUser('@{{user.id}}')"><i class="hide icon"></i>{{ __('Hide') }}</button>
                        @{{/if}}
                        @endcan
                        @can('banUser')
                        @{{if user.is_ban}}
                        <button class="ui primary button" onclick="confirm('{{ __('Are you sure to unban') }} @{{user.name}}?') &amp;&amp; unbanUser('@{{user.id}}')"><i class="undo icon"></i>{{ __('Unban') }}</button>
                        @{{else}}
                        <button class="ui orange button" onclick="confirm('{{ __('Are you sure to ban') }} @{{user.name}}?') &amp;&amp; banUser('@{{user.id}}')"><i class="ban icon"></i>{{ __('Ban') }}</button>
                        @{{/if}}
                        @endcan
                        @can('deleteUser')
                        <button class="ui negative button" onclick="confirm('{{ __('Are you sure to delete') }} @{{user.name}}?') &amp;&amp; deleteUser('@{{user.id}}')"><i class="trash icon"></i>{{ __('Delete') }}</button>
                        @endcan
                    </td>
                </tr>
                @{{/each}}
                </tbody>
            </table>
        </div>
        <div class="ui vertical clearing segment">
            <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadUsers('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
            <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadUsers('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
        </div>
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
                    if(status === "success" && response && response.status === 200) {
                        if(response.success) {
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
                        }else{

                        }
                    }
                },
                "error": function(jqXHR, textStatus, error) {

                },
                "complete": function() {

                }
            });
        }
        @can('listRoles')
        function loadRoles() {
            $.ajax({
                "url": "{{ url('api/roles/all') }}",
                "success": function(response, status) {
                    if(response && response.status === 200) {
                        if(response.success) {
                            $("#select-roles").html(
                                template('tpl-select-roles', {
                                    "roles": response.data
                                })
                            );
                        }else{
                            alert("加载角色失败");
                        }
                    }
                },
                "error": function() {
                    alert("加载角色失败");
                },
                "complete": function() {
                    $("#loading-relation").removeClass("active");
                }
            });
        }
        @endcan
        @can('addUser')
        function addUser() {
            $("#form-method").val("POST");
            $("#user-id").val("");
            $("#user-sid").val("");
            $("#user-name").val("");
            $("#user-nickname").val("");
            $("#user-gender").dropdown("set selected", "UNKNOWN");
            $("#user-email").val("");
            $("#user-save").modal('show');
        }
        @endcan
        @can('editUser')
        function editUser(id) {
            let user = usersDict[id];
            $("#form-method").val("PUT");
            $("#user-id").val(id);
            $("#user-sid").val(user.sid);
            $("#user-name").val(user.name);
            $("#user-nickname").val(user.nickname);
            $("#user-gender").dropdown("set selected", user.gender);
            $("#user-email").val(user.email);
            $("#user-save").modal('show');
        }
        @endcan
        @can('hideUser')
        function hideUser(id) {
            $.ajax({
                "url": "{{ url('api/user/hide') }}",
                "type": "POST",
                "data": {
                    "id": id,
                    "_method": "PUT"
                },
                "success": function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        loadUsers();
                    }
                },
                "error": function() {

                },
                "complete": function() {

                }
            });
        }
        function unhideUser(id) {
            $.ajax({
                "url": "{{ url('api/user/unhide') }}",
                "type": "POST",
                "data": {
                    "id": id,
                    "_method": "PUT"
                },
                "success": function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        loadUsers();
                    }
                },
                "error": function() {

                },
                "complete": function() {

                }
            });
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
                    if(status === "success" && response && response.status === 200) {
                        loadUsers();
                    }
                },
                "error": function() {

                },
                "complete": function() {

                }
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
                    if(status === "success" && response && response.status === 200) {
                        loadUsers();
                    }
                },
                "error": function() {

                },
                "complete": function() {

                }
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
                    if(status === "success" && response && response.status === 200) {
                        loadUsers();
                    }
                },
                "error": function() {

                },
                "complete": function() {

                }
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
            loadUsers();
            loadRoles();
            @canany(['addUser', 'editUser'])
            $("#user-gender").dropdown();
            @endcanany
            @canany(['addUser', 'editUser'])
            let validator = $("#form-user").validate({
                "submitHandler": function(form) {
                    $(form).ajaxSubmit({
                        "success": function(response, status) {
                            if(status === "success" && response && response.status === 200) {
                                $("#user-save").modal('hide');
                                loadUsers();
                            }
                        },
                        "error": function(jqXHR, textStatus, errorThrown) {
                            if(textStatus !== "parsererror") {
                                response = JSON.parse(jqXHR.responseText);
                                switch(jqXHR.status) {
                                    case 422:
                                        let errors = {};
                                        for(let name in response.errors) {
                                            if(response.errors[name].length > 0) {
                                                errors[name] = response.errors[name][0];
                                            }
                                        }
                                        validator.showErrors(errors);
                                        break;
                                }

                            }
                        },
                        "complete": function(jqXHR, textStatus) {

                        }
                    });
                },
                "rules": {
                    "sid": {
                        "required": requiredIfAdd,
                        "maxlength": 10
                    },
                    "name": {
                        "required": requiredIfAdd,
                        "maxlength": 16
                    },
                    "nickname": {
                        "maxlength": 16
                    },
                    "email": {
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
            let relationValidator = $("#form-relation").validate({
                "submitHandler": function (form) {
                    $("[name='assigns[]'], [name='assigns[]']").each(function() {
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
                                    loadUsers();
                                }
                            },
                            "error": function(jqXHR, status) {
                                alert(status);
                            }
                        });
                    }else{
                        alert("{{ __('Nothing changed') }}");
                    }
                }
            });
            @endcan
        })
    </script>
@endpush