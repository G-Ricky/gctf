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
                <th>{{ __('Nickname') }}</th>
                <th>{{ __('Student Number') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Group') }}</th>
                <th>{{ __('Operation') }}</th>
            </tr>
            </thead>
            <tbody id="table-users"></tbody>
        </table>
    </div>
    <script id="tpl-table-users" type="text/html">
        @{{each users user index}}
        <tr>
            <td>@{{user.nickname}}</td>
            <td>@{{user.sid}}</td>
            <td>@{{user.email}}</td>
            <td></td>
            <td>
                <button class="ui primary button" onclick="editUser('@{{user.id}}')"><i class="edit icon"></i>{{ __('Edit') }}</button>
                <button class="ui negative button" onclick="deleteUser('@{{user.id}}')"><i class="trash icon"></i>{{ __('Delete') }}</button>
            </td>
        </tr>
        @{{/each}}
    </script>
@endsection

@push('scripts')
    <script src="{{ asset('js/art-template.js') }}"></script>
    <script>
        function loadUsers(page) {
            $.ajax({
                "url": "{{ url('adm1n/users') }}",
                "type": "GET",
                "data": {
                    "p": page
                },
                "success": function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        if(response.success) {
                            render('tpl-table-users', 'table-users', {
                                "success": true,
                                "users": response.data
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