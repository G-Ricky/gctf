@include('component.navigation')
@include('component.footer')
@extends('layouts/app')

@push('stylesheets')
    <link href="{{ asset('css/wu-ui/wu-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/wu-ui/iconfont.css') }}" rel="stylesheet">
    <style>
        #time-refresh {
            color: #c0c1c2;
            padding-top: 0;
            padding-bottom: 0;
        }
    </style>
@endpush

@section('content')
    <div class="ui container" id="container-rankings"></div>

    <!-- template -->
    <script id="tpl-container-rankings" type="text/html">
        <div class="ui basic vertical segment" id="chart-rankings" style="height: 350px;-ms-overflow-x: scroll;overflow-x: scroll;"></div>
        <div class="ui basic right aligned segment" id="time-refresh">&nbsp;</div>
        <div class="ui basic vertical segment" id="table-rankings">
            @{{if rankings && rankings.length > 0}}
            <table class="ui fixed selectable single line compact table">
                <thead>
                <tr>
                    <th>{{ __('ranking.view.table.range') }}</th>
                    <th>{{ __('ranking.view.table.username') }}</th>
                    <th>{{ __('ranking.view.table.points') }}</th>
                    <th>{{ __('ranking.view.table.total') }}</th>
                    <th>{{ __('ranking.view.table.solutions') }}</th>
                </tr>
                </thead>
                <tbody>
                @{{each rankings ranking index}}
                <tr>
                    <td>@{{index + 1}}</td>
                    <td>
                        @{{ranking.username}}
                        @{{if ranking.nickname}}
                        (@{{ranking.nickname}})
                        @{{/if}}
                    </td>
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
    <script src="{{ asset('js/echarts.min.js') }}"></script>
    <script src="{{ asset('js/wu-ui/wu-ui.min.js') }}"></script>
    <script src="{{ asset('js/common/tip.js') }}"></script>
    <script src="{{ asset('js/common/error.js') }}"></script>
    <script>
        function loadRankings() {
            $.ajax({
                "url": "{{ url('api/rankings') . '?bank=' . $bank }}",
                "type": "GET",
                "success": function(response, status) {
                    if(response && response.success) {
                        for(let i = 0;i < response.data.length;++i) {
                            response.data[i].points = parseInt(response.data[i].points);
                        }
                        $("#container-rankings").html(
                            template("tpl-container-rankings", {
                                "rankings": response.data
                            })
                        );
                        makeChartRankings(response.data);
                    } else {
                        tip.error(response.message || "{{ __('global.unknownError') }}");
                    }
                },
                "error": handleError,
                "complete": function() {

                }
            });
        }
        function makeChartRankings(rankings) {
            let userNames = [];
            let seriesOption = [];
            let rangeShown = Math.min(rankings.length, 10);


            for(let i = 0;i < rangeShown;++i) {
                userNames.push(rankings[i].username);
                let solutions = rankings[i].solutions;
                let data = [];
                let userPoints = 0;
                solutions.sort(function(a, b) {
                    return a.solved_time - b.solved_time;
                });
                for(let j = 0;j < solutions.length;++j) {
                    userPoints += solutions[j].points;
                    data.push([
                        solutions[j].solved_date, userPoints
                    ]);
                }

                seriesOption.push({
                    name: rankings[i].username,
                    showAllSymbol: true,
                    symbolSize: 10,
                    type: "line",
                    data: data
                });
            }

            let chartRankings = echarts.init(document.getElementById('chart-rankings'));
            chartRankings.setOption({
                tooltip: {
                    trigger: 'item',
                    formatter: function (params) {
                        return params.seriesName + "(" + parseInt(params.value[1]) + ")";
                    }
                },
                toolbox: {
                    show: true,
                    feature: {
                        mark: {show: true},
                        dataView: {show: true, readOnly: false},
                        restore: {show: true},
                        saveAsImage: {show: true}
                    }
                },
                dataZoom: {
                    show: true,
                    start: 70
                },
                legend: {
                    data: userNames
                },
                grid: {
                    y2: 80
                },
                xAxis: [
                    {
                        type: 'time',
                        splitNumber:10
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: seriesOption
            });

        }
        $(document).ready(function() {
            loadRankings();
        });
    </script>
@endpush