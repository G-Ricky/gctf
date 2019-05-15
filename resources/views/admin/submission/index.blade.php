@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    <link href="{{ asset('css/wu-ui/wu-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/iconfont.css') }}" rel="stylesheet">
    <style>
        #container-submissions {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }
        #container-submissions #table-submissions {
            flex: 1;
        }
    </style>
@endpush

@section('content')
    <div class="ui dimmer" id="global-loader">
        <div class="ui big text loader"></div>
    </div>
    <div class="ui container" id="container-submissions"></div>
    <script id="tpl-container-submissions" type="text/html">
        <div class="ui basic vertical segment" id="table-submissions">
            @{{if submissions && submissions.length > 0}}
            <table class="ui selectable single line compact table">
                <thead>
                <tr>
                    <th>{{ __('submission.view.admin.table.challenge') }}</th>
                    <th>{{ __('submission.view.admin.table.content') }}</th>
                    <th>{{ __('submission.view.admin.table.submitter') }}</th>
                    <th>{{ __('submission.view.admin.table.time') }}</th>
                    @canany(['deleteSubmission'])
                    <th>{{ __('submission.view.admin.table.operation') }}</th>
                    @endcanany
                </tr>
                </thead>
                <tbody>
                @{{each submissions submission index}}
                <tr class="@{{if submission.isCorrect}}positive@{{else}}negative@{{/if}}" data-entity="submission" data-id="@{{submission.id}}">
                    <td @{{if submission.challenge.length > 10}}data-tooltip="@{{submission.challenge}}"@{{/if}}>
                        @{{if submission.content.length > 10}}
                        @{{submission.challenge.substr(0, 10)}}...
                        @{{else}}
                        @{{submission.challenge}}
                        @{{/if}}
                    </td>
                    <td @{{if submission.content.length > 20}}data-tooltip="@{{submission.content}}"@{{/if}}>
                        @{{if submission.content.length > 20}}
                        @{{submission.content.substr(0, 20)}}...
                        @{{else}}
                        @{{submission.content}}
                        @{{/if}}
                    </td>
                    <td>@{{submission.submitter}}</td>
                    <td>@{{submission.createTime}}</td>
                    @canany(['correctSubmission', 'deleteSubmission'])
                    <td>
                        @can('correctSubmission')
                        @{{if submission.isCorrect}}
                        <button class="ui orange icon button" data-tooltip="{{ __('submission.view.admin.table.row.tooltip.setIncorrect') }}" onclick="confirm('{{ __('submission.view.admin.table.row.confirm.setIncorrect') }}') &amp;&amp; changeCorrectness('@{{submission.id}}', 0)">
                            <i class="times icon"></i>
                        </button>
                        @{{else}}
                        <button class="ui positive icon button" data-tooltip="{{ __('submission.view.admin.table.row.tooltip.setCorrect') }}" onclick="confirm('{{ __('submission.view.admin.table.row.confirm.setCorrect') }}') &amp;&amp; changeCorrectness('@{{submission.id}}', 1)">
                            <i class="check icon"></i>
                        </button>
                        @{{/if}}
                        @endcan
                        @can('deleteSubmission')
                        <button class="ui negative icon button" data-tooltip="{{ __('submission.view.admin.table.row.tooltip.delete') }}" onclick="confirm('{{ __('submission.view.admin.table.row.confirm.delete') }}') &amp;&amp; deleteSubmission('@{{submission.id}}')">
                            <i class="trash icon"></i>
                        </button>
                        @endcan
                    </td>
                    @endcanany
                </tr>
                @{{/each}}
                </tbody>
                <tfoot>
                </tfoot>
            </table>
            @{{else}}
            <div class="ui warning message">
                <div class="content">
                    <p>暂无数据</p>
                </div>
            </div>
            @{{/if}}
        </div>
        @{{if paginate.last_page > 1}}
        <div class="ui vertical clearing segment">
            <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadSubmissions('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
            <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadSubmissions('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
        </div>
        @{{/if}}
    </script>
@endsection

@push('scripts')
<script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
<script src="{{ asset('js/common/tip.js') }}"></script>
<script src="{{ asset('js/common/misc.js') }}"></script>
<script src="{{ asset('js/common/error.js') }}"></script>
<script>
    function loadSubmissions(url) {
        let html = "";
        if(url == null) {
            url = "{{ url('api/submissions') }}";
        }
        openLoader("正在加载用户提交...");
        $.ajax({
            "url": url,
            "success": function(response, status, jqXHR) {
                if(response && response.success) {
                    html = template('tpl-container-submissions', {
                        "submissions": response.data,
                        "paginate": response.paginate
                    });
                }else{
                    tip.error(response.message || "{{ __('global.fail') }}");
                }
            },
            "complete": function(jqXHR, textStatus) {
                $("#container-submissions").html(html);
                closeLoader();
            }
        });
    }
    @can('deleteSubmission')
    function deleteSubmission(id) {
        $.ajax({
            "url": "{{ url('api/submission') }}",
            "type": "POST",
            "data": {
                "id": id,
                "_method": "DELETE"
            },
            "success": function(response, status) {
                if(response || response.success) {
                    tip.success("{{ __('global.success') }}");
                    loadSubmissions();
                }else{
                    tip.error(response.message || "{{ __('global.fail') }}");
                }
            },
            "error": handleError
        });
    }
    @endcan
    @can('correctSubmission')
    function changeCorrectness(id, isCorrect) {
        $.post(
            "{{ url('api/submission') }}",
            {
                "_method": "PUT",
                "id": id,
                "is_correct": isCorrect
            },
            function (response) {
                if(response && response.success) {
                    tip.success("{{ __('global.success') }}");
                    loadSubmissions();
                } else {
                    tip.error(response.message || "{{ __('global.fail') }}");
                }
            }
        ).fail(handleError);
    }
    @endcan
    $(document).ready(function() {
        loadSubmissions("{{ $apiUrl }}");
    });
</script>
@endpush