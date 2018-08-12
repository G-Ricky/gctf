@extends('layouts.app')

@section('content')
<div class="container">
    <form name="add-challenge" action="{{ url()->current() }}" method="post">
        @csrf
        <div class="form-group">
            <label for="ch-title">Title</label>
            <input name="title" type="text" class="form-control" id="ch-title" maxlength="32" value="GCTF">
        </div>
        @if ($errors->has('title'))
            <span class="invalid-feedback">
            <strong>{{ $errors->first('title') }}</strong>
        </span>
        @endif

        <div class="form-group">
            <label for="ch-description">Description</label>
            <textarea name="description" type="text" class="form-control" id="ch-description" rows="5" maxlength="1024"></textarea>
        </div>
        @if ($errors->has('description'))
            <span class="invalid-feedback">
            <strong>{{ $errors->first('description') }}</strong>
        </span>
        @endif

        <div class="form-group">
            <label for="ch-points">Points</label>
            <input name="points" type="text" class="form-control" id="ch-points" value="500">
        </div>
        @if ($errors->has('points'))
            <span class="invalid-feedback">
            <strong>{{ $errors->first('points') }}</strong>
        </span>
        @endif

        <div class="form-group">
            <label for="ch-flag">Flag</label>
            <input name="flag" type="text" class="form-control" id="ch-flag" value="flag{default}">
        </div>
        @if ($errors->has('flag'))
            <span class="invalid-feedback">
            <strong>{{ $errors->first('flag') }}</strong>
        </span>
        @endif

        <div class="form-group">
            <label for="ch-category">Category</label>
            <input name="category" type="text" class="form-control" id="ch-category" value="CRYPTO">
        </div>
        @if ($errors->has('category'))
            <span class="invalid-feedback">
            <strong>{{ $errors->first('category') }}</strong>
        </span>
        @endif

        <div class="form-group">
            <label for="ch-tags">Tags</label>
            <input name="tags" type="text" class="form-control" id="ch-tags" value="CRYPTO">
        </div>
        @if ($errors->has('tags'))
            <span class="invalid-feedback">
            <strong>{{ $errors->first('tags') }}</strong>
        </span>
        @endif

        <div class="form-group">
            <label for="ch-bank">Bank</label>
            <input name="bank" type="text" class="form-control" id="ch-bank" value="1">
        </div>
        @if ($errors->has('bank'))
            <span class="invalid-feedback">
            <strong>{{ $errors->first('bank') }}</strong>
        </span>
        @endif

        <div class="form-group">
            <input id="submit" type="button" class="btn btn-default btn-block" value="提交">
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script src="//cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#submit").click(function() {
                var url = $("[name=add-challenge]")[0].action;
                var data = $("[name=add-challenge]").serialize();
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
