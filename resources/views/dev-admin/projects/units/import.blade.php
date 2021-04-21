@extends('layouts.app', ['title' => __('Unit Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])
<div class="container mt--7">
        <div class="row">
            <div class="col">
                <div class="case-card card shadow">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                {{-- <div class="col-8">
                                    <h3 class="mb-0">{{ __('Import Units') }}</h3>
                                </div> --}}
                                <div class="col">
                                    <h3 class="mb-0">{{ __('Import Units') }}</h3>
                                </div>
                                <div class="text-right pr-3">
                                    <a href="{{ route('dev-admin.projects.units.create-excel', $proj_id) }}"
                                        class="btn btn-sm btn-primary">{{ __('Download Unit Excel Template') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form role="form" method="post" action="{{ route('dev-admin.projects.units.import.post', ['proj_id' => $proj_id]) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="pl-lg-4">
                                    <label class="form-control-label" for="units-excel">{{ __('Units Excel') }}</label>
                                        <div class="form-group{{ $errors->has('units_excel') ? ' has-danger' : '' }}">
                                            <input type="file" id="units-excel" name="units_excel" class="form-control form-control-alternative{{ $errors->has('units_excel') ? ' is-invalid' : '' }}">
                                            @if ($errors->has('units_excel'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('units_excel') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    <div class="text-center">
                                        <button onclick="return confirm('Are you sure to import these units?')" type="submit"
                                            class="btn btn-primary mt-4">{{ __('Import') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer">
        <div class="row align-items-center justify-content-xl-between">
        <div class="col-xl-12">
            <div class="copyright text-center text-muted">
                © 2019 <a href="https://www.linkzzapp.com/" class="font-weight-bold ml-1" target="_blank">LinkZZapp™</a>
    
            </div>
        </div>
    </div>
    </footer>
</div>
@endsection