@extends('layouts.app', ['title' => __('Clerk Of Work Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Add Clerk Of Work') }}</h3>
                        </div>
                        <div class="pr-3 text-right">
                            <a href="{{ route('dev-admin.clerks-of-work.index') }}"
                                class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('dev-admin.clerks-of-work.add.post') }}" autocomplete="off"
                        novalidate>
                        @csrf
                        <h6 class="heading-small text-muted mb-4">{{ __('Clerk Of Work information') }}</h6>
                        <div class="pl-lg-4">
                            <label class="form-control-label" for="input name">{{ __('Email')}}</label>
                            <div class="form-group mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input type="text" name="email" id="input-email"
                                        class="form-control form-control-alternative{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Email') }}" value="{{ old('email') }}" required autofocus>
                                    @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                            <div class="form-group mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-single-02"></i></span>
                                    </div>
                                    <input type="text" name="name" id="input-name"
                                        class="form-control form-control-alternative{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Name') }}" value="{{ old('name') }}" required autofocus>
                                    @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-center">
                                <button onclick="return confirm('Are you sure you want to add this clerk of work?')"
                                    type="submit" class="btn btn-success mt-4">{{ __('Add') }}</button>
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