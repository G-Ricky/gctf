@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
@endpush

@section('content')
    <div class="ui container"></div>

    <!-- template -->
    <script id="tpl-container-rankings" type="text/html">
        <div class="ui basic vertical segment" id="table-rankings">
            <table class="ui fixed selectable single line compact table">
                <thead>
                <tr>
                    <th>{{ __('Range') }}</th>
                    <th>{{ __('Nickname') }}</th>
                    <th>{{ __('Points') }}</th>
                    <th>{{ __('Solutions') }}</th>
                </tr>
                </thead>
                <tbody>
                @{{each rankings ranking index}}
                <tr class="@{{if submission.isCorrect}}positive@{{else}}negative@{{/if}}">
                    <td>@{{ranking.range}}</td>
                    <td>@{{ranking.nickname}}</td>
                    <td>@{{ranking.points}}</td>
                    <td>@{{ranking.solutions}}</td>
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
            <a class="huge ui button@{{if paginate.current_page === 1}} disabled@{{/if}}" href="javascript:@{{if paginate.prev_page_url}}loadRankings('@{{paginate.prev_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron left icon"></i></a>
            <a class="huge ui right floated button@{{if paginate.current_page === paginate.last_page}} disabled@{{/if}}" href="javascript:@{{if paginate.next_page_url}}loadRankings('@{{paginate.next_page_url}}')@{{else}}void(0);@{{/if}}"><i class="chevron right icon"></i></a>
        </div>
    </script>
    <!-- end template -->
@endsection

@push('scripts')
    <script>
        function loadRankings() {
            
        }
    </script>
@endpush