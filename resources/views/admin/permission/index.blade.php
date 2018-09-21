@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
@endpush

@section('content')
    <div class="ui container">
        <div class="ui styled fluid accordion" id="list-privileges"></div>
    </div>
    <script id="tpl-list-privileges" type="text/html">
        <div class="active title"><i class="dropdown icon"></i> 权限 </div>
        <div class="active content">
            @{{each privileges privilege index}}
            <div class="ui vertical segment">
                <div class="ui toggle checkbox">
                    <input id="privilege-@{{privilege.id}}" type="checkbox" name="privileges[@{{privilege.id}}]" data-origin="0">
                    <label for="privilege-@{{privilege.id}}">@{{privilege.title}}</label>
                </div>
            </div>
            @{{/each}}
        </div>
    </script>
@endsection

@push('scripts')
    <script>
        function loadPermissions() {
            $.get(
                "{{ url('api/permissions') . '/' . $roleId }}",
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
        function loadPrivilege() {
            $.get(
                "{{ url('api/privileges') }}",
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
        $(document).ready(function() {
            loadPrivilege();
        });
    </script>
@endpush