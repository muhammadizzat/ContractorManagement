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
                            <a href="{{ route("admin.users.index") }}" class="btn btn-sm btn-primary"><i
                                    class="fas fa-sign-out-alt"></i></a>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.users.update', ['id' => $user->id]) }}"
                        autocomplete="off" novalidate enctype="multipart/form-data">
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
                                        value="{{ old('email', $user->email) }}" readonly>
                                </div>
                                <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <input type="text" name="name" id="input-name"
                                        class="form-control form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Name') }}" value="{{ old('name', $user->name) }}"
                                        required>
                                    @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <span class="clearfix"></span>
                                    <div class="custom-control custom-checkbox">
                                        <input id="input-is_disabled" name="is_disabled" class="custom-control-input" type="checkbox" {{ $user->is_disabled ? 'checked' : '' }}>
                                        <label class="custom-control-label form-control-label" for="input-is_disabled">{{ __('Disabled') }}</label>
                                    </div>
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