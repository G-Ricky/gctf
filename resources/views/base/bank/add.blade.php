@extends('layouts.app')

@section('content')
<div class="ui container">
    <form name="add-bank" action="{{ url()->current() }}" method="post">
        @csrf
        <div class="form-group">
            <label for="bk-name">Name</label>
            <input name="name" type="text" class="form-control" id="bk-name" maxlength="256" value="GCTF">
        </div>
        @if ($errors->has('name'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif


        <div class="form-group">
            <label for="bk-description">Description</label>
            <textarea name="description" class="form-control" rows="5" id="bk-description" maxlength="256"></textarea>
        </div>
        @if ($errors->has('description'))
            <span class="invalid-feedback">
            <strong>{{ $errors->first('description') }}</strong>
        </span>
        @endif

        <div class="form-group">
            <label for="bk-ishidden">Hidden</label>
            <input name="is_hidden" type="checkbox" id="bk-ishidden">
        </div>

        <div class="form-group">
            <input id="submit" type="button" class="btn btn-default btn-block" value="提交">
        </div>
    </form>
</div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $("#submit").click(function() {
                var url = $("[name=add-bank]")[0].action;
                var data = $("[name=add-bank]").serialize();
                $.post(
                    url,
                    data,
                    function (data, status) {
                        if(status == "success") {
                            if(data.success) {
                                alert('success');
                                location.reload(true);
                            }
                        }
                    },
                    "json"
                );
            });
        })

    </script>
@endpush