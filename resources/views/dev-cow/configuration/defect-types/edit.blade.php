@extends('layouts.app', ['title' => __('Defect Management')])

@section('content')
@include('users.partials.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Edit Defect Type') }}</h3>
                        </div>
                        <div class="pr-3 text-right">
                            <a href="{{ route('dev-cow.configuration.defect-types.index') }}"
                                class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post"
                        action="{{ route('dev-cow.configuration.defect-types.edit.post', ['id' => $defecttype->id]) }}"
                        autocomplete="off" novalidate>
                        @csrf
                        <h6 class="heading-small text-muted mb-4">{{ __('Defect Type information') }}</h6>
                        <div class="pl-lg-4">
                            <label class="form-control-label" for="input-title">{{ __('Title') }}</label>
                            <div class="form-group{{ $errors->has('title') ? ' has-danger' : '' }}">
                                <input type="text" name="title" id="input-title"
                                    class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Title') }}" value="{{ old('title', $defecttype->title) }}">
                                @if ($errors->has('title'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                                @endif
                            </div>
                            <label class="form-control-label" for="input-details">{{ __('Details') }}</label>
                            <div class="form-group{{ $errors->has('details') ? ' has-danger' : '' }}">
                                <input type="text" name="details" id="input-details"
                                    class="form-control {{ $errors->has('details') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Details') }}"
                                    value="{{ old('details', $defecttype->details) }}">
                                @if ($errors->has('details'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('details') }}</strong>
                                </span>
                                @endif
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
<script type="text/javascript">
</script>
@endpush