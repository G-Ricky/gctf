@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
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
    <script id="tpl-container-privileges" type="text/html">
        <div class="ui basic vertical clearing segment">
            <button class="ui primary right floated button" onclick="addPrivilege()"><i class="add circle icon"></i> {{ __('Add') }}</button>
        </div>
        <div class="ui basic vertical segment" id="table-privileges">
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
                        <button class="ui primary button" onclick="editPrivilege('@{{privilege.id}}')"><i class="edit icon"></i>{{ __('Edit') }}</button>
                        <button class="ui negative button" onclick="deletePrivilege('@{{privilege.id}}')"><i class="trash icon"></i>{{ __('Delete') }}</button>
                    </td>
                </tr>
                @{{/each}}
                </tbody>
            </table>
        </div>
        <div class="ui vertical clearing segment">
            <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadSubmissions('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
            <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadSubmissions('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
        </div>
    </script>
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
        $(document).ready(function() {
            loadPrivileges();
        })
    </script>
@endpush