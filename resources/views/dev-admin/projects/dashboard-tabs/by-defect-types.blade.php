{{-- TABLE --}}
<div class="tab-pane fade" id="pills-by-defect-types" role="tabpanel" aria-labelledby="pills-by-defect-types-tab">
    <div class="project-card card shadow">
        <div class="card-body border-0 py-3">
            <div class="justify-content-center">
                Number of Defects Per Type
            </div>
            <div class="chart-container">
                <canvas id="defect-types-stat-chart"></canvas>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(function() {
        $('#pills-by-defect-types-tab').on('shown.bs.tab', function (e) {
            console.log("ON: Tab shown -> By Defect Types");
            // INFO: Data loading to be done here - when tab is loaded 
            getDefectTypeStatsAndLoadChart();

            // TODO: Loading overlay
        })

        function getDefectTypeStatsAndLoadChart() {
            getDashboardStatsByDefectTypes(function (results) {
                var chartData = generateChartDataFromByDefectTypesStatsData(results);

                loadChart(chartData);
            })
        }

        // var backgroundColorsByDefectStatus = {
        //     'open': "rgba(255,102,0,1)", 
        //     'wip': "rgba(231,169,0,1)", 
        //     'resolved': "rgba(0,146,37,1)", 
        //     'closed': "rgba(63,103,326,1)", 
        // }

        function generateChartDataFromByDefectTypesStatsData(statsData) {
            var labels = [];
            var datasetsByDefectStatus = [];

            var defectStatuses = statsData.related_data.defect_statuses
            for (var statusTag in defectStatuses) {
                if (Object.prototype.hasOwnProperty.call(defectStatuses, statusTag)) {
                    // var backgroundColor = backgroundColorsByDefectStatus[statusTag];
                    // if(!backgroundColor) {
                    //     // Get random color
                    // }

                    datasetsByDefectStatus.push({
                        label: defectStatuses[statusTag],
                        tag: statusTag,
                        maxBarThickness: 30,
                        // backgroundColor: backgroundColor,
                        data: [],
                    });
                }
            }

            for (defectTypeEntry of statsData.data) {
                labels.push(defectTypeEntry.defect_type.title);
                for (dataset of datasetsByDefectStatus) {
                    var count = 0;
                    for (defectStatsEntry of defectTypeEntry.defect_stats) {
                        if (dataset.tag === defectStatsEntry.status) {
                            count = defectStatsEntry.count;
                            break;
                        } 
                    }
                    dataset.data.push(count);
                }
            }

            return {
                labels: labels,
                datasets: datasetsByDefectStatus
            }
        }

        function loadChart(defectTypesChartData) {
            var barOptions_stacked = {
                maintainAspectRatio: false,
                responsive: true,
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
                            // fontFamily: "'Open Sans Bold', sans-serif",
                            fontSize:11
                        },
                        stacked: true,
                    }],
                    yAxes: [{
                        ticks: {
                            // fontFamily: "'Open Sans Bold', sans-serif",
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

            var ctx = document.getElementById("defect-types-stat-chart");
            var defectTypesStatChart = new Chart(ctx, {
                type: 'horizontalBar',
                data: defectTypesChartData,
                options: barOptions_stacked,
            });
        }
    })

    // SECTION: API
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function getDashboardStatsByDefectTypes(onSuccess) {
        var getDashboardStatsByDefectTypesRoute = "{{ route('dev-admin.projects.dashboard.stats.by-defect-types.ajax.get', ['proj_id' => $proj_id ]) }}";
        $.ajax({
            url: getDashboardStatsByDefectTypesRoute,
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
</script>
@endpush