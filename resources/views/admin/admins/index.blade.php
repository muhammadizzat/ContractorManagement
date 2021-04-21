@extends('layouts.app', ['title' => __('Linkzzapp Admin Management')])

@section('content')
@include('users.partials.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row justify-content-md-center">
        <div class="col col-lg-11">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Linkzzapp Admins') }}</h3>
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
            dom: '<"button-section btn-back"><"button-section btn-excel"><"button-section btn-add">Bfrtip',
            order: [[ 4, "asc"], [ 3, "desc" ], [1, "asc"]],
            processing: true,
            serverSide: true,
            responsive:true,
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
            ajax: "{{ route('admin.admins.dt', 'status = $status') }}",
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
                                        +'<span onclick="resetPassword('+row.id+')" class="dropdown-item pointer">Reset Password</span>'
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


        $('.button-section.btn-add').tooltip({
            placement: 'auto',
            title: 'Add',
        });
        $('.button-section.btn-back').tooltip({
            placement: 'auto',
            title: 'Home',
        });
        $('.button-section.btn-excel').tooltip({
            placement: 'auto',
            title: 'Export To Excel',
        });

        $("div.button-section.btn-add").html('<a href="{{ route("admin.admins.add") }}" class="btn btn-sm btn-primary"><i class="fas fa-plus-circle fa-fw"></i></a>');
        $("div.button-section.btn-back").html('<a href="{{ route("admin.developers.index") }}" class="btn btn-sm btn-primary"><i class="fas fa-sign-out-alt"></i></a>');
        $("div.button-section.btn-excel").html('<a href="{{ route("admin.admins.export") }}" class="btn btn-sm btn-primary"><i class="fas fa-file-excel"></i></a>');   
    });
        
    function resetPassword(user_id){
        if (confirm("Are you sure you want to reset the user's password?") == true) {
            var postLinkzzappAdminResetPasswordRouteTemplate = "{{ route('reset-user-password', ['user_id' => '<<user_id>>'])}}"
            var postLinkzzappAdminResetPasswordRoute = postLinkzzappAdminResetPasswordRouteTemplate.replace(encodeURI('<<user_id>>'), user_id);
        
        $.ajax({
            url: postLinkzzappAdminResetPasswordRoute,
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
        return confirm('Are you sure you want to delete this admin?');
    }
</script>
@endpush