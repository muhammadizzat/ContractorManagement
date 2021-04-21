@extends('layouts.app', ['title' => __('Developer Admin Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Developer Admins') }}</h3>
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
                    <table class="table align-items-center table-flush" id="admin">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('No') }}</th>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Email') }}</th>
                                <th scope="col">{{ __('Created At') }}</th>
                                <th scope="col">{{ __('Manager ?') }}</th>
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
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.js"></script>

<script type="text/javascript">
    $(function() {
        var adminTable = $('#admin').DataTable({
            dom: '<"button-section btn-back"><"button-section btn-add">Bfrtip',
            order: [[ 5, "asc" ], [3, "desc"]],
            processing: true,
            serverSide: true,
            responsive: true,
            buttons: [{
                text: 'Show Disabled',
                action: function ( e, dt, node, config ) {
                    if (dt.column(6).search() === '1') {
                        this.text('Show Disabled');
                        dt.column(6).search(0).draw(true);
                    } else  {
                        this.text('Show Active');
                        dt.column(6).search(1).draw(true);
                    }
                }
            }],
            ajax: "{{ route('admin.developers.admins.dt', ['dev_id' => $dev_id]) }}",
            columns: [{
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta)
                    {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'user.name',
                    name: 'user.name',
                },
                {
                    data: 'user.email',
                    name: 'user.email',
                },
                {
                    data: 'user.created_at',
                    type:'unix',
                    render:function(data){
                        return moment(data).format('DD/MM/YYYY')
                    }
                },
                {
                    data: 'primary_admin',
                    render: function(data) {
                        if(data ==true) {
                            return '<i class="fas fa-check-circle" style="color:green"></i>'
                        }
                        else {
                            return '<i class="fas fa-times-circle" style="color:red"></i>'
                        }
                    },
                    defaultContent: ''
                },
                {
                    data: 'user.is_disabled',
                    name: 'user.is_disabled',
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
                    data: 'user.is_disabled',
                    name: 'user.is_disabled',
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
                                        +'<a href="' + row.editUrl + '" class="dropdown-item">Edit</a>'
                                        +'<form action="' + row.deleteUrl + '" method=POST>@csrf<input type="submit" value="Delete" class="dropdown-item pointer" onclick="return deleteFunction()"> </form>'
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
            columnDefs: [{
                "targets": [6],
                "visible": false,
            }],
            searchCols: [
                null,
                null,
                null,
                null,
                null,
                null,
                {search: "0" }
            ]
        });
        
        $('.button-section.btn-add').tooltip({
            placement: 'bottom',
            title: 'Add',
        });
        $('.button-section.btn-back').tooltip({
            placement: 'bottom',
            title: 'Export to Excel',
        });
        $("div.button-section.btn-add").html('<a href="{{ route("admin.developers.admins.add", $dev_id) }}" class="btn btn-sm btn-primary"><i class="fas fa-plus-circle fa-fw"></i></a>');
        $("div.button-section.btn-back").html('<a href="{{ route("admin.developers.admins.export", $dev_id) }}" class="btn btn-sm btn-primary"><i class="fas fa-file-excel"></i></a>');
    }); 

    function deleteFunction() 
        {
            return confirm('Are you sure you want to delete this developer admin?');
        }
</script>
@endpush