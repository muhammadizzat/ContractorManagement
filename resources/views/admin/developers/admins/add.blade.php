@extends('layouts.app', ['title' => __('Admin Management')])

@section('content')
@include('layouts.headers.cards')

<div class="container-fluid mt--7">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Add Developer Admin') }}</h3>
                        </div>
                        <div class="text-right pr-3">
                            <a href="{{ route('admin.developers.admins.index', $dev_id) }}"
                                class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form role="form" method="post" action="{{route('admin.developers.admins.add.post', $dev_id)}}"
                        autocomplete="off">
                        @csrf
                        <h6 class="heading-small text-muted mb-4">{{ __('Developer Admin information') }}</h6>
                        <div class="pl-lg-4">
                            <label class="form-control-label" for="input name">{{ __('Email')}}</label>
                            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" type="email" name="email" value="{{ old('email') }}">
                                </div>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
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
                            <div class="form-group">
                                <label class="form-control-label"
                                    for="input-name">{{ __('Manager ?') }}</label>
                                <span class="clearfix"></span>
                                <label class="custom-toggle">
                                    <input type="checkbox" name="primary_admin">
                                    <span class="custom-toggle-slider rounded-circle" data-on="true" data-off="false">
                                    </span>
                                </label>
                            </div>
                            <div class="text-center">
                                <button onclick="return confirm('Are you sure to add this developer admin?')"
                                    type="submit" class="btn btn-primary my-4">Add</button>
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