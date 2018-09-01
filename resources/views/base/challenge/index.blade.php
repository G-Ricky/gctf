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
    <img class="ui medium circular centered image" src="{{ asset('img/logo.png') }}">
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
                <form class="ui form" id="form-challenge" name="challenge-add" method="post">
                    @csrf
                    <input id="id" name="id" type="hidden">
                    <div class="field">
                        <label for="title">{{ __('Title') }}</label>
                        <input name="title" type="text" id="title" maxlength="32" value="">
                    </div>

                    <div class="field">
                        <label for="description">{{ __('Description') }}</label>
                        <textarea name="description" type="text" id="description" rows="5" maxlength="1024"></textarea>
                    </div>

                    <div class="field">
                        <label for="points">{{ __('Points') }}</label>
                        <input name="points" type="text" id="points" value="">
                    </div>

                    <div class="field">
                        <label for="flag">{{ __('Flag') }}</label>
                        <input name="flag" type="text" id="flag" value="">
                    </div>

                    <div class="field">
                        <label for="category">{{ __('Category') }}</label>
                        <select id="category" name="category">
                            <option value="CRYPTO">CRYPTO</option>
                            <option value="MISC">MISC</option>
                            <option value="PWN">PWN</option>
                            <option value="REVERSE">REVERSE</option>
                            <option value="WEB">WEB</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="tags">{{ __('Tags') }}</label>
                        <input name="tags" type="text" id="tags" value="">
                    </div>

                    <div class="field">
                        <label for="bank">{{ __('Bank') }}</label>
                        <select name="bank" id="bank"></select>
                    </div>

                    <div class="field">
                        <div class="ui checkbox">
                            <input name="is_hidden" type="checkbox" tabindex="0">
                            <label>{{ __('Hide') }}</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" id="btn-save" type="button" value="{{ __('Save') }}">
        </div>
    </div>
    <div class="ui tiny basic flat modal" id="challenge-detail">
        <i class="close icon"></i>
        <div class="header" id="detail-title"></div>
        <div class="scrolling content">
            <div class="description">
                <div class="ui segment" id="detail-description"></div>
                <div class="ui form">
                    <div class="field">
                        <input id="detail-id" type="hidden" value="">
                        <input id="detail-flag" type="text" value="">
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <input class="ui basic fluid button" id="btn-submit" type="button" value="Submit">
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
                <div class="content" onclick="challengeDetail('@{{ challenge.id }}')">
                    <div class="header">@{{ challenge.title }}</div>
                </div>
                <div class="content" onclick="challengeDetail('@{{ challenge.id }}')">
                    <div class="description">@{{ challenge.description }}</div>
                    <div class="point">@{{ challenge.points }} pt</div>
                </div>
                <div class="ui two bottom attached buttons">
                    <div class="ui button" onclick="challengeEdit('@{{ challenge.id }}')"><i class="edit icon"></i></div>
                    <div class="ui button" onclick="confirm('是否删除') &amp;&amp; challengeDelete('@{{ challenge.id }}')"><i class="trash icon"></i></div>
                </div>
            </div>
            @{{/each}}
        </div>
    </div>
    @{{/each}}
</script>
<script id="tpl-banks" type="text/html">
    @{{each banks bank index}}
    <option value="@{{bank.id}}">@{{bank.name}}</option>
    @{{/each}}
</script>
@endsection
@push('scripts')
<script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
<script>

    $(document).ready(function() {
        loadChallenges();
        loadBanks();
        $("#form-challenge").validate({
            "submitHandler": function(form) {
                var type = '';
                if($("#id").val()) {
                    form.action = "{{ url('challenge/edit') }}";
                    type = "PUT";
                }else{
                    form.action = "{{ url('challenge/add') }}";
                    type = "POST";
                }
                $(form).ajaxSubmit({
                    "type": type,
                    "success": function(data) {
                        if(data.success) {
                            alert("成功");
                            location.reload();
                        }else{
                            alert("失败");
                        }
                    }
                });
            },
            "rules": {
                "title": {
                    "required": true,
                    "maxlength": 32
                },
                "description": {
                    "maxlength": 1000
                },
                "points": {
                    "required": true,
                    "digits": true,
                    "range": [0, 10000]
                },
                "flag": {
                    "required": true,
                    "rangelength": [10, 200]
                },
                "tags": {
                    "maxlength": 200
                },
                "bank": {
                    "required": true
                }
            }
        });
        $("select[name=category]").dropdown();
        $('#challenge-modify').modal({
            "onHide": function() {
                challengeClear();
            }
        });
        $("#challenge-add").click(function() {
            $("#challenge-modify").modal('show');
        });
        $("#btn-save").click(function() {
            $("#form-challenge").submit();
        });
        $("#btn-submit").click(function() {
            var flag = $("#detail-flag").val().trim();
            var challengeId = $("#detail-id").val().trim();
            $.ajax({
                "url": "{{ url('flag') }}",
                "type": "POST",
                "dataType": "json",
                "data": {
                    "challengeId": challengeId,
                    "flag": flag
                },
                "success": function (response) {
                    if(response.success) {
                        if(response.correct) {
                            alert("flag 正确");
                            location.reload();
                        }else{
                            alert("flag 错误")
                        }
                    }
                }
            });
        });
    });

    function challengeClear() {
        $("#challenge-modify input[type=text]").val("");
        $("#challenge-modify textarea").val("");
        $("#challenge-modify select").dropdown("clear");
    }

    function challengeDetail(id) {
        $.ajax({
            "type": "GET",
            "url": "{{ url('challenge/detail') }}?id=" + id,
            "async": false,
            "success": function(response) {
                if(response.success) {
                    $("#challenge-detail").modal('show');
                    fillDetail(response.data);
                }
            }
        });
    }

    function challengeDelete(id) {
        $.ajax({
            "type": "POST",
            "url": "{{ url('challenge/remove') }}",
            "async": false,
            "data": {
                "id": id
            },
            "success": function (response) {
                if(response.success) {
                    alert("删除成功");
                    location.reload();
                }
            }
        });
    }

    function sendChallengeAction(data, action) {
        let url = "";
        if(action === "add") {
            url = "{{ url('challenge/add') }}"
        }else{
            url = "{{ url('challenge/edit') }}"
        }
        $.ajax({
            "type": "POST",
            "url": url,
            "data": data,
            "dataType": "json",
            "async": false,
            "success": function(response) {
                if(response.success) {
                    alert("成功保存");
                    location.reload();
                }
            }
        });
    }

    function fillForm(data) {
        $("#id").val(data.id);
        $("#title").val(data.title);
        $("#description").val(data.description);
        $("#points").val(data.points);
        $("#flag").val(data.flag);
        $("#category").dropdown("set selected", data.category);
        $("#tags").val(data.tags);
        $("#bank").dropdown("set selected", data.bank);
    }

    function fillDetail(data) {
        $("#detail-id").val(data.id);
        $("#detail-title").html(data.title);
        $("#detail-description").html(data.description);
    }

    function challengeEdit(id) {
        $.ajax({
            "type": "GET",
            "url": "{{ url('challenge/info') }}?id=" + id,
            "async": false,
            "success": function(response) {
                if(response.success) {
                    fillForm(response.data);
                    $("#challenge-modify").modal('show');
                }
            }
        });
    }

    function loadBanks() {
        let data = [];
        $.ajax({
            "type": "GET",
            "url": "{{ url('bank/list') }}",
            "async": false,
            "success": function(response) {
                if(response.success) {
                    data = response.data;
                }
            }
        });
        let html = template('tpl-banks', {
            "banks": data
        });
        $("#bank").html(html);
        $("#bank").dropdown();
    }

    function loadChallenges(page = 1) {
        var pagination;
        $.ajax({
            "type": "GET",
            "url": "{{ url('challenge/list') }}?page=" + page + "&bank={{$bank}}",
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
