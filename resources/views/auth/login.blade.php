@extends('layouts.app', ['class' => 'bg-white'])

@section('content')
    @include('layouts.headers.guest')

    <div class="container mt--8 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card bg-white shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <div class="text-center text-muted mb-4">
                            @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @elseif (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            <small>
                                {{ __('Sign in with credentials') }}
                            </small>
                        </div>
                        <form role="form" method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }} mb-3">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" type="email" name="email" value="{{ old('email') }}" required autofocus>
                                </div>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                <div class="input-group input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ __('Password') }}" type="password" required>
                                </div>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary my-4">{{ __('Sign in') }}</button>
                            </div>
                            <div class="text-center">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-dark">
                                    <small>{{ __('Forgot password?') }}</small>
                                </a>
                            @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
