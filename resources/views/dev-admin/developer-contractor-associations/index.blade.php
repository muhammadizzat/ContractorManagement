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
                        <div class="col-4 text-right">
                            <a href="{{ route('dev-admin.contractors.associations.add') }}" class="btn btn-sm btn-primary">{{ __('Associate Contractor') }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div id="alert-container">
                        @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                        @endif</div>
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center table-flush" id="developer_contractor_association">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">{{ __('Contractor') }}</th>
                                <th scope="col">{{ __('Created At') }}</th>
                                <th scope="col"></th>
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
<script type="text/javascript">
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var developerContractorAssociationTable = $('#developer_contractor_association').DataTable({
            dom: 'frtip',
            processing: true,
            serverSide: true,
            ajax: "{{ route('dev-admin.developer-contractor-associations.dt') }}",
            columns: [{
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) 
                    {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'contractor.user.name',
                    name: 'contractor.user.name',
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return '<form action="' + row.deleteAssociationUrl  + '" method=DELETE>@csrf<input type="submit" value="Remove Contractor" class="btn btn-primary btn-sm" onclick="return deleteFunction()"></form>';
                    }
                }
            ],
            language: {
                paginate: {
                    previous: `<span class="fas fa-angle-left"></span>`,
                    next: `<span class="fas fa-angle-right"></span>`,
                }
            },
        });

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
