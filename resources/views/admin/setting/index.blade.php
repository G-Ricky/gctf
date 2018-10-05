@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    @canany(['addSetting', 'editSetting'])
    <link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
    @endcanany
@endpush

@section('content')
    <div class="ui container" id="container-settings"></div>

    @canany(['addSetting', 'editSetting'])
    <!-- modal -->
    <div class="ui tiny basic flat modal" id="setting-save">
        <i class="close icon"></i>
        <div class="header">
            {{ __('Save Setting') }}
        </div>
        <div class="scrolling content">
            <div class="description">
                <form class="ui form" id="form-setting" name="setting" action="{{ url('api/setting') }}" method="post">
                    @csrf
                    <input id="setting-id" name="id" type="hidden" value="">
                    <input id="form-method" name="_method" type="hidden" value="">
                    <div class="field">
                        <label for="setting-name">{{ __('Name') }}</label>
                        <input id="setting-name" name="name" type="text" value="" required maxlength="128">
                    </div>

                    <div class="field">
                        <label for="setting-type">{{ __('type') }}</label>
                        <select id="setting-type" name="type">
                            <option value="string">String</option>
                            <option value="integer">Integer</option>
                            <option value="float">Float</option>
                            <option value="boolean">Boolean</option>
                            <option value="null">Null</option>
                            <option value="array">Array</option>
                            <option value="object">Object</option>
                            <option value="stdclass">StdClass</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="setting-value">{{ __('Value') }}</label>
                        <textarea id="setting-value" name="value" maxlength="2000" rows="3"></textarea>
                    </div>

                    <div class="field">
                        <label for="setting-description">{{ __('Description') }}</label>
                        <textarea id="setting-description" name="description" maxlength="250" rows="6"></textarea>
                    </div>
                </form>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" form="form-setting" type="submit" value="{{ __('Save') }}">
        </div>
    </div>
    <!-- end modal -->
    @endcanany

    <!-- template -->
    <script id="tpl-container-settings" type="text/html">
        <div class="ui basic vertical clearing segment">
            @can('addSetting')
                <button class="ui primary right floated button" onclick="addSetting()"><i class="add circle icon"></i> {{ __('Add') }}</button>
            @endcan
        </div>
        <div class="ui basic vertical segment" id="table-settings">
            <table class="ui single line compact table">
                <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Value') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Operation') }}</th>
                </tr>
                </thead>
                <tbody>
                @{{each settings setting index}}
                <tr>
                    <td>@{{setting.id}}</td>
                    <td>@{{setting.name}}</td>
                    <td>@{{setting.value}}</td>
                    <td>@{{setting.type}}</td>
                    <td>@{{setting.description}}</td>
                    <td>
                        @can('editSetting')
                        <button class="ui primary icon button" data-tooltip="{{ __('Edit setting') }}" onclick="editSetting('@{{setting.name}}')">
                            <i class="edit icon"></i>
                        </button>
                        @endcan
                        @can('deleteSetting')
                        <button class="ui negative icon button" data-tooltip="{{ __('Delete setting') }}" onclick="confirm('{{ __('Are you sure to delete it?')}}') &amp;&amp; deleteSetting('@{{setting.name}}')">
                            <i class="trash icon"></i>
                        </button>
                        @endcan
                    </td>
                </tr>
                @{{/each}}
                </tbody>
            </table>
        </div>
        @{{if paginate.last_page > 1}}
        <div class="ui vertical clearing segment">
            <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadSettings('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
            <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadSettings('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
        </div>
        @{{/if}}
    </script>
    <!-- end template-->
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
    <script>
        let settingsDict = {};
        function loadSettings(url) {
            if(url == null) {
                url = "{{ url("api/settings") }}";
            }
            $.ajax({
                "url": url,
                "type": "GET",
                "success": function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        if(response.success) {
                            let settings = response.data;
                            for(let i = 0;i < settings.length;++i) {
                                let name = settings[i].name;
                                settingsDict[name] = settings[i];
                            }
                            $("#container-settings").html(
                                template("tpl-container-settings", {
                                    "success": true,
                                    "settings": response.data,
                                    "paginate": response.paginate
                                })
                            );
                            $("#setting-type").dropdown();
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
        @can('addSetting')
        function addSetting() {
            $("#form-method").val("POST");
            $("#setting-id").val("");
            $("#setting-name").val("");
            $("#setting-value").val("");
            $("#setting-type").dropdown("set selected", "STRING");
            $("#setting-description").val("");
            $("#setting-save").modal('show');
        }
        @endcan
        @can('editSetting')
        function editSetting(name) {
            let setting = settingsDict[name];
            $("#form-method").val("PUT");
            $("#setting-id").val(setting.id);
            $("#setting-name").val(setting.name);
            $("#setting-value").val(setting.value);
            $("#setting-type").dropdown("set selected", setting.type.toUpperCase());
            $("#setting-description").val(setting.description);
            $("#setting-save").modal('show');
        }
        @endcan
        @can('deleteSetting')
        function deleteSetting(name) {
            $.ajax({
                "url": "{{ url('api/setting') }}",
                "type": "POST",
                "data": {
                    "name": name,
                    "_method": "DELETE"
                },
                "success": function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        loadSettings();
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
            loadSettings();
            @canany(['addSetting', 'editSetting'])
            $("#form-setting").validate({
                "submitHandler": function(form) {
                    $(form).ajaxSubmit({
                        "success": function(response, status) {
                            if(status === "success" && response && response.status === 200) {
                                $("#setting-save").modal('hide');
                                loadSettings();
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
                        "maxlength": 128
                    },
                    "type": {
                        "required": true
                    },
                    "value": {
                        "required": false,
                        "maxlength": 2000
                    },
                    "description": {
                        "required": false,
                        "maxlength": 250
                    }
                }
            });
            @endcanany
        });
    </script>
@endpush