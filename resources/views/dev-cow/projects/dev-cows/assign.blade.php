@extends('layouts.app', ['title' => __('Assigned Clerk Of Work')])

@section('content')
@include('layouts.headers.developer.header', ['title' => __('')])

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Assign Clerk Of Work') }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('dev-admin.projects.dev-cows.index', $proj_id) }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
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
                <form role="form" method="post" action="{{route('dev-admin.projects.dev-cows.assign.post', $proj_id)}}" autocomplete="off">
                        @csrf
                        <div class="pl-lg-4">
                            <label class="form-control-label" for="input-dev-cow-user-id">{{ __('Assign Project Clerk Of Work') }}</label>
                            <div class="form-group{{ $errors->has('dev_cow_user_id') ? ' has-danger' : '' }}">
                                <select class="selectpicker form-control form-control-alternative{{ $errors->has('dev_cow_user_id') ? 'is-invalid' : '' }}" name="dev_cow_user_id" id="dev_cow_user_id" data-live-search="true" title="Please Choose Name" required>
                                    <option value="">Please Choose Name</option>
                                    @foreach(App\User::whereHas("roles", function($q){
                                                $q->where("name", "cow"); 
                                            })->whereHas("clerk_of_work", function($q){
                                                $q->where("developer_id", auth()->user()->developer_admin->developer_id);
                                            })
                                            ->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('dev_cow_user_id') }}</span>
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