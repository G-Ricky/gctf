@extends('layouts.app')

@push('stylesheets')
    <style>
        html, body, #app {
            height: 100%;
        }
        #app>.grid {
            height: 100%;
        }
        .column {
            max-width: 400px;
        }

        .ui.message ul {
            padding-left: 20px;
        }

        .field,
        .ui.message ul,
        .ui.error.message {
            text-align: left;
        }
        .ui.stacked.segment:after {
            content: none;
        }

        .ui.error.message.visible {
            display: block;
        }
    </style>
@endpush

@section('content')
<div class="ui middle aligned center aligned grid">
    <div class="column">
        <h2 class="ui image header">
            <img src="{{ asset('img') }}/logo.png" class="image">
            <div class="content">{{ __('auth.resetPassword.view.reset') }}</div>
        </h2>
        <form class="ui large form" id="form-reset" method="POST" action="{{ route('password.request') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="ui stacked segment">

                <div class="field">
                    <div class="ui left icon input{{ $errors->has('password') ? ' error' : '' }}">
                        <i class="lock icon"></i>
                        <input id="password" type="password" name="password" value="" placeholder="{{ __('auth.resetPassword.view.placeholder.password.new') }}" required>
                    </div>
                </div>

                <div class="field">
                    <div class="ui left icon input{{ $errors->has('password_confirmation') ? ' error' : '' }}">
                        <i class="lock icon"></i>
                        <input id="password-confirm" type="password" name="password_confirmation" value="" placeholder="{{ __('auth.resetPassword.view.placeholder.password.confirm') }}" required>
                    </div>
                </div>

                <div class="field">
                    <button type="submit" class="ui fluid large primary submit button">{{ __('auth.resetPassword.view.button.reset') }}</button>
                </div>

                <div class="field">
                    <button type="button" class="ui fluid large button" onclick="location.href='{{ url('login') }}'">{{ __('auth.resetPassword.view.button.cancel') }}</button>
                </div>
            </div>
            <div class="ui error message{{ count($errors) > 0 ? ' visible':'' }}">
                @if ($errors->has('password'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif
                @if ($errors->has('password_confirmation'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
