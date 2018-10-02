@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
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
    <div class="ui container" id="container-submissions"></div>
    <script id="tpl-container-submissions" type="text/html">
        <div class="ui basic vertical segment" id="table-submissions">
            @{{if submissions && submissions.length > 0}}
            <table class="ui selectable single line compact table">
                <thead>
                <tr>
                    <th>{{ __('Challenge') }}</th>
                    <th>{{ __('Content') }}</th>
                    <th>{{ __('Submitter') }}</th>
                    <th>{{ __('Time') }}</th>
                    @canany(['deleteSubmission'])
                    <th>{{ __('Operation') }}</th>
                    @endcanany
                </tr>
                </thead>
                <tbody>
                @{{each submissions submission index}}
                <tr class="@{{if submission.isCorrect}}positive@{{else}}negative@{{/if}}">
                    <td>@{{submission.challenge}}</td>
                    <td>@{{submission.content}}</td>
                    <td>@{{submission.submitter}}</td>
                    <td>@{{submission.updateTime}}</td>
                    @canany(['deleteSubmission'])
                    <td>
                        @can('deleteSubmission')
                        <button class="ui negative icon button" data-tooltip="{{ __('Delete submission') }}" onclick="confirm('{{ __('Are you sure to delete it ?') }}') &amp;&amp; deleteSubmission('@{{submission.id}}')">
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
        <div class="ui vertical clearing segment">
            <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadSubmissions('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
            <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadSubmissions('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
        </div>
    </script>
@endsection

@push('scripts')
<script>
    function loadSubmissions(url) {
        let html = "";
        if(url == null) {
            url = "{{ url('api/submissions') }}";
        }
        $.ajax({
            "url": url,
            "success": function(response, status, jqXHR) {
                if(status === "success" && response && response.status === 200 && response.success) {
                    html = template('tpl-container-submissions', {
                        "submissions": response.data,
                        "paginate": response.paginate
                    });
                }else{
                    html = template('tpl-container-submissions', {});
                }
            },
            "complete": function(jqXHR, textStatus) {
                $("#container-submissions").html(html);
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
                if(response.success) {
                    loadSubmissions();
                }else{

                }
            }
        });
    }
    @endcan
    $(document).ready(function() {
        loadSubmissions("{{ $apiUrl }}");
    });
</script>
@endpush