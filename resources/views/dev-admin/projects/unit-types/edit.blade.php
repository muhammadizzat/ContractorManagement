@extends('layouts.app', ['title' => __('Unit Type Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div id="page-edit-unit-type" class="container-fluid mt--7">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8 order-xl-1">
            <div id="edit-unit-type-card" class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Edit Unit Type') }}</h3>
                        </div>
                        <div class="pr-3 text-right">
                            <a href="{{ route('dev-admin.projects.unit-types.index', $proj_id) }}"
                                class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post"
                        action="{{ route('dev-admin.projects.unit-types.edit.post', ['proj_id' => $proj_id, 'id' => $id]) }}"
                        autocomplete="off" novalidate>
                        @csrf
                        <h6 class="heading-small text-muted mb-4">{{ __("Edit Unit Type ($unit_type->name)") }}</h6>

                        @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                        <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                            <input type="text" name="name" id="input-name"
                                class="form-control form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                placeholder="{{ __('Unit Type name') }}" value="{{ old('name', $unit_type->name) }}"
                                required>

                            @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="text-center">
                            <button onclick="return confirm('Are you sure to edit this unit type?')" type="submit"
                                class="btn btn-success mt-4">{{ __('Save') }}</button>
                        </div>
                    </form>

                    <div class="pt-2">
                        <label class="form-control-label" for="input-name">{{ __('Floors') }}</label>
                        <div id="floors-list">
                            @foreach($unit_type->floors as $floor)
                            <div class="floor-card card mb-1">
                                <div class="card-body p-2">
                                    <div class="d-flex flex-row align-items-center">
                                        <div class="flex-fill">
                                            <strong>Floor</strong> {{ $floor->name }}
                                        </div>
                                        <div>
                                            <button class="btn btn-sm mr-0" data-toggle="modal"
                                                data-target="#view-floor-modal" data-floor-name="{{ $floor->name }}"
                                                data-id="{{ $floor->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm" data-toggle="modal"
                                                data-target="#edit-floor-modal" data-floor-name="{{ $floor->name }}"
                                                data-id="{{ $floor->id }}">
                                                <i class="far fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @endforeach
                            {{-- <div class="add-floor-card card mb-1">
                                btn
                            </div> --}}
                            <button id="add-floor-btn" class="btn" data-toggle="modal" data-target="#add-floor-modal">
                                Add a floor ...
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
<div class="modal fade" id="add-floor-modal" tabindex="-1" role="dialog" aria-labelledby="add-floor-modal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Floor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <div class="form-group">
                    <label for="add-floor-name-input" class="col-form-label">Floor Name:</label>
                    <input type="text" class="form-control" id="add-floor-name-input">
                </div>
                <label for="add-floor-plan-img-file-input" class="col-form-label">Floor Plan Image:</label>
                <div class="d-flex flex-row">
                    <div class="flex-fill custom-file mr-2" style="overflow:hidden;">
                        <input type="file" class="custom-file-input" id="add-floor-plan-img-file-input"
                            accept=".png,.jpg,.jpeg" required>
                        <label class="custom-file-label" for="add-floor-plan-img-file-input">Choose file...</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="submit-add-floor-btn" type="button" class="btn btn-primary">Add Floor</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="view-floor-modal" tabindex="-1" role="dialog" aria-labelledby="view-floor-modal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Floor <span class="floor-name">1</span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="floor-plan-img-holder modal-body pt-2">
                <h5>Floor Plan</h5>
                <img src="" alt="">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-floor-modal" tabindex="-1" role="dialog" aria-labelledby="edit-floor-modal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Floor</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-2">
                <div class="form-group">
                    <label for="edit-floor-name-input" class="col-form-label">Floor Name:</label>
                    <input type="text" class="form-control" id="edit-floor-name-input" placeholder="">
                </div>
                <label for="edit-floor-plan-img-file-input" class="col-form-label">Floor Plan Image:</label>
                <div class="floor-plan-img-holder modal-body pt-2">
                    <img src="" alt="">
                </div>
                <div class="d-flex flex-row">
                    <div class="flex-fill custom-file mr-2">
                        <input type="file" class="custom-file-input" id="edit-floor-plan-img-file-input"
                            accept=".png,.jpg,.jpeg" required>
                        <label class="custom-file-label" for="edit-floor-plan-img-file-input"
                            id="floor-plan-image-file-input">Choose file...</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="submit-delete-floor-btn" type="button" class="btn btn-danger">Delete</button>
                <button id="submit-edit-floor-btn" type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(function() {

        $("#datepicker").datepicker({
            autoclose: true,
            todayHighlight : true,
            todayBtn : true,
        });

        $('#add-floor-modal #submit-add-floor-btn').click(function() {
            var name = $('#add-floor-modal #add-floor-name-input').val();
            var floorPlanImgInputEl = $('#add-floor-modal #add-floor-plan-img-file-input');
            console.log("Name: ", name);

            const fileType = floorPlanImgInputEl.get(0).files[0].type;
            if (fileType !== 'image/png' && fileType !== 'image/jpeg') {
                alert('Please upload a png or jpeg image.');
                return false;
            }

            if(!name) {
                alert('Please insert name'); 
                return false; 
            }

            readFileAsDataUrl(floorPlanImgInputEl.get(0).files[0], function (floorPlanImgDataUrl) {
                postUnitTypeFloor(name, floorPlanImgDataUrl,  function() {
                    location.reload();
                })
            });
        });

        var floorPlanId;
        $('#edit-floor-modal').on('show.bs.modal', function (event) {
            var sourceEl = $(event.relatedTarget) 
            var id = sourceEl.data('id')
            var floorName = sourceEl.data('floor-name')
            console.log("Floor -> ID:" + id+ ", Name:" + floorName);

            var modal = $(this);
            
            floorPlanId = id;
            modal.find('#edit-floor-name-input').val(floorName);
            modal.find('.floor-plan-img-holder img').attr('src', floorPlanImgRoute.replace(encodeURI('<<id>>'), id));
        });

        $('#edit-floor-modal #submit-edit-floor-btn').click(function() {
            var name = $('#edit-floor-modal #edit-floor-name-input').val();
            var floorPlanImgInputEl = $('#edit-floor-modal #edit-floor-plan-img-file-input');
            
            const fileType = floorPlanImgInputEl.get(0).files[0].type;
            if (fileType !== 'image/png' && fileType !== 'image/jpeg') {
                alert('Please upload a png or jpeg image.');
                return false;
            }

            console.log("Post Edit Name: ", floorPlanId);

            if(!name) {
                alert('Please insert name'); 
                return false; 
            }

            readFileAsDataUrl(floorPlanImgInputEl.get(0).files[0], function (floorPlanImgDataUrl) {
                postEditUnitTypeFloor(name, floorPlanImgDataUrl, floorPlanId, function() {
                    location.reload();
                })
            });
        });

        $('#edit-floor-modal #submit-delete-floor-btn').click(function() {
            if (confirm('Are you sure you want to delete this floor?')){
                postDeleteUnitTypefloor(floorPlanId, function() {
                    location.reload();
                });
            }
        });

        var floorPlanImgRoute = "{{ route('dev-admin.projects.unit-types.floors.floor-plan.get', ['proj_id' => $proj_id, 'unit_type_id' => $unit_type->id, 'id' => '<<id>>']) }}"
        $('#view-floor-modal').on('show.bs.modal', function (event) {
            var sourceEl = $(event.relatedTarget) 
            var id = sourceEl.data('id')
            var floorName = sourceEl.data('floor-name')
            console.log("Floor -> ID:" + id+ ", Name:" + floorName);

            var modal = $(this);
        
            modal.find('.floor-name').text(floorName);
            modal.find('.floor-plan-img-holder img').attr('src', floorPlanImgRoute.replace(encodeURI('<<id>>'), id));
        });
    });

    // SECTION: UI
    function initModal(modalEl) {
        modalEl.find('#edit-floor-plan-img-file-input').change(function(event) {
            console.log("something changed");
            var fileInputEl = event.currentTarget;
            floorPlanOnSubmitAddImageSelected(modalEl, fileInputEl.files[0]);
        })
    }
    initModal($('#edit-floor-modal'));

    function floorPlanOnSubmitAddImageSelected(modal, file) {
        var fileInputEl = modal.find('#edit-floor-plan-img-file-input');
        readFileAsDataUrl(file, function (dataUrl) {
            modal.find('.floor-plan-img-holder img').attr('src', dataUrl);
        });    
    }

    function postUnitTypeFloor(name, floorPlanImgDataUrl,  onSuccess) {
        var postUnitTypeFloorRoute = "{{ route('dev-admin.projects.unit-types.floors.ajax.post', ['proj_id' => $proj_id, 'id' => $id]) }}";
        $.ajax({
            url: postUnitTypeFloorRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                name: name,
                floor_plan_data_url: floorPlanImgDataUrl
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

    function postEditUnitTypeFloor(name, floorPlanImgDataUrl, floorPlanId, onSuccess) {
        var postEditUnitTypeFloorRoute = "{{ route('dev-admin.projects.unit-types.floors.edit.post', ['proj_id' => $proj_id, 'id' => $id]) }}";
        $.ajax({
            url: postEditUnitTypeFloorRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                floorPlanId: floorPlanId,
                name: name,
                floor_plan_data_url: floorPlanImgDataUrl
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

    function postDeleteUnitTypefloor(floorPlanId, onSuccess) {
        var postDeleteUnitTypeFloorRoute = "{{ route('dev-admin.projects.unit-types.floors.delete.post', ['proj_id' => $proj_id, 'id' => $id]) }}";
        $.ajax({
            url: postDeleteUnitTypeFloorRoute,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                floorPlanId: floorPlanId
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

    function readFileAsDataUrl(file, callback) {
        var reader  = new FileReader();
        reader.addEventListener("load", function () {
            callback(reader.result);
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        } else {
            callback();
        }
    }
</script>
@endpush