@include('component.navigation')
@include('component.footer')
@extends('layouts.app')

@push('stylesheets')
<link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
@endpush
@section('content')
<div class="ui container">
    <!-- Add Button -->
    <div class="ui basic vertical clearing segment">
        <button id="bank-add" class="ui primary right floated button"><i class="add circle icon"></i> Add</button>
    </div>
    <!-- Bank List -->
    <div id="bank-list"></div>
</div>
<!-- Modal -->
<div class="ui tiny basic flat modal" id="bank-modify">
    <div class="ui dimmer" id="bank-dimmer">
        <div class="ui big text loader"></div>
    </div>
    <i class="close icon"></i>
    <div class="header">
        {{ __('Add Bank') }}
    </div>
    <div class="scrolling content">
        <div class="description">
            <form class="ui form" id="form" name="bank-add" action="{{ url('bank/add') }}" method="post">
                @csrf
                <div class="field">
                    <label for="name">{{ __('Title') }}</label>
                    <input name="name" type="text" maxlength="61" value="">
                </div>

                <div class="field">
                    <label for="description">{{ __('Description') }}</label>
                    <textarea name="description" type="text" rows="6" maxlength="1001"></textarea>
                </div>

                <div class="field">
                    <div class="ui checkbox">
                        <input id="is_hidden" name="is_hidden" type="checkbox">
                        <label for="is_hidden">{{ __('Hide') }}</label>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="actions">
        <input class="ui basic fluid button" id="btn-submit" type="button" value="{{ __('Add') }}">
    </div>
</div>
<script id="bank-template" type="text/html">
    @{{each banks bank index}}
    <div class="ui icon message">
        <i class="inbox icon"></i>
        <div class="content">
            <div class="header"><a href="{{ url('challenge') }}?bank=@{{bank.id}}">@{{bank.name}}</a></div>
            <p>@{{bank.description}}</p>
        </div>
    </div>
    @{{/each}}
</script>
@endsection
@push('scripts')
<script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
<script src="{{ asset('js/common/misc.js') }}"></script>
<script>
    function disableBankForm() {
        $("#form input[type=text]").attr("readonly", "");
        $("#bank-dimmer").addClass("active").children(".loader").html("正在添加");
    }
    function enableBankForm() {
        $("#bank-dimmer").removeClass("active");
        $("#form input[type=text]").removeAttr("readonly");
    }
    $(document).ready(function() {
        let validator = $("#form").validate({
            "submitHandler": function(form) {
                disableBankForm();
                $(form).ajaxSubmit({
                    "success": function(response) {
                        if(response.success) {
                            validator.resetForm();
                            enableBankForm();
                        }
                    },
                    "error": function() {

                    }
                });
            },
            "rules": {
                "name": {"required": true, "maxlength": 60},
                "description": {"required": true, "maxlength": 1000}
            }
        });
        $.ajax({
            "url": "{{ url('bank/list') }}",
            "method": "GET",
            "success": function(request) {
                if(request.success) {
                    let html = template("bank-template", {
                        "banks": request.data
                    });
                    $("#bank-list").html(html);
                }
            },
            "error": function() {

            }
        });
        $("#bank-add").click(function() {
            $("#bank-modify").modal('show');
        });
        $("#btn-submit").click(function() {
            $("#form").submit();
        });
    });
</script>
@endpush
