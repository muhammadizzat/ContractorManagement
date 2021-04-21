@extends('layouts.app', ['title' => __('User Profile')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Edit Developer Admin') }}</h3>
                        </div>
                        <div class="pr-3 text-right">
                            <a href="{{ route('dev-admin.developer-admins.index') }}"
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
                    <form method="post"
                        action="{{ route('dev-admin.developer-admins.edit.post', ['id' => $user->id]) }}"
                        autocomplete="off" novalidate>
                        @csrf


                        <h6 class="heading-small text-muted mb-4">{{ __('Developer Admin information') }}</h6>

                        @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <div class="pl-lg-4">
                            <label class="form-control-label" for="input name">{{ __('Email')}}</label>
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
                            <div class="form-group">
                                <span class="clearfix"></span>
                                <div class="custom-control custom-checkbox">
                                    <input id="input-primary_admin" name="primary_admin" class="custom-control-input"
                                        type="checkbox" {{ $developer_admin->primary_admin ? 'checked' : '' }} disabled>
                                    <label class="custom-control-label form-control-label"
                                        for="input-primary_admin">{{ __('Manager') }}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <span class="clearfix"></span>
                                <div class="custom-control custom-checkbox">
                                    <input id="input-is_disabled" name="is_disabled" class="custom-control-input"
                                        type="checkbox" {{ $user->is_disabled ? 'checked' : '' }}>
                                    <label class="custom-control-label form-control-label"
                                        for="input-is_disabled">{{ __('Disabled') }}</label>
                                </div>
                            </div>
                            <div class="text-center">
                                <button onclick="return confirm('Are you sure to edit this developer admin?')"
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