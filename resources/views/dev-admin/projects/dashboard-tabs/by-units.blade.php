{{-- TABLE --}}
<div class="tab-pane fade" id="pills-by-units" role="tabpanel" aria-labelledby="pills-by-units-tab">
    <div class="project-card card shadow">
        <div class="card-body border-0 py-3">
            <div class="justify-content-center">
                <div class="row align-items-center">
                    {{-- <div class="number-stat col-6" style="color: red !important;">
                        <div class="title">
                            Avg. Response Time in the Past 30 Days
                        </div>
                        <div class="value">
                            -
                        </div>
                    </div> --}}
                    <div class="number-stat col-6">
                        <div class="title">
                            Total Units with Defects
                        </div>
                        <div id="total-units-defect-percentage" class="value">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(function() {
        $('#pills-by-units-tab').on('shown.bs.tab', function (e) {
            getUnitsStatsAndPopulateData();
        })

        function getUnitsStatsAndPopulateData(){
            getDashboardStatsByUnits(function (results){
                getUnitsStatsDefectsPercentages(results);
            })
        }

        function getUnitsStatsDefectsPercentages(statsData){
            totalUnits = statsData.total_unit;
            totalUnitsWithDefects = statsData.total_unit_with_defects
            totalUnitsWithDefectsPercentages = ((totalUnitsWithDefects/totalUnits) * 100).toFixed(0);
            if(totalUnits != 0 ){
                $("#total-units-defect-percentage").text(`${totalUnitsWithDefects} (${totalUnitsWithDefectsPercentages}%)`);
            }else{
                totalUnitsWithDefectsPercentages = 0;
                $("#total-units-defect-percentage").text(`${totalUnitsWithDefects} (${totalUnitsWithDefectsPercentages}%)`); 
            
            }   
        }

        // SECTION: API
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        function getDashboardStatsByUnits(onSuccess) {
            var getDashboardStatsByUnitsRoute = "{{ route('dev-admin.projects.dashboard.stats.by-units.ajax.get', ['proj_id' => $proj_id ]) }}";
            $.ajax({
                url: getDashboardStatsByUnitsRoute,
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