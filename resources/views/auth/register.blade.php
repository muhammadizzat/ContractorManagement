@extends('layouts.app', ['class' => 'bg-white'])

@section('content')
@include('layouts.headers.guest')

<div class="container mt--8 pb-5">
    <!-- Table -->
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card bg-secondary shadow border-0">
                <div class="card-header bg-transparent">
                    <div class="text-muted text-center">{{ __('Register as Contractor') }}</div>
                    <div class="text-center">
                    </div>
                </div>
                <div class="card-body px-lg-5 py-lg-5">
                    <form role="form" method="POST" action="{{ route('register.contractor') }}">
                        @csrf

                        <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                            <div class="input-group input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-circle-08"></i></span>
                                </div>
                                <input class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Company Name') }}" type="text" name="name"
                                    value="{{ old('name') }}" required autofocus>
                            </div>
                            @if ($errors->has('name'))
                            <span class="invalid-feedback" style="display: block;" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                            <div class="input-group input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                </div>
                                <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Email') }}" type="email" name="email" value="{{ old('email') }}"
                                    required>
                            </div>
                            @if ($errors->has('email'))
                            <span class="invalid-feedback" style="display: block;" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('contact_no') ? ' has-danger' : '' }}">
                            <div class="input-group input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-phone-square"></i></span>
                                </div>
                                <input class="form-control{{ $errors->has('contact_no') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Contact No.') }}" type="tel" name="contact_no"
                                    value="{{ old('contact_no') }}" required>
                            </div>
                            @if ($errors->has('contact_no'))
                            <span class="invalid-feedback" style="display: block;" role="alert">
                                <strong>{{ $errors->first('contact_no') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('address_1') ? ' has-danger' : '' }}">
                            <div class="input-group input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-road"></i></span>
                                </div>
                                <input class="form-control{{ $errors->has('address_1') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Street Address') }}" type="address_1" name="address_1"
                                    value="{{ old('address_1') }}" required>
                            </div>
                            @if ($errors->has('address_1'))
                            <span class="invalid-feedback" style="display: block;" role="alert">
                                <strong>{{ $errors->first('address_1') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('address_2') ? ' has-danger' : '' }}">
                            <div class="input-group input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-signs"></i></span>
                                </div>
                                <input class="form-control{{ $errors->has('address_2') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Street Address Line 2') }}" type="address_2" name="address_2"
                                    value="{{ old('address_2') }}" required>
                            </div>
                            @if ($errors->has('address_2'))
                            <span class="invalid-feedback" style="display: block;" role="alert">
                                <strong>{{ $errors->first('address_2') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('city') ? ' has-danger' : '' }}">
                            <div class="input-group input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-city"></i></span>
                                </div>
                                <input class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('City') }}" type="city" name="city" value="{{ old('city') }}"
                                    required>
                            </div>
                            @if ($errors->has('city'))
                            <span class="invalid-feedback" style="display: block;" role="alert">
                                <strong>{{ $errors->first('city') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('state') ? ' has-danger' : '' }}">
                            <div class="input-group input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                </div>
                                <input class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('State/Province') }}" type="text" name="state"
                                    value="{{ old('state') }}" required>
                            </div>
                            @if ($errors->has('state'))
                            <span class="invalid-feedback" style="display: block;" role="alert">
                                <strong>{{ $errors->first('state') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group{{ $errors->has('postal_code') ? ' has-danger' : '' }}">
                            <div class="input-group input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                <input class="form-control{{ $errors->has('postal_code') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Postal/Zip Code') }}" type="postal_code" name="postal_code"
                                    value="{{ old('postal_code') }}" required>
                            </div>
                            @if ($errors->has('postal_code'))
                            <span class="invalid-feedback" style="display: block;" role="alert">
                                <strong>{{ $errors->first('postal_code') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group row mt-4 d-flex">
                            <div class="m-auto">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register Contractor Account') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection