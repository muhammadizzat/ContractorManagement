@extends('layouts.app', ['title' => __('Contractors Associations Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])
<div class="container mt--7">
        <div class="row">
            <div class="col">
                <div class="case-card card shadow">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Import Unit Types') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form role="form" method="post" action="{{ route('dev-cow.projects.unit-types.import.post', ['proj_id' => $proj_id]) }}" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <div class="pl-lg-4">
                                    <label class="form-control-label" for="unit-types-excel">{{ __('Unit Types Excel') }}</label>
                                        <div class="form-group{{ $errors->has('unit_types_excel') ? ' has-danger' : '' }}">
                                            <input type="file" id="unit-types-excel" name="unit_types_excel" class="form-control form-control-alternative{{ $errors->has('unit_types_excel') ? ' is-invalid' : '' }}">
                                            @if ($errors->has('unit_types_excel'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('unit_types_excel') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    <div class="text-center">
                                        <button onclick="return confirm('Are you sure to import these unit types?')" type="submit"
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