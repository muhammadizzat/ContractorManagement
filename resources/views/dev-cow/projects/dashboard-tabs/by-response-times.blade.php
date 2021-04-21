{{-- TABLE --}}
<div class="tab-pane fade" id="pills-by-response-times" role="tabpanel" aria-labelledby="pills-by-response-times-tab">
    <div class="project-card card shadow">
        <div class="card-body border-0 py-3">
            <div class="justify-content-center">
                <div class="col">
                    <h2 class="project-view-type">
                        By Defect Average Response Time
                    </h2>
                    <div class="project-header">
                        <h3 class="project-sub-header">
                        Percentage Completed Within 15 Days
                        </h3>
                    </div>
                    <div id="percentage-defects-closed-within-15-days" class="value">-</div>
                    <div class="chart-container">
                        <canvas id="defect-stat-chart"></canvas>
                    </div>
                </div>

                <div class="col">
                    <h2 class="project-view-type">
                        By Case Average Response Time
                    </h2>
                    <div class="project-header">
                        <h3 class="project-sub-header">
                        Percentage Closed Within 30 Days
                        </h3>
                    </div>
                    <div id="percentage-cases-closed-within-30-days" class="value">
                    -
                    </div>
                    <div class="chart-container">
                        <canvas id="case-percent-stat-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $(function() {
        $('#pills-by-response-times-tab').on('shown.bs.tab', function (e) {
            getStatsAndLoadChartsAndStats();
        })

        function getStatsAndLoadChartsAndStats() {
            getDashboardStatsByResponseTimes(function(statsData) {
                calculateAndShowCaseStats(statsData);
                calculateAndShowDefectStats(statsData);
                populateAndShowCasesProgressAvgDaysChart(statsData);
                populateAndShowDefectsProgressAvgDaysChart(statsData);
            })
        }

        function calculateAndShowCaseStats(statsData) {
            var casesClosedWithinDaysStats = statsData.data.cases_closed_within_days;
            for(stat of casesClosedWithinDaysStats) {
                if(statsData.data.total_cases != 0 ){
                    if(stat.count != 0) {
                        $("#percentage-cases-closed-within-30-days").text("(" + (stat.count/statsData.data.total_cases *100).toFixed(2) + "%)");
                    }else{
                        $("#percentage-cases-closed-within-30-days").text("(" + (stat.count) + "%)");
                    }                
                }
            }
        }

        function calculateAndShowDefectStats(statsData) {
            var defectsClosedWithinDaysStats = statsData.data.defects_open_to_closed_within_days;
            for(stat of defectsClosedWithinDaysStats) {
                if(stat.days == 15) {
                    if(statsData.data.total_defects != 0 ){
                        $("#percentage-defects-closed-within-15-days").text("(" + (stat.count/statsData.data.total_defects *100).toFixed(2) + "%)");
                    }else{
                        statsData.data.total_defects = 0;
                        $("#percentage-defects-closed-within-15-days").text("(" + (statsData.data.total_defects) + "%)");
                    }
                }
            }
        }

        function populateAndShowDefectsProgressAvgDaysChart(statsData) {
            var labels = [];
            var datasetData = [];

            var defectsAvgDaysStats = statsData.data.defects_avg_days;
            for(stat of defectsAvgDaysStats) {
                labels.push(stat.from_status + " to " + stat.to_status);
                datasetData.push(stat.avg_days);
            }

            loadDefectsProgressAvgDaysChart({
                labels: labels,
                datasets: [{
                    data: datasetData,
                    label: 'Average Days',
                }]
            })
        }

        function populateAndShowCasesProgressAvgDaysChart(statsData) {
            var labels = [];
            var datasetData = [];

            var casesAvgDaysStats = statsData.data.cases_avg_days;
            for(stat of casesAvgDaysStats) {
                labels.push(stat.from_status + " to " + stat.to_status);
                datasetData.push(stat.avg_days);
            }

            loadCasesProgressAvgDaysChart({
                labels: labels,
                datasets: [{
                    data: datasetData,
                    label: 'Average Days',
                }]
            })
        }

        function loadDefectsProgressAvgDaysChart(defectPercentChartData) {
            var bar_option_stacked = {
                maintainAspectRatio: false,
                tooltips: {
                    enabled: true
                },
                hover :{
                    animationDuration:0
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero:true,
                            barPercentage: 0.01,
                            fontSize:11
                        },
                        stacked: true,
                    }],
                    yAxes: [{
                        ticks: {
                            fontSize:11
                        },
                        stacked: true
                    }]
                },
                legend:{
                    display:true
                },
                plugins: {
                    colorschemes: {
                        scheme: 'office.BlueRed6'
                    }

                }
            };

            var ctxs = document.getElementById("defect-stat-chart");
            var defectPercentChart = new Chart(ctxs, {
            type: 'horizontalBar',
            data: defectPercentChartData,
            options: bar_option_stacked,
            });
        }

        function loadCasesProgressAvgDaysChart(casePercentChartData) {
            var bar_stacked = {
                maintainAspectRatio: false,
                tooltips: {
                    enabled: true
                },
                hover :{
                    animationDuration:0
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            beginAtZero:true,
                            barPercentage: 0.01,
                            fontSize:11
                        },
                        stacked: true,
                    }],
                    yAxes: [{
                        ticks: {
                            fontSize:11
                        },
                        stacked: true
                    }]
                },
                legend:{
                    display:true
                },
                plugins: {
                    colorschemes: {
                        scheme: 'office.BlueRed6'
                    }

                }
            };

            var ctx = document.getElementById("case-percent-stat-chart");
            var casePercentStatChart = new Chart(ctx, {
            type: 'horizontalBar',
            data: casePercentChartData,
            options: bar_stacked,
            });
        }

        function getDashboardStatsByResponseTimes(onSuccess) {
            var getDashboardStatsByResponseTimesRoute = "{{ route('dev-cow.projects.dashboard.stats.by-response-times.ajax.get', ['proj_id' => $proj_id ]) }}";
            $.ajax({
                url: getDashboardStatsByResponseTimesRoute,
                type: 'GET',
                data: data = {
                _token: '{{ csrf_token() }}',
            },
            success: function(results) {
                onSuccess(results)
            },
            error: function(xhr) {
                if(xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    console.log("Error 422: ", xhr);
                }
                console.log("Error: ", xhr);
            }
        });
    }

    })
</script>

@endpush