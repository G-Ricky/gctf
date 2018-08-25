@section('navigation')
<div class="ui menu"></div>
<div class="ui inverted fixed borderless menu">
    <div class="ui container">
        <a href="{{ url('/') }}" class="header item">
            <img class="logo" src="{{ asset('img/logo.png') }}">
            {{ config('app.name', 'Laravel') }}
        </a>
        <a href="{{ url('challenge') }}" class="item">Challenge</a>
        <div class="ui simple dropdown item">
            Banks <i class="dropdown icon"></i>
            <div class=" menu">
                <a href="#" class="item">题库1</a>
                <a href="#" class="item">题库2</a>
                <a href="#" class="item">题库3</a>
                <div class="ui fitted divider"></div>
                <a href="{{ url('bank') }}" class="item">更多</a>
            </div>
        </div>
        <a href="#" class="item">Ranking</a>
        @can('view-all-submissions', Auth::class)
        <div class="ui simple dropdown item">
            Submissions <i class="dropdown icon"></i>
            <div class="menu">
                <a href="#" class="item">Correct</a>
                <a href="#" class="item">Wrong</a>
                <a href="#" class="item">All</a>
            </div>
        </div>
        @endcan
        @can('view-all-users', Auth::class)
        <a href="#" class="item">Users</a>
        @endcan
        <div class="right menu">
            @guest
                <a class="item" href="{{ route('login') }}">{{ __('Login') }}</a>
                <a class="item" href="{{ route('register') }}">{{ __('Register') }}</a>
            @else
                <div class="ui simple dropdown item">
                    {{ Auth::user()->nickname }} <i class="dropdown icon"></i>
                    <div class="menu">
                        <a class="item" href="{{ url('user') }}">Profile</a>
                        <a class="item" href="#">Change Password</a>
                        <a class="item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</div>
@endsection