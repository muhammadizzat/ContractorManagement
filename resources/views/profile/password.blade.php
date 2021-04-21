@extends('layouts.app', ['title' => __('User Profile')])

@section('content')
@include('users.partials.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-5 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <h3 class="col mb-0">{{ __('Change Password') }}</h3>
                        @if (Auth::user()->change_password == 1)
                        <div class="btn-home text-right pr-3">
                            @hasrole('admin|super-admin')
                            <a href="{{ route("admin.developers.index") }}" class="btn btn-sm btn-primary"><i
                                    class="fas fa-sign-out-alt"></i></a>
                            @endhasrole
                            @hasrole('dev-admin')
                            <a href="{{ route("dev-admin.projects.index") }}" class="btn btn-sm btn-primary"><i
                                    class="fas fa-sign-out-alt"></i></a>
                            @endhasrole
                            @hasrole('cow')
                            <a href="{{ route("dev-cow.projects.index") }}" class="btn btn-sm btn-primary"><i
                                    class="fas fa-sign-out-alt"></i></a>
                            @endhasrole
                            @hasrole('contractor')
                            <a href="{{ route("contractor.dashboard") }}" class="btn btn-sm btn-primary"><i
                                    class="fas fa-sign-out-alt"></i></a>
                            @endhasrole
                        </div>
                        @endif
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
                    <form method="post" action="{{ route('profile.password.post') }}" autocomplete="off" novalidate>
                        @csrf
                        <div class="lg-4">
                            <div class="form-group{{ $errors->has('current_password') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input
                                        class="form-control{{ $errors->has('current_password') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Current Password') }}" type="password"
                                        name="current_password">
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('new_password') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('new_password') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('New Password') }}" type="password" name="new_password">
                                </div>
                                @if ($errors->has('new_password'))
                                <span class="invalid-feedback" style="display: block;" role="alert">
                                    <strong>{{ $errors->first('new_password') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="{{ __('Confirm New Password') }}"
                                        type="password" name="confirm_new_password">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
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
@endpush