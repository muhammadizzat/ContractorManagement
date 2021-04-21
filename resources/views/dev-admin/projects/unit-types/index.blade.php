@extends('layouts.app', ['title' => __('Unit Type Management')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])
{{-- @include('units.modal') --}}

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Unit Types') }}</h3>
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
                    @elseif (session('failed'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('failed') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush" id="unit">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('No') }}</th>
                                <th scope="col">{{ __('Unit Type') }}</th>
                                <th scope="col">{{ __('# of Units') }}</th>
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
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var unitTable = $('#unit').DataTable({
            dom: '<"button-section btn-excel"><"button-section btn-back-to-project"><"button-section btn-add">frtip',
            processing: true,
            serverSide: true,
            responsive: true,
            order: [[ 2, "created_at" ]],
            ajax: "{{ route('dev-admin.projects.unit-types.dt', ['proj_id' => $proj_id]) }}",
            columns: [
                {
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) 
                    {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {  
                    data: 'name',
                    name: 'name'
                },
                {  
                    data: 'count',
                    name: 'count'
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
        });

        $('.button-section.btn-add').tooltip({
            placement: 'bottom',
            title: 'Add',
        });
        $('.button-section.btn-excel').tooltip({
            placement: 'bottom',
            title: 'Export Unit Types To Excel',
        });
        $('.button-section.btn-back-to-project').tooltip({
            placement: 'bottom',
            title: 'Projects',
        });
        $("div.button-section.btn-add").html('<a href="{{ route("dev-admin.projects.unit-types.add", $proj_id) }}" class="btn btn-sm btn-primary"><i class="fas fa-plus-circle fa-fw"></i></a>');
        $("div.button-section.btn-excel").html('<a href="{{ route("dev-admin.projects.unit-types.export", $proj_id) }}" class="btn btn-sm btn-primary"><i class="fas fa-file-excel"></i></a>');
        $("div.button-section.btn-back-to-project").html('<a href="{{ route("dev-admin.projects.index") }}" class="btn btn-sm btn-primary"><i class="fas fa-building"></i></a>');
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
            return confirm('Are you sure you want to delete this unit type?');
        }
</script>
@endpush