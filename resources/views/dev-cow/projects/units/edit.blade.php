@extends('layouts.app', ['title' => __('User Profile')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Edit Unit') }}</h3>
                        </div>
                        <div class="pr-3 text-right">
                            <a href="{{ route('dev-cow.projects.units.index', $proj_id) }}"
                                class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post"
                        action="{{ route('dev-cow.projects.units.edit.post', ['proj_id' => $proj_id, 'units' => $id]) }}"
                        autocomplete="off" novalidate>
                        @csrf

                        <h6 class="heading-small text-muted mb-4">{{ __('Edit Unit information') }}</h6>

                        <div class="pl-lg-4">
                            <label class="form-control-label" for="input-unit-no">{{ __('Unit No') }}</label>
                            <div class="form-group{{ $errors->has('unit_no') ? ' has-danger' : '' }}">
                                <input type="text" name="unit_no" id="input-unit-no"
                                    class="form-control form-control{{ $errors->has('unit_no') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Unit No') }}" value="{{ old('unit_no', $units->unit_no) }}"
                                    required>
                                @if ($errors->has('unit_no'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('unit_no') }}</strong>
                                </span>
                                @endif
                            </div>

                                <label class="form-control-label" for="input-unit-type-id">{{ __('Unit Type') }}</label>
                                <div class="form-group{{ $errors->has('unit_type_id') ? ' has-danger' : '' }}">
                                    <select name="unit_type_id" id="unit_type_id"
                                    class="selectpicker form-control form-control-alternative{{ $errors->has('unit_type_id') ? ' is-invalid' : '' }}"
                                    data-live-search="true" required>
                                    @foreach($unit_types as $unit_type)
                                    @if(($units->unit_type_id) == ($unit_type->id))
                                    <option value="{{ $unit_type->id }}" selected>{{ $unit_type->name }}</option>
                                    @else
                                        <option value="{{ $unit_type->id }}">{{ $unit_type->name }}</option>
                                    @endif
                                    @endforeach
                                </select>

                                @if ($errors->has('unit_type_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('unit_type_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <h3 class="heading-small text-muted mb-4">{{ __('Owner Details') }}</h3>
                        <div class="pl-lg-4">

                            <label class="form-control-label" for="input-owner-name">{{ __('Owner Name') }}</label>
                            <div class="form-group{{ $errors->has('owner_name') ? ' has-danger' : '' }}">
                                <input type="text" name="owner_name" id="input-owner-name"
                                    class="form-control form-control{{ $errors->has('owner_name') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Owner Name') }}"
                                    value="{{ old('owner_name', $units->owner_name) }}">

                                @if ($errors->has('owner_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('owner_name') }}</strong>
                                </span>
                                @endif
                            </div>

                            <label class="form-control-label"
                                for="input-owner-contact-no">{{ __('Owner Contact No') }}</label>
                            <div class="form-group{{ $errors->has('owner_contact_no') ? ' has-danger' : '' }}">
                                <input type="text" name="owner_contact_no" id="input-owner-contact-no"
                                    class="form-control form-control{{ $errors->has('owner_contact_no') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Owner Contact No') }}"
                                    value="{{ old('owner_contact_no', $units->owner_contact_no) }}">

                                @if ($errors->has('owner_contact_no'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('owner_contact_no') }}</strong>
                                </span>
                                @endif
                            </div>

                            <label class="form-control-label" for="input-owner-email">{{ __('Owner Email') }}</label>
                            <div class="form-group{{ $errors->has('owner_email') ? ' has-danger' : '' }}">
                                <input type="text" name="owner_email" id="input-owner-contact-no"
                                    class="form-control form-control{{ $errors->has('owner_email') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Owner Email') }}"
                                    value="{{ old('owner_email', $units->owner_email) }}">

                                @if ($errors->has('owner_email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('owner_email') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="text-center">
                            <button onclick="return confirm('Are you sure to edit this unit?')" type="submit"
                                class="btn btn-success mt-4">{{ __('Save') }}</button>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.footers.auth')
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
    });
</script>
@endpush