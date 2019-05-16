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
        box-shadow: none;
        border: none;
    }
    #container-challenges {
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }
    #container-challenges>#cards-challenges {
        flex: 1;
    }
</style>
@endpush
@section('content')

<div class="ui dimmer" id="global-loader">
    <div class="ui big text loader"></div>
</div>

@can('addChallenge')
<div class="ui container">
    <div class="ui basic vertical clearing segment">
        <button id="challenge-add" class="ui primary right floated button"><i class="add circle icon"></i> {{ __('challenge.view.add') }}</button>
    </div>
</div>
@endcan

<div class="ui container" id="container-challenges"></div>

@canany(['addChallenge', 'editChallenge'])
<!-- modal -->
<div class="ui tiny basic flat modal" id="challenge-modify">
    <i class="close icon"></i>
    <div class="header" id="challenge-modal-title">
        {{ __('challenge.view.modal.title.add') }}
    </div>
    <div class="scrolling content">
        <div class="description">
            <form class="ui form" id="form-challenge" name="challenge-add" action="{{ url('api/challenge') }}" method="post">
                @csrf
                <input id="id" name="id" type="hidden">
                <input id="form-method" name="_method" type="hidden" value="">
                <div class="field">
                    <label for="title">{{ __('challenge.view.modal.label.title') }}</label>
                    <input name="title" type="text" id="title" value="" maxlength="32" required>
                </div>

                <div class="field">
                    <label for="description">{{ __('challenge.view.modal.label.description') }}</label>
                    <textarea name="description" type="text" id="description" rows="5" maxlength="1024"></textarea>
                </div>

                <div class="field">
                    <label for="basic_points">{{ __('challenge.view.modal.label.points') }}</label>
                    <input name="basic_points" type="text" id="basic_points" value="">
                </div>

                <div class="field">
                    <label for="flag">{{ __('challenge.view.modal.label.flag') }}</label>
                    <input name="flag" type="text" id="flag" value="">
                </div>

                <div class="field">
                    <label for="category">{{ __('challenge.view.modal.label.category') }}</label>
                    <select id="category" name="category">
                        <option value="CRYPTO">CRYPTO</option>
                        <option value="MISC">MISC</option>
                        <option value="PWN">PWN</option>
                        <option value="REVERSE">REVERSE</option>
                        <option value="WEB">WEB</option>
                    </select>
                </div>

                <div class="field">
                    <label for="bank">{{ __('challenge.view.modal.label.bank') }}</label>
                    <select name="bank" id="bank"></select>
                </div>
            </form>
        </div>
    </div>
    <div class="actions">
        <input class="ui basic fluid button" id="btn-save" type="button" value="{{ __('challenge.view.modal.button.save') }}">
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
        <input class="ui basic fluid button" id="btn-submit" type="button" value="{{ __('challenge.view.modal.submit') }}">
    </div>
    @endcan
</div>
<!-- end modal -->
<!-- template -->
<script id="tpl-container-challenges" type="text/html">
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
    <div class="ui basic vertical segment">
        <div class="ui warning message">
            <div class="content">
                <p>暂无数据</p>
            </div>
        </div>
    </div>
    @{{/if}}
    @{{if paginate.last_page > 1}}
    <div class="ui vertical clearing segment">
        <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadChallenges('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
        <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadChallenges('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
    </div>
    @{{/if}}
</script>
<script id="tpl-challenge-errors" type="text/html">
    @{{each errors error index}}
    <div class="ui basic vertical segment">
        <div class="ui negative message">
            <div class="content">
                <p>@{{error.message}}</p>
            </div>
        </div>
    </div>
    @{{/each}}
</script>
<!-- end template -->
@endsection
@push('scripts')
<script src="{{ asset('js/jquery/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/jquery/jquery.form.min.js') }}"></script>
<script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
<script src="{{ asset('js/common/tip.js') }}"></script>
<script src="{{ asset('js/common/misc.js') }}"></script>
<script src="{{ asset('js/common/error.js') }}"></script>
<script>
    $(document).ready(function() {
        loadChallenges();
        loadBanks();
        $("#challenge-detail").modal();
        $('#challenge-modify').modal();
        $("select[name=category]").dropdown();
        @canany(['addChallenge', 'editChallenge'])
        $("#btn-save").click(function() {
            $("#form-challenge").submit();
        });
        $("#challenge-add").click(function() {
            $("#challenge-modal-title").text("{{ __('challenge.view.modal.title.add') }}");
            $("#challenge-modify").modal('show');
            fillForm();
        });
        $("#form-challenge").validate({
            "submitHandler": function(form) {
                if($("#id").val()) {
                    $("#form-method").val("PUT");
                }else{
                    $("#form-method").val("POST");
                }
                $(form).ajaxSubmit({
                    "success": function(data) {
                        if(data.success) {
                            $("#challenge-modify").modal('hide');
                            tip.success("{{ __('global.success') }}");
                            loadChallenges();
                        }else{
                            tip.error(data.message || "{{ __('global.fail') }}");
                        }
                    },
                    "error": handleError
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
                "basic_points": {
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
        @can('submitFlag')
        $("#btn-submit").click(function() {
            var flag = $("#detail-flag").val().trim();
            var challengeId = $("#detail-id").val().trim();
            if(flag.length === 0) {
                tip.error("Flag 为空！");
                return;
            }
            $.ajax({
                "url": "{{ url('api/flag') }}",
                "type": "POST",
                "dataType": "json",
                "data": {
                    "challengeId": challengeId,
                    "flag": flag
                },
                "success": function (response) {
                    if(response && response.success) {
                        if(response.correct) {
                            tip.success("Flag 正确");
                            $("#challenge-detail").modal("hide");
                            loadChallenges();
                        }else{
                            tip.error("Flag 错误");
                        }
                    }else{
                        tip.error(response.message || "{{ __('global.unknownError') }}");
                    }
                },
                "error": handleError
            });
        });
        @endcan
    });
    function challengeDetail(id) {
        $.ajax({
            "type": "GET",
            "url": "{{ url('api/ch') }}/" + id,
            "async": false,
            "success": function(response) {
                if(response && response.success) {
                    $("#challenge-detail").modal('show');
                    $("#detail-flag").val("");
                    fillDetail(response.data);
                } else {
                    tip.error(response.message || "{{ __('global.unknownError') }}");
                }
            },
            "error": handleError
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
                    tip.success("删除成功");
                    loadChallenges();
                }else{
                    tip.error("删除失败");
                }
            },
            "error": handleError
        });
    }
    @endcan
    @canany(['addChallenge', 'editChallenge'])
    function fillForm(data) {
        data = data || {};
        $("#id").val(data.id || "");
        $("#title").val(data.title || "");
        $("#description").val(data.description || "");
        $("#basic_points").val(data.basic_points || "");
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
            "url": "{{ url('api/challenge') }}/" + id,
            "async": false,
            "success": function(response) {
                if(response.success) {
                    fillForm(response.data);
                    $("#challenge-modal-title").text("{{ __('challenge.view.modal.title.edit') }}");
                    $("#challenge-modify").modal('show');
                }
            },
            "error": handleError
        });
    }
    @endcan
    function loadBanks() {
        $.ajax({
            "type": "GET",
            "url": "{{ url('api/banks') }}",
            "success": function(response) {
                if(response && response.success) {
                    let banks = response.data;
                    let html = "";
                    for(let i = 0;i < banks.length;++i) {
                        html += `<option value="${banks[i].id}">${banks[i].name}</option>`
                    }
                    $("#bank").html(html).dropdown("set selected", {{$bank}});
                } else {
                    tip.error(response.message || "{{ __('global.unknownError') }}");
                }
            },
            "error": handleError
        });
    }
    function loadChallenges(url) {
        if(url == null) {
            url = "{{ url('api/bank') . '/' . $bank }}";
        }
        openLoader("正在加载挑战...");
        $.ajax({
            "type": "GET",
            "url": url,
            "success": function(response, status) {
                if(response && response.success) {
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
                }else{
                    response.message = response.message || "{{ __('global.unknownError') }}";
                    tip.error(response.message,);
                    $("#container-challenges").html(
                        template("tpl-challenge-errors", {
                            "errors": [
                                {
                                    "message": response.message,
                                    "code": response.code
                                }
                            ]
                        })
                    );
                }
            },
            "error": handleError,
            "complete": function () {
                closeLoader();
            }
        });
    }
</script>
@endpush
