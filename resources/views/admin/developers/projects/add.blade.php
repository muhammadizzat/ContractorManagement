@extends('layouts.app', ['title' => __('Project Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Add Project') }}</h3>
                        </div>
                        <div class="text-right pr-3">
                            <a href="{{ route('admin.developers.projects.index', $dev_id) }}"
                                class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
                <form role="form" method="post" action="{{route('admin.developers.projects.add.post', $dev_id)}}"
                    enctype="multipart/form-data" autocomplete="off" novalidate>
                    @csrf
                    <div class="card-body">
                        <h6 class="heading-small text-muted mb-4">{{ __('Project information') }}</h6>
                        <div class="pl-lg-4 row">
                            <div class="text-center col-lg-3 col-sm-4">
                                <label class="form-control-label " for="project-img">{{ __('Project Logo') }}
                                    <div class="hover-image">
                                        <img id="project-img-tag"
                                            class="img-thumbnail rounded-circle p-0 logo-media-img"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=">
                                        <i class="fa fa-upload fa-4x text-dark upload"></i>
                                        <input type="file" name="attachment" id="project-img"
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
                            <div class="col-8">
                                <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <input type="text" name="name" id="input-name"
                                        class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Name') }}" value="{{ old('name') }}">
                                    @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="form-control-label" for="input-name">{{ __('Address Line 1') }}</label>
                                <div class="form-group{{ $errors->has('address') ? ' has-danger' : '' }}">
                                    <input type="text" name="address" id="input-name"
                                        class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Address') }}" value="{{ old('address') }}">
                                    @if ($errors->has('address'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="form-control-label" for="input-name">{{ __('Address Line 2') }}</label>
                                <div class="form-group{{ $errors->has('address2') ? ' has-danger' : '' }}">
                                    <input type="text" name="address2" id="input-name"
                                        class="form-control{{ $errors->has('address2') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Address2') }}" value="{{ old('address2') }}">
                                    @if ($errors->has('address2'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('address2') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="form-control-label" for="input-name">{{ __('Address Line 3') }}</label>
                                <div class="form-group{{ $errors->has('address3') ? ' has-danger' : '' }}">
                                    <input type="text" name="address3" id="input-name"
                                        class="form-control{{ $errors->has('address3') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Address3') }}" value="{{ old('address3') }}">
                                    @if ($errors->has('address3'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('address3') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="form-control-label" for="input-name">{{ __('Zip Code') }}</label>
                                <div class="form-group{{ $errors->has('zipcode') ? ' has-danger' : '' }}">
                                    <input type="text" name="zipcode" id="input-name"
                                        class="form-control{{ $errors->has('zipcode') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Zip Code') }}" value="{{ old('zipcode') }}">
                                    @if ($errors->has('zipcode'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('zipcode') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <label class="form-control-label" for="input-name">{{ __('Description') }}</label>
                                <div class="form-group{{ $errors->has('description') ? ' has-danger' : '' }}">
                                    <input type="text" name="description" id="input-name"
                                        class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                        placeholder="{{ __('Description') }}" value="{{ old('description') }}">
                                    @if ($errors->has('description'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="text-center">
                                    <button onclick="return confirm('Are you sure to add this project?')" type="submit"
                                        class="btn btn-success mt-4">{{ __('Add') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                console.log("input files size",input.files[0].size);
                reader.onload = function(e) {
                    $('#project-img-tag').attr('src', e.target.result);
                    reader.result;
                    $('#attach').val(reader.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#project-img").change(function() {
            readURL(this);
            $('#project-img-tag').show();
        });

        $(".datepicker").datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            todayHighlight : true,
            todayBtn : true,
        });
    });
</script>
@endpush