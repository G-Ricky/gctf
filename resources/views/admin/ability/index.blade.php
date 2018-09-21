@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
@endpush

@section('content')
    <div class="ui container">
        <div class="ui basic vertical clearing segment">
            <button class="ui primary right floated button" onclick="addPrivilege()"><i class="add circle icon"></i> {{ __('Add') }}</button>
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
            <tbody id="table-privileges"></tbody>
        </table>
        <script id="tpl-table-privileges" type="text/html">
            @{{each privileges privilege index}}
            <tr>
                <td>@{{privilege.id}}</td>
                <td>@{{privilege.name}}</td>
                <td>@{{privilege.title}}</td>
                <td>
                    <button class="ui primary button" onclick="editPrivilege('@{{privilege.id}}')"><i class="edit icon"></i>{{ __('Edit') }}</button>
                    <button class="ui negative button" onclick="deletePrivilege('@{{privilege.id}}')"><i class="trash icon"></i>{{ __('Delete') }}</button>
                </td>
            </tr>
            @{{/each}}
        </script>
    </div>
@endsection

@push('scripts')
    <script>
        function loadPrivileges() {
            $.ajax({
                "url": "{{ url('api/privileges') }}",
                "type": "GET",
                "success": function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        if(response.success) {
                            $("#table-privileges").html(
                                template("tpl-table-privileges", {
                                    "privileges": response.data
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
        $(document).ready(function() {
            loadPrivileges();
        })
    </script>
@endpush