@extends('layouts.app', ['title' => __('User Profile')])

@section('content')
@include('users.partials.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Edit Contractor') }}</h3>
                        </div>
                        <div class="pr-3 text-right">
                            <a href="{{ route('admin.contractors.index') }}"
                                class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-12">
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
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.contractors.edit.post', ['id' => $user->id]) }}"
                        autocomplete="off" novalidate>
                        @csrf
                        <h6 class="heading-small text-muted mb-4">{{ __('Contractor information') }}</h6>

                        @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <div class="pl-lg-4">
                            <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                            <div class="form-group mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-single-02"></i></span>
                                    </div>
                                    <input type="name" name="name" id="input-name" class="form-control"
                                        placeholder="{{ __('Name') }}" value="{{ old('name', $user->name) }}" required>
                                    @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <label class="form-control-label" for="input-email">{{ __('Email')}}</label>
                            <div class="form-group mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input type="email" name="email" id="input-email" class="form-control"
                                        placeholder="{{ __('Email') }}" value="{{ old('email', $user->email) }}"
                                        readonly>
                                    @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <label class="form-control-label" for="input-contact_no">{{ __('Contact No.')}}</label>
                            <div class="form-group{{ $errors->has('contact_no') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-phone-square"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('contact_no') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Contact No.') }}" type="tel" name="contact_no"
                                        value="{{ old('contact_no', $contractor->contact_no) }}" required>
                                </div>
                                @if ($errors->has('contact_no'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('contact_no') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label class="form-control-label"
                                        for="input-address_1">{{ __('Street Address')}}</label>
                                    <div class="form-group{{ $errors->has('address_1') ? ' has-danger' : '' }}">
                                        <div class="input-group input-group-alternative mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-road"></i></span>
                                            </div>
                                            <input
                                                class="form-control{{ $errors->has('address_1') ? ' is-invalid' : '' }}"
                                                placeholder="{{ __('Street Address') }}" type="address_1"
                                                name="address_1" value="{{ old('address_1', $contractor->address_1) }}"
                                                required>
                                        </div>
                                        @if ($errors->has('address_1'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('address_1') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-control-label"
                                        for="input-address_2">{{ __('Street Address Line 2')}}</label>
                                    <div class="form-group{{ $errors->has('address_2') ? ' has-danger' : '' }}">
                                        <div class="input-group input-group-alternative mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-map-signs"></i></span>
                                            </div>
                                            <input
                                                class="form-control{{ $errors->has('address_2') ? ' is-invalid' : '' }}"
                                                placeholder="{{ __('Street Address Line 2') }}" type="address_2"
                                                name="address_2" value="{{ old('address_2', $contractor->address_2) }}"
                                                required>
                                        </div>
                                        @if ($errors->has('address_2'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('address_2') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label class="form-control-label" for="input-city">{{ __('City')}}</label>
                                    <div class="form-group{{ $errors->has('city') ? ' has-danger' : '' }}">
                                        <div class="input-group input-group-alternative mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-city"></i></span>
                                            </div>
                                            <input class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}"
                                                placeholder="{{ __('City') }}" type="city" name="city"
                                                value="{{ old('city', $contractor->city) }}" required>
                                        </div>
                                        @if ($errors->has('city'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('city') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-control-label" for="input-state">{{ __('State/Province')}}</label>
                                    <div class="form-group{{ $errors->has('state') ? ' has-danger' : '' }}">
                                        <div class="input-group input-group-alternative mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            </div>
                                            <input class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}"
                                                placeholder="{{ __('State/Province') }}" type="text" name="state"
                                                value="{{ old('state', $contractor->state) }}" required>
                                        </div>
                                        @if ($errors->has('state'))
                                        <span class="invalid-feedback" style="display: block;" role="alert">
                                            <strong>{{ $errors->first('state') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <label class="form-control-label" for="input-postal_code">{{ __('Postal/Zip Code')}}</label>
                            <div class="form-group{{ $errors->has('postal_code') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('postal_code') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Postal/Zip Code') }}" type="postal_code" name="postal_code"
                                        value="{{ old('postal_code', $contractor->postal_code) }}" required>
                                </div>
                                @if ($errors->has('postal_code'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('postal_code') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <span class="clearfix"></span>
                                <div class="custom-control custom-checkbox">
                                    <input id="input-is_disabled" name="is_disabled" class="custom-control-input"
                                        type="checkbox" {{ $contractor->is_disabled ? 'checked' : '' }}>
                                    <label class="custom-control-label form-control-label"
                                        for="input-is_disabled">{{ __('Disabled') }}</label>
                                </div>
                            </div>
                            <div class="text-center">
                                <button onclick="return confirm('Are you sure about editing this Contractor?')"
                                    type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
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