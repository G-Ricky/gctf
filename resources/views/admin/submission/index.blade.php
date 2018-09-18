@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
@endpush

@section('content')
<div class="ui container" id="list-submissions"></div>
<script id="tpl-list-submissions" type="text/html">
    <div class="ui basic segment">
        @{{each result submission index}}
        <div class="ui @{{if submission.isCorrect}}success@{{else}}error@{{/if}} message">
            <div class="header">@{{submission.submitter}} 在 @{{submission.updateTime}} 提交了 @{{submission.challenge}}</div>
            <p>@{{submission.content}}</p>
        </div>
        @{{/each}}
    </div>
    <div class="ui vertical clearing segment">
        <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadSubmissions('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
        <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadSubmissions('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
    </div>
</script>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        loadSubmissions("{{ url('submissions') }}{{ $queryString }}");
    });
    function loadSubmissions(url) {
        let html = "";
        $.ajax({
            "url": url,
            "success": function(response, status, jqXHR) {
                if(status === "success" && response && response.status === 200 && response.success) {
                    html = template('tpl-list-submissions', response);
                }else{
                    html = template('tpl-list-submissions', {});
                }
            },
            "complete": function(jqXHR, textStatus) {
                $("#list-submissions").html(html);
            }
        });
    }
</script>
@endpush