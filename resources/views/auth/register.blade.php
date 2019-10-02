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
            <div class="content">{{ __('auth.register.view.title') }}</div>
        </h2>
        <form class="ui large form" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="ui stacked segment">
                <div class="field">
                    <div class="ui left icon input{{ $errors->has('username') ? ' error' : '' }}">
                        <i class="user icon"></i>
                        <input type="text" name="username" placeholder="{{ __('auth.register.view.placeholder.username') }}" required autofocus>
                    </div>
                </div>
                <!--div class="field">
                    <div class="ui left icon input{{ $errors->has('email') ? ' error' : '' }}">
                        <i class="mail icon"></i>
                        <input type="email" name="email" placeholder="{{ __('auth.register.view.placeholder.email') }}" required>
                    </div>
                </div-->
                <div class="field">
                    <div class="ui left icon input{{ $errors->has('password') ? ' error' : '' }}">
                        <i class="lock icon"></i>
                        <input type="password" name="password" placeholder="{{ __('auth.register.view.placeholder.password') }}" required>
                    </div>
                </div>
                <div class="field">
                    <div class="ui left icon input{{ $errors->has('password_confirmation') ? ' error' : '' }}">
                        <i class="lock icon"></i>
                        <input type="password" name="password_confirmation" placeholder="{{ __('auth.register.view.placeholder.password.confirm') }}" required>
                    </div>
                </div>
                <button type="submit" class="ui fluid large primary submit button">{{ __('auth.register.view.button.register') }}</button>
            </div>
            <div class="ui error message{{ count($errors) > 0 ? ' visible':'' }}">
                @if ($errors->has('name'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('username') }}</strong>
                </span>
                @endif
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

        <div class="ui message">
            <ul>
                <li>Already has account? Please <a href="{{ route('login') }}">Sign In</a>.</li>
            </ul>
        </div>
    </div>
</div>
@endsection