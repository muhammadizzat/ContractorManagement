@extends('layouts.app')

@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    <div class="container">
        <div class="header-body">
            <div class="row">
                <div class="col-xl-3 col-lg-6">
                    <div class="card card-stats mb-4 mb-xl-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Pending Defects</h5>
                                    <span class="h2 font-weight-bold mb-0">{{ $pending_defects_count }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-muted text-sm">
                                <span class="text-warning mr-2"><i class="far fa-calendar"></i>
                                    {{ $overdue_defects_count }} overdue defects</span>
                                {{-- <span class="text-nowrap">overdue</span> --}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="page-contractor-home" class="container mt--7">
    <div class="row">
        <div class="col ml-3 p-2 h3 text-white">
            Assigned Defects
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 mb-5">
            <div id="assigned-defects-card" class="card shadow">
                @foreach($projects_defects_summary as $project_summary)
                <div class="project-header card-header py-2" data-toggle="collapse"
                    data-target="#project-{{ $project_summary->project_id }}" aria-expanded="false"
                    aria-controls="project-{{ $project_summary->project_id }}">
                    <div class="d-flex flex-row align-items-center">
                        <div class="flex-fill">
                            <h6 class="card-title text-uppercase text-muted mb-0">{{ $project_summary->developer_name }}
                            </h6>
                            <span class="h3 font-weight-bold mb-0">{{ $project_summary->project_name }}</span>
                        </div>
                        <div class="">
                            <h3 class="mb-0">{{ $project_summary->total_defects }} defects</h3>
                        </div>
                        <div class="pl-4">
                            <i class="fas fa-angle-down"></i>
                        </div>
                    </div>
                </div>
                <div id="project-{{ $project_summary->project_id }}" class="project-body card-body p-0 collapse"
                    data-project-id="{{ $project_summary->project_id }}" data-parent="#assigned-defects-card">
                    <div class="filter-section py-2 px-4">
                        <input class="defects-search" type="text" placeholder="Search"
                            aria-label="Search" oninput="defectsFilter()">
                    </div>
                    <div class="defects-list p-2">

                    </div>
                </div>
                @endforeach
                {{-- Next --}}
                {{-- <div class="project-header card-header py-2" data-toggle="collapse" data-target="#project-2"
                    aria-expanded="false" aria-controls="project-2">
                    <div class="d-flex flex-row align-items-center">
                        <div class="flex-fill">
                            <h6 class="card-title text-uppercase text-muted mb-0">Sunway Group</h6>
                            <span class="h3 font-weight-bold mb-0">Pyramid Residences</span>
                        </div>
                        <div class="">
                            <h3 class="mb-0">4 defects</h3>
                        </div>
                        <div class="pl-4">
                            <i class="fas fa-angle-down"></i>
                        </div>
                    </div>
                </div>
                <div id="project-2" class="project-body card-body p-0 collapse">
                    <div class="filter-section py-2 px-4">
                        <div class="text-muted">Search</div>
                    </div>
                    <div class="defects-list p-2">
                        <div class="defect-card card shadow mb-1">
                            <div class="card-body py-2 pl-2">
                                <div class="d-flex flex-row">
                                    <div class="defect-ref-no-section pr-2">
                                        <span class="defect-ref-no badge badge-warning">C1-D1</span>
                                    </div>
                                    <div class="defect-info flex-fill">
                                        <div class="defect-title">Toilets not working</div>
                                        <div class="defect-assigned-contractor">Contractor:
                                            <span class="contractor-name">Justin</span>
                                        </div>
                                    </div>
                                    <div class="defect-type px-4">
                                        <span class="badge badge-primary">Plumbing</span>
                                    </div>
                                    <div class="defect-status-section">
                                        <div class="defect-status text-right">Abc</div>
                                        <div class="defect-due-date text-right"><i class="far fa-clock"></i>
                                            <span>09/09/2019</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
<div id="defect-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex flex-row">
                    <div class="defect-info-loading">
                        <div class="loader">Loading...</div>
                    </div>
                    <div class="defect-info-section flex-fill" style="visibility: hidden;">
                        <div class="defect-info-header">
                            <div class="d-inline">
                                <span class="defect-ref-no badge badge-warning">(Ref No)</span>
                            </div>
                            <h4 class="defect-title d-inline mb-0 px-2">
                                (Defect Title)
                            </h4>
                        </div>
                        <div class="defect-info-unit">
                            Unit: <span class="unit-no">UNIT_NO</span><span class="px-4">
                            Owner: <span class="unit-owner-name">OWNER</span>(<span class="unit-owner-no"></span>)</span>
                        </div>
                        <div class="defect-info-item d-flex flex-row">
                            <div class="info-name">
                                Due Date:
                            </div>
                            <div class="info-value flex-fill">
                                <span class="view-due-date-section">
                                    <span class="defect-due-date">

                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="defect-info-item d-flex flex-row">
                            <div class="info-name">
                                Defect Type:
                            </div>
                            <div class="info-value flex-fill">
                                <span id="defect-type-view" class="badge badge-primary"></span>
                            </div>
                        </div>
                        <div class="defect-info-item d-flex flex-row">
                            <div class="info-name">
                                Assigned Contractor:
                            </div>
                            <div class="info-value flex-fill">
                                <button type="button" class="assigned-contractor btn btn-sm btn-secondary">
                                </button>
                            </div>
                        </div>
                        <div class="defect-info-item d-flex flex-row">
                            <div class="info-name">
                                Tags:
                            </div>
                            <div class="info-value flex-fill">
                                <span class="edit-defect-tags-section">
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="defect-tags-input-container">
                                        </div>
                                    </div>
                                </span>
                            </div>
                        </div>
                        <div class="flex-column d-inline-flex d-flex mr-auto">
                            <div class="defect-info-item d-flex flex-row">
                                <div class="info-name">
                                    Submitted Date:
                                </div>
                                <div class="info-value">
                                    <span class="defect-submitted-date">
                                        -
                                    </span>
                                </div>
                            </div>
                            <div class="defect-info-item d-flex flex-row">
                                <div class="info-name">
                                    Resolved Date:
                                </div>
                                <div class="info-value ">
                                    <span class="defect-resolved-date">
                                        -
                                    </span>
                                </div>
                            </div>
                            <div class="defect-info-item d-flex flex-row">
                                <div class="info-name">
                                    Closed Date:
                                </div>
                                <div class="info-value ">
                                    <span class="defect-closed-date">
                                        -
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="defect-status-section pl-3" style="visibility: hidden;">
                        <div class="defect-status-dropdown dropdown">
                            <button class="status-btn btn btn-sm btn-success btn-block dropdown-toggle" type="button"
                                id="defect-status-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                (Status)
                            </button>
                            <div class="dropdown-menu" id="defect-status-list" aria-labelledby="defect-status-btn">
                                @foreach([
                                    App\Constants\DefectStatus::WIP,
                                    App\Constants\DefectStatus::RESOLVED,
                                ] as $status_code)
                                <a class="dropdown-item defect-status" href="#"
                                    data-name="{{ $status_code }}">{{ App\Constants\DefectStatus::$dict[$status_code] }}</a>
                                @endforeach
                                <div class="dropdown-divider"></div>
                                <div class="request-section">
                                    <h6 class="dropdown-header text-primary">Request For</h6>
                                    <a class="dropdown-item request" href="#" data-name="request-closed">Close</a>
                                    <a class="dropdown-item request" href="#" data-name="request-extend">Extend</a>
                                    <a class="dropdown-item request" href="#" data-name="request-reject">Reject</a>
                                </div>
                                <div class="request-form-section px-3 pb-1" data-request-type="" style="display:none;">
                                    <div class="d-flex flex-row align-items-center mb-2">
                                        <h4 class="mb-0 flex-fill">Request: <span class="request-name"></span></h4> <a style="cursor: pointer;" class="cancel-btn px-2">&times;</a>
                                    </div>
                                    <div class="form-group">
                                        {{-- <label for="reason-input">Reason</label> --}}
                                        <input type="text" class="reason-input form-control form-control-sm" placeholder="Please enter reason">
                                    </div>
                                    <button type="button" class="submit-btn btn btn-sm btn-primary">Submit Request</button>
                                </div>
                            </div>
                            {{-- <div class="defect-status dropdown">
                                <button class="status-btn btn btn-sm btn-success btn-block dropdown-toggle" type="button"
                                    id="defect-status-btn" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    (Status)
                                </button>
                                
                            </div> --}}
                            {{-- <div class="dropdown-menu" id="defect-status-list" aria-labelledby="defect-status-btn">
                                @foreach(App\Constants\DefectStatus::$dict as $status_code => $status_name)
                                <a class="dropdown-item defect-status" href="#"
                                    data-name="{{ $status_code }}">{{ $status_name }}</a>
                            @endforeach
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="tabs-header modal-body px-0 py-0 text-white">
            <ul class="nav nav-tabs mb-0" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-activity-tab" data-toggle="pill" href="#pills-activity"
                        role="tab" aria-controls="pills-activity" aria-selected="true">Activity</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-description-tab" data-toggle="pill" href="#pills-description"
                        role="tab" aria-controls="pills-description" aria-selected="false">Description</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-location-tab" data-toggle="pill" href="#pills-location" role="tab"
                        aria-controls="pills-location" aria-selected="false">Location</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-images-tab" data-toggle="pill" href="#pills-images" role="tab"
                        aria-controls="pills-images" aria-selected="false">Images</a>
                </li>
            </ul>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="pills-activity" role="tabpanel"
                aria-labelledby="pills-activity-tab">
                <div class="activity-add-comment-section modal-body p-1 pt-2">
                    <div class="commenter-name pl-2">
                        <h5 class="commenter-name card-title mb-0">
                            {{ auth()->user()->name }} <span
                                class="badge badge-warning">{{ auth()->user()->roles[0]->name}}</span>
                        </h5>
                    </div>
                    <div class="comment-images-input" style="display: none;">
                        <div class="d-flex flex-row flex-wrap px-2 pt-2">
                            <button class="add-image-btn btn mr-2">
                                <i class="fas fa-plus"></i>
                            </button>
                            <input id="comment-image-file-input" type="file">
                        </div>
                    </div>

                    <div class="d-flex flex-row">
                        <div class="p-2 flex-grow-1">
                            <textarea class="form-control text-default" id="comment-textarea" rows="2"></textarea>
                        </div>
                        <div class="p-2 buttons-container">
                            <button class="images-btn btn btn-sm btn-icon btn-2 btn-secondary" type="button">
                                <span class="btn-inner--icon"><i class="ni ni-image"></i></span>
                            </button>
                        </div>
                        <div class="p-2 add-comment-btn"><button type="button"
                                class="btn btn-sm btn-primary">Comment</button></div>
                    </div>
                </div>
                <div class="modal-body p-2 activity-list-loading">
                    <div class="loader">Loading...</div>
                </div>
                <div class="activity-list modal-body p-2" style="visibility: hidden;">
                    {{-- Card with images --}}
                </div>
            </div>
            <div class="tab-pane fade" id="pills-description" role="tabpanel" aria-labelledby="pills-description-tab">
                <div class="p-2">
                    <div id="description-editor"></div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-location" role="tabpanel" aria-labelledby="pills-location-tab">
                <div id="floor-plan-menu" class="d-flex flex-row align-items-center m-2">
                    <div class="flex-fill">
                        <span class="floor-view">Floor: <strong class="floor-name">(Floor Name)</strong></span>
                    </div>
                </div>
                <div id="floor-plan-control-panel" class="d-flex pb-2">
                    <div id="floor-plan-pins-menu" class="flex-shrink-0 mx-2 p-2">
                        <div class="h4">Pins</div>
                        <div class="pin-list pb-2">
                        </div>

                        <button class="add-pin-btn btn btn-sm" style="display: none;">Add Pin</button>
                    </div>
                    <div id="floor-plan-img-container" class="mb-2 mx-2" style="display: inline-block">
                        <img id="floor-plan-image" src="" alt="">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-images" role="tabpanel" aria-labelledby="pills-images-tab">
                <div id="defect-images-container" class="m-2 row">
                    <div class="images-flex-container d-flex flex-row flex-wrap">
                        {{-- Insert defect images here --}}

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
<!-- Activity Image Modal -->
<div id="activity-image-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Activity Image</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2">
                <img class="activity-image" src="" alt="">
            </div>
        </div>
    </div>
</div>

<!-- Defect Image Modal -->
<div id="defect-image-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Defect Image</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2">
                <img class="defect-image" src="" alt="">
            </div>
        </div>
    </div>
</div>
<div id="templates" hidden="true">
    <div class="defect-card card shadow mb-1">
        <div class="card-body py-2 pl-2" data-toggle="modal" data-target="#defect-modal" data-defect-id="">
            <div class="d-flex flex-row">
                <div class="defect-ref-no-section pr-2">
                    <span class="defect-ref-no badge badge-warning"></span>
                </div>
                <div class="defect-info flex-fill">
                    <div class="defect-title"></div>
                    <div class="defect-case">Case:
                        <span class="case-title"></span>
                    </div>
                </div>
                <div class="defect-type px-4">
                    <span class="badge badge-primary"></span>
                </div>
                <div class="defect-status-section">
                    <div class="defect-status text-right"></div>
                    <div class="defect-due-date text-right"><i class="far fa-clock"></i>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="activity-comment card shadow mb-2">
        <div class="row no-gutters">
            <div class="commenter-profile-pic col-md-1 mt-3 text-center d-none d-lg-block">
                <img class="img-thumbnail rounded-circle p-0"
                    src="{{ asset('argon/img/theme/profile-pic-placeholder.png') }}">
            </div>
            <div class="col-md-11">
                <div class="card-body p-3">
                    <h5 class="commenter card-title mb-1">
                        <span class="commenter-name">(Commenter)</span> <span
                            class="commenter-role badge badge-primary">(Role)</span>
                        <span class="float-right"> <small class="comment-date-time text-muted">(Date
                                Time)</small></span>
                    </h5>
                    {{-- Insert .comment-images here --}}
                    <p class="comment card-text">(Comment)</p>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-row flex-wrap comment-images pt-2">
        {{-- Insert .view-comment-img-holder(s) here --}}
    </div>
    <div class="view-comment-img-holder pr-2 pb-2">
        <div class="img-options-overlay">
            <div class="options-container d-flex flex-column align-items-center justify-content-around">
                <div>
                    <button class="view-btn option-btn"><i class="fas fa-expand"></i></button>
                </div>
            </div>
        </div>
        <img src="https://via.placeholder.com/300x150" alt="">
    </div>
    <div class="activity-update">
        <div class="d-flex flex-row py-2 px-3">
            <div class="updater-user-name mr-4">
                <h5>
                    (User Name)
                </h5>
            </div>
            <div class="update flex-grow-1">
                (Update)
            </div>
            <div class="update-date-time">
                <h5><small class="text-muted">(Date)</small></h5>
            </div>
        </div>
    </div>
    <div class="defect-img-holder mr-2">
        <div class="img-options-overlay">
            <div class="options-container d-flex flex-column align-items-center justify-content-around">
                <div>
                    <button class="view-btn option-btn"><i class="fas fa-expand"></i></button>
                </div>
            </div>
        </div>
        <img src="" alt="">
    </div>
    <button class="add-image-btn btn mr-2">
        <i class="fas fa-plus"></i>
    </button>
    <div class="comment-img-holder mr-2">
        <div class="img-options-overlay">
            <div class="options-container d-flex flex-column align-items-center justify-content-around">
                <div>
                    <button class="delete-btn option-btn"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        </div>
        <img src="" alt="">
    </div>
    <div class="pin-entry d-flex align-items-center my-1 p-1">
        <span class="pin-no"></span>
        <span class="pin-label pl-1 flex-fill">
            <span class="view"></span>
        </span>
    </div>
    <div class="activity-request card shadow mb-2">
        <div class="row no-gutters">
            <div class="requester-icon-section col-md-1 text-center d-flex align-items-center justify-content-center">
                {{-- <img class="img-thumbnail rounded-circle p-0"
                    src="{{ asset('argon/img/theme/profile-pic-placeholder.png') }}"> --}}
                    <div class="requester-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-edit"></i>
                    </div>
            </div>
            <div class="col-md-11">
                <div class="card-body p-3">
                    <h5 class="requester card-title mb-1">
                        <span class="requester-name">(Requester)</span> <span
                            class="requester-role badge badge-primary">(Role)</span>
                        <span class="float-right"> <small class="request-date-time text-muted">(Date
                                Time)</small></span>
                    </h5>
                    <p class="request-type-section card-text mb-1">Request: <span class="request-type">(Request Type)</span></p>
                    <p class="request-reason-section card-text"><span class="font-weight-bold">Reason:</span> <span class="reason">(Reason)</span></p>
                    <div class="request-response-section card-text py-1 px-2"><span class="font-weight-bold">
                        <div class="pending-response-details" style="display: none;">
                            <span class="pr-3">Pending Approval</span>
                            {{-- <button class="approve-btn btn btn-sm">Approve</button>
                            <button class="reject-btn btn btn-sm btn-warning">Reject</button> --}}
                            <button class="cancel-btn btn btn-sm btn-warning">Cancel</button>
                        </div>
                        <div class="post-response-details" style="display: none;">
                            <span class="pr-3">
                                Response: <span class="response badge">(Response)</span> 
                                by: <span class="response-user">(User)</span><span class="response-user-role badge badge-primary">(Role)</span>
                            </span>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="text" class="defect-tags-input" value="">
</div>
@endsection
@push('scripts')
<link href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="//cdn.quilljs.com/1.3.6/quill.bubble.css">
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.js"></script>
<link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.default.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script type="text/javascript">

    var defectId;
    var defectInfo;
    var defectStatusDict = {!! json_encode(App\Constants\DefectStatus::$dict) !!};
    var id = {{ $id }};
    
    if (id !== 0) {
        $(window).on('load', function(){
            $('#defect-modal').modal('show');
            var defectId = id;
            var modal = $('#defect-modal');

            modal.find('.nav-tabs #pills-activity-tab').tab('show');

            $('.nav-tabs #pills-activity-tab').tab('show');
        
            getDefectAndUpdateModal(defectId, modal);         
            defectModalHideCommentImages(modal);
            defectModalActivityShowLoading(modal);
            getDefectActivitiesAndUpdateModal(defectId, modal);
        });
    }

    $('#defect-modal').on('show.bs.modal', function (event) {
        var sourceEl = $(event.relatedTarget)

        elDefectId = sourceEl.data('defect-id')
        if(!elDefectId) {
            return;
        }
        defectId = elDefectId;

        var modal = $(this);
        modal.find('.nav-tabs #pills-activity-tab').tab('show');
        
        getDefectAndUpdateModal(defectId, modal);         
        defectModalHideCommentImages(modal);
        defectModalActivityShowLoading(modal);
        getDefectActivitiesAndUpdateModal(defectId, modal);
    }); 

    var projectDefects = [];
    $('#assigned-defects-card').on('show.bs.collapse', '.project-body', function (event) {
        var projectBodyEl = $(event.currentTarget);
        var projectId = projectBodyEl.data('project-id');
        var searchInputField = projectBodyEl.find('.defects-search');
        searchInputField.val("");
        getDefectsAssignedToMe(projectId, function(defects) {
            projectDefects = [];
            console.log(defects);
            var defectsListEl = projectBodyEl.find('.defects-list');
            defectsListEl.empty();
            for(defect of defects) {
                var newDefectEl = $('#templates .defect-card').clone();
                newDefectEl.find('.card-body').attr('data-defect-id', defect.id);
                newDefectEl.find('.defect-ref-no').text('C' + defect.case.ref_no + '-D' + defect.ref_no);
                newDefectEl.find('.defect-title').text(defect.title);
                var caseNameEl = newDefectEl.find('.defect-case .case-title').text(defect.case.title);
                newDefectEl.find('.defect-type span').text(defect.type.title);
                var defectStatusDisplayEl = newDefectEl.find('.defect-status-section .defect-status');
                defectStatusDisplayEl.text(getDefectStatusName(defect.status));
                updateDefectStatusBtnColorClass(defectStatusDisplayEl, defect.status);

                var defectDueDate = moment(defect.due_date, "YYYY-MM-DD").format("DD/MM/YYYY");
                newDefectEl.find('.defect-status-section .defect-due-date span').text(defectDueDate);

                defectsListEl.append(newDefectEl);

                projectDefects.push(defect);
            }
        });
    });

    function defectsFilter() {
        var q = $(event.currentTarget).val();
        var projectBodyEl = $(event.currentTarget).closest('#assigned-defects-card');
        var defectsListEl = projectBodyEl.find('.defects-list');
        defectsListEl.empty();
        
        projectDefects.forEach(defect => {
            var defectTitle = defect.title;
            console.log("uuh",defectTitle.toLowerCase().indexOf(q.toLowerCase()));
            if (defectTitle.toLowerCase().indexOf(q.toLowerCase()) !== -1) {
                var newDefectEl = $('#templates .defect-card').clone();
                newDefectEl.find('.card-body').attr('data-defect-id', defect.id);
                newDefectEl.find('.defect-ref-no').text('C' + defect.case.ref_no + '-D' + defect.ref_no);
                newDefectEl.find('.defect-title').text(defect.title);
                var caseNameEl = newDefectEl.find('.defect-case .case-title').text(defect.case.title);
                newDefectEl.find('.defect-type span').text(defect.type.title);
                var defectStatusDisplayEl = newDefectEl.find('.defect-status-section .defect-status');
                defectStatusDisplayEl.text(getDefectStatusName(defect.status));
                updateDefectStatusBtnColorClass(defectStatusDisplayEl, defect.status);

                var defectDueDate = moment(defect.due_date, "YYYY-MM-DD").format("DD/MM/YYYY");
                newDefectEl.find('.defect-status-section .defect-due-date span').text(defectDueDate);
    
                defectsListEl.append(newDefectEl);
            } 
        });
    }

    // SECTION: Defect Pin on Image
    {{-- var floors = {!! $case->unit->unit_type->floors !!} --}};
    var floors = [];
    var currentFloorId;

    var locationPinsData = [];
    var selectedPinNo;

    var floorPlanImgContainerEl = $('#floor-plan-img-container');

    function initFloorPlan(modal) {
        // Store pins
        if(defectInfo.pins != null) {
            locationPinsData = JSON.parse(JSON.stringify(defectInfo.pins));
        }

        loadFloorPlanImageAndDetails(modal, defectInfo.unit_type_floor_id);

        removePinsFromView(modal);
        loadPinsOnView(modal);
    }

    function loadFloorPlanImageAndDetails(modal, floorId) {
        if(floorId) {
            var imageUrl = getUrlForUnitFloorPlanImage(defectInfo.project_id, defectInfo.case.unit_id, floorId);

            modal.find('#floor-plan-image').attr('src', imageUrl);
            getUnitUnitType(defectInfo.project_id, defectInfo.case.unit_id, function(unitType) {
                var selectedFloor;
                for(floor of unitType.floors) {
                    if(floor.id == floorId) {
                        selectedFloor = floor;
                        break;
                    }
                }

                modal.find('#floor-plan-menu .floor-name').text(selectedFloor.name);
            })

            modal.find('#floor-plan-menu #floor-select-input').val(floorId);
            modal.find('#floor-plan-image').attr('src', imageUrl);
        } else {
            clearFloorPlanUi(modal);
            removePinsFromView(modal);
        }
    }

    function loadPinsOnView(modal, editMode) {
        for (let i = 0; i < locationPinsData.length; i++) {
            var pinNo = i+1;
            addLocationPinToPinList(modal, pinNo, locationPinsData[i].label, !!locationPinsData[i].x);
            if(locationPinsData[i].x) {
                placePinOnImage(pinNo, locationPinsData[i].x, locationPinsData[i].y);
            }
        }

        if(editMode) {
            enableLocationPinsControlPanel(modal);
        }
    }

    function addLocationPinToPinList(modal, no, label, hasLocation) {
        var pinListEl = modal.find('#pills-location #floor-plan-control-panel .pin-list');
        var newPinEntryEl = $('#templates .pin-entry').clone();

        newPinEntryEl.data('pin-no', no);
        newPinEntryEl.find('.pin-no').text(no);
        if(!hasLocation) {
            newPinEntryEl.find('.pin-no').addClass('no-location');
        }
        newPinEntryEl.find('.pin-label .view').text(label);

        if(no == selectedPinNo) {
            newPinEntryEl.addClass('selected');
        }

        pinListEl.append(newPinEntryEl);
    }

    function placePinOnImage(pinNo, ratioX, ratioY) {
        floorPlanImgContainerEl.find('.defect-location-pin.pin-' + pinNo).remove();
        floorPlanImgContainerEl.append(
            $('<span class="defect-location-pin pin-' + pinNo + '">' + pinNo + '</span>').css({
                position: 'absolute',
                top: ratioY*100 + '%',
                left: ratioX*100 + '%',
                'margin-left': '-10px',
                'margin-top': '-10px',
                width: '20px',
                height: '20px',
            })
        );
    }

    function removePinsFromView(modal) {
        // TODO
        for (let i = 0; i < locationPinsData.length; i++) {
            var pinNo = i+1;
            clearPinOnImage(pinNo);
        }

        clearPinList(modal);
    }

    function clearPinOnImage(pinNo) {
        floorPlanImgContainerEl.find('.defect-location-pin.pin-' + pinNo).remove();
    }

    function clearPinList(modal) {
        modal.find('#pills-location #floor-plan-control-panel .pin-list').empty();
    }

    function clearFloorPlanUi(modal) {
        modal.find('#floor-plan-menu .floor-name').text("(No floor selected)");
        modal.find('#floor-plan-menu #floor-select-input').val(null);
        modal.find('#floor-plan-image').attr('src', null);
    }

    function enableLocationPinsControlPanel(modal) {
        // Show pin buttons
        modal.find('#pills-location #floor-plan-control-panel .pin-list .pin-entry .options-section').css('display', '');
        // Show add pin button
        modal.find('#pills-location #floor-plan-control-panel .add-pin-btn').css('display', '');
        
        defectModalEnableEditLocationPins();
    }

     // SECTION: API

     function getDefectsAssignedToMe(projectId, onSuccess) {
        var getDefectsRoute = "{{ route('contractor.defects.me.ajax.get') }}";
        $.ajax({
            url: getDefectsRoute,
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                project_id: projectId
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

     var getDefectRouteTemplate = "{{ route('contractor.defects.ajax.info', ['id' => '<<id>>']) }}";
    function getDefectAndUpdateModal(defectId, modal, dontShowLoading) {
        if(!dontShowLoading) {
            defectModalInfoShowLoading(modal);
        }
        defectModalClearDescription();

        var getDefectRoute = getDefectRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: getDefectRoute,
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                defectInfo = response;

                var defectInfoSectionEl = modal.find('.defect-info-section');

                defectInfoSectionEl.find('.defect-ref-no').text("D" + defectInfo.ref_no);
                defectInfoSectionEl.find('.defect-title').text(defectInfo.title);
                defectInfoSectionEl.find('.defect-info-unit .unit-no').text(defectInfo.case.unit.unit_no);
                defectInfoSectionEl.find('.defect-info-unit .unit-owner-name').text(defectInfo.case.unit.owner_name);
                defectInfoSectionEl.find('.defect-info-unit .unit-owner-no').text(defectInfo.case.unit.owner_contact_no);
                // <div class="defect-info-unit">
                //             Unit: <span class="unit-no">UNIT_NO</span><span class="px-4">Owner:
                //                 <span class="unit-owner-name">OWNER</span>(<span class="unit-owner-no"></span>)</span>
                //         </div>
                var dueDate = moment(defectInfo.due_date, "YYYY-MM-DD").format("DD/MM/YYYY");
                defectInfoSectionEl.find('.defect-due-date').text(dueDate);
                defectInfoSectionEl.find('#defect-due-date-input').datepicker('setDate', dueDate);
                defectInfoSectionEl.find('#defect-type-view').text(defectInfo.type.title);
                defectInfoSectionEl.find('#defect-type-input').val(defectInfo.type.id);
                var assignedContractorEl = defectInfoSectionEl.find('.assigned-contractor')
                if(defectInfo.assigned_contractor != null) {
                    assignedContractorEl.text(defectInfo.assigned_contractor.name);
                    assignedContractorEl.removeClass('btn-secondary');
                    assignedContractorEl.addClass('btn-default');
                } else {
                    assignedContractorEl.text('Assign');
                    assignedContractorEl.addClass('btn-secondary');
                    assignedContractorEl.removeClass('btn-default');
                }

                var submittedDate = moment(defectInfo.created_at, "YYYY-MM-DD").format("DD/MM/YYYY");
                defectInfoSectionEl.find('.defect-submitted-date').text(submittedDate);
                if(defectInfo.resolved_date != null) {
                    var resolvedDate = moment(defectInfo.resolved_date, "YYYY-MM-DD").format("DD/MM/YYYY");
                    defectInfoSectionEl.find('.defect-resolved-date').text(resolvedDate);
                } else {
                    defectInfoSectionEl.find('.defect-resolved-date').text('-');
                }
                if(defectInfo.closed_date != null) {
                    var closedDate = moment(defectInfo.closed_date, "YYYY-MM-DD").format("DD/MM/YYYY");
                    defectInfoSectionEl.find('.defect-closed-date').text(closedDate);
                } else {
                    defectInfoSectionEl.find('.defect-closed-date').text('-');
                }

                var defectTagsEl = defectInfoSectionEl.find('.defect-tags-list');
                var defectTagsInputContainerEl = defectInfoSectionEl.find('.defect-tags-input-container');
                var tags = "";
                if(defectInfo.tags) {
                    for(tag of defectInfo.tags) {
                        if(tags) {
                            tags = (tags+","+tag.tag)
                        } else {
                            tags = (tags+tag.tag)
                        }

                    }
                }
                defectTagsEl.text(tags);

                var defectTagsInputEl = $('#templates .defect-tags-input').clone();
                defectTagsInputEl.val(tags);
                defectTagsInputContainerEl.empty();
                defectTagsInputContainerEl.append(defectTagsInputEl);

                var $defectTagsSelectize = defectTagsInputEl.selectize({
                    delimiter: ',',
                    persist: false,
                    maxOptions: 10,
                    readOnly: true,
                    create: function(input, callback) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                });
                defectTagsSelectize = $defectTagsSelectize[0].selectize;
                defectTagsSelectize.lock();

                var defectStatusSectionEl = modal.find('.defect-status-section');
                
                var defectStatusBtn = defectStatusSectionEl.find('#defect-status-btn')
                defectStatusBtn.text(getDefectStatusName(defectInfo.status));
                updateDefectStatusBtnColorClass(defectStatusBtn, defectInfo.status);
                defectStatusSectionEl.find('.defect-status').css('display', 'none');
                switch(defectInfo.status) {
                    case 'open':
                        defectStatusSectionEl.find('.defect-status[data-name="wip"]').css('display', '');
                    break;
                    case 'wip':
                        defectStatusSectionEl.find('.defect-status[data-name="resolved"]').css('display', '');
                    break;
                }

                

                if(defectInfo.description) {
                    defectModalSetDescription(JSON.parse(defectInfo.description));
                }

                initFloorPlan(modal);

                // Images
                var defectImagesContainerEl = modal.find('#pills-images #defect-images-container .images-flex-container');
                defectImagesContainerEl.empty();
                if(defectInfo.images) {
                    var defectImageTemplateEl = $('#templates .defect-img-holder');
                    for(defectImage of defectInfo.images) {
                        var defectImageUrl = getUrlForDefectImage(defectInfo.id, defectImage.id);
                        var defectImageEl = defectImageTemplateEl.clone();
                        defectImageEl.data('id', defectImage.id);
                        defectImageEl.find('img').attr("src", defectImageUrl);

                        defectImagesContainerEl.append(defectImageEl);
                    }
                }

                
                defectModalInfoShowContent(modal);
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
    
    var getDefectActivitiesRouteTemplate = "{{ route('contractor.defects.ajax.activities', ['id' => '<<id>>']) }}";
    function getDefectActivitiesAndUpdateModal(defectId, modal) {

        var getDefectActivitiesRoute = getDefectActivitiesRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: getDefectActivitiesRoute,
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(defectActivities) {
                defectModalActivityClearContent(modal);

                let activityList = modal.find('.activity-list');
                defectActivities = defectActivities.reverse();
                for(activity of defectActivities) {
                    if(activity.type === "comment") {
                        let newCommentEl = $('#templates .activity-comment').clone();

                        if(activity.user.profile_pic_media_id){
                            var profileImageUrl = getActivityUserProfileImage(activity.defect_id, activity.id, activity.user.profile_pic_media_id);
                            newCommentEl.find('.commenter-profile-pic').html(`<img class="logo-media-icon rounded-circle p-0" src="${profileImageUrl}">`);
                        }

                        newCommentEl.find('.commenter-name').text(activity.user.name);
                        newCommentEl.find('.commenter-role').text(activity.user.roles[0].name);
                        newCommentEl.find('.comment-date-time').text(activity.created_at);
                        newCommentEl.find('.comment').html(activity.content);

                        if(activity.images != null && activity.images.length > 0) {
                            var commentImgsEl = $('#templates .comment-images').clone();
                            var commentImgHolderElTemplate = $('#templates .view-comment-img-holder');
                            for(image of activity.images) {
                                var imageUrl = getUrlForDefectActivityImage(defectId, activity.id, image.id);
                                var commentImgHolderEl = commentImgHolderElTemplate.clone();
                                commentImgHolderEl.find('img').attr('src', imageUrl);

                                
                                commentImgsEl.append(commentImgHolderEl);
                            }

                            newCommentEl.find('.comment').before(commentImgsEl);
                        }

                        newCommentEl.appendTo(activityList);
                    } else if(activity.type === "update") {
                        let newCommentEl = $('#templates .activity-update').clone();

                        newCommentEl.find('.updater-user-name h5').text(activity.user.name);
                        newCommentEl.find('.update-date-time small').text(activity.created_at);
                        newCommentEl.find('.update').text(activity.content);

                        newCommentEl.appendTo(activityList);
                    } else if(activity.type === "request") {
                        console.log("Request: ", activity);
                        let newRequestEl = $('#templates .activity-request').clone();

                        newRequestEl.data('activity-id', activity.id);

                        if(activity.user.profile_pic_media_id){
                            var profileImageUrl = getActivityUserProfileImage(activity.defect_id, activity.id, activity.user.profile_pic_media_id);
                            newRequestEl.find('.requester-icon').html(`<img class="logo-media-icon rounded-circle p-0" src="${profileImageUrl}">`);
                        }

                        newRequestEl.find('.requester-name').text(activity.user.name);
                        newRequestEl.find('.requester-role').text(activity.user.roles[0].name);
                        newRequestEl.find('.request-date-time').text(activity.created_at);
                        var requestType = '';
                        switch (activity.request_type) {
                            case "close":
                                requestType = "Close Defect";
                                break;
                            case "extend":
                                requestType = "Extend Defect Due Date";
                                break;
                            case "reject":
                                requestType = "Reject Defect As Invalid";
                                break;
                            default:
                                requestType = "(" + activity.request_type + ")";

                        }
                        newRequestEl.find('.request-type-section .request-type').text(requestType);
                        newRequestEl.find('.request-reason-section .reason').text(activity.content);

                        var requestResponseEl = newRequestEl.find('.request-response-section');
                        if(activity.request_response) {
                            requestResponseEl.find('.post-response-details').css('display', '');
                            requestResponseEl.find('.response').text(activity.request_response);
                            switch(activity.request_response) {
                                case "approved":
                                    requestResponseEl.find('.response').addClass('badge-success');
                                    break;
                                case "rejected":
                                    requestResponseEl.find('.response').addClass('badge-warning');
                                    break;
                                case "cancelled":
                                    requestResponseEl.find('.response').addClass('badge-warning');
                                    break;
                            }
                            requestResponseEl.find('.response-user').text(activity.request_response_user.name);
                            requestResponseEl.find('.response-user-role').text(activity.request_response_user.roles[0].name);                           
                        } else {
                            requestResponseEl.find('.pending-response-details').css('display', '');
                        }


                        newRequestEl.appendTo(activityList);
                    }
                }
                
                defectModalActivityShowContent(modal);
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

    var getUnitFloorPlanImageRouteTemplate = "{{ route('contractor.projects.unit.floors.floor-plan.get', ['proj_id' => '<<proj_id>>', 'unit_id' => '<<unit_id>>', 'id' => '<<id>>']) }}";
    function getUrlForUnitFloorPlanImage(projectId, unitId, floorId) {
        var route = getUnitFloorPlanImageRouteTemplate.replace(encodeURI('<<proj_id>>'), projectId);
        var route = route.replace(encodeURI('<<unit_id>>'), unitId);
        var route = route.replace(encodeURI('<<id>>'), floorId);

        return route;
    }

    var getDefectImageRouteTemplate = "{{ route('contractor.defects.images.get', ['defect_id' => '<<defect_id>>', 'id' => '<<id>>']) }}";
    function getUrlForDefectImage(defectId, defectImageId) {
        var getDefectImageRoute = getDefectImageRouteTemplate.replace(encodeURI('<<defect_id>>'), defectId);
        var getDefectImageRoute = getDefectImageRoute.replace(encodeURI('<<id>>'), defectImageId);

        return getDefectImageRoute;
    }
    
    var getDefectActivityImageRouteTemplate = "{{ route('contractor.defects.activities.images.get', ['defect_id' => '<<defect_id>>', 'activity_id' => '<<activity_id>>', 'id' => '<<id>>']) }}";
    function getUrlForDefectActivityImage(defectId, activityId, id) {
        var getDefectActivityImageRoute = getDefectActivityImageRouteTemplate.replace(encodeURI('<<defect_id>>'), defectId);
        var getDefectActivityImageRoute = getDefectActivityImageRoute.replace(encodeURI('<<activity_id>>'), activityId);
        var getDefectActivityImageRoute = getDefectActivityImageRoute.replace(encodeURI('<<id>>'), id);

        return getDefectActivityImageRoute;
    }

    var getActivityUserProfileImageRouteTemplate = "{{ route('contractor.defects.activities.user-profile-images.get', ['defect_id' => '<<defect_id>>', 'activity_id' => '<<activity_id>>', 'id' => '<<id>>']) }}";
    function getActivityUserProfileImage(defectId, activityId, id) {
        var getActivityUserProfileImageRoute = getActivityUserProfileImageRouteTemplate.replace(encodeURI('<<defect_id>>'), defectId);
        var getActivityUserProfileImageRoute = getActivityUserProfileImageRoute.replace(encodeURI('<<activity_id>>'), activityId);
        var getActivityUserProfileImageRoute = getActivityUserProfileImageRoute.replace(encodeURI('<<id>>'), id);

        return getActivityUserProfileImageRoute;
    }

    var postDefectActivityCommentRouteTemplate = "{{ route('contractor.defects.ajax.activities.comment.post', ['id' => '<<id>>']) }}";
    function postDefectActivityComment(defectId, comment, commentImagesData, onSuccess, onError){
        var postDefectActivityCommentRoute = postDefectActivityCommentRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postDefectActivityCommentRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                comment: comment,
                images: commentImagesData
            },
            success: function(response) {
                onSuccess();
            },
            error: function(xhr) {
                if(xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    console.log("Error 422: ", xhr);
                }
                console.log("Error: ", xhr);
                onError();
            }
        });
    }

    var getUnitUnitTypeRouteTemplate = "{{ route('contractor.projects.units.ajax.type.get', ['proj_id' => '<<proj_id>>', 'unit_id' => '<<unit_id>>']) }}";
    function getUnitUnitType(projectId, unitId, onSuccess) {
        var getUnitUnitTypeRoute = getUnitUnitTypeRouteTemplate.replace(encodeURI('<<proj_id>>'), projectId);
            getUnitUnitTypeRoute = getUnitUnitTypeRoute.replace(encodeURI('<<unit_id>>'), unitId);
        $.ajax({
            url: getUnitUnitTypeRoute,
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(unitType) {
                onSuccess(unitType);
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

    var postDefectStatusRouteTemplate = "{{ route('contractor.defects.ajax.status.post', ['id' => '<<id>>']) }}";
    function postDefectStatus(defectStatus, defectId, onSuccess) {
        var postDefectStatusRoute = postDefectStatusRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postDefectStatusRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: defectStatus,
            },
            success: function(response) {
                onSuccess();
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

    var postDefectRequestRouteTemplate = "{{ route('contractor.defects.ajax.request.post', ['id' => '<<id>>']) }}";
    function postDefectRequest(defectId, requestType, data, onSuccess) {
        var postDefectRequestRoute = postDefectRequestRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postDefectRequestRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                type: requestType,
                reason: data.reason,
            },
            success: function(response) {
                if(response.message){
                    alert(response.message);
                }
                onSuccess();
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
    
    var postDefectRequestCancelRouteTemplate = "{{ route('contractor.defects.ajax.request.cancel.post', ['defect_id' => '<<defect_id>>', 'activity_id' => '<<activity_id>>']) }}";
    function postDefectRequestCancel(defectId, activityId, onSuccess) {
        var postDefectRequestCancelRoute = postDefectRequestCancelRouteTemplate.replace(encodeURI('<<defect_id>>'), defectId);
        postDefectRequestCancelRoute = postDefectRequestCancelRoute.replace(encodeURI('<<activity_id>>'), activityId);
        $.ajax({
            url: postDefectRequestCancelRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function() {
                onSuccess();
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

     // SECTION: UI
     function initModal(modalEl) {
        modalEl.find('.activity-add-comment-section .add-comment-btn').click(function() {
            defectModalOnCommentClicked(modalEl);
        })
        modalEl.find('.activity-add-comment-section .images-btn').click(function() {
            defectModalToggleShowHideCommentImages(modalEl);
        })
        modalEl.find('#pills-activity .activity-add-comment-section .add-image-btn').click(function() {
            defectModalAddCommentImageClicked(modalEl);
        })
        modalEl.find('#pills-activity .activity-add-comment-section #comment-image-file-input').change(function(event) {
            var fileInputEl = event.currentTarget;

            defectModalAddCommentImageFileSelected(modalEl, fileInputEl.files[0]);
        })
        modalEl.find("#pills-activity .activity-add-comment-section").on("click", ".delete-btn", function(event) {
            defectModalDeleteCommentImageClicked(modalEl, event.currentTarget);
        })
        modalEl.find("#pills-activity .activity-list").on("click", ".activity-request .cancel-btn", function(event) {
            defectModalOnDefectRequestCancelClicked(modalEl, event.currentTarget);  
        })

        modalEl.find("#pills-activity .activity-list").on("click", ".view-btn", function(event) {
            defectModalOnActivityImageViewClicked(modalEl, event.currentTarget);
        })
        
        modalEl.find("#pills-images #defect-images-container").on("click", ".view-btn", function(event) {
            defectModalOnImageViewClicked(modalEl, event.currentTarget);
        })

        modalEl.find('#defect-status-list .defect-status').click(function(event) {
            defectModalOnDefectStatusOptionClicked(modalEl, $(event.currentTarget));
        })
        modalEl.find('#defect-status-list .request').click(function(event) {
            defectModalOnDefectStatusRequestOptionClicked(modalEl, $(event.currentTarget));
            event.stopPropagation();
        })
        modalEl.find('#defect-status-list .request-form-section .submit-btn').click(function(event) {
            defectModalOnDefectStatusRequestFormSubmitClicked(modalEl);  
            event.stopPropagation();
        })
        modalEl.find('#defect-status-list .request-form-section .cancel-btn').click(function(event) {
            defectModalOnDefectStatusRequestFormCancelClicked(modalEl);
            event.stopPropagation();
        })
    }
    initModal($('#defect-modal'));

    var descriptionEditor = new Quill('#description-editor', {
        theme: 'bubble',
        modules: {
        }
    });
    descriptionEditor.disable();

    function defectModalOnDefectStatusOptionClicked(modalEl, defectStatusEl) {
        defectStatusVal = defectStatusEl.data('name');

        postDefectStatus(defectStatusVal, defectId, function() {
            var defectStatusBtnEl = modalEl.find('#defect-status-btn');
            defectStatusBtnEl.text(getDefectStatusName(defectStatusVal));
            updateDefectStatusBtnColorClass(defectStatusBtnEl, defectStatusVal);
            
            // getDefectsAndUpdateDefectsList();
            getDefectActivitiesAndUpdateModal(defectId, modalEl);
            getDefectAndUpdateModal(defectId, modalEl, true);
        });
    };

    function defectModalOnDefectStatusRequestOptionClicked(modalEl, defectStatusRequestEl) {
        defectStatusRequestVal = defectStatusRequestEl.data('name');

        var defectStatusSectionEl = modalEl.find('.defect-status-section');

        // TODO Set the UI for dropdown to show form for request with confirm buttons
        var request = null;
        var requestType = null;
        switch (defectStatusRequestVal) {
            case 'request-closed':
                request = "Close";
                requestType = "close";
                break;
            case 'request-extend':
                request = "Extend";
                requestType = "extend";
                break;
            case 'request-reject':
                request = "Reject";
                requestType = "reject";
                break;
        }

        if(request) {
            // Hide request section
            defectStatusSectionEl.find('.request-section').css('display', 'none');

            var requestFormSectionEl = defectStatusSectionEl.find('.request-form-section');
            requestFormSectionEl.css('display', '');

            requestFormSectionEl.find('.request-name').text(request);
            requestFormSectionEl.data('request-type', requestType);
        }
    }

    function defectModalOnDefectStatusRequestFormSubmitClicked(modal) {
        var defectStatusSectionEl = modal.find('.defect-status-section');
        var requestFormSectionEl = defectStatusSectionEl.find('.request-form-section');

        var reason = requestFormSectionEl.find('.reason-input').val();
        var requestType = requestFormSectionEl.data('request-type');
        console.log("RT: " + requestType)

        if(reason) {
            postDefectRequest(defectId, requestType, {
                reason: reason
            }, function () {
                // Show request section and hide request form
                defectStatusSectionEl.find('.request-section').css('display', '');
                defectStatusSectionEl.find('.request-form-section').css('display', 'none');

                getDefectActivitiesAndUpdateModal(defectId, modal);
            });
        } else {
            alert(`Please state the ${requestType} reason`)
        }
    }

    function defectModalOnDefectStatusRequestFormCancelClicked(modal) {
        
        var defectStatusSectionEl = modal.find('.defect-status-section');

        defectStatusSectionEl.find('.request-section').css('display', '');
        defectStatusSectionEl.find('.request-form-section').css('display', 'none');
        defectStatusSectionEl.find('.request-form-section .reason-input').val('');
    }

    function defectModalOnDefectRequestCancelClicked(modal, cancelBtnEl) {
        var activityRequestEl = cancelBtnEl.closest('.activity-request');
        activityRequestEl = $(activityRequestEl);

        var activityId = activityRequestEl.data('activity-id');
        console.log("Activity ID: " + activityId);

        postDefectRequestCancel(defectId, activityId, function () {
            getDefectActivitiesAndUpdateModal(defectId, modal);
        });
    }

    function defectModalClearDescription() {
        descriptionEditor.setContents([{ insert: '\n' }]);
    }

    function defectModalSetDescription(description) {
        descriptionEditor.setContents(description);
    }

     function defectModalInfoShowLoading(modalEl) {
        modalEl.find('.defect-info-section').css("visibility", "hidden");
        modalEl.find('.defect-status-section').css("visibility", "hidden");
        modalEl.find('.defect-info-loading').css('visibility', 'visible');
    }
    function defectModalInfoShowContent(modalEl) {
        modalEl.find('.defect-info-section').css("visibility", "visible");
        modalEl.find('.defect-status-section').css("visibility", "visible");
        modalEl.find('.defect-info-loading').css('visibility', 'hidden');
    }

     function defectModalActivityShowLoading(modalEl) {
        modalEl.find('.activity-list').css("visibility", "hidden");
        modalEl.find('.activity-list-loading').removeAttr('hidden');
    }
    function defectModalActivityShowContent(modalEl) {
        modalEl.find('.activity-list').css("visibility", "visible");
        modalEl.find('.activity-list-loading').attr('hidden', 'true');
    }
    function defectModalActivityClearContent(modalEl) {
        modalEl.find('.activity-list').html("");
    }

     var showCommentImages = false;
    function defectModalToggleShowHideCommentImages(modalEl) {
        if(showCommentImages) {
            // defectModalHideCommentImages(modalEl);
        } else {
            defectModalShowCommentImages(modalEl);
        }
    }
    function defectModalShowCommentImages(modalEl) {
        showCommentImages = true;
        var activityAddCommentsSectionEl = modalEl.find('.activity-add-comment-section');
        activityAddCommentsSectionEl.find('.comment-images-input').css('display', '');
    }

    function defectModalHideCommentImages(modalEl) {
        showCommentImages = false;
        var activityAddCommentsSectionEl = modalEl.find('.activity-add-comment-section');
        activityAddCommentsSectionEl.find('.comment-images-input').css('display', 'none');
    }

    function defectModalAddCommentImageClicked(modalEl) {
        modalEl.find('.activity-add-comment-section .comment-images-input #comment-image-file-input').click();
    }

    function defectModalAddCommentImageFileSelected(modalEl, file) {
        readFileAsDataUrl(file, function (dataUrl) {
            var imageEl = $('#templates .comment-img-holder').clone();
            imageEl.find('img').attr('src', dataUrl);
            var addImageBtn = modalEl.find('#pills-activity .activity-add-comment-section .comment-images-input .add-image-btn');
            addImageBtn.before(imageEl);

            var currentImagesCount = modalEl.find('#pills-activity .activity-add-comment-section .comment-images-input .comment-img-holder').length;
            
            if(currentImagesCount >= 3) {
                addImageBtn.css('display', 'none'); 
            }
        });
    }

    function defectModalDeleteCommentImageClicked(modalEl, imageOptionBtnEl) {
        var imgHolder = imageOptionBtnEl.closest('.comment-img-holder');
        imgHolder = $(imgHolder);

        imgHolder.remove();

        modalEl.find('#pills-activity .activity-add-comment-section .comment-images-input .add-image-btn').css('display', ''); 
    }

    var commentBtnDisabled = false;
    function defectModalOnCommentClicked(modalEl) {
        if(commentBtnDisabled) {
            return;
        }

        var comment = modalEl.find('#comment-textarea').val();
        console.log("ON: Comment Clicked -> ", comment);
        if(comment) {
            commentBtnDisabled = true;

            var commentImagesData = getCommentImagesDataUrlList(modalEl);
            postDefectActivityComment(defectId, comment, commentImagesData, function () {
                // Clear comment textarea
                modalEl.find('#pills-activity .activity-add-comment-section #comment-textarea').val('');
                modalEl.find('#pills-activity .activity-add-comment-section .comment-images-input .comment-img-holder').remove();
                modalEl.find('#pills-activity .activity-add-comment-section .comment-images-input .add-image-btn').css('display', '');
                modalEl.find('#comment-textarea').val('');
                defectModalHideCommentImages(modalEl);
                getDefectActivitiesAndUpdateModal(defectId, modalEl);
                
                commentBtnDisabled = false;
            }, function() {
                commentBtnDisabled = false;
            });
        } else {
            // TODO Prompt user to enter comment
        }
    }

    function getCommentImagesDataUrlList(modalEl) {
        var imagesDataUrlList = [];
        var imagesContainerEl = modalEl.find('#pills-activity .activity-add-comment-section .comment-images-input > div');
        imagesContainerEl.find('img').each(function (index, imgEl) {
            if(index < 3) {
                var dataUrl = imgEl.getAttribute('src');
                console.log("src: ", dataUrl)
                imagesDataUrlList.push(dataUrl);
            }
        });

        return imagesDataUrlList;
    }

     var defectStatusDict = {!! json_encode(App\Constants\DefectStatus::$dict) !!};
     function getDefectStatusName(statusCode) {
        for (var prop in defectStatusDict) {
            if (defectStatusDict.hasOwnProperty(prop)) {
                if(prop == statusCode) {
                    return defectStatusDict[prop];
                }
            }
        }
    }

    function updateDefectStatusBtnColorClass(defectStatusBtnEl, status) {
        var lastBtnStatus = defectStatusBtnEl.data('status');
        var newStatusClass = "defect-status-" + status;

        if (lastBtnStatus == status) {
            return;
        }

        if(lastBtnStatus) {
            defectStatusBtnEl.removeClass("defect-status-" + lastBtnStatus);
        }

        defectStatusBtnEl.addClass(newStatusClass);
        defectStatusBtnEl.data('status', status);

    }

    function defectModalOnActivityImageViewClicked(modal, imageOptionBtnEl) {
        var imageModal = $('#activity-image-modal');
        // Get image URL
        var imgHolder = imageOptionBtnEl.closest('.view-comment-img-holder');
        // Hide defect modal
        modal.modal('hide');
        // Show image modal
        imageModal.find('.activity-image').attr('src', $(imgHolder).find('img').attr('src'));
        imageModal.modal('show');
    }

    function defectModalOnImageViewClicked(modal, imageOptionBtnEl) {
        var imageModal = $('#defect-image-modal');
        // Get image URL
        var imgHolder = imageOptionBtnEl.closest('.defect-img-holder');
        // Hide defect modal
        modal.modal('hide');
        // Show image modal
        imageModal.find('.defect-image').attr('src', $(imgHolder).find('img').attr('src'));
        imageModal.modal('show');
    }

    $('#defect-image-modal').on('hide.bs.modal', function (event) {
        var sourceEl = $(event.relatedTarget)
        var modal = $(this);
        
        $('#defect-modal').modal('show');
    });

    $('#activity-image-modal').on('hide.bs.modal', function (event) {
        var sourceEl = $(event.relatedTarget)
        var modal = $(this);
        
        $('#defect-modal').modal('show');
    });

    function readFileAsDataUrl(file, callback) {
        var reader  = new FileReader();
        reader.addEventListener("load", function () {
            callback(reader.result);
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }
    
    $('.defect-status-dropdown').on('hidden.bs.dropdown', function(e) {
        var defectStatusSectionEl = $('#defect-modal').find('.defect-status-section');
        var requestFormSectionEl = defectStatusSectionEl.find('.request-form-section');
        var reason = requestFormSectionEl.find('.reason-input');
        
        reason.val('');
        defectStatusSectionEl.find('.request-section').css('display', '');
        defectStatusSectionEl.find('.request-form-section').css('display', 'none');
    })
</script>
@endpush