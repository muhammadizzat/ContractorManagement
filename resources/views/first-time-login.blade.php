@extends('layouts.app', ['class' => 'bg-white'])

@section('content')
    @include('layouts.headers.guest')

    <div class="container mt--8 pb-5">
        <!-- Table -->
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card bg-secondary shadow border-0">
                    <div class="card-header bg-transparent pb-5">
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
                    <div class="card-body px-lg-5 py-lg-5">
                        <form role="form" method="POST" action="{{ route('change-password') }}" novalidate>
                            @csrf
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
                            <div class="form-group{{ $errors->has('current_password') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('current_password') ? ' is-invalid' : '' }}" placeholder="{{ __('Current Password') }}" type="password" name="current_password">
                                </div>

                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('New Password') }}" type="password" name="password">
                                </div>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control" placeholder="{{ __('Confirm New Password') }}" type="password" name="confirm_password">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary mt-4">{{ __('Update password') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
