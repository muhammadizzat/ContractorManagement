@extends('layouts.app', ['title' => __('Unit Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class= "col">
                            <h3 class="mb-0">{{ __('Add Unit')}} </h3>
                        </div>
                        <div class= "pr-3 text-right">
                            <a href="{{ route('dev-cow.projects.units.index', $proj_id) }}" class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form role="form" method="post" action="{{route('dev-cow.projects.units.add.post', $proj_id)}}" autocomplete="off" novalidate>
                        @csrf
                        <h3 class="heading-small text-muted mb-4">{{ __('Unit Details') }}</h3>
                        <div class="pl-lg-4">
                            <label class="form-control-label" for="input-unit-no">{{ __('Unit No') }}</label>
                            <div class="form-group{{ $errors->has('unit_no') ? ' has-danger' : '' }}">
                                <input type="text" name="unit_no" id="input-unit-no"
                                    class="form-control form-control-alternative{{ $errors->has('unit_no') ? ' is-invalid' : '' }}" placeholder="{{ __('Unit No') }}" value="{{ old('unit_no') }}" required autofocus>
                                @if ($errors->has('unit_no'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('unit_no') }}</strong>
                                </span>
                                @endif
                            </div>
                            <label class="form-control-label" for="input-unit-type-id">{{ __('Unit Type') }}</label>
                            <div class="form-group{{ $errors->has('unit_type_id') ? ' has-danger' : '' }}">
                                <select name="unit_type_id" id="unit_type_id" class="selectpicker form-control form-control-alternative{{ $errors->has('unit_type_id') ? ' is-invalid' : '' }}" placeholder="{{ __('Unit Type ID') }}" title="Choose Unit Type" data-live-search="true" required>
                                    @foreach($unit_types as $unit_type) 
                                    <option value="{{ $unit_type->id }}">{{ $unit_type->name }} </option>
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
                                    class="form-control form-control-alternative{{ $errors->has('owner_name') ? ' is-invalid' : '' }}" placeholder="{{ __('Owner Name') }}" value="{{ old('owner_name') }}">
                            </div>

                            <label class="form-control-label"
                                for="input-owner-contact-no">{{ __('Owner Contact No') }}</label>
                            <div class="form-group{{ $errors->has('owner_contact_no') ? ' has-danger' : '' }}">
                                <input type="text" name="owner_contact_no" id="input-owner-contact-no" class="form-control form-control-alternative{{ $errors->has('owner_contact_no') ? ' is-invalid' : '' }}"
                                maxlength="15" placeholder="{{ __('Owner Contact No') }}" value="{{ old('owner_contact_no') }}">
                                @if ($errors->has('owner_contact_no'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('owner_contact_no') }}</strong>
                                </span>
                                @endif
                            </div>

                            <label class="form-control-label"
                                for="input-owner-email">{{ __('Owner Email Address') }}</label>
                            <div class="form-group{{ $errors->has('owner_email') ? ' has-danger' : '' }}">
                                <input type="text" name="owner_email" id="input-owner-email"
                                    class="form-control form-control-alternative{{ $errors->has('owner_email') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Owner Email Address') }}" value="{{ old('owner_email') }}">

                                @if ($errors->has('owner_email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('owner_email') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="text-center">
                                <button onclick="return confirm('Are you sure you want to add this unit?')" type="submit" class="btn btn-success mt-4">{{ __('Add') }}</button>
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