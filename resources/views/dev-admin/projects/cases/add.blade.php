@extends('layouts.app', ['title' => __('Case Management')])

@section('content')
@include('users.partials.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Add Case') }}</h3>
                        </div>
                        <div class="pr-3 text-right">
                            <a href="{{ route('dev-admin.projects.cases.index', $proj_id) }}"
                                class="btn btn-sm btn-primary">{{ __('Cancel') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form role="form" method="post" action="{{ route('dev-admin.projects.cases.add.post', $proj_id) }}"
                        autocomplete="off">
                        @csrf
                        <h6 class="heading-small text-muted mb-4">{{ __('Case information') }}</h6>
                        <div class="pl-lg-4">
                            <div class="form-group{{ $errors->has('title') ? ' has-danger' : '' }}">
                                <label class="form-control-label" for="title">{{ __('Title') }}</label>
                                <input type="text" name="title" id="title"
                                    class="form-control form-control-alternative{{ $errors->has('title') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Title') }}" required autofocus>

                                @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('title') }}</strong>
                                </span>
                                @endif
                                
                                <label class="form-control-label" for="unit_id">{{ __('Unit No') }}</label>
                                <select name="unit_id" id="unit_id"
                                    class="selectpicker form-control form-control-alternative{{ $errors->has('unit_id') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Unit ID') }}" title="Choose Unit No." data-live-search="true"
                                    required autofocus>

                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_no }}</option>
                                    @endforeach

                                </select>
                                @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('unit_id') }}</strong>
                                </span>
                                @endif

                                <label class="form-control-label" for="description">{{ __('Description') }}</label>
                                <input type="text" name="description" id="description"
                                    class="form-control form-control-alternative{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('Description') }}" required autofocus>

                                @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('description') }}</strong>
                                </span>
                                @endif
                                
                                <label class="form-control-label" for="tags">{{ __('Tags') }}</label>
                                <input name="tags" type="text" id="case-tags-input" placeholder="{{ __('Tags') }}" required autofocus value="">

                                @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('tags') }}</strong>
                                </span>
                                @endif

                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary my-4">Add</button>
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
<script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.js"></script>
<link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.default.css" />

<script type="text/javascript">
    $(function() {
        var $caseTagsSelectize = $('#case-tags-input').selectize({
            delimiter: ',',
            persist: false,
            maxOptions: 10,
            create: function(input, callback) {
                return {
                    value: input,
                    text: input
                }
            }
        })
        caseTagsSelectize = $caseTagsSelectize[0].selectize;
    })
</script>
@endpush