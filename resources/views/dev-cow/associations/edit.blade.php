@extends('layouts.app', ['title' => __('Setting Up Contractor Scope of Work')])
@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])
<div class="container-fluid mt--7">
    <div class="row d-flex justify-content-center">
        <div class="col-xl-8">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Edit Contractor Scope of Work') }}</h3>
                        </div>
                    </div>
                </div>
                <div id="associate-contractor-form" class="card-body">
                    <div class="pl-lg-4">
                        <h2> {{ $developer_contractor_association->user->name }} </h2>
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
                        <div id="contractor-details" class="col-sm-12 col-md-12" style="margin-top: 15px;">
                            <div class="row" style="margin-bottom: 13px;">
                                <div class="col-1">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="col-10">
                                    {{ $developer_contractor_association->user->contractor->address ?? '-'}}
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 13px;">
                                <div class="col-1">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="col-10">
                                    {{ $developer_contractor_association->user->email }}
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 13px;">
                                <div class="col-1">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="col-10">
                                    {{ $developer_contractor_association->user->contractor->contact_no ?? '-'}}
                                </div>
                            </div>
                        </div>
                        <form role="form" method="post"
                            action="{{route('dev-cow.associations.edit.post', $id)}}" autocomplete="off">
                            @csrf
                            <div class="form-group{{ $errors->has('dca-id') ? ' has-danger' : '' }}"
                                style="display: none;">
                                <input type="number" name="dca_id" id="dca-id" min="0"
                                    class="form-control{{ $errors->has('dca-id') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('ID') }}" value="{{ $developer_contractor_association->id }}">
                                @if ($errors->has('dca-id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('dca-id') }}</strong>
                                </span>
                                @endif
                            </div>

                            <label class="form-control-label" for="defect-type-id">{{ __('Defect Types') }}</label>
                            <div class="form-group {{ $errors->has('defect-type-id') ? ' has-danger' : '' }}">
                                <select id="defect-type-id" name="defect_type_ids[]"
                                    class="selectpicker form-control form-control-alternative{{ $errors->has('defect-type-id') ? 'is-invalid' : '' }}"
                                    multiple data-style="selectpicker-style" title="Choose Defect Type"
                                    data-live-search="true">

                                    @foreach($defect_type_list as $defect_type)
                                    <option value="{{$defect_type->id}}"
                                        {{ $developer_contractor_association->defect_types->pluck('id')->contains($defect_type->id) ? 'selected' : '' }}>
                                        {{$defect_type->title}}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('defect-type-id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('defect-type-id') }}</strong>
                                </span>
                                @endif
                            </div>


                            <div class="text-center">
                                <button onclick="return confirm('Are you sure to edit this contractor scope of work?')"
                                    type="submit" class="btn btn-primary my-4">Associate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
@endsection
