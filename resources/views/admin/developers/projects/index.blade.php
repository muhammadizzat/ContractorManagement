@extends('layouts.app', ['title' => __('Project Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div id="projects-page" class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Projects') }}</h3>
                        </div>
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
                            <a href="{{ route("admin.developers.projects.add", $dev_id) }}" class="btn btn-sm btn-primary"><i class="fas fa-plus-circle fa-fw"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
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
        <img class="card-img-top" height="156" src="" onerror="this.onerror=null; this.src='{{ asset('images/project-default-image.png') }}'">
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
                            <a id="project-edit" href="" class="dropdown-item project-edit">Edit</a>
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
                var projectRoute = getUrlForProjectRoutes(project.id, project.developer_id);
                newProjectEl.find('.card-img-top').attr('src', projectRoute.imageUrl);
                newProjectEl.find('.project-title').text(project.name);
                newProjectEl.find('.project-edit').attr('href', projectRoute.editUrl);
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
            
            //Update Project Entries Info
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
        var getProjectsRoute = "{{ route('admin.developers.projects.dt', ['dev_id' => $dev_id]) }}";

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

    var getUrlForProjectImageRouteTemplate = "{{ route('admin.developers.projects.logo', ['id' => '<<proj_id>>', 'dev_id' => '<<dev_id>>']) }}";
    var getUrlForEditRouteTemplate = "{{ route('admin.developers.projects.edit', ['id' => '<<proj_id>>', 'dev_id' => '<<dev_id>>']) }}";


    function getUrlForProjectRoutes(proj_id, dev_id) {
        
        var imageUrl = getUrlForProjectImageRouteTemplate.replace(encodeURI('<<proj_id>>'), proj_id);
        var imageUrl = imageUrl.replace(encodeURI('<<dev_id>>'), dev_id);
        
        var editUrl = getUrlForEditRouteTemplate.replace(encodeURI('<<proj_id>>'), proj_id);
        var editUrl = editUrl.replace(encodeURI('<<dev_id>>'), dev_id);

        var projectRoute = {
                    'imageUrl' : imageUrl,
                    'editUrl': editUrl,
                }

        return projectRoute;
    }

    function showAlert(message) {
        $('#alert-container').html(`<div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>`)
    }
    
</script>
@endpush