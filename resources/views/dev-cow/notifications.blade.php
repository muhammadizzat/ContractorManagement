@extends('layouts.app', ['title' => __('Notifications')])
@section('content')
@include('users.partials.header', ['title' => __('')])
<div id="notifications-page" class="container-fluid mt--7">
    <div class="card shadow">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                        <h3 class="mb-0">{{ __('Notifications') }}</h3>
                </div>           
                <div class="btn-home text-right pr-3" data-original-title="" title="">
                    <a href="{{ route("dev-cow.projects.index") }}" class="btn btn-sm btn-primary"><i class="fas fa-sign-out-alt"></i></a>                         
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0">
            <div id="options-section" class="d-flex justify-content-between pt-2 pb-1 px-3">
                <div class="no-to-show-option">
                    <label>
                        Show 
                        <select name="page-size">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                         entries
                    </label>
                </div>
            </div>
            <div class="notifications-list">
                <div class="p-3">Loading...</div>
                {{-- INSERT: Notifications --}}
            </div>
            <div id="pagination-section" class="d-flex justify-content-between px-3 pt-2 pb-2">
                <div class="info">
                    Showing <span class="start">0</span> to <span class="end">0</span> of <span class="filtered">0</span> entries <span class="total-view">(Total: <span class="total"></span>)</span>
                </div>
                <div class="page-selection-section">
                    <button type="button" class="prev-btn btn btn-sm btn-primary mr-0"><i class="fas fa-chevron-left"></i></button>
                    <span class="page-no px-2 badge badge-warning">0</span>
                    <button type="button" class="next-btn btn btn-sm btn-primary"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
<div id="templates" hidden="true">
    <a class="defect-activity-comment-notification dropdown-item py-1">
        <div class="header d-flex flex-row align-items-center">
            <div class="details-container flex-fill">
                <span class="defect-label">Defect</span>
                <span class="project-name"></span><span class="defect-no badge-warning"></span>
            </div>
            <div class="date-time">
                
            </div>
        </div>
        <div class="d-flex flex-row pt-1">
            <div class="pr-2">
                <i class="far fa-comment-alt"></i>
            </div>
            <div class="notification-info">
                <div class="content"></div>
            </div>
        </div>
    </a>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
    $(function() {
        getNotificationsAndUpdateList();

        var optionsSectionEl = $('#options-section');
        optionsSectionEl.find('select[name=page-size]').change(onPageSizeSelectChanged);

        var pageSelectionSectionEl = $('#pagination-section .page-selection-section');
        pageSelectionSectionEl.find('.prev-btn').click(onPrevBtnClicked);
        pageSelectionSectionEl.find('.next-btn').click(onNextBtnClicked);
    });

    var disablePrevBtn = false;
    var disableNextBtn = false;

    function onPrevBtnClicked() {
        if(disablePrevBtn != true){
            console.log("ON: Clicked -> Prev Btn");
            pageNo--;
            getNotificationsAndUpdateList();
        }
    }

    function onNextBtnClicked() {
        if(disableNextBtn != true) {
            console.log("ON: Clicked -> Next Btn");
            pageNo++;
            getNotificationsAndUpdateList();
        }
    }

    function onPageSizeSelectChanged(e) {
        console.log("ON: Page Size Changed -> ", e);
        getNotificationsAndUpdateList();
    }

    var pageNo = 1;
    function getNotificationsAndUpdateList() {
        disablePrevBtn = true;
        disableNextBtn = true;
        var optionsSectionEl = $('#options-section');
        var pageSize = optionsSectionEl.find('select[name=page-size]').val();

        var options = {
            length: pageSize,
            start: (pageNo-1)*pageSize
        };


        getNotificationsDt(options, function (results) {
            var notificationsListEl = $('#notifications-page .notifications-list');
            notificationsListEl.empty();

            var paginationSectionEl = $('#pagination-section');
            var pageSelectionSectionEl = paginationSectionEl.find('.page-selection-section');
            pageSelectionSectionEl.find('.page-no').text(pageNo);
            if(options.start == 0) {
                pageSelectionSectionEl.find('.prev-btn').prop('disabled', true);
            } else {
                pageSelectionSectionEl.find('.prev-btn').prop('disabled', false);
            }
            if(parseInt(options.start) + 10 > results.recordsTotal) {
                pageSelectionSectionEl.find('.next-btn').prop('disabled', true);
            } else {
                pageSelectionSectionEl.find('.next-btn').prop('disabled', false);
            }

            for(notification of results.data) {
                var newNotificationEl = $('#templates .defect-activity-comment-notification').clone();

                newNotificationEl.find('.date-time').text(moment(notification.created_at).format("LT DD MMM YYYY"));
                newNotificationEl.find('.project-name').text(notification.data.project_name);

                switch(notification.type) {
                    case "App\\Notifications\\DefectDueDateExtended":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                            notification.data.defect_id,
                        ));   
                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> extended the due date");
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                        break;

                    case "App\\Notifications\\DefectImageUpdated":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                            notification.data.defect_id,
                        ));   
                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> updated the images");
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                        break;
                    
                    case "App\\Notifications\\PinUpdated":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                            notification.data.defect_id,
                        ));   
                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> updated the pins");
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                        break;

                    case "App\\Notifications\\DefectRequestResponse":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                            notification.data.defect_id,
                        ));   
                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> responded to the request");
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                        break;

                    case "App\\Notifications\\ContractorDefectRequest":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                            notification.data.defect_id,
                        ));   
                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> requested for status update");
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                        break;
                        
                    case "App\\Notifications\\NewDefect":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                            notification.data.defect_id,
                        ));   
                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> created a new defect");
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                        break;
                        
                    case "App\\Notifications\\DefectContractorUnassign":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                            notification.data.defect_id,
                        ));   
                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> unassigned contractor " + notification.data.assigned_contractor.name + " to defect");
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                        break;

                    case "App\\Notifications\\DefectContractorAssign":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                            notification.data.defect_id,
                        ));    

                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> assigned contractor " + notification.data.assigned_contractor.name + " to defect")
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                        break;

                    case "App\\Notifications\\CaseCowAssigned":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                        ));    


                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> assigned you to " + notification.data.case_title )
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no);

                        break;

                    case "App\\Notifications\\CaseCowUnassigned":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                        ));    


                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> unassigned you from " + notification.data.case_title )
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no);

                        break;

                    case "App\\Notifications\\NewCaseStatus":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                        ));    


                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> changed the status of case to " + notification.data.case_status )
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no);

                        break;

                    case "App\\Notifications\\NewDefectStatus":
                        newNotificationEl.attr('href', getUrlForCase(
                            notification.data.project_id,
                            notification.data.case_id,
                            notification.data.defect_id,
                        ));    

                        newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> changed the status of defect to " + notification.data.defect_status )
                        newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                        break;

                    case "App\\Notifications\\NewDefectActivity":
                        switch(notification.data.type) {
                            case "comment":
                                newNotificationEl.attr('href', getUrlForCase(
                                    notification.data.project_id,
                                    notification.data.case_id,
                                    notification.data.defect_id,
                                ));    

                                newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> added a comment")
                                newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

    
                                break;
                        }
                        break;
                    }
                    notificationsListEl.append(newNotificationEl);
                }               

            var infoEl = paginationSectionEl.find('.info');
            if (options.length == 0) {
                infoEl.find('.start').text(parseInt(options.start));                
                infoEl.find('.end').text(parseInt(options.start));
            } else {
                infoEl.find('.start').text(parseInt(options.start) + 1);
                if (options.start + 10 > results.recordsTotal) {
                    infoEl.find('.end').text(results.recordsTotal);
                } else {
                    infoEl.find('.end').text(parseInt(options.start) + parseInt(options.length));
                }
            }
            infoEl.find('.filtered').text(results.recordsFiltered);
            var totalViewEl = infoEl.find('.total-view');
            if(results.recordsFiltered == results.recordsTotal) {
               totalViewEl.css('display', 'none');
            } else {
                totalViewEl.css('display', '');
                totalViewEl.find('.total').text(results.recordsTotal);
            }

            disablePrevBtn = false;
            disableNextBtn = false;
        });
    }

    // SECTION: API
    function getNotificationsDt(options, onSuccess) {
        var getNotificationsRoute = "{{ route('notifications.dt') }}";

        $.ajax({
            url: getNotificationsRoute,
            type: 'GET',
            data: options,
            success: function(results) {
                onSuccess(results);
            },
            error: function(xhr) {
                if(xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    console.log("Error 422: ", xhr);
                }
                console.log("Error: ", xhr);
            }
        });
    };

    @role('dev-admin')
    var getCaseRouteTemplate = "{{ route('dev-admin.projects.cases.view', ['proj_id' => '<<proj_id>>', 'case_id' => '<<case_id>>']) }}";
    @endrole
    @role('cow')
    var getCaseRouteTemplate = "{{ route('dev-cow.projects.cases.view', ['proj_id' => '<<proj_id>>', 'case_id' => '<<case_id>>']) }}";
    @endrole
    @role('contractor')
    var getCaseRouteTemplate = "{{ route('contractor.dashboard') }}";
    @endrole
        
    function getUrlForCase(projectId, caseId, defectId) {
        var getCaseRoute = getCaseRouteTemplate.replace(encodeURI('<<proj_id>>'), projectId);
        getCaseRoute = getCaseRoute.replace(encodeURI('<<case_id>>'), caseId);
        getCaseRoute = getCaseRoute.replace(encodeURI('<<id>>'), defectId);
        
        return getCaseRoute;
    }
</script>
@endpush