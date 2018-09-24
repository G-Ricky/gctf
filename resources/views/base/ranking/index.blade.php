@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
@endpush

@section('content')
    <div class="ui container" id="container-rankings"></div>

    <!-- template -->
    <script id="tpl-container-rankings" type="text/html">
        <div class="ui basic vertical segment" id="table-rankings">
            @{{if rankings && rankings.length > 0}}
            <table class="ui fixed selectable single line compact table">
                <thead>
                <tr>
                    <th>{{ __('Range') }}</th>
                    <th>{{ __('Username') }}</th>
                    <th>{{ __('Nickname') }}</th>
                    <th>{{ __('Points') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Some Solutions') }}</th>
                </tr>
                </thead>
                <tbody>
                @{{each rankings ranking index}}
                <tr>
                    <td>@{{index + 1}}</td>
                    <td>@{{ranking.username}}</td>
                    <td>@{{ranking.nickname}}</td>
                    <td>@{{ranking.points}}</td>
                    <td>@{{ranking.solutions_count}}</td>
                    <td>
                        <% for (let i = 0;i < ranking.solutions.length;++i) { %>
                        <% if (i != 0) { %>, <% } %>
                        <% if (i >= 3){ break; } %>
                        @{{ranking.solutions[i].title}}
                        <% } %>
                    </td>
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
    </script>
    <!-- end template -->
@endsection

@push('scripts')
    <script>
        function loadRankings() {
            $.ajax({
                "url": "{{ url('api/rankings') }}",
                "type": "GET",
                "success": function(response, status) {
                    if(status === "success" && response && response.status === 200) {
                        if(response.success) {
                            for(let i = 0;i < response.data.length;++i) {
                                response.data[i].points = parseInt(response.data[i].points);
                            }
                            $("#container-rankings").html(
                                template("tpl-container-rankings", {
                                    "rankings": response.data
                                })
                            );
                        }
                    }
                },
                "error": function(XmlHttpRequest, textStatus, error) {

                },
                "complete": function() {

                }
            });
        }
        $(document).ready(function() {
            loadRankings();
        });
    </script>
@endpush