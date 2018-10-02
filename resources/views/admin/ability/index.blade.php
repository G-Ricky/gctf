@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    <link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
    <style>
        #container-privileges {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }
        #container-privileges #table-privileges {
            flex: 1;
        }
    </style>
@endpush

@section('content')
    <div class="ui container" id="container-privileges"></div>
    @canany(['addPrivilege', 'editPrivilege'])
    <div class="ui tiny basic flat modal" id="privilege-save">
        <i class="close icon"></i>
        <div class="header">
            {{ __('Add Privilege') }}
        </div>
        <div class="scrolling content">
            <div class="description">
                <form class="ui form" id="form-privilege" name="privilege" action="{{ url('api/privilege') }}" method="post">
                    @csrf
                    <input id="privilege-id" name="id" type="hidden" value="">
                    <input id="form-method" name="_method" type="hidden" value="">
                    <div class="field">
                        <label for="privilege-name">{{ __('Name') }}</label>
                        <input id="privilege-name" name="name" type="text" value="" required maxlength="100">
                    </div>

                    <div class="field">
                        <label for="privilege-title">{{ __('Title') }}</label>
                        <textarea id="privilege-title" name="title" maxlength="200"></textarea>
                    </div>
                </form>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" form="form-privilege" type="submit" value="{{ __('Save') }}">
        </div>
    </div>
    @endcanany
    <script id="tpl-container-privileges" type="text/html">
        @can('addPrivilege')
        <div class="ui basic vertical clearing segment">
            <button class="ui primary right floated button" onclick="addPrivilege()"><i class="add circle icon"></i> {{ __('Add') }}</button>
        </div>
        @endcan
        <div class="ui basic vertical segment" id="table-privileges">
            @{{if privileges && privileges.length > 0}}
            <table class="ui single line table">
                <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Operation') }}</th>
                </tr>
                </thead>
                <tbody>
                @{{each privileges privilege index}}
                <tr>
                    <td>@{{privilege.id}}</td>
                    <td>@{{privilege.name}}</td>
                    <td>@{{privilege.title}}</td>
                    <td>
                        @can('editPrivilege')
                        <button class="ui primary icon button" data-tooltip="{{ __('Edit privilege') }}" onclick="editPrivilege('@{{privilege.id}}')">
                            <i class="edit icon"></i>
                        </button>
                        @endcan
                        @can('deletePrivilege')
                        <button class="ui negative icon button" data-tooltip="{{ __('Delete privilege') }}" onclick="confirm('确定删除？') &amp;&amp; deletePrivilege('@{{privilege.id}}')">
                            <i class="trash icon"></i>
                        </button>
                        @endcan
                    </td>
                </tr>
                @{{/each}}
                </tbody>
            </table>
            @{{else}}
            <div class="ui warning message">
                <div class="content">
                    <p>暂无数据</p>
                </div>
            </div>
            @{{/if}}
        </div>
        <div class="ui vertical clearing segment">
            <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadPrivileges('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
            <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadPrivileges('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
        </div>
    </script>
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
    <script>
        let privilegesDict = {};
        function loadPrivileges(url) {
            if(url == null) {
                url = "{{ url('api/privileges') }}";
            }
            $.ajax({
                "url": url,
                "type": "GET",
                "success": function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        if(response.success) {
                            let privileges = response.data;
                            for(let i = 0;i < privileges.length;++i) {
                                privilegesDict[privileges[i].id] = privileges[i];
                            }
                            $("#container-privileges").html(
                                template("tpl-container-privileges", {
                                    "privileges": response.data,
                                    "paginate": response.paginate
                                })
                            );
                        }
                    }
                },
                "error": function(XmlHttpRequest, textStatus, error) {

                },
                "complete": function() {

                }
            });
        }
        @can('addPrivilege')
        function addPrivilege() {
            $("#form-method").val("POST");
            $("#privilege-id").val("");
            $("#privilege-name").val("");
            $("#privilege-title").val("");
            $("#privilege-save").modal('show');
        }
        @endcan
        @can('editPrivilege')
        function editPrivilege(id) {
            let privilege = privilegesDict[id];
            $("#form-method").val("PUT");
            $("#privilege-id").val(id);
            $("#privilege-name").val(privilege.name);
            $("#privilege-title").val(privilege.title);
            $("#privilege-save").modal('show');
        }
        @endcan
        @can('deletePrivilege')
        function deletePrivilege(id) {
            $.ajax({
                "url": "{{ url('api/privilege') }}",
                "type": "POST",
                "data": {
                    "id": id,
                    "_method": "DELETE"
                },
                "success": function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        loadPrivileges();
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
            loadPrivileges();
            @canany(['addPrivilege'])
            $("#form-privilege").validate({
                "submitHandler": function(form) {
                    $(form).ajaxSubmit({
                        "success": function(response, status) {
                            if(status === "success" && response && response.status === 200) {
                                $("#privilege-save").modal('hide');
                                loadPrivileges();
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
        });
    </script>
@endpush