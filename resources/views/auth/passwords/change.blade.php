@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    <link href="{{ asset('css/wu-ui/wu-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/iconfont.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="ui centered grid container">
        <div class="ui dimmer" id="global-loader">
            <div class="ui big text loader"></div>
        </div>
        <div class="twelve wide column">
            <div class="ui basic vertical center aligned segment">
                <h1 class="ui header">{{ __('auth.changePassword.view.title') }}</h1>
            </div>
            <div class="ui basic vertical segment">
                <form class="ui form" id="form-reset" action="{{ url('api/password') }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="field">
                        <label>{{ __('auth.changePassword.view.label.password.old') }}</label>
                        <input id="old_password" name="old_password" type="password" value="" required maxlength="16">
                    </div>
                    <div class="field">
                        <label>{{ __('auth.changePassword.view.label.password.new') }}</label>
                        <input id="password" name="password" type="password" value="" required maxlength="16">
                    </div>
                    <div class="field">
                        <label>{{ __('auth.changePassword.view.label.password.confirm') }}</label>
                        <input id="password-confirmation" name="password_confirmation" type="password" value="" required maxlength="16">
                    </div>
                </form>
            </div>
            <div class="ui basic vertical segment">
                <div class="ui form" id="form-reset">
                    <div class="field">
                        <button type="submit" form="form-reset" class="ui fluid large primary submit button">{{ __('auth.changePassword.view.button.save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
    <script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
    <script src="{{ asset('js/common/tip.js') }}"></script>
    <script src="{{ asset('js/common/error.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#form-reset").validate({
                "submitHandler": function(form) {
                    $(form).ajaxSubmit({
                        "success": function(response, status) {
                            if(response && response.success) {
                                tip.success("{{ __('global.success') }}");
                                setTimeout(function() {
                                    location.href = "{{ route('login') }}";
                                }, 3000);
                            }else{
                                tip.error(response.message || "{{ __('global.fail') }}");
                            }
                        },
                        "error": handleError
                    });
                    return false;
                },
                "rules": {
                    "old_password": {
                        "required": true,
                        "rangelength": [6, 16],
                    },
                    "password": {
                        "required": true,
                        "rangelength": [6, 16]

                    },
                    "password_confirmation": {
                        "required": true,
                        "rangelength": [6, 16],
                        "equalTo": "#password"
                    }
                }
            });
        });
    </script>
@endpush