@extends('layouts.app', ['title' => __('Cases Management')])

@section('content')
@include('users.partials.header', ['title' => __('')])

<?php $defectTypes = App\DefectType::forDeveloper($developer_id)->get(); ?>
<div class="container mt--7">
    <div class="row">
        <div class="col">
            <div class="case-card card shadow">
                <div class="card-body border-0 py-3">
                    <div id="alert-container">
                        @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                        @elseif (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif
                    </div>
                    <div class="d-flex flex-row">
                        <div class="case-info-section flex-fill">
                            <div class="case-info-header">
                                <div class="d-inline">
                                    <span class="case-no badge badge-warning">C{{ $case->ref_no }}</span>
                                </div>
                                <h4 class="case-title d-inline mb-0 px-2">
                                    {{ $case->title }}
                                </h4>
                            </div>
                            <div class="case-info-unit">
                                <span>Unit: {{ $case->unit->unit_no }}</span>
                                <span class="px-4">Unit Type:
                                    @if(!empty($case->unit->unit_type->name))
                                    {{ $case->unit->unit_type->name }}
                                    @else
                                    -
                                    @endif
                                </span>
                                <span class="px-4">Unit Owner Name:
                                    @if(!empty($case->unit->owner_name))
                                    {{ $case->unit->owner_name }} ({{ $case->unit->owner_contact_no }})
                                    @else
                                    -
                                    @endif
                                </span>
                            </div>
                            <div class="case-info-item d-flex flex-row">
                                <div class="info-name">
                                    Assigned CoW:
                                </div>
                                <div class="info-value flex-fill" id="assigned-cow-name">
                                    @if(!empty($case->assigned_cow))
                                    <button type="button" class="btn btn-sm btn-default" id="assign-cow-btn">
                                        {{ $case->assigned_cow->name }}
                                    </button>
                                    @else
                                    <button type="button" id="assign-cow-btn" class="btn btn-sm btn-secondary">
                                        Not Assigned
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div class="case-info-item d-flex flex-row">
                                <div class="info-name">
                                    Description:
                                </div>
                                <div class="info-value flex-fill">
                                    <button class="btn btn-sm btn-outline-light" data-toggle="modal"
                                        data-target="#case-description-modal" id="case-description-btn">View</button>
                                </div>
                            </div>
                            <div class="case-info-item d-flex flex-row">
                                <div class="info-name">
                                    Tags:
                                </div>
                                <div class="info-value flex-fill">
                                    <span class="edit-case-tags-section">
                                        <?php 
                                            $tags = "";
                                            foreach($case->tags as $tagEntry) {
                                                if(empty($tags)) {
                                                    $tags = $tags.$tagEntry->tag;
                                                } else {
                                                    $tags = $tags.",".$tagEntry->tag;
                                                }
                                            }
                                        ?>
                                        <div class="d-flex flex-row align-items-center">
                                            <div>
                                                <input type="text" id="case-tags-input" placeholder=""
                                                    value="{{ $tags }}">
                                            </div>
                                            <div class="pl-2">
                                                <button id="edit-case-tags-btn" class="btn btn-sm btn-light">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                                <button type="submit" id="save-case-tags-btn"
                                                    class="btn btn-sm btn-success" style="display: none">Save</button>
                                                <button type="submit" id="cancel-case-tags-btn" class="btn btn-sm"
                                                    style="display: none">Cancel</button>
                                            </div>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="case-info-item d-flex flex-row">
                                <div class="info-name">
                                    Report:
                                </div>
                                <div class="info-value flex-fill">
                                    <a href="{{ route("dev-admin.projects.cases.report", ['proj_id' => $proj_id, 'id' => $case->id]) }}"
                                        class="btn btn-sm btn-light"><i class="fas fa-file-excel"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="case-status-section pl-3">
                            <div class="case-info-header">
                                <div class="pr-3 text-right" style="margin-bottom:20px;">
                                    <a href="{{ route('dev-admin.projects.cases.index', $proj_id) }}"
                                        class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                                </div>
                            </div>
                            <div class="case-info-unit">
                                <div class="case-status dropdown">
                                    <button
                                        class="case-status-btn btn btn-sm btn-block dropdown-toggle case-status-{{$case->status}}"
                                        type="button" id="case-status-btn" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        {{ App\Constants\CaseStatus::$dict[$case->status] }}
                                    </button>
                                    <div class="dropdown-menu" id="case-status-list" aria-labelledby="case-status-btn">
                                        @foreach(App\Constants\CaseStatus::$dict as $status_code => $status_name)
                                        <a class="dropdown-item case-status" href="#"
                                            data-name="{{ $status_code }}">{{ $status_name }}</a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="case-defects-statistics p-1">
                                    <div>Outstanding Defects: <span class="outstanding-defects-count"></span></div>
                                    <div>Overdue Defects: <span class="overdue-defects-count"></span></div>
                                    <div>Closed Defects: <span class="closed-defects-count"></span></div>
                                    <div><strong>Total Defects: <span class="total-defects-count"></span></strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div id="defects-header" class="defects-header card-body bg-warning py-1 text-white d-flex flex-row">
                    <div class="flex-fill">
                        Defects
                    </div>
                    <div id="add-defect-btn" data-toggle="modal" data-target="#add-defect-modal" class="px-2">
                        <i class="fas fa-plus"></i>
                    </div>
                </div>
                <div class="defects-list card-body border-0 p-2">
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>

<!-- Assign CoW modal -->
<div id="assign-cow-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Assign CoW</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                    <label for="assign-cow-input" class="col-form-label">Assigned CoW:</label>
                    <select
                        class="selectpicker form-control form-control-alternative{{ $errors->has('name') ? ' is-invalid' : '' }}"
                        title="Choose CoW" data-live-search="true" placeholder="{{ __('Clerk of Work') }}"
                        value="{{ old('name') }}" id="assign-cow-input" autofocus required>

                        @foreach(App\Project::find($proj_id)->dev_cow_users()->get() as $cow)
                        <option value="{{ $cow->id }}">{{ $cow->name }}</option>
                        @endforeach
                    </select>

                    @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit-assign-cow-btn" class="btn btn-primary">Assign</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Contractor modal -->
<div id="assign-contractor-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Assign Contractor</h4>
                <button type="button" class="close" id="cancel-assign-contractor-btn" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label class="col-form-label">Currently Assigned Contractor:
                    <h3 id="current-assigned-contractor"></h3>
                </label>
                <div class="form-group">
                    <label for="assign-contractor-input" class="col-form-label">Assigned Contractor:</label>
                    <select class="tagsinput" id="contractor" name="contractor[]">
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit-assign-contractor-btn" class="btn btn-primary">Assign</button>
                <button type="button" id="cancel-assign-contractor-btn" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Close Defect -->
<div id="close-defect-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Close Defect - Confirm</h4>
                <button type="button" class="close" id="close-close-defect-modal-btn" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="closed-status-input">Closed Status (Optional)</label>
                    <select id="closed-status-input" class="form-control" name="closed-status">
                        <option value="" selected>None</option>
                        <option value="duplicate">Duplicate</option>
                        <option value="reject">Reject</option>
                    </select>
                </div>
                <div class="optional-form-sections">
                    <div class="duplicate-defect-section" style="display: none;">
                        <select id="duplicate-defect-select" class="repositories"
                            placeholder="Find duplicate defect by ID (eg. C1-D1) ..."></select>
                    </div>
                    <div class="reject-defect-section" style="display: none;">
                        <div class="form-group">
                            <label for="reject-reason-input">Reject Reason</label>
                            <input type="text" class="form-control" id="reject-reason-input"
                                placeholder="Please enter reason for rejection">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit-close-defect-btn" class="btn btn-primary">Close Defect</button>
                <button type="button" id="cancel-close-defect-btn" class="btn btn-secondary">Cancel</button>
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
<div id="case-description-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Case Description</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="case-description-editor">

                </div>
                <div id="case-description-error" class="mt-2">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="save-case-description-btn" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div id="add-defect-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Defect</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="defect-title-input" class="col-form-label">Title:</label>
                <div class="form-group">
                    <input type="text" class="form-control" id="defect-title-input">

                    <span class="invalid-feedback" role="alert"> </span>
                </div>

                <label for="add-defect-type-input" class="col-form-label">Defect Type:</label>
                <div class="form-group">
                    <select class="selectpicker form-control" id="add-defect-type-input" data-live-search="true">
                        @foreach($defectTypes as $defectType)
                        <option value="{{ $defectType->id }}">{{ $defectType->title }}</option>
                        @endforeach
                    </select>

                    <span class="invalid-feedback" role="alert"> </span>
                </div>
                <div class="form-group">
                    <label for="add-defect-description-editor" class="col-form-label">Description:</label>
                    <div id="add-defect-description-editor"></div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="submit-add-defect-btn" class="btn btn-primary"
                    onclick="return confirm('Are you sure you want to add this defect?')">Add</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
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
                            <span>Unit: {{ $case->unit->unit_no }}</span>
                            <span class="px-4">Unit Type:
                                @if(!empty($case->unit->unit_type->name))
                                {{ $case->unit->unit_type->name }}
                                @else
                                -
                                @endif
                            </span>
                            <span class="px-4">Owner:
                                @if(!empty($case->unit->owner_name))
                                {{ $case->unit->owner_name }} ({{ $case->unit->owner_contact_no }})
                                @else
                                -
                                @endif
                            </span>
                        </div>
                        <div class="defect-info-item d-flex flex-row">
                            <div class="info-name">
                                Due Date:
                            </div>
                            <div class="info-value flex-fill">
                                <div class="d-flex align-items-center view-due-date-section">
                                    <span class="defect-due-date pr-2">

                                    </span>
                                    <span class="defect-extended-count badge badge-light" style="display: none;">
                                        (Extended: <span class="count"></span>)
                                    </span>
                                    <button id="extend-defect-due-date-btn" class="btn btn-sm btn-light"><i
                                            class="fas fa-pen"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="defect-info-item d-flex flex-row">
                            <div class="info-name">
                                Defect Type:
                            </div>
                            <div class="info-value flex-fill">
                                <span id="defect-type-view" class="badge badge-primary"></span>
                                <span class="edit-defect-type-section" style="display:none;">
                                    <div class="d-flex flex-row align-items-center">
                                        <div>
                                            <select class="selectpicker my-1 mr-sm-2" id="defect-type-input" data-live-search="true">
                                                @foreach($defectTypes as $defectType)
                                                <option value="{{ $defectType->id }}">{{ $defectType->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="pl-2">
                                            <button type="submit" id="save-defect-type-btn"
                                                class="btn btn-sm btn-success">Save</button>
                                            <button type="submit" id="cancel-edit-defect-type-btn"
                                                class="btn btn-sm">Cancel</button>
                                        </div>
                                    </div>
                                </span>
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
                                        <div class="pl-2">
                                            <button id="edit-defect-tags-btn" class="btn btn-sm btn-light">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button type="submit" id="save-defect-tags-btn"
                                                class="btn btn-sm btn-success" style="display: none;">Save</button>
                                            <button type="submit" id="cancel-edit-defect-tags-btn" class="btn btn-sm"
                                                style="display: none;">Cancel</button>
                                        </div>
                                    </div>
                                </span>
                            </div>
                        </div>
                        <div class="defect-info-item d-flex flex-row">
                            <div class="info-name">
                                Report:
                            </div>
                            <div class="info-value flex-fill">
                                <span class="edit-defect-report-section">
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
                                id="defect-status-btn" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                (Status)
                            </button>
                            <div class="dropdown-menu" id="defect-status-list" aria-labelledby="defect-status-btn">
                                @foreach(App\Constants\DefectStatus::$dict as $status_code => $status_name)
                                <a class="dropdown-item defect-status" href="#"
                                    data-name="{{ $status_code }}">{{ $status_name }}</a>
                                @endforeach
                            </div>
                        </div>
                        <div class="additional-status-info-section pt-2">
                            <div class="closed-status-info additional-info" style="display: none;">
                                <div class="additional-info-type"><strong>Closed Status</strong></div>
                                <div class="closed-status additional-info-value font-weight-bold text-warning">
                                    (Closed Status)
                                </div>
                                <div class="duplicate-defect-section additional-info mt-1" style="display: none;">
                                    <div class="additional-info-type"><strong>Duplicate Defect</strong></div>
                                    <div class="additional-info-value">
                                        <div class="defect-info-card p-1">
                                            <div><span class="badge badge-warning defect-ref-no">(Defect No)</span>
                                            </div>
                                            <div><span class="defect-title">(Defect Title)</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="reject-reason-section additional-info mt-1" style="display: none;">
                                    <div class="additional-info-type"><strong>Reason</strong></div>
                                    <div class="reject-reason additional-info-value">
                                        (Reject Reason)
                                    </div>
                                </div>
                            </div>
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
                <div class="tab-pane fade" id="pills-description" role="tabpanel"
                    aria-labelledby="pills-description-tab">
                    <div class="p-2">
                        <div id="editor-custom-btn-container" class="px-4 py-2">
                            <div id="edit-description-btn" class="btn btn-light btn-sm"><i class="far fa-edit"></i>
                            </div>
                            <div id="save-description-btn" class="btn btn-primary btn-sm" style="display: none;"><i
                                    class="far fa-save"></i></div>
                            <div id="cancel-edit-description-btn" class="btn btn-light btn-sm" style="display: none;">
                                Cancel</div>
                        </div>
                        <div id="description-editor">

                        </div>
                        <div id="defect-description-error" class="mt-2">
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-location" role="tabpanel" aria-labelledby="pills-location-tab">
                    <div id="floor-plan-menu" class="d-flex flex-row align-items-center m-2">
                        <div class="flex-fill">
                            <span class="floor-view">Floor: <strong class="floor-name">(Floor Name)</strong></span>
                            <div class="floor-select-input-container form-inline" style="display: none;">
                                <label for="floor-select-input">Floor: </label>
                                <select class="custom-select mx-2 py-1" id="floor-select-input">
                                    <option selected>Choose...</option>
                                    @foreach($case->unit->unit_type->floors as $floor)
                                    <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="btn-container">
                            <div id="edit-pins-btn" class="btn btn-light btn-sm"><i class="far fa-edit"></i></div>
                            <div id="save-pin-btn" class="btn btn-primary btn-sm" style="display: none;"><i
                                    class="far fa-save"></i></div>
                            <div id="cancel-edit-pins-btn" class="btn btn-light btn-sm" style="display: none;">Cancel
                            </div>
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
                    <div id="add-defect-image-section" class="m-2">
                        <div class="add-defect-image-container" style="display: none;">
                            <div class="d-flex flex-row pt-2 px-2 align-items-center">
                                <div class="flex-fill">
                                    <h4 class="mb-0">Add Defect Image</h4>
                                </div>
                                <div>
                                    <button id="cancel-add-defect-img-btn" type="button" class="btn btn-sm"><i
                                            class="fas fa-times"></i></button>
                                </div>
                            </div>
                            <div class="d-flex flex-row p-2">
                                <div class="flex-fill custom-file mr-2">
                                    <input type="file" class="custom-file-input" id="defect-img-file-input" required>
                                    <label class="custom-file-label" for="defect-img-file-input">Choose file...</label>
                                    {{-- <div class="invalid-feedback">Example invalid custom file feedback</div> --}}
                                </div>
                                <div class="">
                                    <input id="submit-defect-img-btn" type="submit" class="btn btn-primary"
                                        value="Upload File" name="submit" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <div class="defect-assigned-contractor">Contractor:
                        <span class="contractor-name"></span>
                    </div>
                </div>
                <div class="defect-type px-4">
                    <span class="badge badge-primary"></span>
                </div>
                <div class="defect-status-section">
                    <div class="defect-status text-right"></div>
                    <div class="defect-due-date text-right"><i class="far fa-clock"></i>
                        <span></span></div>
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
                <div>
                    <button class="delete-btn option-btn"><i class="fas fa-trash"></i></button>
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
            <input class="edit-label-input" style="display:none;" type="text" value="">
        </span>
        <div class="options-section" style="display: none;">
            <a class="edit-btn px-1"><i class="fas fa-edit"></i></a>
            <a class="complete-edit-btn px-1" style="display: none;"><i class="fas fa-check"></i></a>
            <a class="delete-pin-btn px-1"><i class="fas fa-trash"></i></a>
        </div>
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
                    <p class="request-type-section card-text mb-1">Request: <span class="request-type">(Request
                            Type)</span></p>
                    <p class="request-reason-section card-text"><span class="font-weight-bold">Reason:</span> <span
                            class="reason">(Reason)</span></p>
                    <div class="request-response-section card-text py-1 px-2"><span class="font-weight-bold">
                            <div class="pending-response-details" style="display: none;">
                                <span class="pr-3">Pending Approval</span>
                                <button class="approve-btn btn btn-sm">Approve</button>
                                <button class="reject-btn btn btn-sm btn-warning">Reject</button>
                            </div>
                            <div class="post-response-details" style="display: none;">
                                <span class="pr-3">
                                    Response: <span class="response badge">(Response)</span>
                                    by: <span class="response-user">(User)</span><span
                                        class="response-user-role badge badge-primary">(Role)</span>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"
    integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" crossorigin="anonymous"></script>
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.js"></script>
<link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.default.css" />

<script type="text/javascript">
    // SECTION: Case

    $(function() {
        var caseId = {!! $case->id !!};

        var $caseTagsSelectize = $('#case-tags-input').selectize({
            delimiter: ',',
            persist: false,
            maxOptions: 10,
            create: function(input, callback) {
                return {
                    value: input,
                    text: input
                }
            }
        })
        caseTagsSelectize = $caseTagsSelectize[0].selectize;
        caseTagsSelectize.lock();

        $('.case-info-section #edit-case-tags-btn').click(function() {
            caseOnEditCaseTagsClicked();
        })
        $('.case-info-section #save-case-tags-btn').click(function() {
            caseOnSaveCaseTagsClicked();
        })
        $('.case-info-section #cancel-case-tags-btn').click(function() {
            caseOnCancelCaseTagsClicked();
        })

        function caseOnEditCaseTagsClicked() {
            $('.case-info-section #edit-case-tags-btn').css('display', 'none');
            $('.case-info-section #save-case-tags-btn').css('display', '');
            $('.case-info-section #cancel-case-tags-btn').css('display', '');

            caseTagsSelectize.unlock();
        }

        function caseOnSaveCaseTagsClicked() {
            var caseTags = $('#case-tags-input').val();
            console.log(caseId);
            postCaseTags(caseTags, caseId, function() {
                $('.case-info-section #edit-case-tags-btn').css('display', '');
                $('.case-info-section #save-case-tags-btn').css('display', 'none');
                $('.case-info-section #cancel-case-tags-btn').css('display', 'none');

                caseTagsSelectize.lock();
            });
        }

        function caseOnCancelCaseTagsClicked() {
            $('.case-info-section #edit-case-tags-btn').css('display', '');
            $('.case-info-section #save-case-tags-btn').css('display', 'none');
            $('.case-info-section #cancel-case-tags-btn').css('display', 'none');

            caseTagsSelectize.lock();
        }



        // SECTION: Edit Case Description
        var caseDescriptionEditor = new Quill('#case-description-editor', {
            theme: 'snow',
            modules: {
            }
        });
        var caseDescriptionData = {!! !empty($case->description)?$case->description : 'null' !!};
        if(caseDescriptionData) {
            caseDescriptionEditor.setContents(caseDescriptionData);
        }
        $('#case-description-modal #save-case-description-btn').click(function(event) {
            var modal = $('#case-description-modal');
            var description;
            if(caseDescriptionEditor.getLength() > 1) {
                description = JSON.stringify(caseDescriptionEditor.getContents());
            }
            
            postCaseDescription(description, function() {
                modal.modal('hide');
            });
        });


        var addDefectDescriptionEditor = new Quill('#add-defect-description-editor', {
            theme: 'snow',
        });

        $("#add-defect-modal #defect-due-date-input").datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            todayHighlight: true,
            todayBtn: true
        });

        $('#add-defect-modal #submit-add-defect-btn').click(function(e) {
            var modal = $('#add-defect-modal');
            
            var title = modal.find('#defect-title-input').val();
            var type = modal.find('#add-defect-type-input').val();
            var dueDate = modal.find('#defect-due-date-input').val();
            var description;

            if(addDefectDescriptionEditor.getLength() > 1) {
                description = JSON.stringify(addDefectDescriptionEditor.getContents());
            }

            // var newDefectData = {
            //     title: title,
            //     type: type,
            //     due_date: dueDate,
            //     description: description
            // };
            clearAddDefectInvalidFeedback();
            postDefect(title, type, dueDate, description, function() {
                modal.modal('hide');
                clearAddDefectModal();
                getDefectsAndUpdateDefectsList();
            }, function(xhr) {
                if(xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    console.log("Error 422: ", xhr);
                    for (var errorField in errors) {
                        if (errors.hasOwnProperty(errorField)) {
                            console.log("Error: ", errorField);
                            switch (errorField) {
                                case 'title':
                                    var inputEl = $('#add-defect-modal input#defect-title-input');
                                    inputEl.closest(".form-group").addClass('has-danger');
                                    inputEl.removeClass('remove-danger');
                                    inputEl.addClass(
                                        'is-invalid');
                                        inputEl.next('.invalid-feedback').html(
                                        '<strong>' + errors[errorField][0] +
                                        '</strong>');
                                    break;
                                case 'defect_type_id':
                                    var inputEl = $('#add-defect-modal select#add-defect-type-input');
                                    inputEl.closest(".form-group").addClass(
                                        'has-danger');
                                        inputEl.removeClass('remove-danger');
                                    inputEl.addClass(
                                        'is-invalid');
                                        inputEl.next('.invalid-feedback').html(
                                        '<strong>' + errors[errorField][0] +
                                        '</strong>');
                                    break;
                                case 'due_date':
                                    var inputEl = $('#add-defect-modal input#defect-due-date-input');
                                    inputEl.closest(".form-group").addClass(
                                        'has-danger');
                                        inputEl.removeClass('remove-danger');
                                    inputEl.addClass(
                                        'is-invalid');
                                        inputEl.next('.invalid-feedback').html(
                                        '<strong>' + errors[errorField][0] +
                                        '</strong>');
                                    break;
                            }
                        }
                    }
                }
            });

        })

        var caseStatusDict = {!! json_encode(App\Constants\CaseStatus::$dict) !!};
        
        $('#case-status-list .case-status').click(function(event) {
            var caseStatusEl = $(event.currentTarget);
            caseStatusVal = caseStatusEl.data('name');

            if(caseStatusVal == 'closed'){
                getDefects(function(defects) {
                    var unclosedDefects = 0;
                    for(defect of defects){
                        if(defect.status !== 'closed'){
                           unclosedDefects = unclosedDefects+1;
                        } else {
                        }
                    }
                    if (unclosedDefects != 0) {
                        return alert('Unable to close case due to unresolved or open defect(s)');
                    } else {
                        updateCaseStatus(caseStatusVal);
                    }
                })
            } else {
                updateCaseStatus(caseStatusVal);
            };
        });

        function updateCaseStatus(caseStatusVal){
            postCaseStatus(caseStatusVal, function() {
                caseStatus = caseStatusVal;
                var caseStatusBtnEl = $('#case-status-btn');
                caseStatusBtnEl.text(getCaseStatusName(caseStatusVal));
                updateCaseStatusBtnColorClass(caseStatusBtnEl, caseStatusVal);
            });
        };

        function updateCaseStatusBtnColorClass(caseStatusBtnEl, status) {
            var lastBtnStatus = caseStatusBtnEl.data('status');
            var newStatusClass = "case-status-" + status;
            if (lastBtnStatus == status) {
                return;
            }

            if(status == 'open'){
                caseStatusBtnEl.removeClass("case-status-closed");
            } else if (status == 'closed'){
                caseStatusBtnEl.removeClass("case-status-open");
            }
            
            caseStatusBtnEl.addClass(newStatusClass);
            caseStatusBtnEl.data('status', status);
        }

        function getCaseStatusName(statusCode) {
            for (var prop in caseStatusDict) {
                if (caseStatusDict.hasOwnProperty(prop)) {
                    if(prop == statusCode) {
                        return caseStatusDict[prop];
                    }
                }
            }
        }

	    $('#assign-cow-btn').click(function(event) {
            var modal = $('#assign-cow-modal');
            modal.modal('show');
        });
        	 
        
        $('#assign-cow-modal #submit-assign-cow-btn').click(function(event){
            var modal = $('#assign-cow-modal');
            var assignedCowInputEl = $('#assign-cow-input');
            assignedCowId = assignedCowInputEl.val();
            postCaseAssignedCow(assignedCowId, function() {
                var assignedCowInputEl = $('#assign-cow-input');
                var assignedCowBtnEl = $('#assign-cow-btn');
                assignedCowBtnEl.removeClass('btn-secondary');
                assignedCowBtnEl.addClass('btn-default');
                $('#assign-cow-btn').text(assignedCowInputEl.find('option:selected').text());
            });
            modal.modal('hide');
        });

        $('#activity-image-modal').on('hide.bs.modal', function (event) {
            var sourceEl = $(event.relatedTarget)
            var modal = $(this);
            
            $('#defect-modal').modal('show');
        });

        $('#defect-image-modal').on('hide.bs.modal', function (event) {
            var sourceEl = $(event.relatedTarget)
            var modal = $(this);
            
            $('#defect-modal').modal('show');
        });

	    $('.assigned-contractor').click(function(event) {
            var modal = $('#assign-contractor-modal');
            var prevModal = $('#defect-modal');
            prevModal.modal('hide');

            modal.modal('show');
        });		   

        $('#assign-contractor-modal').on('hide.bs.modal', function (event) {
            var modal = $(this);
            var $select = $('#contractor').selectize();
            var control = $select[0].selectize;
            control.clear();
            control.clearOptions();
        })

        $('#assign-contractor-modal #submit-assign-contractor-btn').click(function(event){
            var assignContractorModal = $('#assign-contractor-modal');
            var modal = $('#defect-modal');

            var assignedContractorInputEl = assignContractorModal.find('#contractor');
            assignedContractorId = assignedContractorInputEl.val();
            var assignedContractorName = assignedContractorInputEl.find('option:selected').text();
            var assignedContractorEl = modal.find('.assigned-contractor');
            postDefectAssignedContractor(assignedContractorId, function(a) {
                var assignedContractorEl = $('#defect-modal').find('.assigned-contractor');
                assignedContractorEl.removeClass('btn-secondary');
                assignedContractorEl.addClass('btn-default');
                assignedContractorEl.text(assignedContractorName);
                getDefectsAndUpdateDefectsList();
            });
            closeCurrentModalAndOpenPrevModal();
        });

        $('#assign-contractor-modal #cancel-assign-contractor-btn').click(function(event){
            closeCurrentModalAndOpenPrevModal();
        })
    });

    $('#close-defect-modal').on('hidden.bs.modal', function () {
        $('#closed-status-input').val("");
        var closeDefectModal = $('#close-defect-modal');
        closeDefectModal.find('.optional-form-sections>div').css('display', 'none');

        $('#reject-reason-input').val("");
        var $select = $('#duplicate-defect-select').selectize();

        var duplicateDefectSelect = $select[0].selectize;
        duplicateDefectSelect.clear();
    })
    
    function clearCommentTextArea(modal) {
        modal.find('#comment-textarea').val('');
    }
   
    function closeCurrentModalAndOpenPrevModal() {
        var modal = $('#assign-contractor-modal');
        var prevModal = $('#defect-modal');

        modal.modal('hide');
        prevModal.modal('show');
    }


    // SECTION: Defects
    var descriptionEditor = new Quill('#description-editor', {
        theme: 'snow',
        modules: {
        }
    });
    descriptionEditor.disable();

    var urlParamDefectId = getUrlParam('defect_id', null);
    var defectId;
    var defectInfo;
    var defectStatus;
    
    // Defect Related Params
    var defectTagsSelectize;

    var defectStatusDict = {!! json_encode(App\Constants\DefectStatus::$dict) !!};
    $('#defect-modal').on('show.bs.modal', function (event) {
        var sourceEl = $(event.relatedTarget)

        elDefectId = sourceEl.data('defect-id')
        if(!elDefectId && !urlParamDefectId) {
            return;
        }

        if(urlParamDefectId) {
            defectId = urlParamDefectId;
            urlParamDefectId = null;
        } else {
            defectId = elDefectId;
        }

        var modal = $(this);
 
        modal.find('.nav-tabs #pills-activity-tab').tab('show');
        getDefectAndUpdateModal(defectId, modal);         
        defectModalHideCommentImages(modal);
        defectModalActivityShowLoading(modal);
        startDefectActivityUpdate(modal);
    }); 

    $('#defect-modal').on('hide.bs.modal', function (event) {
        var modal = $(this);
        clearCommentTextArea(modal);
        stopDefectActivityUpdate();
    });
    
    $('#add-defect-modal').on('hidden.bs.modal', function (event) {
        clearAddDefectModal();
    });

    var defectActivityUpdateInterval;
    var lastUpdateTime;
    function startDefectActivityUpdate(modal) {
        defectModalActivityClearContent(modal);
        lastUpdateTime = null;
        getDefectActivitiesAndUpdateModal(defectId, modal);

        defectActivityUpdateInterval = setInterval(function() {
            getDefectActivitiesAndUpdateModal(defectId, modal);
        }, 60000);
    }

    function stopDefectActivityUpdate() {
        clearInterval(defectActivityUpdateInterval);
    }

    function getDefectActivitiesAndUpdateModal(defectId, modal, reset) {
        if(reset) {
            lastUpdateTime = null;
        }

        getDefectActivities(defectId, lastUpdateTime, function(defectActivities) {
            let activityList = modal.find('.activity-list');
            if(reset) {
                modal.find('.activity-list').empty();
            }
            // defectActivities = defectActivities.reverse();
            
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

                    newCommentEl.prependTo(activityList);
                } else if(activity.type === "update") {
                    let newUpdateEl = $('#templates .activity-update').clone();

                    newUpdateEl.find('.updater-user-name h5').text(activity.user.name);
                    newUpdateEl.find('.update-date-time small').text(activity.created_at);
                    newUpdateEl.find('.update').text(activity.content);

                    newUpdateEl.prependTo(activityList);
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

                    newRequestEl.prependTo(activityList);
                }
            }

            defectModalActivityShowContent(modal);

            // Last update time 
            if(defectActivities.length > 0) {
                lastUpdateTime = defectActivities[defectActivities.length-1].created_at;
            }
        })
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
    
    function updateDefectStatusColorClass(statusCode) {
        var lastClass = $('#defect-status-btn').attr('class').split(' ').pop();
        $('#defect-status-btn').removeClass(lastClass);
        $('#defect-status-btn').addClass(getDefectStatusClass(statusCode));
    }

    function clearAddDefectModal() {
        $('#add-defect-modal #defect-title-input').val("");
        $('#add-defect-modal #add-defect-type-input').val('');
        $('#add-defect-modal #defect-due-date-input').val('');
        $('#add-defect-modal .ql-editor').text("");

        clearAddDefectInvalidFeedback();
    }

    function clearAddDefectInvalidFeedback() {
        $('#add-defect-modal .has-danger').removeClass('has-danger');
        $('#add-defect-modal .is-invalid').removeClass('is-invalid');
    }

    function getDefectStatusName(statusCode) {
        for (var prop in defectStatusDict) {
            if (defectStatusDict.hasOwnProperty(prop)) {
                if(prop == statusCode) {
                    return defectStatusDict[prop];
                }
            }
        }
    } 

    $("#defect-modal #defect-due-date-input").datepicker({
        autoclose: true,
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        todayBtn: true
    });

    // SECTION: UI
    
    var caseStatus = '{{$case->status}}';
    
    function enableDisableCaseFunction(){
        if(caseStatus !== 'closed'){
            $('#assign-cow-btn').prop('disabled', false);
            $('#case-description-btn').prop('disabled', false);
            $('#edit-case-tags-btn').prop('disabled', false);
            $('#save-case-tags-btn').prop('disabled', false);
            $('#add-defect-btn').prop('disabled', false);
            $('#add-defect-btn').removeAttr('onclick');
        } else {
            $('#assign-cow-btn').prop('disabled', true);
            $('#case-description-btn').prop('disabled', true);
            $('#edit-case-tags-btn').prop('disabled', true);
            $('#save-case-tags-btn').prop('disabled', true);
            $('#add-defect-btn').prop('disabled', true);
            $('#add-defect-btn').attr('onclick', 'alert("You are unable to add defects to a closed case.")');
        }
    };
    
    enableDisableCaseFunction();
    
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
        modalEl.find("#pills-activity .activity-list").on("click", ".activity-request .approve-btn", function(event) {
            defectModalOnDefectRequestApproveClicked(modalEl, event.currentTarget);  
        })
        modalEl.find("#pills-activity .activity-list").on("click", ".activity-request .reject-btn", function(event) {
            defectModalOnDefectRequestRejectClicked(modalEl, event.currentTarget);  
        })
        modalEl.find("#pills-activity .activity-list").on("click", ".view-btn", function(event) {
            defectModalOnActivityImageViewClicked(modalEl, event.currentTarget);
        })
        modalEl.find("#pills-description #save-description-btn").click(function() {
            defectModalOnSaveDescriptionClicked(modalEl);
        })
        modalEl.find("#pills-description #edit-description-btn").click(function() {
            defectModalOnEditDescriptionClicked(modalEl);
        })
        modalEl.find("#pills-description #cancel-edit-description-btn").click(function() {
            defectModalOnCancelEditDescriptionClicked(modalEl);
        })
        modalEl.find("#pills-location #edit-pins-btn").click(function() {
            defectModalOnEditPinsClicked(modalEl);
        })
        modalEl.find("#pills-location #save-pin-btn").click(function() {
            defectModalOnSavePinsClicked(modalEl);
        })
        modalEl.find("#pills-location #floor-select-input").on('change', function(e){
            defectModalOnFloorChanged(modalEl, this.value);
        });
        modalEl.find("#pills-location #cancel-edit-pins-btn").click(function() {
            defectModalOnCancelEditPinsClicked(modalEl);
        })
        modalEl.find("#pills-location #floor-plan-control-panel .pin-list").on('click', ".edit-btn", function(event) {
            defectModalOnLocationPinEntryEditBtnClicked(modalEl, event.currentTarget);
        })
        modalEl.find("#pills-location #floor-plan-control-panel .pin-list").on('click', ".complete-edit-btn", function(event) {
            defectModalOnLocationPinEntryCompleteEditBtnClicked(modalEl, event.currentTarget);
        })
        modalEl.find("#pills-location #floor-plan-control-panel .pin-list").on('click', ".delete-pin-btn", function(event) {
            defectModalOnLocationPinEntryDeleteBtnClicked(modalEl, event.currentTarget);
        })
        modalEl.find("#pills-location #floor-plan-control-panel .pin-list").on('input', ".edit-label-input", function(event) {
            defectModalOnLocationPinEntryEditLabelInputClicked(modalEl, event.currentTarget);
        })
        modalEl.find("#pills-location #floor-plan-control-panel .add-pin-btn").click(function() {
            defectModalOnLocationAddPinBtnClicked(modalEl);
        })
        modalEl.find("#pills-location #floor-plan-control-panel .pin-list").on('click', ".pin-no", function(event) {
            defectModalOnLocationPinEntryPinNoClicked(modalEl, event.currentTarget);
        })
        modalEl.find("#pills-images").on("click", ".add-image-btn", function() {
            defectModalOnAddImageClicked(modalEl);
        })
        modalEl.find("#pills-images #defect-images-container").on("click", ".view-btn", function(event) {
            defectModalOnImageViewClicked(modalEl, event.currentTarget);
        })
        modalEl.find("#pills-images #defect-images-container").on("click", ".delete-btn", function(event) {
            defectModalOnDeleteImageClicked(modalEl, event.currentTarget);
        })
        modalEl.find("#pills-images #cancel-add-defect-img-btn").click(function() {
            defectModalOnCancelAddImageClicked(modalEl);
        })
        modalEl.find("#pills-images #submit-defect-img-btn").click(function() {
            defectModalOnSubmitAddImageClicked(modalEl);
        })
        modalEl.find('.defect-info-section #extend-defect-due-date-btn').click(function() {
            defectModalOnExtendDueDateClicked(modalEl);
        })
        modalEl.find('.defect-info-section #defect-type-view').click(function() {
            if(defectStatus != 'closed') {
                defectModalOnDefectTypeClicked(modalEl);
            }
        })
        modalEl.find('.defect-info-section #save-defect-type-btn').click(function() {
            defectModalOnSaveEditDefectTypeClicked(modalEl);
        })
        modalEl.find('.defect-info-section #cancel-edit-defect-type-btn').click(function() {
            defectModalOnCancelEditDefectTypeClicked(modalEl);
        })
        modalEl.find('#defect-status-list .defect-status').click(function() {
            defectModalOnDefectStatusOptionClicked(modalEl, $(event.currentTarget));
        })
        modalEl.find('.defect-info-section #edit-defect-tags-btn').click(function() {
            defectOnEditDefectTagsClicked(modalEl);
        })
        modalEl.find('.defect-info-section #save-defect-tags-btn').click(function() {
            defectOnSaveDefectTagsClicked(modalEl);
        })
        modalEl.find('.defect-info-section #cancel-edit-defect-tags-btn').click(function() {
            defectOnCancelEditDefectTagsClicked(modalEl);
        })
    }
    initModal($('#defect-modal'));

    function initCloseDefectModal() {
        var closeDefectModal = $('#close-defect-modal');

        closeDefectModal.on('hide.bs.modal', function () {
            $('#defect-modal').modal('show');
        })
        closeDefectModal.find('#close-close-defect-modal-btn').click(function() {
            closeDefectModal.modal('hide');
        });
        closeDefectModal.find('#cancel-close-defect-btn').click(function() {
            closeDefectModal.modal('hide');
        });

        closeDefectModal.find('#closed-status-input').change(function(e) {
            var closedStatus = $(e.currentTarget).val();
            console.log("Closed Status: ", closedStatus);

            closeDefectModal.find('.optional-form-sections>div').css('display', 'none');
            switch(closedStatus) {
                case 'duplicate':
                    closeDefectModal.find('.optional-form-sections .duplicate-defect-section').css('display', '');
                    break;
                case 'reject':
                    closeDefectModal.find('.optional-form-sections .reject-defect-section').css('display', '');
                    break;
            }
        })

        var duplicateDefectSelect = closeDefectModal.find('#duplicate-defect-select');
        duplicateDefectSelect.selectize({
            valueField: 'id',
            labelField: 'full_ref_no',
            searchField: 'full_ref_no',
            create: false,
            load: function(query, callback) {
                postSearchDefects(query, function(defects) {
                    for(defect of defects) {
                        defect.full_ref_no = "C" + defect.case_ref_no + "-D" + defect.ref_no;
                    }
                    callback(defects);
                }, function() {
                    callback();
                })
            },
            render: {
                option: function(item, escape) {
                    return "<div class='p-1'>"
                    + "<div><span class='badge badge-warning'>C" + item.case_ref_no + "-D" + item.ref_no + "</span> " + item.title + "</div>"
                    + "</div>";
                }
            },
        })
        var duplicateDefectSelect = duplicateDefectSelect[0].selectize;

        closeDefectModal.find('#submit-close-defect-btn').click(function (e) {
            var closedStatus = closeDefectModal.find('#closed-status-input').val();
            if(!closedStatus) {
                updateDefectStatus("closed", null, function() {
                    closeDefectModal.modal('hide');

                    onDefectStatusUpdated($('#defect-modal'));
                })
            } else {
                switch(closedStatus) {
                    case 'duplicate':
                        var duplicateDefectId = duplicateDefectSelect.getValue();
                        if(duplicateDefectId) {
                            updateDefectStatus("closed", {
                                closedStatus: closedStatus,
                                duplicateDefectId: duplicateDefectId,
                            }, function() {
                                closeDefectModal.modal('hide');

                                onDefectStatusUpdated($('#defect-modal'));
                            })  
                        } else {
                            alert('Please select a duplicated defect.');
                        }
                        
                        break;
                    case 'reject':
                        var rejectReason = closeDefectModal.find('#reject-reason-input').val();
                        if(rejectReason) {
                            updateDefectStatus("closed", {
                                closedStatus: closedStatus,
                                rejectReason: rejectReason
                            }, function() {
                                closeDefectModal.modal('hide');

                                onDefectStatusUpdated($('#defect-modal'));
                            })  
                        } else {
                            alert('Please state your reason to reject this defect.');
                        }
                        break;
                }
            }
        });

    }
    initCloseDefectModal();

    function defectModalOnDefectStatusOptionClicked(modalEl, defectStatusEl) {
        defectStatusVal = defectStatusEl.data('name');
        if(caseStatus == 'closed'){
            alert('You are unable to change defect status from a case that have been already closed');
        }
        else{
            defectStatusVal = defectStatusEl.data('name');

            if(defectStatusVal != 'closed') {
                updateDefectStatus(defectStatusVal, null, function () {
                    onDefectStatusUpdated(modalEl);
                });
            } else {
                showCloseDefectModal(modalEl);
            }
        }
    };

    function updateDefectStatus(defectStatus, additionalData, onSuccess) {
        postDefectStatus(defectStatus, additionalData, defectId, function() {
            onSuccess();
        });
    }

    function onDefectStatusUpdated(modalEl) {
        var defectStatusBtnEl = modalEl.find('#defect-status-btn');

        defectStatusBtnEl.text(getDefectStatusName(defectStatusVal));
        updateDefectStatusBtnColorClass(defectStatusBtnEl, defectStatusVal);

        getDefectAndUpdateModal(defectId, modalEl);
        getDefectsAndUpdateDefectsList();
        getDefectActivitiesAndUpdateModal(defectId, modalEl);
        // getDefectsAndUpdateResolvedAndClosedDate(defectId, modalEl);
    }

    function showCloseDefectModal(modal) {
        var closeDefectModal = $('#close-defect-modal');
        
        // Hide defect modal
        modal.modal('hide');
        // Show close defect modal
        closeDefectModal.modal('show');
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

    function defectModalOnDefectRequestApproveClicked(modalEl, approveBtnEl) {
        var activityRequestEl = approveBtnEl.closest('.activity-request');
        activityRequestEl = $(activityRequestEl);
        
        var activityId = activityRequestEl.data('activity-id');

        postDefectRequestResponse(defectId, activityId, "approve", function () {
            getDefectAndUpdateModal(defectId, modalEl);
            getDefectActivitiesAndUpdateModal(defectId, modalEl, true);
        })
    }

    function defectModalOnDefectRequestRejectClicked(modalEl, rejectBtnEl) {
        var activityRequestEl = rejectBtnEl.closest('.activity-request');
        activityRequestEl = $(activityRequestEl);

        var activityId = activityRequestEl.data('activity-id');

        postDefectRequestResponse(defectId, activityId, "reject", function () {
            getDefectAndUpdateModal(defectId, modalEl);
            getDefectActivitiesAndUpdateModal(defectId, modalEl, true);
        })
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

    function defectModalOnSaveDescriptionClicked(modal) {
        if(descriptionEditor.getLength() > 1) {
            var content = JSON.stringify(descriptionEditor.getContents());
            postDefectDescription(defectId, content, function() {
                defectInfo.description = content;
                descriptionEditor.disable();
                modal.find('#pills-description #edit-description-btn').css('display', '');
                modal.find('#pills-description #save-description-btn').css('display', 'none');
                modal.find('#pills-description #cancel-edit-description-btn').css('display', 'none');
            });
        }
    }
    
    function defectModalOnEditDescriptionClicked(modal) {
        descriptionEditor.enable();

        modal.find('#pills-description #edit-description-btn').css('display', 'none');
        modal.find('#pills-description #save-description-btn').css('display', '');
        modal.find('#pills-description #cancel-edit-description-btn').css('display', '');
    }

    function defectModalOnCancelEditDescriptionClicked(modal) {
        descriptionEditor.disable();
        modal.find('#pills-description #edit-description-btn').css('display', '');
        modal.find('#pills-description #save-description-btn').css('display', 'none');
        modal.find('#pills-description #cancel-edit-description-btn').css('display', 'none');
        defectModalSetDescription(JSON.parse(defectInfo.description));
    }

    function defectModalOnSavePinsClicked(modal) {
        var floorId = modal.find('#floor-plan-menu #floor-select-input').val();
        postDefectPins(defectId, floorId, locationPinsData, function(updatedPins) {
            defectInfo.pins = updatedPins;

            modal.find('#pills-location #edit-pins-btn').css('display', '');
            modal.find('#pills-location #save-pin-btn').css('display', 'none');
            modal.find('#pills-location #cancel-edit-pins-btn').css('display', 'none');

            modal.find('#pills-location .floor-view').css('display', '');
            modal.find('#pills-location .floor-select-input-container').css('display', 'none');

            disableLocationPinsControlPanel(modal);
        });
    }
    function defectModalOnEditPinsClicked(modal) {
        modal.find('#pills-location #edit-pins-btn').css('display', 'none');
        modal.find('#pills-location #save-pin-btn').css('display', '');
        modal.find('#pills-location #cancel-edit-pins-btn').css('display', '');

        modal.find('#pills-location .floor-view').css('display', 'none');
        modal.find('#pills-location .floor-select-input-container').css('display', '');

        enableLocationPinsControlPanel(modal);
    }
    function defectModalOnCancelEditPinsClicked(modal) {
        // defectModalDisableEditLocationPin();
        modal.find('#pills-location #edit-pins-btn').css('display', '');
        modal.find('#pills-location #save-pin-btn').css('display', 'none');
        modal.find('#pills-location #cancel-edit-pins-btn').css('display', 'none');

        modal.find('#pills-location .floor-view').css('display', '');
        modal.find('#pills-location .floor-select-input-container').css('display', 'none');

        disableLocationPinsControlPanel(modal);
        initFloorPlan(modal);
    }

    function defectModalOnLocationPinEntryEditBtnClicked(modal, editBtnEl) {
        var pinEntryEl = $(editBtnEl).closest('.pin-entry');

        // Hide edit-btn and show complete edit btn
        pinEntryEl.find('.edit-btn').css('display', 'none');
        pinEntryEl.find('.complete-edit-btn').css('display', '');
        // Show input and hide view
        pinEntryEl.find('.pin-label .view').css('display', 'none');
        pinEntryEl.find('.pin-label .edit-label-input').css('display', '');
    }
    
    function defectModalOnLocationPinEntryCompleteEditBtnClicked(modal, editBtnEl) {
        var pinEntryEl = $(editBtnEl).closest('.pin-entry');

        // Show edit-btn and hide complete edit btn
        pinEntryEl.find('.edit-btn').css('display', '');
        pinEntryEl.find('.complete-edit-btn').css('display', 'none');
        // Hide input and show view
        pinEntryEl.find('.pin-label .view').css('display', '');
        pinEntryEl.find('.pin-label .edit-label-input').css('display', 'none');
    }
    
    function defectModalOnLocationPinEntryDeleteBtnClicked(modal, deleteBtnEl) {
        var pinEntryEl = $(deleteBtnEl).closest('.pin-entry');
        var pinNo = pinEntryEl.data('pin-no');

        pinEntryEl.remove();
        removePinsFromView(modal);
        locationPinsData.splice(pinNo-1, 1);

        reloadPinsOnView(modal, true);
    }

    function defectModalOnLocationPinEntryEditLabelInputClicked(modal, editLabelInputEl) {
        editLabelInputEl = $(editLabelInputEl);
        var pinEntryEl = editLabelInputEl.closest('.pin-entry');
        var pinNo = pinEntryEl.data('pin-no');
        var newLabel = editLabelInputEl.val();

        pinEntryEl.find('.pin-label .view').text(newLabel);
        locationPinsData[pinNo-1].label = newLabel;
    }

    function defectModalOnLocationAddPinBtnClicked(modal) {
        addNewLocationPinToPinList(modal);
    }

    function defectModalOnLocationPinEntryPinNoClicked(modal, pinNoEl) {
        selectPinEntry($(pinNoEl).closest('.pin-entry'));
    }

    function defectModalOnFloorChanged(modal, floorId) {
        changeFloor(modal, floorId);
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

    function defectModalOnDeleteImageClicked(modal, imageOptionBtnEl) {
        if (confirm("Are you sure you want to delete this image?")) {
            var imageModal = $('#defect-image-modal');
            // Get image URL
            var imgHolder = imageOptionBtnEl.closest('.defect-img-holder');
            imgHolder = $(imgHolder);

            var defectImageId = imgHolder.data('id');

            postDeleteDefectImage(defectImageId, defectId, function() {
                imgHolder.remove();
                // If add button not present in image container, add it
                var defectImagesContainerEl = modal.find('#pills-images #defect-images-container .images-flex-container');
                if(defectImagesContainerEl.find('.add-image-btn').length < 1) {
                    var addDefectImageBtnEl = $('#templates .add-image-btn').clone();
                    defectImagesContainerEl.append(addDefectImageBtnEl);
                }   
            });
        } 
    }

    function defectModalOnAddImageClicked(modal) {
        modal.find('#pills-images .add-defect-image-container').css('display', '');
        modal.find('#pills-images .add-defect-image-container #defect-img-file-input').val('');
    }
    function defectModalOnCancelAddImageClicked(modal) {
        modal.find('#pills-images .add-defect-image-container').css('display', 'none');
    }
    function defectModalOnSubmitAddImageClicked(modal) {
        var fileInputEl = modal.find('#pills-images #defect-img-file-input');
        readFileAsDataUrl(fileInputEl.get(0).files[0], function (dataUrl) {
            postDefectImage(defectId, dataUrl, function () {
                getDefectAndUpdateModal(defectId, modal);
                $('.custom-file-label').text('Choose file...')
                modal.find('#pills-images .add-defect-image-container').css('display', 'none');
                
            });
        });
    }

    function defectModalOnExtendDueDateClicked(modal) {
        var extendDueDateConfirmation = confirm("Are you sure you want to extend due date for another {{ App\Constants\DefectConfig::EXPIRY_DAYS }} days?");
        if (extendDueDateConfirmation == true) {
            postDefectExtendDueDate(defectId, function() {
                getDefectAndUpdateModal(defectId, modal);
            });
        }
    }

    function defectModalOnDefectTypeClicked(modal) {
        modal.find('#defect-type-view').css('display', 'none');
        modal.find('.edit-defect-type-section').css('display', '');
    }
    function defectModalOnSaveEditDefectTypeClicked(modal) {
        var defectTypeInputEl = modal.find('#defect-type-input');
        postDefectDefectType(defectTypeInputEl.val(), defectId, function() {
            var defectTypeEl = modal.find('#defect-type-view')
            defectTypeEl.text(defectTypeInputEl.find('option:selected').text());
            defectTypeEl.css('display', '');
            modal.find('.edit-defect-type-section').css('display', 'none');

            getDefectsAndUpdateDefectsList();
        });

        
    }
    function defectModalOnCancelEditDefectTypeClicked(modal) {
        modal.find('#defect-type-view').css('display', '');
        modal.find('.edit-defect-type-section').css('display', 'none');
    }

    function readFileAsDataUrl(file, callback) {
        var reader  = new FileReader();
        reader.addEventListener("load", function () {
            callback(reader.result);
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    function defectModalClearDescription() {
        descriptionEditor.setContents([{ insert: '\n' }]);
    }

    function defectModalSetDescription(description) {
        descriptionEditor.setContents(description);
    }

    $('#contractor').selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        sortField: 'text',
        preload: 'focus',
        load: function(query, callback) {
            postSearchContractors(query, defectInfo.defect_type_id, function(contractors) {
                callback(contractors);
            }, function() {
                callback();
            })
        },
    })

    function defectOnEditDefectTagsClicked(modal) {
        var editDefectTagsSectionEl = modal.find('.edit-defect-tags-section');
        editDefectTagsSectionEl.find('#edit-defect-tags-btn').css('display', 'none');
        editDefectTagsSectionEl.find('#save-defect-tags-btn').css('display', '');
        editDefectTagsSectionEl.find('#cancel-edit-defect-tags-btn').css('display', '');

        defectTagsSelectize.unlock();
    }

    function defectOnSaveDefectTagsClicked(modal) {
        var editDefectTagsSectionEl = modal.find('.edit-defect-tags-section');

        var defectTags = editDefectTagsSectionEl.find('.defect-tags-input').val();
        postDefectTags(defectTags, defectId, function() {
            console.log("Defect Tag Post: ");
            
            // modal.find('.edit-defect-tags-section').css('display', 'none');
            // modal.find('.view-defect-tags-section').css('display', '');

            // $('.defect-tags-list').text(defectTags);
            editDefectTagsSectionEl.find('#edit-defect-tags-btn').css('display', '');
            editDefectTagsSectionEl.find('#save-defect-tags-btn').css('display', 'none');
            editDefectTagsSectionEl.find('#cancel-edit-defect-tags-btn').css('display', 'none');

            defectTagsSelectize.lock();
        });

    }

    function defectOnCancelEditDefectTagsClicked(modal) {
        var editDefectTagsSectionEl = modal.find('.edit-defect-tags-section');

        editDefectTagsSectionEl.find('#edit-defect-tags-btn').css('display', '');
        editDefectTagsSectionEl.find('#save-defect-tags-btn').css('display', 'none');
        editDefectTagsSectionEl.find('#cancel-edit-defect-tags-btn').css('display', 'none');

        defectTagsSelectize.lock();
    }




    // SECTION: API
    function postCaseStatus(caseStatus, onSuccess) {
        var postCaseStatusRoute = "{{ route('dev-admin.projects.cases.ajax.status.post', ['project_id' => $proj_id, 'case_id' => $case->id]) }}";
        $.ajax({
            url: postCaseStatusRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: caseStatus,
            },
            success: function(response) {
                onSuccess();
                enableDisableCaseFunction();
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

    function postCaseAssignedCow(assignedCow, onSuccess){
        var postCaseAssignedCowRoute = "{{ route('dev-admin.projects.cases.ajax.assigned-cow.post', ['project_id' => $proj_id, 'case_id' => $case->id])}}"
        $.ajax({
            url: postCaseAssignedCowRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                assigned_cow_user_id: assignedCow,
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

    var postCaseTagsRouteTemplate = "{{ route('dev-admin.projects.cases.ajax.tags.post', ['proj_id' => $proj_id, 'id' => '<<id>>']) }}";
        function postCaseTags(caseTags, caseId, onSuccess) {
        var postCaseTagsRoute = postCaseTagsRouteTemplate.replace(encodeURI('<<id>>'), caseId);
        $.ajax({
            url: postCaseTagsRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tags: caseTags,
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

    var postDefectAssignedContractorRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.assigned-contractor.post', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>'])}}"
    function postDefectAssignedContractor(assignedContractor, onSuccess) {
    var postDefectAssignedContractorRoute = postDefectAssignedContractorRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postDefectAssignedContractorRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                assigned_contractor_user_id: assignedContractor,
            },
            success: function(response) {
                onSuccess();
                var assignedContractorEl = $('#defect-modal').find('.assigned-contractor');
                assignedContractorEl.tooltip({
                    items: assignedContractorEl,
                    content: `<strong>Name</strong> : ${response[1].assigned_contractor.name} <br> 
                                <strong>Email</strong> : ${response[1].assigned_contractor.email} <br>
                                <strong>Contact</strong> : ${response[1].assigned_contractor.contractor.contact_no} <br>
                                <strong>Address 1</strong> : ${response[1].assigned_contractor.contractor.address_1} <br>
                                <strong>Address 2</strong> : ${response[1].assigned_contractor.contractor.address_2} <br>
                                <strong>City</strong> : ${response[1].assigned_contractor.contractor.city} <br>
                                <strong>Postal Code</strong> : ${response[1].assigned_contractor.contractor.postal_code} <br>
                                <strong>State</strong> : ${response[1].assigned_contractor.contractor.state} <br>`
                });
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

    function postCaseDescription(description, onSuccess) {
        var postCaseDescriptionRoute = "{{ route('dev-admin.projects.cases.ajax.description.post', ['project_id' => $proj_id, 'case_id' => $case->id]) }}";
        $.ajax({
            url: postCaseDescriptionRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                description: description,
            },
            success: function(response) {
                onSuccess();
                $('case-description-error').html(``);
            },
            error: function(xhr) {
                if(xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                }
                $('#case-description-error').html(`<strong class="invalid-text">${xhr.responseJSON.errors.description}</strong>`);
            }
        });
    }

    $('#case-description-modal').on('hidden.bs.modal', function () {
        $('#case-description-error').html(``);
    })

    var postDefectStatusRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.status.post', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
    function postDefectStatus(defectStatus, additionalData, defectId, onSuccess) {
        var data = {
            _token: '{{ csrf_token() }}',
            status: defectStatus,
        };

        if(additionalData) {
            data.closed_status = additionalData.closedStatus;
            data.duplicate_defect_id = additionalData.duplicateDefectId;
            data.reject_reason = additionalData.rejectReason;
        }
        
        var postDefectStatusRoute = postDefectStatusRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postDefectStatusRoute,
            type: 'POST',
            data: data,
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

    var getDefectRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.info', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
    function getDefectsAndUpdateResolvedAndClosedDate(defectId, modalEl) {
        var getDefectRoute = getDefectRouteTemplate.replace(encodeURI('<<id>>'), defectId);

            $.ajax({
            url: getDefectRoute,
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                defectInfo = response;

                console.log(defectInfo);
                var defectInfoSectionEl = modalEl.find('.defect-info-section');
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

    var postDefectTagsRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.tags.post', ['proj_id' => $proj_id,'case_id' => $case->id , 'id' => '<<id>>']) }}";
    function postDefectTags(defectTags, defectId, onSuccess) {
        var postDefectTagsRoute = postDefectTagsRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postDefectTagsRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tags: defectTags,
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

    var postDefectExtendDueDateRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.extend-due-date.post', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
    function postDefectExtendDueDate(defectId, onSuccess) {
        var postDefectExtendDueDateRoute = postDefectExtendDueDateRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postDefectExtendDueDateRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
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

    var postDeleteDefectImageRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.image.delete', ['project_id' => $proj_id, 'case_id' => $case->id, 'defect_id' => '<<defect_id>>', 'id' => '<<id>>']) }}";
    function postDeleteDefectImage(defectImageId, defectId, onSuccess) {
        var postDeleteDefectImageRoute = postDeleteDefectImageRouteTemplate.replace(encodeURI('<<defect_id>>'), defectId);
        postDeleteDefectImageRoute = postDeleteDefectImageRoute.replace(encodeURI('<<id>>'), defectImageId)
        $.ajax({
            url: postDeleteDefectImageRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
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

    var postDefectDefectTypeRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.defect-type.post', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
    function postDefectDefectType(defectTypeId, defectId, onSuccess) {
        var postDefectDefectTypeRoute = postDefectDefectTypeRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postDefectDefectTypeRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                defect_type_id: defectTypeId,
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

    function getDefects(onSuccess) {
        var getDefectsRoute = "{{ route('dev-admin.projects.cases.defects.ajax.get', ['project_id' => $proj_id, 'case_id' => $case->id]) }}";
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

    var getDefectRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.info', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
    function getDefectsAndUpdateResolvedAndClosedDate(defectId, modalEl) {
        var getDefectRoute = getDefectRouteTemplate.replace(encodeURI('<<id>>'), defectId);

            $.ajax({
            url: getDefectRoute,
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                defectInfo = response;

                console.log(defectInfo);
                var defectInfoSectionEl = modalEl.find('.defect-info-section');
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

    var getDefectRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.info', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
    function getDefectAndUpdateModal(defectId, modal) {
        defectModalInfoShowLoading(modal);
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
                var DefectContractorAssignEl = $('#assign-contractor-modal').find('#current-assigned-contractor');
                
                defectInfoSectionEl.find('.defect-ref-no').text("D" + defectInfo.ref_no);
                defectInfoSectionEl.find('.defect-title').text(defectInfo.title);
                var dueDate = moment(defectInfo.due_date, "YYYY-MM-DD").format("DD/MM/YYYY");
                var submittedDate = moment(defectInfo.created_at, "YYYY-MM-DD").format("DD/MM/YYYY");
                defectInfoSectionEl.find('.defect-due-date').text(dueDate);
                if(defectInfo.extended_count){
                    defectInfoSectionEl.find('.defect-extended-count').css('display', '');
                    defectInfoSectionEl.find('.defect-extended-count .count').text(defectInfo.extended_count);
                } else {
                    defectInfoSectionEl.find('.defect-extended-count').css('display', 'none');
                }
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
                defectInfoSectionEl.find('#defect-due-date-input').datepicker('setDate', dueDate);
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
                
                var getDefectReportUrlRouteTemplate = "{{ route("dev-admin.projects.cases.defects.report", ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}"
                var getDefectReportUrlRoute = getDefectReportUrlRouteTemplate.replace(encodeURI('<<id>>'), defectId);
                defectInfoSectionEl.find('.edit-defect-report-section').html(`<a href=${getDefectReportUrlRoute} class="btn btn-sm btn-light"><i class="fas fa-file-excel"></i></a>`);
                defectInfoSectionEl.find('#defect-type-view').text(defectInfo.type? defectInfo.type.title : '-');
                defectInfoSectionEl.find('#defect-type-input').val(defectInfo.type? defectInfo.type.id : '');
                var assignedContractorEl = defectInfoSectionEl.find('.assigned-contractor')
                if(defectInfo.assigned_contractor != null) {
                    DefectContractorAssignEl.text(defectInfo.assigned_contractor.name);
                    assignedContractorEl.text(defectInfo.assigned_contractor.name);
                    assignedContractorEl.removeClass('btn-secondary');
                    assignedContractorEl.addClass('btn-default');

                    assignedContractorEl.tooltip({
                        items: assignedContractorEl,
                        content: `<strong>Name</strong> : ${defectInfo.assigned_contractor.name} <br> 
                                    <strong>Email</strong> : ${defectInfo.assigned_contractor.email} <br>
                                    <strong>Contact</strong> : ${defectInfo.assigned_contractor.contractor.contact_no} <br>
                                    <strong>Address 1</strong> : ${defectInfo.assigned_contractor.contractor.address_1} <br>
                                    <strong>Address 2</strong> : ${defectInfo.assigned_contractor.contractor.address_2} <br>
                                    <strong>City</strong> : ${defectInfo.assigned_contractor.contractor.city} <br>
                                    <strong>Postal Code</strong> : ${defectInfo.assigned_contractor.contractor.postal_code} <br>
                                    <strong>State</strong> : ${defectInfo.assigned_contractor.contractor.state} <br>`
                    });
                } else {
                    DefectContractorAssignEl.text("None");
                    assignedContractorEl.text('Assign');
                    assignedContractorEl.addClass('btn-secondary');
                    assignedContractorEl.removeClass('btn-default');
                    assignedContractorEl.tooltip({
                        items: assignedContractorEl,
                        content: ``
                    });
                }

                var defectStatusSectionEl = modal.find('.defect-status-section');
                
                var defectStatusBtn = defectStatusSectionEl.find('#defect-status-btn');
                defectStatus = defectInfo.status;
                defectStatusBtn.text(getDefectStatusName(defectInfo.status));
                updateDefectStatusBtnColorClass(defectStatusBtn, defectInfo.status);
                if(defectStatus == 'closed') {
                    defectInfoSectionEl.find('#extend-defect-due-date-btn').prop('disabled', true);
                    defectInfoSectionEl.find('#edit-defect-tags-btn').prop('disabled', true);
                    modal.find('#edit-description-btn').css('display', 'none');
                    modal.find('#edit-pins-btn').css('display', 'none');
                    modal.find('#add-image-btn').css('display', 'none');
                    assignedContractorEl.prop('disabled', true);
                    modal.find('.activity-add-comment-section').css('display', 'none');
                } else {
                    defectInfoSectionEl.find('#extend-defect-due-date-btn').prop('disabled', false);
                    defectInfoSectionEl.find('#edit-defect-tags-btn').prop('disabled', false);
                    modal.find('#edit-description-btn').css('display', '');
                    modal.find('#edit-pins-btn').css('display', '');
                    modal.find('#add-image-btn').css('display', '');
                    assignedContractorEl.prop('disabled', false);
                    modal.find('.activity-add-comment-section').css('display', '');
                }

                initDefectStatusInfoSection(defectInfo, defectStatusSectionEl);

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
                if(defectInfo.images.length < 5) {
                    // TODO put add image button
                    if(defectStatus !== 'closed') {
                        var addDefectImageBtnEl = $('#templates .add-image-btn').clone();
                        defectImagesContainerEl.append(addDefectImageBtnEl);
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

    function initDefectStatusInfoSection(defectInfo, defectStatusSectionEl) {
        var closedStatusInfoEl = defectStatusSectionEl.find('.additional-status-info-section .closed-status-info');        
        if(defectInfo.status != "closed" || !defectInfo.closed_status) {
            closedStatusInfoEl.css('display', 'none');
            return;
        }

        closedStatusInfoEl.css('display', '');
        closedStatusInfoEl.find('.duplicate-defect-section').css('display', 'none');
        closedStatusInfoEl.find('.reject-reason-section').css('display', 'none');
        switch(defectInfo.closed_status) {
            case "duplicate":
                closedStatusInfoEl.find('.closed-status').text('Duplicate');
                var duplicateDefectSectionEl = closedStatusInfoEl.find('.duplicate-defect-section');
                getDefectDuplicateDefectInfo(defectId, function(duplicateDefectInfo) {
                    duplicateDefectSectionEl.find('.defect-ref-no').text('C' + duplicateDefectInfo.case_ref_no + '-D' + duplicateDefectInfo.ref_no);
                    duplicateDefectSectionEl.find('.defect-title').text(duplicateDefectInfo.title);

                    var duplicateDefectCardEl = duplicateDefectSectionEl.find('.defect-info-card');
                    duplicateDefectCardEl.off('click').click(function() {
                        goToUrl(getDefectUrl(duplicateDefectInfo.case_id, duplicateDefectInfo.id));
                    });

                    duplicateDefectSectionEl.css('display', '');
                });
                break;
            case "reject":
                closedStatusInfoEl.find('.closed-status').text('Rejected');
                var rejectReasonSectionEl = closedStatusInfoEl.find('.reject-reason-section');
                rejectReasonSectionEl.css('display', '');
                rejectReasonSectionEl.find('.reject-reason').text(defectInfo.reject_reason);
                break;
        }   
    }

    var getDefectUrlRouteTemplate = "{{ route('dev-admin.projects.cases.view', ['project_id' => $proj_id, 'case_id' => '<<case_id>>']) }}";
    function getDefectUrl(caseId, defectId) {
        var getDefectUrlRoute = getDefectUrlRouteTemplate.replace(encodeURI('<<case_id>>'), caseId);
        return getDefectUrlRoute + '?defect_id=' + defectId;
    }

    var getDefectActivitiesRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.activities', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
    function getDefectActivities(defectId, lastUpdateTime, onSuccess) {
        var getDefectActivitiesRoute = getDefectActivitiesRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: getDefectActivitiesRoute,
            type: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
                last_update_time: lastUpdateTime
            },
            success: function(defectActivities) {
               onSuccess(defectActivities);
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

    var postDefectActivityCommentRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.activities.comment.post', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
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

    var postDefectDescriptionRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.description.post', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
    function postDefectDescription(defectId, description, onSuccess){

        var postDefectDescriptionRoute = postDefectDescriptionRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postDefectDescriptionRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                description: description,
            },
            success: function(response) {
                // TODO Show success message
                if(onSuccess) onSuccess();
            },
            error: function(xhr) {
                if(xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    console.log("Error 422: ", xhr);
                }
                
                $('#defect-description-error').html(`<strong class="invalid-text">${xhr.responseJSON.errors.description}</strong>`);
            }
        });
    }

    $('#defect-modal').on('hidden.bs.modal', function () {
        $('#defect-description-error').html(``);
    })

    var postDefectPinsRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.pins.post', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
    function postDefectPins(defectId, floorId, pins, onSuccess){
        var postDefectPinsRoute = postDefectPinsRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postDefectPinsRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                unit_type_floor_id: floorId, 
                pins: pins,
            },
            success: function(response) {
                // TODO Show success message
                if(onSuccess) onSuccess(response);
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

    var getDefectDuplicateDefectInfoRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.duplicate-defect-info.get', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
    function getDefectDuplicateDefectInfo(defectId, onSuccess) {
        var getDefectDuplicateDefectInfoRoute = getDefectDuplicateDefectInfoRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: getDefectDuplicateDefectInfoRoute,
            type: 'GET',
            success: function(duplicateDefectInfo) {
               onSuccess(duplicateDefectInfo);
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

    
    var postDefectImageRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.images.post', ['project_id' => $proj_id, 'case_id' => $case->id, 'id' => '<<id>>']) }}";
    function postDefectImage(defectId, imageDataUrl, onSuccess){

        var postDefectImageRoute = postDefectImageRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postDefectImageRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                image_data_url: imageDataUrl
            },
            success: function(response) {
                // TODO Show success message
                if(onSuccess) onSuccess();
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

    var postDefectRequestResponseRouteTemplate = "{{ route('dev-admin.projects.cases.defects.ajax.requests.response.post', ['project_id' => $proj_id, 'case_id' => $case->id, 'defect_id' => '<<defect_id>>', 'activity_id' => '<<activity_id>>']) }}";
    function postDefectRequestResponse(defectId, activityId, response, onSuccess){

        var postDefectRequestResponseRoute = postDefectRequestResponseRouteTemplate.replace(encodeURI('<<defect_id>>'), defectId);
        var postDefectRequestResponseRoute = postDefectRequestResponseRoute.replace(encodeURI('<<activity_id>>'), activityId);
        $.ajax({
            url: postDefectRequestResponseRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
               response: response
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
    
    var getDefectImageRouteTemplate = "{{ route('dev-admin.projects.cases.defects.images.get', ['project_id' => $proj_id, 'case_id' => $case->id, 'defect_id' => '<<defect_id>>', 'id' => '<<id>>']) }}";
    function getUrlForDefectImage(defectId, defectImageId) {
        var getDefectImageRoute = getDefectImageRouteTemplate.replace(encodeURI('<<defect_id>>'), defectId);
        var getDefectImageRoute = getDefectImageRoute.replace(encodeURI('<<id>>'), defectImageId);

        return getDefectImageRoute;
    }
    
    var getDefectActivityImageRouteTemplate = "{{ route('dev-admin.projects.cases.defects.activities.images.get', ['project_id' => $proj_id, 'case_id' => $case->id, 'defect_id' => '<<defect_id>>', 'activity_id' => '<<activity_id>>', 'id' => '<<id>>']) }}";
    function getUrlForDefectActivityImage(defectId, activityId, id) {
        var getDefectActivityImageRoute = getDefectActivityImageRouteTemplate.replace(encodeURI('<<defect_id>>'), defectId);
        var getDefectActivityImageRoute = getDefectActivityImageRoute.replace(encodeURI('<<activity_id>>'), activityId);
        var getDefectActivityImageRoute = getDefectActivityImageRoute.replace(encodeURI('<<id>>'), id);

        return getDefectActivityImageRoute;
    }

    var getActivityUserProfileImageRouteTemplate = "{{ route('dev-admin.projects.cases.defects.activities.user-profile-images.get', ['defect_id' => '<<defect_id>>', 'activity_id' => '<<activity_id>>', 'id' => '<<id>>']) }}";
    function getActivityUserProfileImage(defectId, activityId, id) {
        var getActivityUserProfileImageRoute = getActivityUserProfileImageRouteTemplate.replace(encodeURI('<<defect_id>>'), defectId);
        var getActivityUserProfileImageRoute = getActivityUserProfileImageRoute.replace(encodeURI('<<activity_id>>'), activityId);
        var getActivityUserProfileImageRoute = getActivityUserProfileImageRoute.replace(encodeURI('<<id>>'), id);

        return getActivityUserProfileImageRoute;
    }

    function postDefect(title, type, dueDate, description, onSuccess, onError){
        var postDefectRoute = "{{ route('dev-admin.projects.cases.defects.ajax.post', ['project_id' => $proj_id, 'case_id' => $case->id]) }}";
        $.ajax({
            url: postDefectRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                title: title,
                defect_type_id: type,
                due_date: dueDate,
                description: description
            },
            success: function(response) {
                onSuccess();
                clearAddDefectModal();
            },
            error: function(xhr) {
                onError(xhr);
            }
        });
    }

    $('#add-defect-modal').on('hidden.bs.modal', function () {
        $('#add-defect-description-error').html(``);
    })
    
    var getUnitTypeFloorPlanImageRouteTemplate = "{{ route('dev-admin.projects.unit-types.floors.floor-plan.get', ['proj_id' => $proj_id, 'unit_type_id' => $case->unit_id, 'id' => '<<id>>']) }}";
    function getUrlForUnitTypeFloorPlanImage(floorId, defectImageId) {
        var route = getUnitTypeFloorPlanImageRouteTemplate.replace(encodeURI('<<id>>'), floorId);

        return route;
    }

    var postSearchDefectsRouteTemplate = "{{ route('dev-admin.projects.defects.ajax.search.post', ['proj_id' => $proj_id, 'defect_id' => '<<defect_id>>']) }}";
    
    function postSearchDefects(searchQuery, onSuccess, onError) {
        var postSearchDefectsRoute = postSearchDefectsRouteTemplate.replace(encodeURI('<<defect_id>>'), defectId);
        $.ajax({
            url: postSearchDefectsRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                q: searchQuery,
                page_limit: 10
            },
            success: function(response) {
                onSuccess(response);
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
    
    var postSearchContractorRouteTemplate = "{{ route('dev-admin.contractors.ajax.search.post') }}";
    function postSearchContractors(searchQuery, defectTypeId, onSuccess, onError) {
        var postSearchContractorRoute = postSearchContractorRouteTemplate.replace(encodeURI('<<id>>'), defectId);
        $.ajax({
            url: postSearchContractorRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                q: searchQuery,
                defect_type_id: defectTypeId,
                page_limit: 10
            },
            success: function(response) {
                onSuccess(response);
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
    // SECTION: Defect Pins on Image
    var floors = {!! $case->unit->unit_type->floors !!};
    var currentFloorId;

    var locationPinsData = [];
    var selectedPinNo;

    function initFloorPlan(modal) {
        // Remove old pins
        removePinsFromView(modal);

        // Store pins
        if(defectInfo.pins != null) {
            locationPinsData = JSON.parse(JSON.stringify(defectInfo.pins));
        }

        loadFloorPlanImageAndDetails(modal, defectInfo.unit_type_floor_id);
        loadPinsOnView(modal);
    }

    function changeFloor(modal, floorId) {
        currentFloorId = floorId;
        loadFloorPlanImageAndDetails(modal, currentFloorId);
        reloadPinsOnView(modal, true);
    }

    function loadFloorPlanImageAndDetails(modal, floorId) {
        if(floorId) {
            var imageUrl = getUrlForUnitTypeFloorPlanImage(floorId);

            var selectedFloor;
            for(floor of floors) {
                if(floor.id == floorId) {
                    selectedFloor = floor;
                    break;
                }
            }

            modal.find('#floor-plan-menu .floor-name').text(floor.name);
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

    function removePinsFromView(modal) {
        // TODO
        for (let i = 0; i < locationPinsData.length; i++) {
            var pinNo = i+1;
            clearPinOnImage(pinNo);
        }

        clearPinList(modal);
    }

    function reloadPinsOnView(modal, editMode) {
        removePinsFromView(modal);
        loadPinsOnView(modal, editMode);
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
        newPinEntryEl.find('.pin-label .edit-label-input').val(label);

        if(no == selectedPinNo) {
            newPinEntryEl.addClass('selected');
        }

        pinListEl.append(newPinEntryEl);
    }

    function addNewLocationPinToPinList(modal) {
        locationPinsData.push({
            label: '(Pin)',
        });

        reloadPinsOnView(modal, true);
    }

    function clearPinList(modal) {
        modal.find('#pills-location #floor-plan-control-panel .pin-list').empty();
    }

    function selectPinEntry(pinEntryEl) {
        // Highlight pin-entry
        pinEntryEl.closest('.pin-list').find('.pin-entry').removeClass('selected');
        pinEntryEl.addClass('selected');

        // Set selectedPinNo
        selectedPinNo = pinEntryEl.data('pin-no');
    }

    function enableLocationPinsControlPanel(modal) {
        // Show pin buttons
        modal.find('#pills-location #floor-plan-control-panel .pin-list .pin-entry .options-section').css('display', '');
        // Show add pin button
        modal.find('#pills-location #floor-plan-control-panel .add-pin-btn').css('display', '');
        
        defectModalEnableEditLocationPins();
    }

    function disableLocationPinsControlPanel(modal) {
        // Hide pin buttons
        modal.find('#pills-location #floor-plan-control-panel .pin-list .pin-entry .options-section').css('display', 'none');
        // Hide add pin button
        modal.find('#pills-location #floor-plan-control-panel .add-pin-btn').css('display', 'none');

        defectModalDisableEditLocationPins();
    }

    var floorPlanImgContainerEl = $('#floor-plan-img-container');
    function defectModalEnableEditLocationPins() {
        var floorPlanImageEl = floorPlanImgContainerEl.find('#floor-plan-image');
        floorPlanImageEl.unbind('click', onImageClickedPlacePin);
        floorPlanImageEl.on('click', onImageClickedPlacePin);
    }

    function defectModalDisableEditLocationPins() {
        selectedPinNo = null;
        floorPlanImgContainerEl.find('#floor-plan-image').unbind('click', onImageClickedPlacePin);
    }

    function onImageClickedPlacePin(event) {
        console.log("Place Pin")
        if(selectedPinNo) {
            var imgEl = $(event.target);

            var imgOffset = imgEl.offset();
            var x = event.pageX - imgOffset.left;
            var y = event.pageY - imgOffset.top;
            var ratioX = x/imgEl.width();
            var ratioY = y/imgEl.height();

            // placePinOnImage(selectedPinNo, ratioX, ratioY);
            locationPinsData[selectedPinNo-1].x = ratioX;
            locationPinsData[selectedPinNo-1].y = ratioY;

            reloadPinsOnView($('#defect-modal'), true);
        }
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

    function clearPinOnImage(pinNo) {
        floorPlanImgContainerEl.find('.defect-location-pin.pin-' + pinNo).remove();
    }
    
    function clearFloorPlanUi(modal) {
        modal.find('#floor-plan-menu .floor-name').text("(No floor selected)");
        modal.find('#floor-plan-menu #floor-select-input').val(null);
        modal.find('#floor-plan-image').attr('src', null);
    }

    function enableDisableDefectFunctions(modal) {
        modal.find('#extend-defect-due-date-btn').disable;
        modal.find('')
    }

    // SECTION: Init
    function getDefectsAndUpdateDefectsList() {
        getDefects(function(defects) {
            var defectsListEl = $('.defects-list');
            defectsListEl.empty();
            for(defect of defects) {
                var newDefectEl = $('#templates .defect-card').clone();
                newDefectEl.find('.card-body').attr('data-defect-id', defect.id);
                newDefectEl.find('.defect-ref-no').text('D' + defect.ref_no);
                newDefectEl.find('.defect-title').text(defect.title);
                var contractorNameEl = newDefectEl.find('.defect-assigned-contractor .contractor-name');
                if(defect.assigned_contractor) {
                    contractorNameEl.text(defect.assigned_contractor.name);
                    contractorNameEl.addClass('contractor-tooltip-' + defect.assigned_contractor_user_id);

                    var tooltipEl = newDefectEl.find('.defect-assigned-contractor .contractor-tooltip-' + defect.assigned_contractor_user_id);
                    tooltipEl.tooltip({
                        items: tooltipEl,
                        content: `<strong>Name</strong> : ${defect.assigned_contractor.name} <br> 
                                    <strong>Email</strong> : ${defect.assigned_contractor.email} <br>
                                    <strong>Contact</strong> : ${defect.assigned_contractor.contractor.contact_no} <br>
                                    <strong>Address 1</strong> : ${defect.assigned_contractor.contractor.address_1} <br>
                                    <strong>Address 2</strong> : ${defect.assigned_contractor.contractor.address_2} <br>
                                    <strong>City</strong> : ${defect.assigned_contractor.contractor.city} <br>
                                    <strong>Postal Code</strong> : ${defect.assigned_contractor.contractor.postal_code} <br>
                                    <strong>State</strong> : ${defect.assigned_contractor.contractor.state} <br>`
                    });
                } else {
                    contractorNameEl.text("(Not Assigned)");
                    contractorNameEl.css('color', 'red');
                }
                newDefectEl.find('.defect-type span').text(defect.type? defect.type.title : '-');
                var defectStatusDisplayEl = newDefectEl.find('.defect-status-section .defect-status');
                defectStatusDisplayEl.text(getDefectStatusName(defect.status));
                updateDefectStatusBtnColorClass(defectStatusDisplayEl, defect.status);
                
                var defectDueDate = moment(defect.due_date, "YYYY-MM-DD").format("DD/MM/YYYY");
                newDefectEl.find('.defect-status-section .defect-due-date span').text(defectDueDate);

                defectsListEl.append(newDefectEl);
            }

            // Update defect stats
            var outstandingDefectsCount = 0;
            var overdueDefectsCount = 0;
            var closedDefectsCount = 0;
            var now = moment();
            for(defect of defects) {
                if(defect.status != "{{ App\Constants\DefectStatus::CLOSED }}") {
                    outstandingDefectsCount++;
                    if(defect.due_date && now.isAfter(moment(defect.due_date), 'day')) {
                        overdueDefectsCount++;
                    }
                } else {
                    closedDefectsCount++;
                }
            }

            var caseDefectsStatisticsEl = $('.case-card .case-status-section .case-defects-statistics');
            caseDefectsStatisticsEl.find('.outstanding-defects-count').text(outstandingDefectsCount);
            caseDefectsStatisticsEl.find('.overdue-defects-count').text(overdueDefectsCount);
            caseDefectsStatisticsEl.find('.closed-defects-count').text(closedDefectsCount);
            caseDefectsStatisticsEl.find('.total-defects-count').text(defects.length);

            // When first time, open defect if url param has defect_id
            if(urlParamDefectId) {
                $('#defect-modal').modal('show');
            }
        });
    }
    getDefectsAndUpdateDefectsList();
    
    // SECTION: Utility
    function getUrlVars() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
            vars[key] = value;
        });
        return vars;
    }

    function getUrlParam(parameter, defaultvalue){
        var urlparameter = defaultvalue;
        if(window.location.href.indexOf(parameter) > -1){
            urlparameter = getUrlVars()[parameter];
            }
        return urlparameter;
    }

    function goToUrl(url) {
        window.location.href = url;
    }
</script>
@endpush