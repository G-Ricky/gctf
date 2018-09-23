@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    @canany(['addRole', 'editRole'])
    <link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
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
    <div class="ui container" id="container-roles"></div>

    @canany(['addRole', 'editRole'])
    <!-- modal -->
    <div class="ui tiny basic flat modal" id="role-save">
        <i class="close icon"></i>
        <div class="header">
            {{ __('Add Role') }}
        </div>
        <div class="scrolling content">
            <div class="description">
                <form class="ui form" id="form-role" name="role" action="{{ url('api/role') }}" method="post">
                    @csrf
                    <input id="role-id" name="id" type="hidden" value="">
                    <input id="form-method" name="_method" type="hidden" value="">
                    <div class="field">
                        <label for="role-name">{{ __('Name') }}</label>
                        <input id="role-name" name="name" type="text" value="" required maxlength="100">
                    </div>

                    <div class="field">
                        <label for="role-title">{{ __('Title') }}</label>
                        <textarea id="role-title" name="title" maxlength="200"></textarea>
                    </div>
                </form>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" form="form-role" type="submit" value="{{ __('Save') }}">
        </div>
    </div>
    <!-- end modal -->
    @endcanany

    <!-- template -->
    <script id="tpl-container-roles" type="text/html">
        <div class="ui basic vertical clearing segment">
            @can('addRole')
            <button class="ui primary right floated button" onclick="addRole()"><i class="add circle icon"></i> {{ __('Add') }}</button>
            @endcan
        </div>
        <div class="ui basic vertical segment" id="table-roles">
            <table class="ui fixed single line compact table">
                <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Operation') }}</th>
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
                        <button class="ui primary button" onclick="editRole('@{{role.id}}')"><i class="edit icon"></i>{{ __('Edit') }}</button>
                        @endcan
                        @can('deleteRole')
                        <button class="ui negative button" onclick="confirm('{{ __('Are you sure to delete')}} @{{role.name}} ?') &amp;&amp; deleteRole('@{{role.id}}')"><i class="trash icon"></i>{{ __('Delete') }}</button>
                        @endcan
                    </td>
                </tr>
                @{{/each}}
                </tbody>
            </table>
        </div>
        <div class="ui vertical clearing segment">
            <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadRoles('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
            <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadRoles('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
        </div>
    </script>
    <!-- end template-->
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
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
                    if(status === "success" && response && response.status === 200) {
                        if(response.success) {
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

                        }
                    }
                },
                "error": function(jqXHR, textStatus, error) {

                },
                "complete": function() {

                }
            });
        }
        @can('addRole')
        function addRole() {
            $("#form-method").val("POST");
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
                    if(status === "success" && response && response.status === 200) {
                        loadRoles();
                    }
                },
                "error": function() {

                },
                "complete": function() {

                }
            });
        }
        @endcan
        $(document).ready(function() {
            loadRoles();
            @canany(['addRole', 'deleteRole'])
            $("#form-role").validate({
                "submitHandler": function(form) {
                    $(form).ajaxSubmit({
                        "success": function(response, status) {
                            if(status === "success" && response && response.status === 200) {
                                $("#role-save").modal('hide');
                                loadRoles();
                            }
                        },
                        "error": function(jqXHR, textStatus, error) {

                        },
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