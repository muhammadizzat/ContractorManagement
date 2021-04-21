{{-- TABLE --}}
<div class="tab-pane fade" id="pills-by-tags" role="tabpanel" aria-labelledby="pills-by-tags-tab">
    <div class="project-card card shadow">
        <div class="card-body border-0 py-3">
            <div class="project-info-section">
                <div class="project-info-header row">
                    <div class="col">
                        <h2 class="project-view-type">
                            By Tags
                        </h2>
                    </div>
                </div>
                <div class="project-header">
                    <h3 class="project-sub-header">
                        Cases
                    </h3>
                </div>
                <hr class="mt-2 mb-2" />
                <div class="chart-container">
                    <canvas id="cases-tags-stat-chart"></canvas>
                </div>
                <div class="project-header">
                    <h3 class="project-sub-header">
                        Defects
                    </h3>
                </div>
                <hr class="mt-2 mb-2" />
                <div class="chart-container">
                    <canvas id="defects-tags-stat-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(function() {
        $('#pills-by-tags-tab').on('shown.bs.tab', function (e) {
            console.log("ON: Tab shown -> By Tags");

            getTagsStatsAndLoadChart();
        })

        function getTagsStatsAndLoadChart(){
            getDashboardStatsByTags(function (results){
                var caseTagsChartData = generateCaseTagsCharDataFromByTagsStatsData(results);
                var defectTagsChartData = generateDefectTagsCharDataFromByTagsStatsData(results);

                loadCaseTagsChart(caseTagsChartData);
                loadDefectTagsChart(defectTagsChartData);
            })
        }

        function generateCaseTagsCharDataFromByTagsStatsData(statsData) {
            var labels = [];
            var datasetsDataByCaseTags = [];

            datasetsDataByCaseTags.push({
                label: "Case Tags",
                minBarThickness: 30,
                maxBarThickness: 30,
                data: [],
            });

            var caseTags = statsData.data.cases_tags_data;

            for (caseTag of caseTags) {
                labels.push(caseTag.case_tag);
                for (dataset of datasetsDataByCaseTags) {
                    dataset.data.push(caseTag.case_tag_count);
                }
            }
            return {
                labels: labels,
                datasets: datasetsDataByCaseTags
            }
        }

        function generateDefectTagsCharDataFromByTagsStatsData(statsData) {
            var labels = [];
            var datasetsDataByDefectTags = [];

            datasetsDataByDefectTags.push({
                label: "Defect Tags",
                minBarThickness: 30,
                maxBarThickness: 30,
                data: [],
            });

            var defectTags = statsData.data.defects_tags_data;
            
            for (defectTag of defectTags) {
                labels.push(defectTag.defect_tag);
                for (dataset of datasetsDataByDefectTags) {
                    dataset.data.push(defectTag.defect_tag_count);
                }
            }
            return {
                labels: labels,
                datasets: datasetsDataByDefectTags
            }
        }

        function loadCaseTagsChart(caseTagsChartData) {
            var barOptions = {
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
                            // fontFamily: "'Open Sans Bold', sans-serif",
                            fontSize:11
                        },
                    }],
                    yAxes: [{
                        ticks: {
                            // fontFamily: "'Open Sans Bold', sans-serif",
                            fontSize:11
                        },
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

            var ctx = document.getElementById('cases-tags-stat-chart');
            var casesTagsStatChart = new Chart(ctx, {
                type: 'horizontalBar',
                data: caseTagsChartData,
                options: barOptions,
            })
        }

        function loadDefectTagsChart(defectTagsChartData) {
            var barOptions = {
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
                            // fontFamily: "'Open Sans Bold', sans-serif",
                            fontSize:11
                        },
                    }],
                    yAxes: [{
                        ticks: {
                            // fontFamily: "'Open Sans Bold', sans-serif",
                            fontSize:11
                        },
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

            var ctx = document.getElementById('defects-tags-stat-chart');
            var defectsTagsStatChart = new Chart(ctx, {
                type: 'horizontalBar',
                data: defectTagsChartData,
                options: barOptions,
            })
        }

        // SECTION: API
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        function getDashboardStatsByTags(onSuccess) {
            var getDashboardStatsByTagsRoute = "{{ route('dev-admin.projects.dashboard.stats.by-tags.ajax.get', ['proj_id' => $proj_id ]) }}";
            $.ajax({
                url: getDashboardStatsByTagsRoute,
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