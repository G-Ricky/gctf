@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    @canany(['addUser', 'editUser'])
    <link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
    @endcanany
    <link href="{{ asset('css/wu-ui/wu-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/iconfont.css') }}" rel="stylesheet">
    <style>
        #container-contents {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        #container-contents>#table-contents {
            flex: 1;
        }
    </style>
@endpush

@section('content')
<div class="ui container" id="container-contents"></div>

@canany(['addContent', 'editContent'])
<!-- modal -->
<div class="ui tiny basic flat modal" id="content-save">
    <i class="close icon"></i>
    <div class="header">
        {{ __('Save Content') }}
    </div>
    <div class="scrolling content">
        <div class="description">
            <form class="ui form" id="form-content" name="content" action="{{ url('api/content') }}" method="post">
                @csrf
                <input id="content-id" name="id" type="hidden" value="">
                <input id="form-method" name="_method" type="hidden" value="">
                <div class="field">
                    <label for="content-title">{{ __('Title') }}</label>
                    <input id="content-title" name="title" type="text" value="" required maxlength="60">
                </div>

                <div class="field">
                    <label for="content-type">{{ __('Type') }}</label>
                    <select id="content-type" name="type">
                        <option value="home">Home</option>
                    </select>
                </div>

                <div class="field">
                    <label for="content-content">{{ __('Content') }}</label>
                    <textarea id="content-content" name="content" rows="6" maxlength="2000"></textarea>
                </div>
            </form>
        </div>
    </div>
    <div class="actions">
        <input class="ui basic fluid button" form="form-content" type="submit" value="{{ __('Save') }}">
    </div>
</div>
<!-- end modal-->
@endcanany

<!-- template -->
<script id="tpl-container-contents" type="text/html">
    @can('addContent')
    <div class="ui basic vertical clearing segment">
        <button class="ui primary right floated button" onclick="addContent()"><i class="add circle icon"></i> {{ __('Add') }}</button>
    </div>
    @endcan
    <div class="ui basic vertical segment" id="table-contents">
        @{{if contents && contents.length > 0}}
        <table class="ui single line table">
            <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Content') }}</th>
                <th>{{ __('Modifier') }}</th>
                <th>{{ __('Operation') }}</th>
            </tr>
            </thead>
            <tbody>
            @{{each contents content index}}
            <tr>
                <td>@{{content.id}}</td>
                <td>@{{content.title}}</td>
                <td>@{{content.content}}</td>
                <td>@{{content.modifier.nickname || content.modifier.username}}</td>
                <td>
                    @can('editContent')
                    <button class="ui primary icon button" data-tooltip="{{ __('Edit content') }}" onclick="editContent('@{{content.id}}')">
                        <i class="edit icon"></i>
                    </button>
                    @endcan
                    @can('deleteContent')
                    <button class="ui negative icon button" data-tooltip="{{ __('Delete content') }}" onclick="confirm('{{ __('Are you sure to delete it?') }}') &amp;&amp; deleteContent('@{{content.id}}')">
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
                <p>{{ __('Empty') }}</p>
            </div>
        </div>
        @{{/if}}
    </div>
    <div class="ui vertical clearing segment">
        <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadContents('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
        <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadContents('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
    </div>
</script>
<!-- end template -->
@endsection

@push('scripts')
    @canany(['addUser', 'editUser'])
    <script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
    @endcanany
    <script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
    <script src="{{ asset('js/common/tip.js') }}"></script>
    <script>
        let contentsDict = {};
        function loadContents(url) {
            if(url == null) {
                url = "{{ url('api/contents') }}";
            }
            $.ajax({
                "url": url,
                "success": function(response, status) {
                    if(response.status === 200) {
                        if(response.success) {
                            for(let i = 0;i < response.data.length;++i) {
                                let id = response.data[i].id;
                                contentsDict[id] = response.data[i];
                            }
                            $("#container-contents").html(
                                template("tpl-container-contents", {
                                    "contents": response.data,
                                    "paginate": response.paginate
                                })
                            );
                        }else{
                            tip.error("{{ __('Fail to load contents .') }}");
                        }
                    }
                },
                "error": function(jqXHR, textStatus) {

                }
            });
        }
        @can('addContent')
        function addContent() {
            $("#form-method").val("POST");
            $("#content-id").val("");
            $("#content-title").val("");
            $("#content-type").dropdown("set selected", "home");
            $("#content-content").val("");
            $("#content-save").modal('show');
        }
        @endcan
        @can('editContent')
        function editContent(id) {
            let content = contentsDict[id] || {};
            $("#form-method").val("PUT");
            $("#content-id").val(id);
            $("#content-title").val(content.title);
            $("#content-type").dropdown("set selected", content.type);
            $("#content-content").val(content.content);
            $("#content-save").modal('show');
        }
        @endcan
        @can('deleteContent')
        function deleteContent(id) {
            $.post(
                "{{ url("api/content") }}",
                {
                    "_method": "DELETE",
                    "id": id
                },
                function(response, status) {
                    if(response.status === 200) {
                        if(response.success) {
                            tip.success("Success");
                        }else{
                            tip.error("Fail");
                        }
                    }
                }
            );
        }
        @endcan
        @canany(['addContent', 'editContent'])
        function requiredIfAdd() {
            return $("#content-id").val().length === 0
        }
        @endcanany
        $(document).ready(function() {
            loadContents();
            @can('addContent')
            $("#content-type").dropdown();
            @endcan
            @canany(['addContent', 'editContent'])
            $("#form-content").validate({
                "submitHandler": function(form) {
                    $(form).ajaxSubmit({
                        "success": function(response, status) {
                            if(response.status === 200) {
                                if(response.success) {
                                    tip.success("Success!");
                                    loadContents();
                                    $("#content-save").modal('hide');
                                }else{
                                    tip.error("Fail");
                                }
                            }
                        },
                        "error": function(jqXHR, textStatus) {

                        }
                    });
                    return false;
                },
                "rules": {
                    "title": {
                        "required": false,
                        "maxlength": 60
                    },
                    "content": {
                        "required": requiredIfAdd,
                        "maxlength": 2000
                    }
                }
            });
            @endcanany
        });
    </script>
@endpush