@extends('layouts.app', ['title' => __('Project Management')])
@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])
<div class="container-fluid mt--9">
    <div id="project-dashboard-card" class="card shadow">
        <div class="card-body p-2">
            {{-- Tab List --}}
            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" id="pills-by-defects-tab" data-toggle="pill" href="#pills-by-defects"
                        role="tab" aria-controls="pills-home" aria-selected="true">By Defects</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-by-defect-types-tab" data-toggle="pill" href="#pills-by-defect-types"
                        role="tab" aria-controls="pills-contact" aria-selected="false">By Defect Types</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" id="pills-by-units-tab" data-toggle="pill" href="#pills-by-units" role="tab"
                        aria-controls="pills-profile" aria-selected="false">By Units</a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link" id="pills-by-tags-tab" data-toggle="pill" href="#pills-by-tags" role="tab"
                    aria-controls="pills-profile" aria-selected="false">By Tags</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-by-response-times-tab" data-toggle="pill" href="#pills-by-response-times" role="tab"
                        aria-controls="pills-response" aria-selected="false">By Response Times</a>
                </li>
                {{-- Add new tabs here --}}
            </ul>
        </div>
        <div class="card-body p-2">

            {{-- Tab Content --}}
            <div class="tab-content" id="dashboard-tabs-content">
                @include('dev-admin.projects.dashboard-tabs.by-defects', ['proj_id' => $proj_id])
                @include('dev-admin.projects.dashboard-tabs.by-defect-types', ['proj_id' => $proj_id])
                {{-- @include('dev-admin.projects.dashboard-tabs.by-units', ['proj_id' => $proj_id]) --}}
                @include('dev-admin.projects.dashboard-tabs.by-tags', ['proj_id' => $proj_id])
                @include('dev-admin.projects.dashboard-tabs.by-response-times', ['proj_id' => $proj_id])
                {{-- Add new tabs content here --}}
            </div>
        </div>
    </div>
</div>

@include('layouts.footers.auth')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-colorschemes"></script>
<script>
    $(function() {
        $('#pills-by-defects-tab').tab('show');
    });

</script>
@endpush
