@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    @canany(['addRole', 'editRole'])
    <link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/wu-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/iconfont.css') }}" rel="stylesheet">
    @endcanany
    <style>
        #container-roles {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        #container-roles>#table-roles {
            flex: 1;
        }
    </style>
@endpush

@section('content')
    <div class="ui container">
        @can('addRole')
        <div class="ui basic vertical clearing segment">
            <button class="ui primary right floated button" onclick="addRole()"><i class="add circle icon"></i> {{ __('role.view.admin.button.add') }}</button>
        </div>
        @endcan
    </div>
    <div class="ui container" id="container-roles"></div>

    @canany(['addRole', 'editRole'])
    <!-- modal -->
    <div class="ui tiny basic flat modal" id="role-save">
        <i class="close icon"></i>
        <div class="header" id="modal-role-title">
            {{ __('role.view.admin.modal.title.add') }}
        </div>
        <div class="scrolling content">
            <div class="description">
                <form class="ui form" id="form-role" name="role" action="{{ url('api/role') }}" method="post">
                    @csrf
                    <input id="role-id" name="id" type="hidden" value="">
                    <input id="form-method" name="_method" type="hidden" value="">
                    <div class="field">
                        <label for="role-name">{{ __('role.view.admin.modal.label.name') }}</label>
                        <input id="role-name" name="name" type="text" value="" required maxlength="100">
                    </div>

                    <div class="field">
                        <label for="role-title">{{ __('role.view.admin.modal.label.title') }}</label>
                        <textarea id="role-title" name="title" maxlength="200"></textarea>
                    </div>
                </form>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" form="form-role" type="submit" value="{{ __('role.view.admin.modal.button.save') }}">
        </div>
    </div>
    <!-- end modal -->
    @endcanany

    <!-- template -->
    <script id="tpl-container-roles" type="text/html">
        <div class="ui basic vertical segment" id="table-roles">
            <table class="ui single line compact table">
                <thead>
                <tr>
                    <th>{{ __('role.view.admin.table.id') }}</th>
                    <th>{{ __('role.view.admin.table.name') }}</th>
                    <th>{{ __('role.view.admin.table.title') }}</th>
                    <th>{{ __('role.view.admin.table.operation') }}</th>
                </tr>
                </thead>
                <tbody>
                @{{each roles role index}}
                <tr>
                    <td>@{{role.id}}</td>
                    <td><a href="{{ url('permissions') }}/@{{role.id}}">@{{role.name}}</a></td>
                    <td>@{{role.title}}</td>
                    <td>
                        @can('editRole')
                        <button class="ui primary icon button" data-tooltip="{{ __('role.view.admin.table.row.tooltip.edit') }}" onclick="editRole('@{{role.id}}')">
                            <i class="edit icon"></i>
                        </button>
                        @endcan
                        @can('deleteRole')
                        <button class="ui negative icon button" data-tooltip="{{ __('role.view.admin.table.row.tooltip.delete') }}" onclick="confirm('{{ __('role.view.admin.table.row.confirm.delete')}} @{{role.name}} ?') &amp;&amp; deleteRole('@{{role.id}}')">
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
            <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadRoles('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
            <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadRoles('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
        </div>
        @{{/if}}
    </script>
    <!-- end template-->
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
    <script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
    <script src="{{ asset('js/common/tip.js') }}"></script>
    <script src="{{ asset('js/common/error.js') }}"></script>
    <script>
        let rolesDict = {};
        function loadRoles(url) {
            if(url == null) {
                url = "{{ url('api/roles') }}";
            }
            $.ajax({
                "url": url,
                "type": "GET",
                "success": function(response, status) {
                    if(response && response.success) {
                        let roles = response.data;
                        for(let i = 0;i < roles.length;++i) {
                            rolesDict[roles[i].id] = roles[i];
                        }
                        $("#container-roles").html(
                            template("tpl-container-roles", {
                                "success": true,
                                "roles": response.data,
                                "paginate": response.paginate
                            })
                        );
                    }else{
                        tip.error(response.message || "{{ __('global.fail') }}");
                    }
                },
                "error": handleError,
                "complete": function() {

                }
            });
        }
        @can('addRole')
        function addRole() {
            $("#form-method").val("POST");
            $("#modal-role-title").text("{{ __('role.view.admin.modal.title.add') }}");
            $("#role-id").val("");
            $("#role-name").val("");
            $("#role-title").val("");
            $("#role-save").modal('show');
        }
        @endcan
        @can('editRole')
        function editRole(id) {
            let role = rolesDict[id];
            $("#form-method").val("PUT");
            $("#modal-role-title").text("{{ __('role.view.admin.modal.title.edit') }}");
            $("#role-id").val(id);
            $("#role-name").val(role.name);
            $("#role-title").val(role.title);
            $("#role-save").modal('show');
        }
        @endcan
        @can('deleteRole')
        function deleteRole(id) {
            $.ajax({
                "url": "{{ url('api/role') }}",
                "type": "POST",
                "data": {
                    "id": id,
                    "_method": "DELETE"
                },
                "success": function(response, status) {
                    if(response && response.success) {
                        loadRoles();
                        tip.success("{{ __('global.success') }}");
                    } else {
                        tip.error(response.message || "{{ __('global.fail') }}")
                    }
                },
                "error": handleError,
                "complete": function() {

                }
            });
        }
        @endcan
        $(document).ready(function() {
            loadRoles();
            @canany(['addRole', 'editRole'])
            $("#form-role").validate({
                "submitHandler": function(form) {
                    $(form).ajaxSubmit({
                        "success": function(response, status) {
                            if(response && response.success) {
                                $("#role-save").modal('hide');
                                loadRoles();
                            } else {
                                tip.error(response.message || "{{ __('global.fail') }}")
                            }
                        },
                        "error": handleError,
                        "complete": function(jqXHR, textStatus) {

                        }
                    });
                },
                "rules": {
                    "name": {
                        "required": true,
                        "maxlength": 100
                    },
                    "title": {
                        "maxlength": 200
                    }
                }
            });
            @endcanany
        })
    </script>
@endpush