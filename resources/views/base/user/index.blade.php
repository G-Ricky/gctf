@include('component.navigation')
@include('component.footer')
@extends('layouts.app')

@push('stylesheets')
@endpush

@section('content')
<div class="ui container">
    <div class="ui dimmer" id="global-loader">
        <div class="ui big text loader"></div>
    </div>
    <div class="ui basic vertical clearing segment">
        <div class="ui right floated main menu">
            <a class="icon item" id="btn-edit" href="javascript:edit();">
                <i class="edit icon"></i>&nbsp;<span>{{ __('Edit') }}</span>
            </a>
            <a class="icon item" id="btn-cancel" href="javascript:cancel();" style="display: none;">
                <i class="edit icon"></i>&nbsp;<span>{{ __('Cancel') }}</span>
            </a>
            <a class="icon item disabled" id="btn-save" href="javascript:save();" style="display: none;">
                <i class="edit icon"></i>&nbsp;<span>{{ __('Save') }}</span>
            </a>
        </div>
    </div>
    <div class="ui basic vertical segment">
        <div class="ui form" id="form">
            <div class="field">
                <label>{{ __('Username') }}</label>
                <input id="username" type="text" value="" readonly disabled>
            </div>
            <div class="field">
                <label>{{ __('Nickname') }}</label>
                <input id="nickname" type="text" value="" readonly>
            </div>
            <div class="field">
                <label>{{ __('Student Number') }}</label>
                <input id="sid" type="text" value="" readonly>
            </div>
            <div class="field">
                <label>{{ __('Name') }}</label>
                <input id="name" type="text" value="" readonly>
            </div>
            <div class="field">
                <label>{{ __('Gender') }}</label>
                <select class="ui dropdown" id="gender" disabled>
                    <option value="UNKNOWN" selected>{{ __('Unknown') }}</option>
                    <option value="MALE">{{ __('Male') }}</option>
                    <option value="FEMALE">{{ __('Female') }}</option>
                </select>
            </div>
            <div class="field">
                <label>{{ __('Email') }}</label>
                <input id="email" type="text" value="" readonly>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/common/error.js') }}"></script>
<script src="{{ asset('js/common/misc.js') }}"></script>
<script>
    window.info = {
        "sid"     : "",
        "nickname": "",
        "name"    : "",
        "gender"  : "",
        "email"   : ""
    };
    function fill(info) {
        $("#sid").val(info.sid);
        $("#username").val(info.username);
        $("#nickname").val(info.nickname);
        $("#name").val(info.name);
        $("#gender").dropdown("clear").dropdown("set selected", info.gender);
        $("#email").val(info.email);
    }
    function getInfo() {
        return {
            "sid"     : $("#sid").val().trim() || undefined,
            "name"    : $("#name").val().trim() || undefined,
            "nickname": $("#nickname").val().trim() || undefined,
            "gender"  : $("#gender").dropdown("get value"),
            "email"   : $("#email").val().trim() || undefined
        };
    }
    function isSameInfo(info1, info2) {
        return info1.sid === info2.sid &&
            info1.name === info2.name &&
            info1.nickname === info2.nickname &&
            info1.gender === info2.gender &&
            info1.email === info2.email;
    }
    function infoHasChanged() {
        let newInfo = getInfo();
        return !isSameInfo(window.info, newInfo);
    }
    function changeSaveButtonStatus() {
        if(infoHasChanged()) {
            $("#btn-save").removeClass("disabled").attr("href", "javascript:save();");
        }else{
            $("#btn-save").attr("href", "javascript:void(0);").addClass("disabled");
        }
    }
    function edit() {
        $("#btn-edit").hide();
        $("#btn-cancel").show();
        $("#btn-save").show();
        $("#form input").removeAttr("readonly");
        $("#form select").removeAttr("disabled").parent().removeClass("disabled");
    }
    function cancel() {
        $("#btn-cancel").hide();
        $("#btn-edit").show();
        $("#btn-save").hide();
        $("#form input").attr("readonly", "readonly");
        $("#form select").attr("disabled", "disabled").parent().addClass("disabled");
        if(infoHasChanged() && confirm("是否撤销修改？")) {
            fill(window.info);
        }
    }
    function save() {
        let userInfo = getInfo();
        $.ajax({
            "url": "{{ url('user/edit') }}",
            "data": userInfo,
            "dataType": "json",
            "method": "POST",
            "success": function(response) {
                if(response.success) {
                    alert("修改成功");
                    location.reload();
                }else{
                    console.log(response.message);
                }
            },
            "error": function(XMLHttpRequest, textStatus, errorThrown) {
                errorHandler(XMLHttpRequest.responseJSON.errors);
            },
            "complete": function() {
                closeLoader();
            }
        });
        openLoader("正在保存...")
    };
    $(document).ready(function() {
        (function() {
            $('#gender').dropdown();
            $.ajax({
                "url": "{{ url('user/info') }}",
                "dataType": "json",
                "success": function(response) {
                    if(response.success) {
                        window.info.sid = response.data.sid || "";
                        window.info.username = response.data.username || "";
                        window.info.nickname = response.data.nickname || "";
                        window.info.name = response.data.name || "";
                        window.info.gender = response.data.gender || "UNKNOWN";
                        window.info.email = response.data.email || "";
                        fill(window.info);
                    }else{
                        console.log(response.message);
                    }
                },
                "error": function() {
                    console.log("请求失败");
                },
                "complete": function() {
                    closeLoader();
                }
            });
            openLoader("正在载入...");
        })();
        $("#form input").keyup(changeSaveButtonStatus);
        $("#form select").change(changeSaveButtonStatus);
    });
</script>
@endpush