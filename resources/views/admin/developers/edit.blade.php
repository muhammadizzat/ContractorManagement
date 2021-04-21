@extends('layouts.app', ['title' => __('User Profile')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])
<div class="container-fluid mt--7">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Edit Developer') }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('admin.developers.index') }}"
                                class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.developers.edit.post', ['id' => $developer->id]) }}"
                        enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="pl-lg-4 row">
                            <div class="text-center col-lg-3 col-sm-4">
                                <label class="form-control-label " for="developer-img">{{ __('Developer Logo') }}
                                    <div class="hover-image">
                                        @if ($developer->logo_media_id)
                                        <img id="developer-img-tag"
                                            class="img-thumbnail rounded-circle p-0 logo-media-img"
                                            src="data:{{ $logoMedia->mimetype }};base64,{{ base64_encode($logoMedia->data) }}">
                                        <i class="fa fa-upload fa-4x text-dark upload"></i>
                                        @else
                                        <img id="developer-img-tag"
                                            class="img-thumbnail rounded-circle p-0 logo-media-img"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=">
                                        <i class="fa fa-upload fa-4x text-dark upload"></i>
                                        @endif
                                        <input type="file" name="attachment" id="developer-img"
                                            class="form-control {{ $errors->has('attachment') ? ' is-invalid' : '' }}"
                                            style="display:none">
                                        @if ($errors->has('attachment'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('attachment') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </label>
                            </div>
                            <div class="offset-col-lg-3 offset-col-sm-4 col-lg-9 col-sm-8">
                                <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <input type="text" name="name" id="input-name"
                                        class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Name') }}" value="{{ old('name', $developer->name) }}">
                                    @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <span class="clearfix"></span>
                                    <div class="custom-control custom-checkbox">
                                        <input id="input-is_disabled" name="is_disabled" class="custom-control-input" type="checkbox" {{ $developer->is_disabled ? 'checked' : '' }}>
                                        <label class="custom-control-label form-control-label" for="input-is_disabled">{{ __('Disabled') }}</label>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                </div>
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
        function readURL(input, onLoad) {
            var mimeType=input.files[0]['type'];
            if (mimeType.split('/')[0] === 'image') {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#developer-img-tag').attr('src', e.target.result);
                    reader.result;
                    $('#attach').val(reader.result);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                $('#developer-img-tag').attr('src', "{{ asset('argon') }}/img/theme/profile-pic-placeholder.png");
            }
        }

        $("#developer-img").change(function() {
            readURL(this);
            $('#developer-img-tag').show();
        });
    });
</script>
@endpush