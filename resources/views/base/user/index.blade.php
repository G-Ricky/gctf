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
                <i class="edit icon"></i>&nbsp;<span>编辑</span>
            </a>
            <a class="icon item" id="btn-cancel" href="javascript:cancel();" style="display: none;">
                <i class="edit icon"></i>&nbsp;<span>取消</span>
            </a>
            <a class="icon item disabled" id="btn-save" href="javascript:save();" style="display: none;">
                <i class="edit icon"></i>&nbsp;<span>保存</span>
            </a>
        </div>
    </div>
    <div class="ui form" id="form">
        <div class="field">
            <label>学号</label>
            <input id="sid" type="text" value="" readonly>
        </div>
        <div class="field">
            <label>姓名</label>
            <input id="name" type="text" value="" readonly>
        </div>
        <div class="field">
            <label>昵称</label>
            <input id="nickname" type="text" value="" readonly>
        </div>
        <div class="field">
            <label>性别</label>
            <select class="ui dropdown" id="gender" disabled>
                <option value="UNKNOWN" selected>未知</option>
                <option value="MALE">男</option>
                <option value="FEMALE">女</option>
            </select>
        </div>
        <div class="field">
            <label>邮箱</label>
            <input id="email" type="text" value="" readonly>
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
        "name"    : "",
        "nickname": "",
        "gender"  : "",
        "email"   : ""
    };
    function fill(info) {
        $("#sid").val(info.sid);
        $("#name").val(info.name);
        $("#nickname").val(info.nickname);
        $("#gender").dropdown("clear").dropdown("set selected", info.gender);
        $("#email").val(info.email);
    }
    function getInfo() {
        return {
            "sid"     : $("#sid").val(),
            "name"    : $("#name").val(),
            "nickname": $("#nickname").val(),
            "gender"  : $("#gender").dropdown("get value"),
            "email"   : $("#email").val()
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
                        window.info.name = response.data.name || "";
                        window.info.nickname = response.data.nickname || "";
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("#form input").keyup(changeSaveButtonStatus);
        $("#form select").change(changeSaveButtonStatus);
    });
</script>
@endpush