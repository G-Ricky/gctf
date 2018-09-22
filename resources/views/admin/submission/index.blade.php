@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    <style>
        #table-submissions {
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }
        #table-submissions>*:first-child {
            flex: 1;
        }
    </style>
@endpush

@section('content')
    <div class="ui container" id="table-submissions"></div>
    <script id="tpl-table-submissions" type="text/html">
        <div>
            <table class="ui fixed selectable single line compact table">
                <thead>
                <tr>
                    <th>{{ __('Challenge') }}</th>
                    <th>{{ __('Content') }}</th>
                    <th>{{ __('Submitter') }}</th>
                    <th>{{ __('Time') }}</th>
                    <th>{{ __('Operation') }}</th>
                </tr>
                </thead>
                <tbody>
                @{{each submissions submission index}}
                <tr class="@{{if submission.isCorrect}}positive@{{else}}negative@{{/if}}">
                    <td>@{{submission.challenge}}</td>
                    <td>@{{submission.content}}</td>
                    <td>@{{submission.submitter}}</td>
                    <td>@{{submission.updateTime}}</td>
                    <td>
                        <button class="ui negative button" onclick="deleteSubmission('@{{submission.id}}')"><i class="trash icon"></i>{{ __('Delete') }}</button>
                    </td>
                </tr>
                @{{/each}}
                </tbody>
                <tfoot>
                </tfoot>
            </table>
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
        $.ajax({
            "url": url,
            "success": function(response, status, jqXHR) {
                if(status === "success" && response && response.status === 200 && response.success) {
                    html = template('tpl-table-submissions', {
                        "submissions": response.data,
                        "paginate": response.paginate
                    });
                }else{
                    html = template('tpl-table-submissions', {});
                }
            },
            "complete": function(jqXHR, textStatus) {
                $("#table-submissions").html(html);
            }
        });
    }
    $(document).ready(function() {
        loadSubmissions("{{ $apiUrl }}");
    });
</script>
@endpush