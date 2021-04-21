@extends('layouts.app', ['title' => __('Contractor Management')])

@section('content')
@include('users.partials.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <!-- Contractor Details Modal -->
    <div id="contractor-details-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Contractor Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-2">
                    <div class="card bg-secondary shadow border-0">
                        {{-- <div class="card-header bg-transparent">
                            <div class="text-muted text-center">{{ __('Register as Contractor') }}</div>
                            <div class="text-center">
                            </div>
                        </div> --}}
                        <div class="card-body px-lg-5 py-lg-5">
                            <form>
                                <div class="pl-lg-4">
                                    <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-single-02"></i></span>
                                            </div>
                                            <input class="contractor-details-field form-control" type="text" id="name" name="name" id="input-name"
                                                placeholder="{{ __('Name') }}" disabled>
                                        </div>
                                    </div>
                                    <label class="form-control-label" for="input-email">{{ __('Email')}}</label>
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-alternative">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                            </div>
                                            <input class="contractor-details-field form-control" type="email" id="email" name="email"
                                                placeholder="{{ __('Email') }}"
                                                disabled>
                                        </div>
                                    </div>
                                    <label class="form-control-label" for="input-contact_no">{{ __('Contact No.')}}</label>
                                    <div class="form-group">
                                        <div class="input-group input-group-alternative mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-phone-square"></i></span>
                                            </div>
                                            <input class="contractor-details-field form-control"
                                                placeholder="{{ __('Contact No.') }}" type="text" id="contact_no" name="contact_no" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label class="form-control-label"
                                                for="input-address_1">{{ __('Street Address')}}</label>
                                            <div class="form-group">
                                                <div class="input-group input-group-alternative mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-road"></i></span>
                                                    </div>
                                                    <input class="contractor-details-field form-control" id="address_1" name="address_1" type="text" placeholder="{{ __('Street Address') }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="form-control-label"
                                                for="input-address_2">{{ __('Street Address Line 2')}}</label>
                                            <div class="form-group">
                                                <div class="input-group input-group-alternative mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-map-signs"></i></span>
                                                    </div>
                                                    <input class="contractor-details-field form-control"
                                                        placeholder="{{ __('Street Address Line 2') }}" type="text"
                                                        id="address_2" name="address_2" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label class="form-control-label" for="input-city">{{ __('City')}}</label>
                                            <div class="form-group">
                                                <div class="input-group input-group-alternative mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-city"></i></span>
                                                    </div>
                                                    <input class="contractor-details-field form-control"
                                                        placeholder="{{ __('City') }}" type="text" id="city" name="city" disabled>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label class="form-control-label" for="input-state">{{ __('State/Province')}}</label>
                                            <div class="form-group{{ $errors->has('state') ? ' has-danger' : '' }}">
                                                <div class="input-group input-group-alternative mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                    </div>
                                                    <input class="contractor-details-field form-control"
                                                        placeholder="{{ __('State/Province') }}" type="text" id="state" name="state" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="form-control-label" for="input-postal_code">{{ __('Postal/Zip Code')}}</label>
                                    <div class="form-group">
                                        <div class="input-group input-group-alternative mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            </div>
                                            <input class="contractor-details-field form-control"
                                                placeholder="{{ __('Postal/Zip Code') }}" type="text" id="postal_code" name="postal_code" disabled>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Contractor') }}</h3>
                        </div>
                    </div>
                </div>
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
                <div class="table-responsive">
                    <table class="table align-items-center table-flush" id="contractor-table">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('No') }}</th>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Email') }}</th>
                                <th scope="col">{{ __('Created At') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col">{{ __('is_disabled') }}</th>
                                <th scope="col">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
@endsection
@push('scripts')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" />
{{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.1.0/css/select.bootstrap.css" /> --}}
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/select/1.1.0/js/dataTables.select.min.js"></script>
<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.js"></script>

<script type="text/javascript">
    $(function() {
        var contractorTable = $('#contractor-table').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                text: 'Show Disabled',
                action: function ( e, dt, node, config ) {
                    if (dt.column(5).search() === '1') {
                        this.text('Show Disabled');
                        dt.column(5).search(0).draw(true);
                    } else  {
                        this.text('Show Active');
                        dt.column(5).search(1).draw(true);
                    }
                }
            }],
            order: [[ 3, "created_at" ]],
            processing: true,
            serverSide: true,
            responsive: true,
            select: {
                 'style': 'single'
            },
            ajax: "{{ route('admin.contractors.dt') }}",
            columns: [{
                    searchable: false,
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'email',
                    name: 'email',
                },
                {
                    data: 'created_at',
                    type:'unix',
                    render:function(data){
                        return moment.utc(data).format('DD/MM/YYYY')
                    }
                },
                {
                    data: 'is_disabled',
                    name: 'is_disabled',
                    render: function(data) {
                        if(data ==true) {
                            return '<i class="fas fa-times-circle" style="color:red">Disabled</i>'
                        }
                        else {
                            return '<i class="fas fa-check-circle" style="color:green">Active</i>'
                        }

                    },
                    defaultContent: ''
                },
                {
                    data: 'is_disabled',
                    name: 'is_disabled',
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta)
                    {
                        return '<div class="dropdown">'
                                    +'<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                                        +'<i class="fas fa-ellipsis-v"></i>'
                                    +'</a>'
                                    +'<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow pointer">'
                                        +'<a href="' + row.editUrl + '" class="dropdown-item">Edit</a>'
                                        +'<form action="' + row.deleteUrl + '" method=POST>@csrf<input type="submit" value="Delete" class="dropdown-item pointer" onclick="return deleteFunction()"> </form>'
                                        +'<span onclick="resetPassword('+data+')" class="dropdown-item pointer">Reset Password</span>'
                                    +'</div>'
                                +'</div>';
                    }
                },
            ],
            columnDefs: [{
                "targets": [5],
                "visible": false,
            }],
            language: {
                paginate: {
                    previous: `<span class="fas fa-angle-left"></span>`,
                    next: `<span class="fas fa-angle-right"></span>`,
                }
            },
            searchCols: [
                null,
                null,
                null,
                null,
                null,
                {search: "0" }
            ]
        });
    });
    
    $('#contractor-table').on('click', 'tbody tr', function (event) {
        if(event.target.tagName === "TD"){
            var contractorDetails = $('#contractor-table').DataTable().row($(this)).data();
            $('#name').val(contractorDetails.name);
            $('#contact_no').val(contractorDetails.contractor.contact_no);
            $('#email').val(contractorDetails.email);
            $('#address_1').val(contractorDetails.contractor.address_1);
            $('#address_2').val(contractorDetails.contractor.address_2);
            $('#city').val(contractorDetails.contractor.city);
            $('#state').val(contractorDetails.contractor.state);
            $('#postal_code').val(contractorDetails.contractor.postal_code);
            $('#contractor-details-modal').modal('show');
        }
    });

    $("#contractor-details-modal").on("hidden.bs.modal", function () {
        $('#contractor-table').DataTable().rows( '.selected' ).nodes().to$().removeClass( 'selected' );
    });

    function resetPassword(user_id){
        if (confirm("Are you sure you want to reset the user's password?") == true) {
            var postContractorResetPasswordRouteTemplate = "{{ route('reset-user-password', ['user_id' => '<<user_id>>'])}}"
            var postContractorResetPasswordRoute = postContractorResetPasswordRouteTemplate.replace(encodeURI('<<user_id>>'), user_id);
        
        $.ajax({
            url: postContractorResetPasswordRoute,
            type: 'POST', 
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function(response) {
                alert(response)
            },
            error: function(xhr) {
                if(xhr.status == 422) {
                    var errors = xhr.responseJSON.errors;
                    console.log("Error 422: ", xhr);
                }
                console.log("Error: ", xhr);
            }
        });
        }
    }
    function deleteFunction() 
    {
        return confirm('Are you sure you want to delete this contractor?');
    }
</script>

@endpush
