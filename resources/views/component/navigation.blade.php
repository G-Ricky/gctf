@push('stylesheets')
<style>
    @media only screen and (max-width: 992px) {
        .ui[class*="mobile only"].menu > .container {
            margin-left: 0em !important;
            margin-right: 0em !important;
            width: 100% !important;
            max-width: none !important;
        }
    }
    @media only screen and (min-width: 650px) {
        #app {
            padding-top: 54px;
        }
        .ui[class*="mobile only"].menu {
            display: none;
        }
        .ui[class*="mobile banner"].menu {
            display: block;
        }
    }
    @media only screen and (max-width: 650px) {
        #app {
            padding-top: 57px;
        }
        .ui[class*="mobile banner"].menu {
            display: none;
        }
        .ui[class*="mobile only"].menu {
            display: block;
        }
    }
    body {
        display: flex;
        flex-direction: column;
    }
    #app {
        flex: 1;
    }
</style>
@endpush

@section('navigation')
<div class="ui mobile only vertical fluid fixed massive inverted accordion menu">
    <div class="item">
        <a class="title"><i class="content icon"></i>&nbsp;</a>
        <div class="content">
            <div class="ui vertical fluid massive inverted menu">
                @can('listChallenges')
                <a class="horizontally fitted item" href="{{ url('bank') }}">{{ __('navigation.view.challenges') }}</a>
                @endcan
                @can('viewRanking')
                <a class="horizontally fitted item" href="{{ url('ranking') }}">{{ __('navigation.view.ranking') }}</a>
                @endcan
                @can('listBanks')
                <a class="horizontally fitted item" href="{{ url('banks') }}">{{ __('navigation.view.banks') }}</a>
                @endcan
                @can('listSubmissions')
                <a class="horizontally fitted item" href="{{ url('submissions') }}">{{ __('navigation.view.submissions') }}</a>
                @endcan
                <a class="horizontally fitted item" href="{{ url('user') }}">{{ __('navigation.view.profile') }}</a>
                <a class="horizontally fitted item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ __('navigation.view.logout') }}</a>
            </div>
        </div>
    </div>
</div>
<div class="ui mobile banner inverted borderless fixed menu">
    <div class="ui container">
        <a href="{{ url('/') }}" class="header item">
            <img class="logo" src="{{ asset('img/logo.png') }}">
            {{ config('app.name', 'GCTF') }}
        </a>
        @can('listChallenges')
        <a href="{{ url('bank') }}" class="item">{{ __('navigation.view.challenges') }}</a>
        @endcan
        @can('viewRanking')
        <a href="{{ url('ranking') }}" class="item">{{ __('navigation.view.ranking') }}</a>
        @endcan
        @can('listBanks')
        <a href="{{ url('banks') }}" class="item">{{ __('navigation.view.banks') }}</a>
        @endcan
        @can('listSubmissions')
        <div class="ui simple dropdown item">
            {{ __('navigation.view.submissions') }} <i class="dropdown icon"></i>
            <div class="menu">
                <a href="{{ url('submissions/correct') }}" class="item">{{ __('navigation.view.submissions.correct') }}</a>
                <a href="{{ url('submissions/incorrect') }}" class="item">{{ __('navigation.view.submissions.incorrect') }}</a>
                <a href="{{ url('submissions') }}" class="item">{{ __('navigation.view.submissions.all') }}</a>
            </div>
        </div>
        @endcan
        @canany(['listContents', 'listUsers', 'listRoles', 'listPrivileges'])
        <div class="ui simple dropdown item">
            {{ __('navigation.view.admin') }} <i class="dropdown icon"></i>
            <div class="menu">
                @can('listSettings')
                <a href="{{ url('settings') }}" class="item">{{ __('navigation.view.settings') }}</a>
                @endcan
                @can('listContents')
                <a href="{{ url('contents') }}" class="item">{{ __('navigation.view.contents') }}</a>
                @endcan
                @can('listUsers')
                <a href="{{ url('users') }}" class="item">{{ __('navigation.view.users') }}</a>
                @endcan
                @can('listRoles')
                <a href="{{ url('roles') }}" class="item">{{ __('navigation.view.roles') }}</a>
                @endcan
                @can('listPrivileges')
                <a href="{{ url('privileges') }}" class="item">{{ __('navigation.view.privileges') }}</a>
                @endcan
            </div>
        </div>
        @endcanany
        <div class="right menu">
            @guest
                <a class="item" href="{{ route('login') }}">{{ __('navigation.view.login') }}</a>
                <a class="item" href="{{ route('register') }}">{{ __('navigation.view.register') }}</a>
            @else
                <div class="ui simple dropdown item">
                    {{ Auth::user()->username }} <i class="dropdown icon"></i>
                    <div class="menu">
                        <a class="item" href="{{ url('user') }}">{{ __('navigation.view.profile') }}</a>
                        <a class="item" href="{{ url('password/change') }}">{{ __('navigation.view.changePassword') }}</a>
                        <a class="item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ __('navigation.view.logout') }}</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</div>
<script id="bank-menu-template" type="text/html">
    @{{each banks bank index}}
    <a href="{{ url('bank') }}/@{{ bank.id }}" class="item">@{{ bank.name }}</a>
    @{{/each}}
    <div class="ui fitted divider"></div>
    <a href="{{ url('bank') }}" class="item">{{ __('navigation.view.banks.more') }}</a>
</script>
@endsection

@push('nav-scripts')
<script>
    (function($) {
        let lastCardsSize = $(".challenge.cards").outerWidth();
        let self = function() {
            let jqCards = $(".challenge.cards");
            let cardsSize = jqCards.outerWidth();
            if(lastCardsSize == null || Math.abs(lastCardsSize - cardsSize) > 1) {
                let childSize = jqCards.children(":first").outerWidth(true);
                let count = parseInt((cardsSize + 10) / childSize);
                let paddingLeft = (cardsSize - count * childSize) / 2;
                jqCards.css("padding-left", paddingLeft);
            }
            lastCardsSize = cardsSize;
            return self;
        };
        setInterval(self(), 100);
    })(jQuery);
    $(document).ready(function() {
        $(".ui.accordion").accordion();
    });
</script>
@endpush