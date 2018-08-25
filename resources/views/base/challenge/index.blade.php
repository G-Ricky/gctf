@include('component.navigation')
@include('component.footer')
@extends('layouts.app')

@push('stylesheets')
<link href="{{ asset('css/g2uc/challenge.css') }}" rel="stylesheet">
<link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
<style>
    .ui.right.aligned.object>* {
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .ui.logo {
        padding-top: 60px;
        min-height: 500px;
        margin-bottom: 40px;
    }

</style>
@endpush
@section('content')
<div class="ui vertical masthead center aligned segment logo">
    <img class="ui medium circular centered image" src="{{ asset('img') }}/logo.png">
</div>
<div class="ui container">
    <div class="ui basic vertical clearing segment">
        <button id="challenge-add" class="ui primary right floated button"><i class="add circle icon"></i> Add</button>
    </div>
    <div id="challenges-list"></div>
    <div class="ui paging" id="pagination">
        <a class="huge ui button" id="pg-prev" href="#"><i class="chevron left icon"></i></a>
        <a class="huge ui button" id="pg-next" href="#"><i class="chevron right icon"></i></a>
    </div>
    <div class="ui tiny basic flat modal" id="challenge-modify">
        <i class="close icon"></i>
        <div class="header">
            {{ __('Add challenge') }}
        </div>
        <div class="scrolling content">
            <div class="description">
                <form class="ui form" name="challenge-add" action="{{ url('/challenge/add') }}" method="post">
                    @csrf
                    <div class="field">
                        <label for="ch-title">{{ __('Title') }}</label>
                        <input name="title" type="text" id="ch-title" maxlength="32" value="">
                    </div>

                    <div class="field">
                        <label for="ch-description">{{ __('Description') }}</label>
                        <textarea name="description" type="text" id="ch-description" rows="5" maxlength="1024"></textarea>
                    </div>

                    <div class="field">
                        <label for="ch-points">{{ __('Points') }}</label>
                        <input name="points" type="text" id="ch-points" value="">
                    </div>

                    <div class="field">
                        <label for="ch-flag">{{ __('Flag') }}</label>
                        <input name="flag" type="text" id="ch-flag" value="">
                    </div>

                    <div class="field">
                        <label for="ch-category">{{ __('Category') }}</label>
                        <select name="category">
                            <option value="CRYPTO">CRYPTO</option>
                            <option value="MISC">MISC</option>
                            <option value="PWN">PWN</option>
                            <option value="REVERSE">REVERSE</option>
                            <option value="WEB">WEB</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="ch-tags">{{ __('Tags') }}</label>
                        <input name="tags" type="text" id="ch-tags" value="">
                    </div>

                    <div class="field">
                        <label for="ch-bank">{{ __('Bank') }}</label>
                        <input name="bank" type="text" id="ch-bank" value="">
                    </div>

                    <div class="field">
                        <div class="ui checkbox">
                            <input type="checkbox" tabindex="0">
                            <label>{{ __('Hide') }}</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" type="submit" value="{{ __('Add') }}">
        </div>
    </div>
</div>
<script id="tpl-challenge-cards" type="text/html">
    @{{each categories challenges category}}
    <div class="ui basic vertical segment">
        <h1>@{{category}}</h1>
        <div class="ui link challenge cards">
            @{{each challenges challenge i}}
            <div class="ui card">
                <div class="content">
                    <div class="header">@{{ challenge.title }}</div>
                </div>
                <div class="content">
                    <div class="description">@{{ challenge.description }}</div>
                    <div class="point">@{{ challenge.points }} pt</div>
                </div>
                <div class="ui two bottom attached buttons">
                    <div class="ui button" onclick="challengeEdit('@{{ challenge.id }}')"><i class="edit icon"></i></div>
                    <div class="ui button" onclick="challengeDelete('@{{ challenge.id }}')"><i class="trash icon"></i></div>
                </div>
            </div>
            @{{/each}}
        </div>
    </div>
    @{{/each}}
</script>
@endsection
@push('scripts')
<script>

    $(document).ready(function() {
        load_challenges();
        $("select[name=category]").dropdown();
        $("#challenge-add").click(function() {
            $("#challenge-modify").modal('show');
        });
    });

    function challengeDelete(id) {
        alert(id);
    }

    function challengeEdit(id) {
        alert(id);
    }

    function get_template(tpl_name) {
        if(templates[tpl_name] == null) {
            $.ajax({
                    "type": "GET",
                    "url": "{{ asset('tpl') }}/" + tpl_name + ".tpl",
                    "async": false,
                    "success": function (data) {
                        templates[tpl_name] = data;
                    }
                }
            );
        }
        return templates[tpl_name];
    }

    function load_challenges(page = 1) {
        var pagination;
        $.ajax({
            "type": "GET",
            "url": "{{ asset('/challenge/list') }}?page=" + page + "&bank={{$bank}}",
            "async": false,
            "success": function(data) {
                pagination = data;
            }
        });
        if(pagination.prev_page_url == null) {
            $("#pg-prev").addClass("disabled");
        }
        if(pagination.next_page_url == null) {
            $("#pg-next").addClass("disabled");
        }
        var data = pagination.data;
        var categories = {};
        for(var i = 0;i < data.length;++i) {
            var category = data[i].category;
            categories[category] = categories[category] || [];
            categories[category].push({
                "id": data[i].id,
                "title": data[i].title,
                "description": data[i].description,
                "points": data[i].points
            });
        }
        var html = template("tpl-challenge-cards", {
            "categories": categories
        });
        $("#challenges-list").html(html);
    }
</script>
@endpush
