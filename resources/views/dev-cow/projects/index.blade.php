@extends('layouts.app', ['title' => __('Project Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])
@push('scripts')
@endpush
<div id="projects-page" class="container-fluid mt--7">
    <div class="row justify-content-md-center">
        <div class="col col-lg-11">
            <div class="card shadow ">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Projects') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div id="alert-container">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @elseif(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                {{-- <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul> --}}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <div id="project-search-filter" class="project-search-filter">
                                    <label>Search:
                                    <input id="project-search-input" type="search" class="project-search-input" placeholder="" aria-controls="clerk-of-work">
                                </label>
                            </div>
                        </div>
                        <div class="text-right pr-3">
                            <a href="{{ route("dev-cow.projects.export") }}" class="btn btn-sm btn-primary"><i class="fas fa-file-excel fa-fw"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="project-list card-deck"></div>
                <div class="row align-items-center">
                    <div class="col">
                            <div id="project-entries-info" role="status" aria-live="polite"></div>
                    </div>
                    <div class="text-right pr-3">
                        <div id="project-pagination" class=" paging_simple_numbers">
                            <a id="project-previous-page" class="project-previous-page paginate_button" onclick="prevPage()" ><span class="fas fa-angle-left"></span></a>
                            <span><a id="project-current-page" class="paginate_button project-current-page">1</a></span>
                            <a id="project-next-page" class="project-next-page paginate_button enabled" onclick="nextPage()" ><span class="fas fa-angle-right"></span></a>
                        </div> 
                    </div>
                </div>
                
                  
            </div>
        </div>
    </div>
    
    @include('layouts.footers.auth')
</div>

<div id="templates" hidden="true">
    <div class="project-card">
        <a class="project-page-link" href="">
            <img class="card-img-top" height="156" src="" onerror="this.onerror=null; this.src='{{ asset('images/project-default-image.png') }}'">
        </a>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h4 id="project-title" class="project-title"></h5>
                </div>
                <div class="text-right">
                    <div class="dropdown">
                        <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                            <a id="project-assign-dev" href="" class="dropdown-item project-assign-dev">Assign Dev Admin</a>
                            <a id="project-assign-cow" href="" class="dropdown-item project-assign-cow">Assign COW</a>
                            <a id="project-unit-types" href="" class="dropdown-item project-unit-types">Unit Types</a>
                            <a id="project-units" href="" class="dropdown-item project-units">Units</a>
                            <a id="project-cases" href="" class="dropdown-item project-cases">Cases</a>
                            <a id="project-calendar" href="" class="dropdown-item project-calendar">Calendar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(function() {
        updateProjectsList();

        $("#project-search-input").on("input", function(e){
            // Print entered value in a div box
            onSearchInput();
        });
    });

    var searchInputTimeoutHandler;
    function onSearchInput() {
        if(searchInputTimeoutHandler == null) {
            searchInputTimeoutHandler = setTimeout(function () {
                updateProjectsList();
                searchInputTimeoutHandler = null;
            }, 300);
        }
    }
    
    var page = 1;
    var pageSize = 12;
    var totalPages = 1;
    function updateProjectsList() {
        var searchInputVal = $("#project-search-input").val();
        var options = {
            start: (page-1) * pageSize,
            length: pageSize,
            columns: [
                {
                    data: 'name',
                    name: 'name',
                },
            ]
        }
        if(searchInputVal !== ""){
            options.search = {
                value: searchInputVal,
                regex: false
            };
        }
        getProjectsDt(options, function (results) {
            totalPages = Math.ceil(results.recordsTotal / pageSize);

            // Update Project Cards
            var projectListEl = $('.project-list');
            projectListEl.empty();
            for(project of results.data) {
                var newProjectEl = $('#templates .project-card').clone();
                var projectRoute = getUrlForProjectRoutes(project.id);
                newProjectEl.find('.card-img-top').attr('src', projectRoute.imageUrl);
                newProjectEl.find('.project-page-link').attr('href', projectRoute.projectPageUrl);
                newProjectEl.find('.project-title').text(project.name);
                newProjectEl.find('.project-assign-dev').attr('href', projectRoute.assignProjectDevAdminsUrl);
                newProjectEl.find('.project-assign-cow').attr('href', projectRoute.assignProjectClerkOfWorksUrl);
                newProjectEl.find('.project-cases').attr('href', projectRoute.casesUrl);
                newProjectEl.find('.project-unit-types').attr('href', projectRoute.unitTypeUrl);
                newProjectEl.find('.project-units').attr('href', projectRoute.unitUrl);
                newProjectEl.find('.project-calendar').attr('href', projectRoute.calendarUrl);
                projectListEl.append(newProjectEl);
            }

            // Update current page text in pagination
            var projectPaginationEl = $('#project-pagination');
            projectPaginationEl.find('.project-current-page').text(page);
            
            if(page == 1) {
                disablePrevBtn();
            } else {
                enablePrevBtn();
            }

            if(page == totalPages) {
                disableNextBtn();
            } else {
                enableNextBtn();
            }
            
            // Update Project Entries Info
            firstEntry = (page-1) * pageSize + 1;
            finalEntry = firstEntry + results.data.length - 1;

            // Check if card list is filtered by user
            if(searchInputVal !== ""){
                updateProjectEntries(firstEntry, finalEntry, results.recordsTotal, results.recordsFiltered);
            }
            else{
                updateProjectEntries(firstEntry, finalEntry, results.recordsTotal, '');
            }
        });
    }

    function updateProjectEntries(firstEntry, finalEntry, totalEntries, totalFiltered){
        if(totalFiltered !== ''){
            $("#project-entries-info").html(`Showing ${firstEntry} to ${finalEntry} of ${totalFiltered} entries (filtered from ${totalEntries} total entries)`);
        } else{
            $("#project-entries-info").html(`Showing ${firstEntry} to ${finalEntry} of ${totalEntries} entries`)
        }
    }

    function nextPage(){
        if(page == totalPages) {
            return;
        }
        page = page + 1;
        updateProjectsList();
    }

    function prevPage(){
        if(page == 1) {
            return;
        }
        page = page - 1;
        updateProjectsList();
        
    };

    function enablePrevBtn(){
        $("#project-previous-page").addClass("enabled");
    }

    function disablePrevBtn(){
        $("#project-previous-page").removeClass("enabled");
    };

    function enableNextBtn(){
        $("#project-next-page").addClass("enabled");
    }

    function disableNextBtn(){
        $("#project-next-page").removeClass("enabled");
    };

    function showAlert(message) {
        $('#alert-container').html(`<div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>`)
    }

    // SECTION: API
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function getProjectsDt(options, onSuccess) {
        var getProjectsRoute = "{{ route('dev-cow.projects.dt') }}";

        var data = {
            _token: '{{ csrf_token() }}',
            start: options.start,
            length: options.length,
            columns: options.columns,
            draw: options.draw,
            search: options.search
        };

        $.ajax({
            url: getProjectsRoute,
            type: 'GET',
            data: data,
            success: function(projects) {
                onSuccess(projects)
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

    var getUrlForProjectImageRouteTemplate = "{{ route('dev-cow.projects.logo', ['proj_id' => '<<proj_id>>']) }}";
    var getUrlForProjectPage = "{{ route('dev-cow.projects.dashboard', ['proj_id' => '<<proj_id>>']) }}";
    var getUrlForAssignDevRouteTemplate = "{{ route('dev-cow.projects.dev-admins.index', ['proj_id' => '<<proj_id>>']) }}";
    var getUrlForAssignClerkOfWorksRouteTemplate = "{{ route('dev-cow.projects.dev-cows.index', ['proj_id' => '<<proj_id>>']) }}";
    var getUrlForCasesRouteTemplate = "{{ route('dev-cow.projects.cases.index', ['proj_id' => '<<proj_id>>']) }}";
    var getUrlForUnitTypesRouteTemplate = "{{ route('dev-cow.projects.unit-types.index', ['proj_id' => '<<proj_id>>']) }}";
    var getUrlForUnitsRouteTemplate = "{{ route('dev-cow.projects.units.index', ['proj_id' => '<<proj_id>>']) }}";
    var getUrlForCalendarRouteTemplate = "{{ route('dev-cow.projects.calendar', ['proj_id' => '<<proj_id>>']) }}";


    function getUrlForProjectRoutes(proj_id) {
        var imageUrl = getUrlForProjectImageRouteTemplate.replace(encodeURI('<<proj_id>>'), proj_id);
        var projectPageUrl = getUrlForProjectPage.replace(encodeURI('<<proj_id>>'), proj_id);
        var assignProjectDevAdminsUrl = getUrlForAssignDevRouteTemplate.replace(encodeURI('<<proj_id>>'), proj_id);
        var assignProjectClerkOfWorksUrl = getUrlForAssignClerkOfWorksRouteTemplate.replace(encodeURI('<<proj_id>>'), proj_id);
        var casesUrl = getUrlForCasesRouteTemplate.replace(encodeURI('<<proj_id>>'), proj_id);
        var unitTypeUrl = getUrlForUnitTypesRouteTemplate.replace(encodeURI('<<proj_id>>'), proj_id);
        var unitUrl = getUrlForUnitsRouteTemplate.replace(encodeURI('<<proj_id>>'), proj_id);
        var calendarUrl = getUrlForCalendarRouteTemplate.replace(encodeURI('<<proj_id>>'), proj_id);

        var projectRoute = {
                    'imageUrl' : imageUrl,
                    'projectPageUrl': projectPageUrl,
                    'assignProjectDevAdminsUrl' : assignProjectDevAdminsUrl,
                    'assignProjectClerkOfWorksUrl' : assignProjectClerkOfWorksUrl,
                    'casesUrl' : casesUrl,
                    'unitTypeUrl' : unitTypeUrl,
                    'unitUrl': unitUrl,
                    'calendarUrl': calendarUrl,
                }

        return projectRoute;
    }
</script>
@endpush