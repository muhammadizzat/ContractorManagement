{{-- TABLE --}}
<div class="tab-pane fade" id="pills-by-defects" role="tabpanel" aria-labelledby="pills-by-defects-tab">
    <div class="tab-content-card card shadow">
        <div class="card-body border-0 py-3">
            <div class="project-info-section">
                <div class="project-info-header row">
                    <div class="col">
                        <h2 class="project-view-type">
                            By Defect
                        </h2>
                        <div class="chart-container">
                            <canvas id="hollow-pie-chart" class="justify-content-center"> </canvas>
                        </div>
                    </div>
                </div>
                <div class="project-header">
                    <h3 class="project-sub-header">
                        Actions
                    </h3>
                </div>
                <hr class="mt-2 mb-2" />
                <div id="actions-items-list" class="row">
                </div>
                <div class="project-header">
                    <h3 class="project-sub-header">
                        Project Tracking
                    </h3>
                </div>
                <hr class="mt-2 mb-2" />
                <div class="row">
                    <div class="project-info-item">
                        <div class="info-name">Defects &lt; 7 Days</div>
                        <div id="defect-0-to-6-count" class="info-value text-warning">
                            -
                        </div>
                    </div>
                    <div class="project-info-item">
                        <div class="info-name">
                            Defects 7-14 Days
                        </div>
                        <div id="defect-7-to-14-count" class="info-value text-warning">
                            -
                        </div>
                    </div>
                    <div class="project-info-item">
                        <div class="info-name">
                            Defects 15-22 Days
                        </div>
                        <div id="defect-15-to-22-count" class="info-value text-warning">
                            -
                        </div>
                    </div>
                    <div class="project-info-item">
                        <div class="info-name">
                            Defects 23-30 Days
                        </div>
                        <div id="defect-23-to-30-count" class="info-value text-warning">
                            -
                        </div>
                    </div>
                    <div class="project-info-item">
                        <div class="info-name">
                            Defects &gt; 30 Days
                        </div>
                        <div id="defect-31-to-59-count" class="info-value text-danger">
                            -
                        </div>
                    </div>
                    <div class="project-info-item">
                        <div class="info-name">
                            Defects &gt; 60 Days
                        </div>
                        <div id="defect-60-to-89-count" class="info-value text-danger">
                            -
                        </div>
                    </div>
                    <div class="project-info-item">
                        <div class="info-name">
                            Defects &gt; 90 Days
                        </div>
                        <div id="defect-90-to-119-count" class="info-value text-danger">
                            -
                        </div>
                    </div>
                    <div class="project-info-item">
                        <div class="info-name">
                            Defects &gt; 120 Days
                        </div>
                        <div id="defect-120-count" class="info-value text-danger">
                            -
                        </div>
                    </div>
                    <div class="project-info-item">
                        <div class="info-name">
                            Defects Overdue
                        </div>
                        <div id="defect-overdue-count" class="info-value text-danger">
                            -
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="templates" hidden="true">
        <div class="by-defect-info project-info-item">
            <div class="info-name">
                Open Defects
            </div>
            <div class="info-value">
                -
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    $(function() {
        $('#pills-by-defects-tab').on('shown.bs.tab', function (e) {
            console.log("ON: Tab shown -> By Defects");
            getDefectStatsLoadByDefectChartAndPopulateDashboard();


            // INFO: Data loading to be done here - when tab is loaded
            // TODO: Loading overlay

        })

        function getDefects(onSuccess) {
            var getDefectsRoute = "{{ route('dev-cow.projects.dashboard.by-defects.ajax.get', ['project_id' => $proj_id]) }}";
            $.ajax({
                url: getDefectsRoute,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(defects) {
                    onSuccess(defects);
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

        function getDefectStatsLoadByDefectChartAndPopulateDashboard() {
            getDefects(function (results) {
                populateByDefectDashboardData(results);
                
                var chartData = generateChartDataFromByDefectsStatsData(results);

                loadByDefectChart(chartData);
            })
        }

        function generateChartDataFromByDefectsStatsData(statsData) {
            var labels = [];
            var datasetsDataByDefectStatus = [];
            
            var defectStatuses = statsData.related_data.defect_statuses;
            var defectStats = statsData.data.defect_stats;

            for (var statusTag in defectStatuses) {
                if (Object.prototype.hasOwnProperty.call(defectStatuses, statusTag)) {
                    labels.push(defectStatuses[statusTag]);

                    var statusCount = 0;
                    for(defectStat of defectStats) {
                        if(statusTag == defectStat.status) {
                            statusCount = defectStat.count;
                            break;
                        }
                    }
                    datasetsDataByDefectStatus.push(statusCount);
                }
            }

            return {
                labels: labels,
                datasets: [{
                    data: datasetsDataByDefectStatus
                }]
            }
        }

        function populateByDefectDashboardData(defectChartData) {
            var defectStatuses = defectChartData.related_data.defect_statuses;
            var defectStats = defectChartData.data.defect_stats;
                // Actions
                var actionsListEl = $('#actions-items-list');
                actionsListEl.empty();

                for (var statusTag in defectStatuses) {
                    if (Object.prototype.hasOwnProperty.call(defectStatuses, statusTag)) {
                        var newDefectStatusCountEl = $('#pills-by-defects .templates .by-defect-info').clone();
                        newDefectStatusCountEl.find('.info-name').text(defectStatuses[statusTag] + " Defects");
                        var statusCount = 0;
                        for(defectStat of defectStats) {
                            if(statusTag == defectStat.status) {
                                statusCount = defectStat.count;
                                break;
                            }
                        }
                        newDefectStatusCountEl.find('.info-value').text(statusCount);
                        
                        actionsListEl.append(newDefectStatusCountEl);
                    }
                }

                // for (defectStatus of defectStatuses) {
                    
                // }
                    // $('#' + defect.status + '-defects-count').text(defect.count);

                // Project Tracking
                var defectProjectTracking = defectChartData.data.defects_tracking;

                for (defect of defectProjectTracking) {
                    if(defect.from) {
                        $('#defect-' + defect.to + '-to-' + defect.from + '-count').text(defect.count);
                    } else {
                        $('#defect-' + defect.to + '-count').text(defect.count);
                    }
                }
                var defectsOverdueCount = defectChartData.data.defectsOverdue;

                $('#defect-overdue-count').text(defectsOverdueCount);
        }

        function loadByDefectChart(defectChartData) {
            var doughnutOptions = {
                plugins: {
                    colorschemes: {
                        scheme: 'office.BlueRed6'
                    }
                }
            };

            var ctx = document.getElementById("hollow-pie-chart");
            ctx.height = 100;
            var doughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: defectChartData,
                options: doughnutOptions,
            });
        };
    })
</script>
@endpush