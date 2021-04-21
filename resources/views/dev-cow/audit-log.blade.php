@extends('layouts.app')

@section('content')
@include('layouts.headers.cards')

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 mb-5">
            <div class="card shadow">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1>Audit Log</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <span>This is audit log</span>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
@endsection
