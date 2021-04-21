@extends('layouts.app', ['title' => __('Cases Management')])

@section('content')
@include('users.partials.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Cases') }}</h3>
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
                    <table class="table align-items-center table-flush" id="cases">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('No') }}</th>
                                <th scope="col">{{ __('Title') }}</th>
                                <th scope="col">{{ __('Unit') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col">{{ __('# of Unresolved Defects') }}</th>
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
        var clerkOfWorkTable = $('#cases').DataTable({
            dom: '<"button-section">frtip',
            processing: true,
            serverSide: true,
            responsive: true,
            order: [[ 3, "created_at" ]],
            ajax: "{{ route('dev-cow.projects.cases.dt', $proj_id) }}",
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
                    data: 'title',
                    name: 'project_cases.title',
                    render: function(data, type, row, meta) {
                        return row.title;
                    }
                },
                {
                    data: 'unit_no',
                    data: 'unit_no',
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data, type, row, meta) {
                        return '<h2><span class="badge badge-success">' + row.status + '</span></h2>';
                    }
                },
                {
                    data: 'count',
                    data: 'count',
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
                                        +'<a href="' + row.viewUrl + '" class="dropdown-item">Edit</a>'
                                        +'<form action="' + row.deleteUrl + '" method=POST>@csrf<input type="submit" value="Delete" class="dropdown-item pointer" onclick="return deleteFunction()"> </form>'
                                    +'</div>'
                                +'</div>';
                    }
                },
            ],
            columnDefs: [
                { width: 50, className: 'dt-body-right', targets: 4 }
            ],
            language: {
                paginate: {
                    previous: `<span class="fas fa-angle-left"></span>`,
                    next: `<span class="fas fa-angle-right"></span>`,
                }
            },
        });
        $('.button-section').tooltip({
            placement: 'auto',
            title: 'Add',
        });
        $("div.button-section").html('<a href="{{ route("dev-cow.projects.cases.add", $proj_id) }}" class="btn btn-sm btn-primary"><i class="fas fa-plus-square fa-fw"></i></a>');
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
            return confirm('Are you sure you want to delete this case?');
        }
</script>
@endpush
