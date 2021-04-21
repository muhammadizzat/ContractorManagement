@extends('layouts.app', ['title' => __('Setting Up Contractor Scope of Work')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                        <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h3 class="mb-0">{{ __('Setting Up Contractor Scope of Work') }}</h3>
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
                    <table class="table align-items-center table-flush" id="developer_contractor_association">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('No') }}</th>
                                <th scope="col">{{ __('Contractor') }}</th>
                                <th scope="col">{{ __('Contact No') }}</th>
                                <th scope="col">{{ __('Email') }}</th>
                                <th scope="col">{{ __('Created At') }}</th>
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
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var developerContractorAssociationTable = $('#developer_contractor_association').DataTable({
            dom: '<"button-section btn-back"><"button-section btn-excel"><"button-section btn-add">frtip',
            order: [[ 4, "created_at" ]],
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('dev-cow.associations.dt') }}",
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) 
                    {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'contact_no',
                    name: 'contact_no',
                    render: function(data, type, row, meta) 
                    {
                    return data ? data : null;
                    },
                },
                {
                    data: 'email',
                    name: 'email',
                    render: function(data) 
                    {
                    return data ? data : null;
                    },
                },
                {
                    data: 'created_at',
                    type:'unix',
                    render:function(data){
                        return moment.utc(data).format('DD/MM/YYYY')
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta)
                    {
                        return '<div class="dropdown">'
                                    +'<a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                                        +'<i class="fas fa-ellipsis-v"></i>'
                                    +'</a>'
                                    +'<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">'
                                        +'<a href="' + row.editAssociationUrl + '" class="dropdown-item">Edit</a>'
                                        +'<form action="' + row.deleteAssociationUrl + '" method=POST>@csrf<input type="submit" value="Delete" class="dropdown-item" onclick="return deleteFunction()"> </form>'
                                    +'</div>'
                                +'</div>';
                    }
                },
            ],
            language: {
                paginate: {
                    previous: `<span class="fas fa-angle-left"></span>`,
                    next: `<span class="fas fa-angle-right"></span>`,
                }
            },
        });

        $('.button-section.btn-add').tooltip({
            placement: 'bottom',
            title: 'Associate Contactor',
        });
        $('.button-section.btn-excel').tooltip({
            placement: 'bottom',
            title: 'Export To Excel',
        });
        $("div.button-section.btn-add").html('<a href="{{ route("dev-cow.associations.add") }}" class="btn btn-sm btn-primary"><i class="fas fa-link fa-fw"></i></a>');
        $("div.button-section.btn-excel").html('<a href="{{ route("dev-cow.associations.export") }}" class="btn btn-sm btn-primary"><i class="fas fa-file-excel"></i></a>');
    });

    function showAlert(message) {
        $('#alert-container').html(`<div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>`)
    }
    function deleteFunction() 
        {
            return confirm('Are you sure you want to remove this contractor?');
        }
</script>
@endpush
