@extends('layouts.app', ['title' => __('Defects Management')])
@section('content')
@include('users.partials.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Defects') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="calendar-defects" class="calendar-defects">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    {{-- </div> --}}

</div>
@include('layouts.footers.auth')
@endsection

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
{{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" /> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"
    integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>

<script>
    $(function(){

            initCalendar();        

    });
    function initCalendar() {
            
            $('#calendar-defects').fullCalendar({
                themeSystem: 'bootstrap4',
                header: {
                  left: 'prev,next today',
                  center: 'title',
                  right: 'month,agendaWeek,agendaDay,listMonth'
                },
                weekNumbers: false,
                eventLimit: true, // allow "more" link when too many events
                eventSources: [{
                    url: "{{ route('dev-admin.projects.ajax.calendar.data.get',['proj_id' => $proj_id])}}",
                    color: '#CCCCCC',
                    textColor: 'black'
                }],

                eventRender: function (event, element) {
                    var case_id = event.case_id;
                    var defect_id = event.id;
                let projectDefects = '{{ route('dev-admin.projects.cases.view', ['proj_id' => $proj_id, 'id' => '<<case_id>>']) }}';
                    
                function defectListRoute() {
                    
                return projectDefects.replace(encodeURI('<<case_id>>'), case_id);
                };


                var caseDefectUrl = defectListRoute(case_id) + '?defect_id=' + defect_id;


                element.attr('href', caseDefectUrl);


                element.click(function() {
                        event_start = new Date(event.start);
                        
                        
                    });
                }, 
                eventClick: function(event) {
                     if (event.url) {
                        window.open(event.url, 'new window', 'width=700,height=600');
                    return false;
                    }
                },
                eventAfterAllRender: function (view) {
                    $('#calendar-defects .progress').attr('hidden', true);
                }
              });

        };
       
      </script>
@endpush