@extends('layouts.app', ['title' => __('Developer Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])


<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Developers') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush" id="developer">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('No') }}</th>
                                <th scope="col">{{ __('Logo') }}</th>
                                <th scope="col">{{ __('Name Of Project') }}</th>
                                <th scope="col">{{ __('Created At') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col">{{ __('is_disabled') }}</th>
                                <th scope="col">{{ __('Option') }}</th>
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
<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.js"></script>

<script type="text/javascript">
    $(function() {
        var developerTable = $('#developer').DataTable({
            dom: '<"button-section">Bfrtip',
            order: [[ 4, "asc"], [2, "asc"]],
            processing: true,
            serverSide: true,
            responsive: true,
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
            ajax: "{{ route('admin.developers.dt') }}",
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
                    data: null,
                    name: 'name',
                    "defaultContent": "<strong>(not set)</strong>",
                    render: function (data, type, row, meta) {
                        if(data.logo_media_id) {
                            return "<img class='img-thumbnail rounded-circle p-0 logo-media-icon' \
                                src='" + data.logoUrl + "'> &nbsp;&nbsp;";
                        } else {
                            return "<i class='fab fa-gg-circle fa-2x'></i> &nbsp;&nbsp;" ;
                        }
                    }
                },
                {
                    data: 'name',
                    name: 'name',
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
                                        +'<a href="' + row.viewAdminUrl + '" class="dropdown-item">Developer Admins</a>'
                                        +'<a href="' + row.viewProjectsUrl + '" class="dropdown-item">Projects</a>'
                                    +'</div>'
                                +'</div>';
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
                "targets": [5],
                "visible": false,
            }],
            searchCols: [
                null,
                null,
                null,
                null,
                null,
                {search: "0" }
            ]
        });

        $('.button-section').tooltip({
            placement: 'auto',
            title: 'Add',
        });
        $("div.button-section").html('<a href="{{ route("admin.developers.add") }}" class="btn btn-sm btn-primary"><i class="fas fa-plus-circle fa-fw"></i></a>');
    });


    function deleteFunction() 
        {
            return confirm('Are you sure you want to delete this developer?');
        }
</script>
@endpush
