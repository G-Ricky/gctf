@section('navigation')
<div class="ui inverted stackable borderless menu">
    <div class="ui container">
        <a href="javascript:void(0)" class="header item">
            <img class="logo" src="{{ asset('img/logo.png') }}">
            {{ config('app.name', 'GCTF') }}
        </a>
        <a href="{{ url('challenge') }}" class="item">{{ __('Challenges') }}</a>
        <div class="ui simple dropdown item">
            {{ __('Banks') }} <i class="dropdown icon"></i>
            <div class=" menu" id="bank-menu"></div>
        </div>
        <a href="{{ url('ranking') }}" class="item">{{ __('Ranking') }}</a>
        <div class="ui simple dropdown item">
            {{ __('Submission') }} <i class="dropdown icon"></i>
            <div class="menu">
                <a href="{{ url('submissions/correct') }}" class="item">{{ __('Correct') }}</a>
                <a href="{{ url('submissions/incorrect') }}" class="item">{{ __('Wrong') }}</a>
                <a href="{{ url('submissions') }}" class="item">{{ __('All') }}</a>
            </div>
        </div>
        <div class="ui simple dropdown item">
            {{ __('Admin') }} <i class="dropdown icon"></i>
            <div class="menu">
                <a href="{{ url('users') }}" class="item">{{ __('Users') }}</a>
                <a href="{{ url('roles') }}" class="item">{{ __('Roles') }}</a>
                <a href="{{ url('privileges') }}" class="item">{{ __('Privileges') }}</a>
            </div>
        </div>
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