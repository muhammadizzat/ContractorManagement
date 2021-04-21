@extends('layouts.app', ['title' => __('Assign Developer Admin')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Assign Developer Admin') }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('dev-admin.projects.dev-admins.index', $proj_id) }}"
                                class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
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
                <div class="card-body">
                    <form role="form" method="post"
                        action="{{route('dev-admin.projects.dev-admins.assign.post', $proj_id)}}" autocomplete="off">
                        @csrf
                        <div class="pl-lg-4">
                            <label class="form-control-label"
                                for="input-dev-admin-user-id">{{ __('Assign Developer Admin') }}</label>
                            <div class="form-group{{ $errors->has('dev_admin_user_id') ? ' has-danger' : '' }}">
                                <select
                                    class="selectpicker form-control form-control-alternative{{ $errors->has('dev_admin_user_id') ? 'is-invalid' : '' }}"
                                    name="dev_admin_user_id" id="dev_admin_user_id" data-live-search="true" title="Please Choose Name" required>
                                    @foreach(App\User::whereHas("roles", function($q){
                                    $q->where("name", "dev-admin");
                                    })->whereHas("developer_admin", function($q){
                                    $q->where("developer_id", auth()->user()->developer_admin->developer_id);
                                    $q->where("primary_admin", 0);
                                    })
                                    ->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}
                                        @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('dev_admin_user_id') }}</span>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn assign-user btn-success mt-4">{{ __('Assign') }}</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
@endsection