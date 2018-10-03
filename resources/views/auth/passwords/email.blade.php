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
        .ui.error.message,
        .ui.info.message {
            text-align: left;
        }
        .ui.stacked.segment:after {
            content: none;
        }

        .ui.error.message.visible,
        .ui.info.message.visible {
            display: block;
        }
    </style>
@endpush

@section('content')
<div class="ui middle aligned center aligned grid">
    <div class="column">
        <h2 class="ui image header">
            <img src="{{ asset('img') }}/logo.png" class="image">
            <div class="content">{{ __('Send Password Reset Email') }}</div>
        </h2>
        <form class="ui large form" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="ui stacked segment">
                <div class="field">
                    <div class="ui left icon input{{ $errors->has('username') ? ' error' : '' }}">
                        <i class="mail icon"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" required autofocus>
                    </div>
                </div>
                <button type="submit" class="ui fluid large primary submit button">
                    {{ __('Send') }}
                </button>
            </div>
            <div class="ui message">
                <ul>
                    <li>Go to <a href="{{ route('login') }}">login</a> or <a href="{{ url('') }}">home</a></li>
                </ul>
            </div>
            <div class="ui error message {{ count($errors) > 0 ? ' visible':'' }}">
                @if ($errors->has('email'))
                <span class="invalid-feedback">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
            </div>
            @if (session('status'))
            <div class="ui info message visible">
                <span class="feedback">
                    <strong>{{ session('status') }}</strong>
                </span>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection
