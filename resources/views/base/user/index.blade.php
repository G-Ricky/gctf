@include('component.navigation')
@include('component.footer')
@extends('layouts.app')

@push('stylesheets')
@endpush

@section('content')
<div class="ui container">
    <div class="ui form">
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
            <select id="gender">
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
<script>
    $(document).ready(function() {
        $.get(
            "{{ url('user/info') }}",
            function(data, status) {
                if(status === "success") {
                    if(data.success) {
                        $("#sid").val(data.data.sid);
                        $("#name").val(data.data.name);
                        $("#nickname").val(data.data.nickname);
                        $("#gender").find("option[text='" + data.data.sex + "']").attr("selected",true);
                        $("#email").val(data.data.email);
                    }
                }
            },
            "json"
        );
    });
</script>
@endpush