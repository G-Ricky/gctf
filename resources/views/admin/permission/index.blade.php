@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
@endpush

@section('content')
    <div class="ui container">
        @can('modifyPermission')
        <div class="ui basic vertical clearing segment">
            <button class="ui primary right floated button" onclick="savePermissions()"><i class="file text outline icon"></i> {{ __('Save') }}</button>
        </div>
        @endcan
        <div class="ui basic vertical segment" id="list-privileges"></div>
    </div>
    <script id="tpl-list-privileges" type="text/html">
        <div class="ui styled fluid accordion">
            <div class="active title"><i class="dropdown icon"></i> 权限 </div>
            <div class="active content">
                @{{each privileges privilege index}}
                <div class="ui vertical segment">
                    <div class="ui toggle checkbox">
                        <input id="privilege-@{{privilege.id}}" type="checkbox" name="privileges[@{{privilege.id}}]" data-origin="0" data-type="permission" data-id="@{{privilege.id}}" data-name="@{{privilege.name}}">
                        <label for="privilege-@{{privilege.id}}">@{{privilege.title}}</label>
                    </div>
                </div>
                @{{/each}}
            </div>
        </div>
    </script>
@endsection

@push('scripts')
    <script>
        function loadPermissions() {
            $.get(
                "{{ url('api/permissions') }}/{{ $roleId }}",
                function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        let permissions = response.data;
                        for(let i in permissions) {
                            $("#privilege-" + permissions[i])
                                .prop("checked", true)
                                .data("origin", 1);
                        }
                    }
                }
            );
        }
        function loadPrivileges() {
            $.get(
                "{{ url('api/privileges/all') }}",
                function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        $("#list-privileges").html(
                            template("tpl-list-privileges", {
                                "privileges": response.data
                            })
                        ).accordion();
                        loadPermissions();
                    }
                }
            );
        }
        @can('modifyPermission')
        function savePermissions() {
            let grants = [];
            let revokes = [];
            $("#list-privileges [data-type=permission]").each(function() {
                let isChecked = $(this).prop("checked");
                let origin = $(this).data("origin");
                let id = $(this).data("id");
                let name = $(this).data("name");
                switch(isChecked - origin) {
                    case 1: //grant
                        grants.push({
                            "id": id,
                            "name": name
                        });
                        break;
                    case -1: //revoke
                        revokes.push({
                            "id": id,
                            "name": name
                        });
                        break;
                    default: //Nothing changed
                }
            });
            if(grants.length === 0 && revokes.length === 0) {
                alert("Nothing changed");
                return;
            }
            $.ajax({
                "url": "{{ url('api/permissions') }}/{{ $roleId }}",
                "type": "POST",
                "data": {
                    "_method": "PUT",
                    "grants": grants,
                    "revokes": revokes
                },
                "success": function (response, status) {
                    if(status === "success" && response && response.status === 200) {
                        if(response.success) {
                            loadPrivileges();
                        }else{
                            alert("修改权限失败！");
                        }
                    }
                },
                "error": function () {
                    
                },
                "complete": function () {

                }
            })
        }
        @endcan
        $(document).ready(function() {
            loadPrivileges();
        });
    </script>
@endpush