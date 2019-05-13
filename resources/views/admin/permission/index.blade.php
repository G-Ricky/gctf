@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
<link href="{{ asset('css/wu-ui/wu-ui.css') }}" rel="stylesheet">
<link href="{{ asset('css/wu-ui/iconfont.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="ui container">
        @can('modifyPermission')
        <div class="ui basic vertical clearing segment">
            <button class="ui primary right floated button" onclick="savePermissions()"><i class="file text outline icon"></i> {{ __('permission.view.admin.button.save') }}</button>
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
    <script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
    <script src="{{ asset('js/common/tip.js') }}"></script>
    <script src="{{ asset('js/common/error.js') }}"></script>
    <script>
        function loadPermissions() {
            $.get(
                "{{ url('api/permissions') }}/{{ $roleId }}",
                function(response, status) {
                    if(response && response.success) {
                        let permissions = response.data;
                        for(let i in permissions) {
                            $("#privilege-" + permissions[i])
                                .prop("checked", true)
                                .data("origin", 1);
                        }
                    } else {
                        tip.error(response.message || "{{ __('global.fail') }}");
                    }
                }
            ).fail(handleError);
        }
        function loadPrivileges() {
            $.get(
                "{{ url('api/privileges/all') }}",
                function(response, status) {
                    if(response && response.success) {
                        $("#list-privileges").html(
                            template("tpl-list-privileges", {
                                "privileges": response.data
                            })
                        ).accordion();
                        loadPermissions();
                    } else {
                        tip.error(response.message || "{{ __('global.fail') }}");
                    }
                }
            ).fail(handleError);
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
                tip.error("没有任何更改");
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
                    if(response && response.success) {
                        tip.error("{{ __('global.success') }}");
                        loadPrivileges();
                    }else{
                        tip.error(response.message || "修改权限失败！");
                    }
                },
                "error": handleError,
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