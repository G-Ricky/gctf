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
            padding-top: 40px;
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
<div class="ui mobile only fixed inverted main menu">
    <div class="ui container">
        <a class="launch icon item">
            <i class="content icon"></i>
        </a>
    </div>
</div>
<div class="ui mobile banner inverted borderless fixed menu">
    <div class="ui container">
        <a href="{{ url('/') }}" class="header item">
            <img class="logo" src="{{ asset('img/logo.png') }}">
            {{ config('app.name', 'GCTF') }}
        </a>
        @can('listChallenges')
        <a href="{{ url('challenge') }}" class="item">{{ __('Challenges') }}</a>
        @endcan
        @can('viewRanking')
        <a href="{{ url('ranking') }}" class="item">{{ __('Ranking') }}</a>
        @endcan
        @can('listBanks')
        <a href="{{ url('banks') }}" class="item">{{ __('Banks') }}</a>
        @endcan
        @can('listSubmissions')
        <div class="ui simple dropdown item">
            {{ __('Submission') }} <i class="dropdown icon"></i>
            <div class="menu">
                <a href="{{ url('submissions/correct') }}" class="item">{{ __('Correct') }}</a>
                <a href="{{ url('submissions/incorrect') }}" class="item">{{ __('Wrong') }}</a>
                <a href="{{ url('submissions') }}" class="item">{{ __('All') }}</a>
            </div>
        </div>
        @endcan
        @canany(['listUsers', 'listRoles', 'listPrivileges'])
        <div class="ui simple dropdown item">
            {{ __('Admin') }} <i class="dropdown icon"></i>
            <div class="menu">
                @can('listUsers')
                <a href="{{ url('users') }}" class="item">{{ __('Users') }}</a>
                @endcan
                @can('listRoles')
                <a href="{{ url('roles') }}" class="item">{{ __('Roles') }}</a>
                @endcan
                @can('listPrivileges')
                <a href="{{ url('privileges') }}" class="item">{{ __('Privileges') }}</a>
                @endcan
            </div>
        </div>
        @endcanany
        <div class="right menu">
            @guest
                <a class="item" href="{{ route('login') }}">{{ __('Login') }}</a>
                <a class="item" href="{{ route('register') }}">{{ __('Register') }}</a>
            @else
                <div class="ui simple dropdown item">
                    {{ Auth::user()->username }} <i class="dropdown icon"></i>
                    <div class="menu">
                        <a class="item" href="{{ url('user') }}">{{ __('Profile') }}</a>
                        <a class="item" href="{{ url('password/change') }}">{{ __('Change Password') }}</a>
                        <a class="item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
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
    <a href="{{ url('challenge') }}?bank=@{{ bank.id }}" class="item">@{{ bank.name }}</a>
    @{{/each}}
    <div class="ui fitted divider"></div>
    <a href="{{ url('bank') }}" class="item">{{ __('More') }}</a>
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
</script>
@endpush