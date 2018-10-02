@include('component.navigation')
@include('component.footer')
@extends('layouts.app')

@push('stylesheets')
    <style>
        p.ui.right.aligned {
            text-align: right;
        }
    </style>
@endpush
@section('content')
    <div class="ui basic vertical masthead center aligned segment">
        <img class="ui medium circular centered image" src="{{ asset('img/logo.png') }}">
    </div>
    <div class="ui stackable grid container">
        <div class="fourteen wide left aligned centered column">
            <div class="ui styled fluid accordion" id="menu-notices">
                @foreach($contents as $i => $content)
                <div class="title @if($i === 0) active @endif">
                    <i class="dropdown icon"></i>
                    {{ $content['title'] }}
                </div>
                <div class="content @if($i === 0)  active @endif">
                    @foreach($content['segments'] as $segment)
                    <p>{{ $segment }}</p>
                    @endforeach
                    <p class="ui right aligned">
                        Last modified by
                        <a href="#">
                            {{ $content['modifier']['nickname'] ?? $content['modifier']['username'] }}
                        </a>
                        at {{ $content['updated_at'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
