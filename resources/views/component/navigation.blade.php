@section('navigation')
<div class="ui menu"></div>
<div class="ui inverted fixed stackable borderless menu">
    <div class="ui container">
        <a href="javascript:void(0)" class="header item">
            <img class="logo" src="{{ asset('img/logo.png') }}">
            {{ config('app.name', 'GCTF') }}
        </a>
        <a href="{{ url('challenge') }}" class="item">Challenge</a>
        <div class="ui simple dropdown item">
            Banks <i class="dropdown icon"></i>
            <div class=" menu" id="bank-menu"></div>
        </div>
        <a href="{{ url('ranking') }}" class="item">Ranking</a>
        <div class="ui simple dropdown item">
            Submissions <i class="dropdown icon"></i>
            <div class="menu">
                <a href="{{ url('submission') }}?correct" class="item">Correct</a>
                <a href="{{ url('submission') }}?incorrect" class="item">Wrong</a>
                <a href="{{ url('submission') }}" class="item">All</a>
            </div>
        </div>
        <a href="{{ url('users') }}" class="item">Users</a>
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
<script id="bank-menu-template" type="text/html">
    @{{each banks bank index}}
    <a href="{{ url('challenge') }}?bank=@{{ bank.id }}" class="item">@{{ bank.name }}</a>
    @{{/each}}
    <div class="ui fitted divider"></div>
    <a href="{{ url('bank') }}" class="item">更多</a>
</script>
@endsection

@push('nav-scripts')
<script>
    $(document).ready(function() {
        $.ajax({
            "url": "{{ url('bank/list') }}?page=1&pageSize=3",
            "method": "GET",
            "success": function(response) {
                if(response.success) {
                    let html = template("bank-menu-template", {
                        'banks': response.data
                    });
                    $("#bank-menu").html(html);
                }
            }
        });
    });
</script>
@endpush