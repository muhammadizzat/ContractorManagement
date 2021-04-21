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
                            <h3 class="mb-0">{{ __('Edit Profile') }}</h3>
                        </div>

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

                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.update') }}" autocomplete="off" novalidate
                        enctype="multipart/form-data">
                        @csrf
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
                        @hasrole('admin|super-admin|cow|dev-admin')
                        <div class="pl-lg-4 row">
                            <div class="text-center col-lg-3 col-sm-4">
                                <div class="hover-image">
                                    <label for="profile-img">
                                        @if ($user->profile_pic_media_id)
                                        <img id="profile-img-tag"
                                            class="img-thumbnail rounded-circle p-0 logo-media-img"
                                            src="data:{{ $profilePicMedia->mimetype }};base64,{{ base64_encode($profilePicMedia->data) }}">
                                        <i class="fa fa-upload fa-4x text-dark upload"></i>
                                        @else

                                        <img id="profile-img-tag" alt="Image placeholder"
                                            class="img-thumbnail rounded-circle p-0 logo-media-img"
                                            src="{{ asset('argon') }}/img/theme/profile-pic-placeholder.png">
                                        <i class="fa fa-upload fa-4x text-dark upload"></i>
                                        @endif
                                        <input type="file" name="attachment" id="profile-img"
                                            class="form-control {{ $errors->has('attachment') ? ' is-invalid' : '' }}"
                                            style="display:none">
                                        @if ($errors->has('attachment'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('attachment') }}</strong>
                                        </span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                            <div class=" col-lg-9 col-sm-8">
                                <label class="form-control-label" for="input-email">{{ __('Email') }}</label>
                                <div class="form-group">
                                    <input type="email" name="email" id="input-email" class="form-control form-control"
                                        placeholder="{{ __('Email') }}"
                                        value="{{ old('email', auth()->user()->email) }}" readonly>
                                </div>
                                <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <input type="text" name="name" id="input-name"
                                        class="form-control form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Name') }}" value="{{ old('name', auth()->user()->name) }}"
                                        required>
                                    @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </div>
                        @endhasrole
                        @hasrole('contractor')
                        <div class="pl-lg-4 row">
                            <div class="text-center col-lg-3 col-sm-4">
                                <div class="hover-image">
                                    <label for="profile-img">
                                        @if ($user->profile_pic_media_id)
                                        <img id="profile-img-tag"
                                            class="img-thumbnail rounded-circle p-0 logo-media-img"
                                            src="data:{{ $profilePicMedia->mimetype }};base64,{{ base64_encode($profilePicMedia->data) }}">
                                        <i class="fa fa-upload fa-4x text-dark upload"></i>
                                        @else

                                        <img id="profile-img-tag" alt="Image placeholder"
                                            class="img-thumbnail rounded-circle p-0 logo-media-img"
                                            src="{{ asset('argon') }}/img/theme/profile-pic-placeholder.png">
                                        <i class="fa fa-upload fa-4x text-dark upload"></i>
                                        @endif
                                        <input type="file" name="attachment" id="profile-img"
                                            class="form-control {{ $errors->has('attachment') ? ' is-invalid' : '' }}"
                                            style="display:none">
                                        @if ($errors->has('attachment'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('attachment') }}</strong>
                                        </span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                            <div class=" col-lg-9 col-sm-8">
                                <label class="form-control-label" for="input-email">{{ __('Email') }}</label>
                                <div class="form-group">
                                    <input type="email" name="email" id="input-email" class="form-control form-control"
                                        placeholder="{{ __('Email') }}"
                                        value="{{ old('email', auth()->user()->email) }}" readonly>
                                </div>
                                <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <input type="text" name="name" id="input-name"
                                        class="form-control form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Name') }}" value="{{ old('name', auth()->user()->name) }}"
                                        required>
                                    @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="form-control-label" for="input-contact-no">{{ __('Contact No') }}</label>
                                <div class="form-group{{ $errors->has('contact_no') ? ' has-danger' : '' }}">
                                    <input type="tel" name="contact_no" id="input-contact-no"
                                        class="form-control form-control{{ $errors->has('contact_no') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Contact No') }}"
                                        value="{{ old('contact_no', $contractor->contact_no) }}">

                                    @if ($errors->has('contact_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('contact_no') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="form-control-label"
                                    for="input-address-1">{{ __('Street Address') }}</label>
                                <div class="form-group{{ $errors->has('address_1') ? ' has-danger' : '' }}">
                                    <input type="text" name="address_1" id="input-address-1"
                                        class="form-control form-control{{ $errors->has('address_1') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Street Address') }}"
                                        value="{{ old('address_1', $contractor->address_1) }}">

                                    @if ($errors->has('address_1'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('address_1') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="form-control-label"
                                    for="input-address-2">{{ __('Street Address Line 2') }}</label>
                                <div class="form-group{{ $errors->has('address_2') ? ' has-danger' : '' }}">
                                    <input type="text" name="address_2" id="input-address-2"
                                        class="form-control form-control{{ $errors->has('address_2') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Street Address Line 2') }}"
                                        value="{{ old('address_2', $contractor->address_2) }}">

                                    @if ($errors->has('address_2'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('address_2') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="form-control-label" for="input-city">{{ __('City') }}</label>
                                <div class="form-group{{ $errors->has('city') ? ' has-danger' : '' }}">
                                    <input type="text" name="city" id="input-city"
                                        class="form-control form-control{{ $errors->has('city') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('City') }}" value="{{ old('city', $contractor->city) }}">

                                    @if ($errors->has('city'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('city') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="form-control-label" for="input-state">{{ __('State') }}</label>
                                <div class="form-group{{ $errors->has('state') ? ' has-danger' : '' }}">
                                    <input type="text" name="state" id="input-state"
                                        class="form-control form-control{{ $errors->has('state') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('State') }}" value="{{ old('state', $contractor->state) }}">

                                    @if ($errors->has('state'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('state') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="form-control-label"
                                    for="input-postal-code">{{ __('Postal Code') }}</label>
                                <div class="form-group{{ $errors->has('postal_code') ? ' has-danger' : '' }}">
                                    <input type="text" name="postal_code" id="input-postal-code"
                                        class="form-control form-control{{ $errors->has('postal_code') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('postal_code') }}"
                                        value="{{ old('postal_code', $contractor->postal_code) }}">

                                    @if ($errors->has('postal_code'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('postal_code') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </div>
                        @endhasrole
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
        function readURL(input, onLoad) {
            var mimeType=input.files[0]['type'];
            if (mimeType.split('/')[0] === 'image') {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#profile-img-tag').attr('src', e.target.result);
                    reader.result;
                    $('#attach').val(reader.result);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                $('#profile-img-tag').attr('src', "{{ asset('argon') }}/img/theme/profile-pic-placeholder.png");
            }
        }

        $("#profile-img").change(function() {
            readURL(this);
            $('#profile-img-tag').show();
        });

        $('.btn-home').tooltip({
            placement: 'auto',
            title: 'Home',
        });
    });
</script>
@endpush