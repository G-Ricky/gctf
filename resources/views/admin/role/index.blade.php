@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
@endpush

@section('content')
    <div class="ui container">
        <div class="ui basic vertical clearing segment">
            <button class="ui primary right floated button" onclick="addUser()"><i class="add circle icon"></i> {{ __('Add') }}</button>
        </div>
        <table class="ui single line table">
            <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Operation') }}</th>
            </tr>
            </thead>
            <tbody id="table-roles"></tbody>
        </table>
    </div>
    <script id="tpl-table-roles" type="text/html">
        @{{each roles role index}}
        <tr>
            <td>@{{role.id}}</td>
            <td><a href="{{ url('permissions') }}/@{{role.id}}">@{{role.name}}</a></td>
            <td>@{{role.title}}</td>
            <td>
                <button class="ui primary button" onclick="editRole('@{{role.id}}')"><i class="edit icon"></i>{{ __('Edit') }}</button>
                <button class="ui negative button" onclick="deleteRole('@{{role.id}}')"><i class="trash icon"></i>{{ __('Delete') }}</button>
            </td>
        </tr>
        @{{/each}}
    </script>
@endsection

@push('scripts')
    <script>
        function loadUsers(page) {
            $.ajax({
                "url": "{{ url('adm1n/roles') }}",
                "type": "GET",
                "data": {
                    "p": page
                },
                "success": function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        if(response.success) {
                            render('tpl-table-roles', 'table-roles', {
                                "success": true,
                                "roles": response.data
                            });
                        }else{

                        }
                    }
                },
                "error": function(XmlHttpRequest, textStatus, error) {

                },
                "complete": function() {

                }
            });
        }

        function render(templateId, targetId, data) {
            $("#" + targetId).html(
                template(templateId, data)
            );
        }

        $(document).ready(function() {
            loadUsers();
        })
    </script>
@endpush