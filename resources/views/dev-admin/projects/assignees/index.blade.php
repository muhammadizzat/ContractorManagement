@extends('layouts.app', ['title' => __('Cases Management')])
@section('content')
@include('users.partials.header', ['title' => __('')])
<div id="page-project-assignees" class="container-fluid mt--7">
    <div class="card shadow">
        <div class="card-header border-0">
            <h3 class="mb-0">{{ __('Assignees: Contractors') }}</h3>
        </div>
        <div class="card-body px-0 pt-0">
            <div id="options-section" class="d-flex justify-content-between pt-2 pb-1 px-3">
                <div class="no-to-show-option">
                    <label>
                        Show 
                        <select name="page-size">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                         entries
                    </label>
                </div>
                <div class="search">
                    <label>
                        Search
                        <input type="search">
                    </label>
                </div>
            </div>
            <div id="assignees-list" class="">
                <div class="p-3">Loading...</div>
                {{-- INSERT: Assignees --}}
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
    <div class="assignee-contractor-card card shadow">
        <div class="card-header py-2" data-toggle="collapse"
            data-target="#contractor-ID" aria-expanded="false"
            aria-controls="contractor-ID">
            <div class="d-flex flex-row align-items-center">
                <div class="no pr-2">
                    <h4 class="index mb-0 text-light">2</h4>
                </div>
                <div class="flex-fill">
                    <span class="contractor-name h4 font-weight-bold mb-0">Contractor Name</span>
                </div>
                <div>
                    <h4 class="assigned-defects-count mb-0"><span class="value"></span> defects</h3>
                </div>
                <div class="pl-4">
                    <i class="fas fa-angle-down"></i>
                </div>
            </div>
        </div>
        <div id="contractor-ID" class="asgnd-con-card-body card-body p-0 collapse"
            data-contractor-id="ID" data-parent="#assignees-list">
            <div class="defects-list p-2">

            </div>
        </div>
    </div>
    <div class="defect-card card shadow mb-1">
        <div class="card-body py-2 pl-2" data-defect-id="" data-case-id="">
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
    <div class="no-entries-found p-3">No entries found.</div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
    $(function() {
        getAssigneesAndUpdateList();

        var optionsSectionEl = $('#options-section');
        optionsSectionEl.find('select[name=page-size]').change(onPageSizeSelectChanged);
        optionsSectionEl.find('.search input[type=search]').on('input', onSearchInputChanged);

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

    var searchInputTimeoutHandler;
    function onSearchInputChanged(e) {
        if(!searchInputTimeoutHandler) {
            searchInputTimeoutHandler = setTimeout(function () {
                getAssigneesAndUpdateList();
                searchInputTimeoutHandler = null;
            }, 300);
        }
    }

    function onPageSizeSelectChanged(e) {
        getAssigneesAndUpdateList();
    }

    var pageNo = 1;
    function getAssigneesAndUpdateList() {
        disablePrevBtn = true;
        disableNextBtn = true;
        var optionsSectionEl = $('#options-section');
        var searchInput = optionsSectionEl.find('.search input[type=search]').val();
        var pageSize = optionsSectionEl.find('select[name=page-size]').val();

        var options = {
            length: pageSize,
            start: (pageNo-1)*pageSize,
            search: {
                value: searchInput,
                regex: false
            }, 
            columns: [
                {
                    name: 'users.name',
                },
            ]
        };

        getAssigneesDt(options, function (results) {
            var assigneesListEl = $('#assignees-list');
            assigneesListEl.empty();

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

            for(assigneeContractor of results.data) {
                var newAssigneeContractorEl = $('#templates .assignee-contractor-card').clone();
                var cardBody = newAssigneeContractorEl.find('.asgnd-con-card-body');
                cardBody.attr('id', 'contractor-' + assigneeContractor.id);
                cardBody.attr('data-contractor-id', assigneeContractor.id);
                
                var cardHeaderEl = newAssigneeContractorEl.find('.card-header');
                cardHeaderEl.attr('data-target', '#contractor-' + assigneeContractor.id);
                cardHeaderEl.attr('aria-controls', 'contractor-' + assigneeContractor.id);
                cardHeaderEl.find('.index').text(assigneeContractor.DT_RowIndex);
                cardHeaderEl.find('.contractor-name').text(assigneeContractor.name);
                cardHeaderEl.find('.assigned-defects-count .value').text(assigneeContractor.defects_count);

                assigneesListEl.append(newAssigneeContractorEl);
            }

            if(results.recordsTotal == 0) {
                var newNoEntriesFoundEl = $('#templates .no-entries-found').clone();
                assigneesListEl.append(newNoEntriesFoundEl);
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

    var assigneeDefects = [];
    $('#assignees-list').on('show.bs.collapse', '.asgnd-con-card-body', function (event) {
        var assignedConBodyEl = $(event.currentTarget);
        var contractorId = assignedConBodyEl.data('contractor-id');
        console.log("Contractor ID: ", contractorId);
        getAssigneesDefects(contractorId, function(defects) {
            assigneeDefects = [];
            var defectsListEl = assignedConBodyEl.find('.defects-list');
            defectsListEl.empty();
            for(defect of defects) {
                var newDefectEl = $('#templates .defect-card').clone();
                newDefectEl.find('.card-body').attr('data-defect-id', defect.id);
                newDefectEl.find('.card-body').attr('data-case-id', defect.case_id);
                newDefectEl.find('.defect-ref-no').text('C' + defect.case.id + '-D' + defect.ref_no);
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
    });

    $('#assignees-list').on('click', '.defect-card .card-body', function (event) {
        var cardBodyEl = $(event.currentTarget);

        var caseId = cardBodyEl.data('case-id');
        var defectId = cardBodyEl.data('defect-id');

        var caseDefectUrl = getUrlCase(caseId) + '?defect_id=' + defectId;
        window.location.href = caseDefectUrl;
    });

    // SECTION: Utility
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

    // SECTION: API
    function getAssigneesDt(options, onSuccess) {
        var getAssigneesRoute = "{{ route('dev-admin.projects.assignees.dt', ['proj_id' => $proj_id ]) }}";

        $.ajax({
            url: getAssigneesRoute,
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
    }

    var getAssigneesDefectsRouteTemplate = "{{ route('dev-admin.projects.assignees.defects.ajax.get', ['proj_id' => $proj_id, 'user_id' => '<<user_id>>']) }}";
    function getAssigneesDefects(userId, onSuccess) {
        var getAssigneesDefectsRoute = getAssigneesDefectsRouteTemplate.replace(encodeURI('<<user_id>>'), userId);
        $.ajax({
            url: getAssigneesDefectsRoute,
            type: 'GET',
            // data: {
            //     _token: '{{ csrf_token() }}',
            // },
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

    var getCaseRouteTemplate = "{{ route('dev-admin.projects.cases.view', ['proj_id' => $proj_id, 'case_id' => '<<case_id>>']) }}";
    function getUrlCase(caseId, onSuccess) {
        return getCaseRouteTemplate.replace(encodeURI('<<case_id>>'), caseId);
    };
</script>
@endpush