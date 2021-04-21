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
                            <h3 class="mb-0">{{ __('Add Contractor Scope of Work') }}</h3>
                        </div>
                    </div>
                </div>
                <div id="search-contractor-form" class="card-body">
                    <div class="pl-lg-4">
                        <h3 class="heading-small text-muted mb-4">{{ __('Search Contractor') }}</h3>
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
                        <label class="form-control-label" for="contractor-email">{{ __('Contractor Email') }}</label>
                        <div class="form-group {{ $errors->has('contractor-email') ? ' has-danger' : '' }}">
                            <input
                                class="form-control form-control-alternative {{ $errors->has('contractor-email') ? ' is-invalid' : '' }}"
                                placeholder="Contractor Email" type="email" name="contractor-email"
                                id="contractor-email" value="{{ old('email') }}">

                            @if ($errors->has('contractor-email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('contractor-email') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="text-center">
                            <button onclick="searchEmail()" class="btn btn-primary my-4">Search</button>
                        </div>
                    </div>
                </div>

                <div id="associate-contractor-form" class="card-body" style="display: none">
                    <div class="pl-lg-4">
                    </div>
                    <div class="text-right">
                        <button onclick="displaySearchContractorForm()" class="btn btn-sm btn-primary">Search
                            Contractor</button>
                    </div>
                    <div class="pl-lg-4">
                        <div id="contractor-details" class="" style="margin-top: 15px;"></div>
                        <form role="form" method="post" action="{{route('dev-cow.associations.add.post')}}"
                            autocomplete="off">
                            @csrf
                            <div class="form-group{{ $errors->has('contractor-user-id') ? ' has-danger' : '' }}"
                                style="display:none;">
                                <input type="number" name="contractor_user_id" id="contractor-user-id" min="0"
                                    class="form-control{{ $errors->has('contractor-user-id') ? ' is-invalid' : '' }}"
                                    placeholder="{{ __('ID') }}" value="" required>

                                @if ($errors->has('contractor-user-id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('contractor-user-id') }}</strong>
                                </span>
                                @endif
                            </div>


                            <label class="form-control-label" for="defect-type-id">{{ __('Defect Types') }}</label>
                            <div class="form-group {{ $errors->has('defect-type-id') ? ' has-danger' : '' }}">
                                <select id="defect-type-id" name="defect_type_ids[]"
                                    class="selectpicker form-control form-control-alternative{{ $errors->has('defect-type-id') ? 'is-invalid' : '' }}"
                                    multiple data-style="selectpicker-style" 
                                    title="Choose Defect Type" data-live-search="true">

                                    @foreach($defect_type_list as $defect_type)
                                    <option value="{{$defect_type->id}}">{{$defect_type->title}}</option>
                                    @endforeach
                                </select>
                                
                                @if ($errors->has('defect-type-id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('defect-type-id') }}</strong>
                                </span>
                                @endif
                            </div>


                            <div class="text-center">
                                <button onclick="return confirm('Are you sure to associate this contractor?')"
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

@push('scripts')
<script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.js"></script>
<link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.default.css" />

<script type="text/javascript">
    function searchEmail(){
            var contractorEmail = document.getElementById("contractor-email").value;
            data = {contractor_email : contractorEmail};
            var getContractorData = $.ajax({
                type: "GET",
                data: data,
                url: "{{ route('dev-cow.associations.contractor-profile') }}",
                dataType: "application/json",
                async: false
            }).responseText;
            contractorData = JSON.parse(getContractorData);
            if(contractorData.message){
                $('#alert-container').html(`<div class="alert alert-danger">
                                                ${contractorData['message']}
                                            </div>`);
            }
            else{
                document.getElementById('search-contractor-form').style.display = 'none';
                document.getElementById('associate-contractor-form').style.display = 'block';
                document.getElementById("contractor-user-id").value = contractorData.contractor_user_id;

                
                $('#contractor-details').html(`
                    <h2> ${contractorData.name} </h2>
                    <div class="pl-lg-4">
                        <div class="row" style="margin-bottom: 13px;">
                            <div class="col-1">
                                <i class="fas fa-map-marker-alt"></i> 
                            </div>
                            <div class="col-10">
                                ${contractorData.address ?? '-'}
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 13px;">
                            <div class="col-1">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="col-10">
                                ${contractorEmail}
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 13px;">
                            <div class="col-1">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="col-10">
                                ${contractorData.contact_no ?? '-'}
                            </div>
                        </div>
                    </div>
                `);
            }  
        }

        function displaySearchContractorForm(){
            document.getElementById("contractor-email").value = '';
            document.getElementById("contractor-details").value = '';
            document.getElementById('search-contractor-form').style.display = 'block';
            document.getElementById('associate-contractor-form').style.display = 'none';
        }
</script>
@endpush