@include('component.navigation')
@include('component.footer')
@extends('layouts.app')

@push('stylesheets')
<link href="{{ asset('css/g2uc/challenge.css') }}" rel="stylesheet">
<link href="{{ asset('css/extends/modal.flat.css') }}" rel="stylesheet">
<link href="{{ asset('css/wu-ui/wu-ui.css') }}" rel="stylesheet">
<link href="{{ asset('css/wu-ui/iconfont.css') }}" rel="stylesheet">
<style>
    .wu-toast.wu-animate-in {
        z-index: 1002;
    }
    .ui.right.aligned.object>* {
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .ui.logo {
        padding-top: 60px;
        min-height: 500px;
        margin-bottom: 40px;
    }
    .ui.basic.segments {
        border: none;
    }
</style>
@endpush
@section('content')
<div class="ui vertical masthead center aligned segment logo">
    <img class="ui medium circular centered image" src="{{ asset('img/logo.png') }}">
</div>
<div class="ui container" id="container-challenges"></div>

@canany(['addChallenge', 'editChallenge'])
<!-- modal -->
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
                    <input name="title" type="text" id="title" value="" maxlength="32" required>
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
<!-- end modal -->
@endcanany
<!-- modal -->
<div class="ui tiny basic flat modal" id="challenge-detail">
    <i class="close icon"></i>
    <div class="header" id="detail-title"></div>
    <div class="scrolling content">
        <div class="description">
            <div class="ui segment" id="detail-description"></div>
            @can('submitFlag')
            <div class="ui form">
                <div class="field">
                    <input id="detail-id" type="hidden" value="">
                    <input id="detail-flag" type="text" value="">
                </div>
            </div>
            @endcan
        </div>
    </div>
    @can('submitFlag')
    <div class="actions">
        <input class="ui basic fluid button" id="btn-submit" type="button" value="Submit">
    </div>
    @endcan
</div>
<!-- end modal -->
<!-- template -->
<script id="tpl-container-challenges" type="text/html">
    @can('addChallenge')
    <div class="ui basic vertical clearing segment">
        <button id="challenge-add" class="ui primary right floated button"><i class="add circle icon"></i> Add</button>
    </div>
    @endcan
    @{{if count > 0}}
    <div class="ui basic segments" id="cards-challenges">
        @{{each categories challenges category}}
        <div class="ui basic vertical segment">
            <h1>@{{category}}</h1>
            <div class="ui link challenge cards">
                @{{each challenges challenge i}}
                <div class="ui card">
                    @{{if challenge.is_solved}}
                    <a class="ui blue corner label">
                        <i class="checkmark icon"></i>
                    </a>
                    @{{/if}}
                    <div class="content" onclick="challengeDetail('@{{ challenge.id }}')">
                        <div class="header">@{{ challenge.title }}</div>
                    </div>
                    <div class="content" onclick="challengeDetail('@{{ challenge.id }}')">
                        <div class="description">@{{ challenge.description }}</div>
                        <div class="point">@{{ challenge.points }} pt</div>
                    </div>
                    <div class="ui two bottom attached buttons">
                        @can('editChallenge')
                        <div class="ui button" onclick="challengeEdit('@{{ challenge.id }}')"><i class="edit icon"></i></div>
                        @endcan
                        @can('deleteChallenge')
                        <div class="ui button" onclick="confirm('是否删除') &amp;&amp; challengeDelete('@{{ challenge.id }}')"><i class="trash icon"></i></div>
                        @endcan
                    </div>
                </div>
                @{{/each}}
            </div>
        </div>
        @{{/each}}
    </div>
    @{{else}}
    <div class="ui warning message">
        <div class="content">
            <p>暂无数据</p>
        </div>
    </div>
    @{{/if}}
    @{{if count > 0}}
    <div class="ui vertical clearing segment">
        <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadChallenges('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
        <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadChallenges('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
    </div>
    @{{/if}}
</script>
<!-- end template -->
@endsection
@push('scripts')
<script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
<script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
<script src="{{ asset('js/common/tip.js') }}"></script>
<script>

    $(document).ready(function() {
        loadChallenges();
        loadBanks();
        $("select[name=category]").dropdown();
        @canany(['addChallenge', 'editChallenge'])
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
        @endcanany
        @canany(['addChallenge', 'editChallenge'])
        $('#challenge-modify').modal({
            "onHide": function() {
                challengeClear();
            }
        });
        @endcanany
        @canany(['addChallenge', 'editChallenge'])
        $("#btn-save").click(function() {
            $("#form-challenge").submit();
        });
        @endcanany
        @can('submitFlag')
        $("#btn-submit").click(function() {
            var flag = $("#detail-flag").val().trim();
            var challengeId = $("#detail-id").val().trim();
            if(flag.length === 0) {
                tip.error("flag 为空！");
                return;
            }
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
                            tip.success("flag 正确");
                            location.reload();
                        }else{
                            tip.error("flag 错误");
                        }
                    }else{
                        tip.error(response.message);
                    }
                }
            });
        });
        @endcan
    });
    function bindEvents() {
        @can('addChallenge')
        $("#challenge-add").unbind("click").bind("click", function() {
            $("#challenge-modify").modal('show');
            fillForm();
        });
        @endcan
    }
    @canany(['addChallenge', 'editChallenge'])
    function challengeClear() {
        $("#challenge-modify input[type=text]").val("");
        $("#challenge-modify textarea").val("");
        $("#challenge-modify select").dropdown("clear");
    }
    @endcanany
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
    @can('deleteChallenge')
    function challengeDelete(id) {
        $.ajax({
            "type": "POST",
            "url": "{{ url('api/challenge') }}",
            "data": {
                "id": id,
                "_method": "DELETE"
            },
            "success": function (response) {
                if(response.success) {
                    alert("删除成功");
                    location.reload();
                }
            }
        });
    }
    @endcan
    @canany(['addChallenge', 'editChallenge'])
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
    @endcanany
    @canany(['addChallenge', 'editChallenge'])
    function fillForm(data) {
        data = data || {};
        $("#id").val(data.id || "");
        $("#title").val(data.title || "");
        $("#description").val(data.description || "");
        $("#points").val(data.points || "");
        $("#flag").val(data.flag || "");
        $("#category").dropdown("set selected", data.category || 'CRYPTO');
        $("#tags").val(data.tags || "");
        $("#bank").dropdown("set selected", {{$bank}});
    }
    @endcanany
    function fillDetail(data) {
        $("#detail-id").val(data.id);
        $("#detail-title").html(data.title);
        $("#detail-description").html(data.description);
    }
    @can('editChallenge')
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
    @endcan
    function loadBanks() {
        $.ajax({
            "type": "GET",
            "url": "{{ url('bank/list') }}",
            "success": function(response) {
                if(response.status === 200 && response.success) {
                    let banks = response.data;
                    let html = "";
                    for(let i = 0;i < banks.length;++i) {
                        html += `<option value="${banks[i].id}">${banks[i].name}</option>`
                    }
                    $("#bank").html(html).dropdown("set selected", {{$bank}});
                }
            }
        });
    }
    function loadChallenges(url) {
        if(url == null) {
            url = "{{ url('api/challenges') }}?bank={{$bank}}";
        }
        $.ajax({
            "type": "GET",
            "url": url,
            "async": false,
            "success": function(response, status) {
                if(response.status === 200 && response.success) {
                    let challenges = response.data;
                    let categories = {};
                    for(let i = 0;i < challenges.length;++i) {
                        let category = challenges[i].category;
                        categories[category] = categories[category] || [];
                        categories[category].push(challenges[i]);
                    }
                    $("#container-challenges").html(
                        template("tpl-container-challenges", {
                            "categories": categories,
                            "paginate": response.paginate,
                            "count": challenges.length
                        })
                    );
                    bindEvents();
                }
            }
        });
    }
</script>
@endpush
