@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    @canany(['addSetting', 'editSetting'])
    <link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
    <link href="{{ asset('css/semantic-ui-calendar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/wu-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/iconfont.css') }}" rel="stylesheet">
    @endcanany
@endpush

@section('content')
    <div class="ui container">
        <div class="ui basic vertical clearing segment">
            @can('addSetting')
                <button class="ui primary right floated button" onclick="addSetting()"><i class="add circle icon"></i> {{ __('setting.view.button.add') }}</button>
            @endcan
        </div>
    </div>
    <div class="ui container" id="container-settings"></div>

    @canany(['addSetting', 'editSetting'])
    <!-- modal -->
    <div class="ui tiny basic flat modal" id="setting-save">
        <i class="close icon"></i>
        <div class="header" id="modal-setting-title">
            {{ __('setting.view.admin.modal.title.add') }}
        </div>
        <div class="scrolling content">
            <div class="description">
                <form class="ui form" id="form-setting" name="setting" action="{{ url('api/setting') }}" method="post" autocomplete="off">
                    @csrf
                    <input id="setting-id" name="id" type="hidden" value="">
                    <input id="form-method" name="_method" type="hidden" value="">
                    <div class="field">
                        <label for="setting-name">{{ __('setting.view.admin.modal.label.name') }}</label>
                        <input id="setting-name" name="name" type="text" value="" required maxlength="128">
                    </div>

                    <div class="field">
                        <label for="setting-type">{{ __('setting.view.admin.modal.label.type') }}</label>
                        <select id="setting-type" name="type">
                            <option value="string">String</option>
                            <option value="integer">Integer</option>
                            <option value="float">Float</option>
                            <option value="date">Date</option>
                            <option value="boolean">Boolean</option>
                            <option value="null">Null</option>
                            <option value="array">Array</option>
                            <option value="object">Object</option>
                            <option value="stdclass">StdClass</option>
                        </select>
                    </div>

                    <textarea id="setting-value" name="value" style="display:none"></textarea>

                    <div class="field" id="field-setting-value-text" data-group="field-value" style="display: none">
                        <label for="setting-value-text">{{ __('setting.view.admin.modal.label.value') }}</label>
                        <textarea id="setting-value-text" maxlength="2000" rows="3"></textarea>
                    </div>

                    <div class="field" id="field-setting-value-integer" data-group="field-value" style="display: none">
                        <label for="setting-value-integer">{{ __('setting.view.admin.modal.label.value') }}</label>
                        <input id="setting-value-integer" type="text" value="0">
                    </div>

                    <div class="field" id="field-setting-value-float" data-group="field-value" style="display: none">
                        <label for="setting-value-float">{{ __('setting.view.admin.modal.label.value') }}</label>
                        <input id="setting-value-float" type="text" value="0">
                    </div>

                    <div class="field" id="field-setting-value-date" data-group="field-value" style="display: none">
                        <label for="setting-value-date">{{ __('setting.view.admin.modal.label.value') }}</label>
                        <div class="ui input left icon">
                            <i class="calendar icon"></i>
                            <input id="setting-value-date" type="text" placeholder="Date" value="2017-06-01">
                        </div>
                    </div>

                    <div class="field" id="field-setting-value-boolean" data-group="field-value" style="display: none">
                        <label for="setting-value-boolean">{{ __('setting.view.admin.modal.label.value') }}</label>
                        <div class="ui toggle checkbox">
                            <input id="setting-value-boolean" type="checkbox">
                        </div>
                    </div>

                    <div class="field">
                        <label for="setting-description">{{ __('setting.view.admin.modal.label.description') }}</label>
                        <textarea id="setting-description" name="description" maxlength="250" rows="6"></textarea>
                    </div>
                </form>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" form="form-setting" type="submit" value="{{ __('setting.view.admin.modal.button.save') }}">
        </div>
    </div>
    <!-- end modal -->
    @endcanany

    <!-- template -->
    <script id="tpl-container-settings" type="text/html">
        <div class="ui basic vertical segment" id="table-settings">
            <table class="ui single line compact table">
                <thead>
                <tr>
                    <th>{{ __('setting.view.admin.table.row.id') }}</th>
                    <th>{{ __('setting.view.admin.table.row.name') }}</th>
                    <th>{{ __('setting.view.admin.table.row.value') }}</th>
                    <th>{{ __('setting.view.admin.table.row.type') }}</th>
                    <th>{{ __('setting.view.admin.table.row.description') }}</th>
                    <th>{{ __('setting.view.admin.table.row.operation') }}</th>
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
                        <button class="ui primary icon button" data-tooltip="{{ __('setting.view.admin.table.row.tooltip.edit') }}" onclick="editSetting('@{{setting.id}}')">
                            <i class="edit icon"></i>
                        </button>
                        @endcan
                        @can('deleteSetting')
                        <button class="ui negative icon button" data-tooltip="{{ __('setting.view.admin.table.row.tooltip.delete') }}" onclick="confirm('{{ __('setting.view.admin.table.row.delete.confirm')}}') &amp;&amp; deleteSetting('@{{setting.id}}')">
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
    <script src="{{ asset('js/semantic.min.js') }}"></script>
    <script src="{{ asset('js/semantic-ui-calendar.min.js') }}"></script>
    <script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
    <script src="{{ asset('js/common/tip.js') }}"></script>
    <script src="{{ asset('js/common/error.js') }}"></script>
    <script>
        let settingsDict = {};
        let placeHolders = {
            "string"  : "{{ __('setting.view.admin.modal.placeholder.string') }}",
            "integer" : "{{ __('setting.view.admin.modal.placeholder.integer') }}",
            "float"   : "{{ __('setting.view.admin.modal.placeholder.float') }}",
            "date"    : "{{ __('setting.view.admin.modal.placeholder.date') }}",
            "boolean" : null,
            "null"    : null,
            "array"   : "{{ __('setting.view.admin.modal.placeholder.array') }}",
            "object"  : "{{ __('setting.view.admin.modal.placeholder.object') }}",
            "stdclass": "{{ __('setting.view.admin.modal.placeholder.stdclass') }}"
        };
        function loadSettings(url) {
            if(url == null) {
                url = "{{ url("api/settings") }}";
            }
            $.ajax({
                "url": url,
                "type": "GET",
                "success": function(response, status) {
                    if(response && response.success) {
                        let settings = response.data;
                        for(let i = 0;i < settings.length;++i) {
                            let id = settings[i].id;
                            settingsDict[id] = settings[i];
                        }
                        $("#container-settings").html(
                            template("tpl-container-settings", {
                                "success": true,
                                "settings": response.data,
                                "paginate": response.paginate
                            })
                        );
                        $("#setting-type").dropdown();
                    }
                },
                "error": handleError,
                "complete": function() {

                }
            });
        }
        @can('addSetting')
        function addSetting() {
            $("#form-method").val("POST");
            $("#modal-setting-title").text("{{ __('setting.view.admin.modal.title.add') }}");
            $("#setting-id").val("");
            $("#setting-name").val("");
            $("#setting-value").val("");
            $("#field-setting-value-date").calendar('set date', new Date());
            $("#setting-value-boolean").checkbox('uncheck');
            $("#setting-value-integer").val(0);
            $("#setting-value-float").val(0);
            $("#setting-value-text").val("");
            $("#setting-type").dropdown("set selected", "string");
            $("#setting-description").val("");
            $("#setting-save").modal('show');
            updateTextBox();
        }
        @endcan
        @can('editSetting')
        function editSetting(id) {
            let setting = settingsDict[id];
            $("#modal-setting-title").text("{{ __('setting.view.admin.modal.title.edit') }}");
            $("#form-method").val("PUT");
            $("#setting-id").val(setting.id);
            $("#setting-name").val(setting.name);
            $("#setting-value").val(setting.value);
            $("#setting-type").dropdown("set selected", setting.type);
            setSettingValue(setting.value, setting.type);
            $("#setting-description").val(setting.description);
            $("#setting-save").modal('show');
        }
        @endcan
        @canany(['addSetting', 'editSetting'])
        function getSettingValueInputBox(type) {
            switch(type) {
                case "date":
                    return $("#field-setting-value-date");
                case "integer":
                    return $("#field-setting-value-integer");
                case "float":
                    return $("#field-setting-value-float");
                case "boolean":
                    return $("#field-setting-value-boolean");
                case "null":
                    return $();
                default:
                    return $("#field-setting-value-text");
            }
        }
        function setSettingValue(value, type) {
            switch(type) {
                case "date":
                    $("#field-setting-value-date").calendar('set date', parseDateString(value));
                    break;
                case "boolean":
                    if(value) {
                        $("#setting-value-boolean").checkbox('check');
                    }else{
                        $("#setting-value-boolean").checkbox('uncheck');
                    }
                    break;
                case "integer":
                case "float":
                    $("#setting-value-integer").val(value);
                    break;
                default:
                    $("#setting-value-text").val(value);
            }
            let $inputBox = getSettingValueInputBox(type).children("input, textarea");
            $inputBox.val(value);
            $("#setting-value").val(value);
        }
        function getSettingValue(type) {
            switch (type) {
                case "date":
                    return $("#setting-value-date").val();
                case "boolean":
                    return $("#setting-value-boolean").prop("checked");
                case "integer":
                    return $("#setting-value-integer").val();
                case "float":
                    return $("#setting-value-float").val();
                default:
                    return $("#setting-value-text").val();
            }
        }
        function updateTextBox() {
            $("[data-group=field-value]").hide();
            let type = $("#setting-type").dropdown("get value").toLowerCase();
            let $inputBoxField = getSettingValueInputBox(type);
            $inputBoxField.show();
            if (placeHolders[type] != null) {
                $inputBoxField.children("[id^=setting-value-]").attr("placeholder", placeHolders[type]);
            }
        }
        @endcanany
        @can('deleteSetting')
        function deleteSetting(id) {
            if(id == null) {
                tip.error("{{ __('setting.view.admin.message.settingNotExist') }}");
            }
            $.ajax({
                "url": "{{ url('api/setting') }}",
                "type": "POST",
                "data": {
                    "id": id,
                    "_method": "DELETE"
                },
                "success": function(response, status) {
                    if(response && response.success) {
                        tip.success("{{ __('global.success') }}");
                        loadSettings();
                    } else {
                        tip.error(response.message || "未知错误！");
                    }
                },
                "error": handleError,
                "complete": function() {

                }
            });
        }
        @endcan
        function fillZero(num, count) {
            let ret = num.toString();
            for(let i = ret.length;i < count;++i) {
                ret = "0" + ret;
            }
            return ret;
        }
        function parseDateString(text, settings) {
            let date = new Date();
            let matches;
            if((matches = text.match(/^\s*(\d\d\d\d)-(\d\d)-(\d\d)(\s+(\d\d):(\d\d):(\d\d))?\s*$/)) == null) {
                return date;
            }

            date.setFullYear(matches[1]);
            date.setMonth(matches[2]);
            date.setDate(matches[3]);
            date.setHours(matches[5] || 0);
            date.setMinutes(matches[6] || 0);
            date.setSeconds(matches[7] || 0);
            date.setMilliseconds(0);
            return date;
        }
        $(document).ready(function() {
            @canany(['addSetting', 'editSetting'])
            $(".ui.modal").modal();
            // modal 移动后需要重新初始化组件
            $("#field-setting-value-date").calendar({
                "formatter": {
                    "date": function (date, setting) {
                        if(!date) {
                            return "1970-01-01";
                        }
                        let year = fillZero(date.getFullYear(), 4);
                        let month = fillZero(date.getMonth() + 1, 2);
                        let day = fillZero(date.getDate(), 2);

                        return year + "-" + month + "-" + day   ;
                    },

                    "time": function(date, setting, forCalendar){
                        if(!date) {
                            return "00:00:00";
                        }
                        let hour = fillZero(date.getHours(), 2);
                        let minute = fillZero(date.getMinutes(), 2);
                        let second = fillZero(date.getSeconds(), 2);
                        return hour + ":" + minute + ":" + second
                    },
                    
                    "parser": parseDateString
                }
            });
            $(".ui.checkbox").checkbox();
            $("#setting-type").dropdown({
                onChange: function (value, text, $choice) {
                    updateTextBox();
                }
            });
            @endcanany
            loadSettings();
            @canany(['addSetting', 'editSetting'])
            $("#form-setting").validate({
                "submitHandler": function(form) {
                    let type = $("#setting-type").dropdown("get value");
                    let value = getSettingValue(type);
                    $("#setting-value").val(value);
                    $(form).ajaxSubmit({
                        "success": function(response, status) {
                            if(response && response.success) {
                                $("#setting-save").modal('hide');
                                loadSettings();
                            } else {
                                tip.error(response.message || "未知错误！");
                            }
                        },
                        "error": handleError,
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