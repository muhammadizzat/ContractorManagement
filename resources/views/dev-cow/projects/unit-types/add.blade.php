@extends('layouts.app', ['title' => __('Unit Type Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Add Unit Type') }}</h3>
                        </div>
                        <div class="pr-3 text-right">
                            <a href="{{ route('dev-cow.projects.unit-types.index', $proj_id) }}"
                                class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form role="form" method="post" action="{{route('dev-cow.projects.unit-types.add.post', $proj_id)}}"
                        autocomplete="off" novalidate>
                        @csrf
                        <div class="pl-lg-4">
                            <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                            <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                <input type="text" name="name" id="input-name"
                                    class="form-control form-control-alternative{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Name') }}" value="{{ old('name') }}">
                                @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                                @endif
                            </div>

                            <div class="text-center">
                                <button onclick="return confirm('Are you sure to add this unit type?')" type="submit"
                                    class="btn btn-success mt-4">{{ __('Add') }}</button>
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