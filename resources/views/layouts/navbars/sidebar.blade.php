@if (Auth::user()->change_password == 1)
<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main"
            aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0 pr-5">
            <img src="{{ asset('storage/syn-logo.png') }}" class="navbar-brand-img" alt="...">
        </a>
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                            @if (Auth::user()->profile_pic_media_id != null)
                            <img class="logo-media-icon"
                                src="data:{{ Auth::user()->profile_pic_media->mimetype }};base64,{{ base64_encode(Auth::user()->profile_pic_media->data) }}">
                            @else
                            <img alt="Image placeholder"
                                src="{{ asset('argon') }}/img/theme/profile-pic-placeholder.png">
                            @endif
                        </span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">{{ __('Welcome!') }}</h6>
                    </div>
                    @if (Auth::user()->change_password == 1)
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="ni ni-single-02"></i>
                        <span>{{ __('Profile Settings') }}</span>
                    </a>
                    @endif
                    <a href="#" class="dropdown-item">
                        <i class="ni ni-settings-gear-65"></i>
                        <span>{{ __('Change Password') }}</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                        <i class="ni ni-user-run"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                </div>
            </li>
        </ul>
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-md-none">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a>
                            <img src="{{ asset('storage/linkzzapp-logo.png') }}">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse"
                            data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false"
                            aria-label="Toggle sidenav">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Form -->
            <form class="mt-4 mb-3 d-md-none">
                <div class="input-group input-group-rounded input-group-merge">
                    <input type="search" class="form-control form-control-rounded form-control-prepended"
                        placeholder="{{ __('Search') }}" aria-label="Search">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-search"></span>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Navigation -->
            @hasrole('admin|super-admin')
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{(request()->is('admin')) ? 'text-primary' : ''}}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="ni ni-planet"></i> {{ __('Dashboard') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#users" data-toggle="collapse" role="button"
                        aria-expanded="{{ (request()->is('admin/admins*')) ? 'true' : 'false' }}" aria-controls="users">
                        <i class="fa fa-users"></i>
                        <span class="nav-link-text">{{ __('Users') }}</span>
                    </a>
                    <div class="collapse{{ (request()->is('admin/users*') || request()->is('admin/admins*') || request()->is('admin/contractors*') || request()->is('admin/developer-admins')) ? 'show' : '' }}"
                        id="users">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('admin/users*')) ? 'text-primary' : '' }}"
                                    href="{{ route('admin.users.index') }}">
                                    {{ __('All Users') }}
                                </a>
                                <a class="nav-link {{ (request()->is('admin/admins*')) ? 'text-primary' : '' }}"
                                    href="{{ route('admin.admins.index') }}">
                                    {{ __('Linkzzapp Admins') }}
                                </a>
                                <a class="nav-link {{ (request()->is('admin/developer-admins')) ? 'text-primary' : '' }}"
                                    href="{{ route('admin.developer-admins.index') }}">
                                    {{ __('Developer Admins') }}
                                </a>
                                <a class="nav-link {{ (request()->is('admin/contractors*')) ? 'text-primary' : '' }}"
                                    href="{{ route('admin.contractors.index') }}">
                                    {{ __('Contractors') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(request()->is('admin/developers*') && !request()->is('admin/developer-admins'))? 'text-primary' : '' }}"
                        href="{{ route('admin.developers.index') }}">
                        <i class="ni ni-planet"></i> {{ __('Developers') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#configurations" data-toggle="collapse" role="button"
                        aria-expanded="{{ (request()->is('admin/configuration*')) ? 'true' : 'false' }}"
                        aria-controls="configurations">
                        <i class="ni ni-settings-gear-65"></i>
                        <span class="nav-link-text">{{ __('Configuration') }}</span>
                    </a>
                    <div class="collapse {{ (request()->is('admin/configuration*')) ? 'show' : '' }}"
                        id="configurations">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('admin/configuration/defect-type*')) ? 'text-primary' : '' }}"
                                    href="{{ route('admin.configuration.defect-types.index') }}">
                                    {{ __('Default Defect Types') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('admin/audit-log*')) ? 'text-primary' : '' }}"
                        href="{{ route('admin.audit-log.index') }}">
                        <i class="fa fa-archive"></i> {{ __('Audit Log') }}
                    </a>
                </li>
            </ul>
            @include('admin.developers.developer-sidebar')
            @endhasrole

            @hasrole('dev-admin')

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('dev-admin')) ? 'text-primary' : '' }}"
                        href="{{ route('dev-admin.dashboard') }}">
                        <i class="ni ni-planet"></i> {{ __('Dashboard') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('dev-admin/projects*')) ? 'text-primary' : '' }}"
                        href="{{ route('dev-admin.projects.index') }}">
                        <i class="ni ni-building"></i> {{ __('Projects') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#users" data-toggle="collapse" role="button"
                        aria-expanded="{{ (request()->is('dev-admin/developer-admins*') || request()->is('dev-admin/clerks-of-work*') || request()->is('dev-admin/contractor**') || request()->is('dev-admin/associations**')) ? 'true' : 'false' }}"
                        aria-controls="users">
                        <i class="fa fa-users"></i>
                        <span class="nav-link-text">{{ __('Users') }}</span>
                    </a>
                    <div class="collapse {{ (request()->is('dev-admin/developer-admins*') || request()->is('dev-admin/clerks-of-work*') || request()->is('dev-admin/contractor**') || request()->is('dev-admin/associations**')) ? 'show' : '' }}"
                        id="users">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('dev-admin/developer-admins*')) ? 'text-primary' : '' }}"
                                    href="{{ route('dev-admin.developer-admins.index') }}">
                                    {{ __('Developer Admins') }}
                                </a>
                                <a class="nav-link {{ (request()->is('dev-admin/clerks-of-work*')) ? 'text-primary' : '' }}"
                                    href="{{ route('dev-admin.clerks-of-work.index') }}">
                                    {{ __('Clerks of Work') }}
                                </a>
                                <a class="nav-link {{ (request()->is('dev-admin/contractor*') && !request()->is('dev-admin/contractors/associations*')) ? 'text-primary' : '' }}"
                                    href="{{ route('dev-admin.contractor.index') }}">
                                    {{ __('Contractor') }}
                                </a>
                                <a class="nav-link {{ (request()->is('dev-admin/associations*')) ? 'text-primary' : '' }}"
                                    href="{{ route('dev-admin.associations.index') }}">
                                    {{ __('Setting Up Contractor Scope of Work') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#configurations" data-toggle="collapse" role="button"
                        aria-expanded="{{ (request()->is('dev-admin/configuration*')) ? 'true' : 'false' }}"
                        aria-controls="configurations">
                        <i class="ni ni-settings-gear-65"></i>
                        <span class="nav-link-text">{{ __('Configuration') }}</span>
                    </a>
                    <div class="collapse {{ (request()->is('dev-admin/configuration*')) ? 'show' : '' }}"
                        id="configurations">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('dev-admin/configuration/defect-type*')) ? 'text-primary' : '' }}"
                                    href="{{ route('dev-admin.configuration.defect-types.index') }}">
                                    {{ __('Defect Types') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('dev-admin/audit-log*')) ? 'text-primary' : '' }}"
                        href="{{ route('dev-admin.audit-log.index') }}">
                        <i class="fa fa-archive"></i> {{ __('Audit Log') }}
                    </a>
                </li>
            </ul>
            @include('dev-admin.project-sidebar')
            @endhasrole

            @hasrole('cow')
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('dev-cow')) ? 'text-primary' : '' }}"
                        href="{{ route('dev-cow.dashboard') }}">
                        <i class="ni ni-planet"></i> {{ __('Dashboard') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('dev-cow/projects*')) ? 'text-primary' : '' }}"
                        href="{{ route('dev-cow.projects.index') }}">
                        <i class="ni ni-building"></i> {{ __('Projects') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#users" data-toggle="collapse" role="button"
                        aria-expanded="{{ (request()->is('dev-cow/clerks-of-work*') || request()->is('dev-cow/contractors*') || request()->is('dev-cow/developer-contractor-associations*')) ? 'true' : 'false' }}"
                        aria-controls="users">
                        <i class="fa fa-users"></i>
                        <span class="nav-link-text">{{ __('Users') }}</span>
                    </a>
                    <div class="collapse {{ (request()->is('dev-cow/clerks-of-work*') || request()->is('dev-cow/associations*') || request()->is('dev-cow/contractors*')) ? 'show' : '' }}"
                        id="users">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('dev-cow/developer-admins*')) ? 'text-primary' : '' }}"
                                    href="{{ route('dev-cow.developer-admins.index') }}">
                                    {{ __('Developer Admins') }}
                                </a>
                                <a class="nav-link {{ (request()->is('dev-cow/clerks-of-work*')) ? 'text-primary' : '' }}"
                                    href="{{ route('dev-cow.clerks-of-work.index') }}">
                                    {{ __('Clerks of Work') }}
                                </a>
                                <a class="nav-link {{ (request()->is('dev-cow/associations*')) ? 'text-primary' : '' }}"
                                    href="{{ route('dev-cow.associations.index') }}">
                                    {{ __('Setting Up Contractor Scope of Work') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#configurations" data-toggle="collapse" role="button"
                        aria-expanded="{{ (request()->is('dev-cow/configuration*')) ? 'true' : 'false' }}"
                        aria-controls="configurations">
                        <i class="ni ni-settings-gear-65"></i>
                        <span class="nav-link-text">{{ __('Configuration') }}</span>
                    </a>
                    <div class="collapse {{ (request()->is('dev-cow/configuration*')) ? 'show' : '' }}"
                        id="configurations">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ (request()->is('dev-cow/configuration/defect-type*')) ? 'text-primary' : '' }}"
                                    href="{{ route('dev-cow.configuration.defect-types.index') }}">
                                    {{ __('Defect Types') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('dev-cow/audit-log*')) ? 'text-primary' : '' }}"
                        href="{{ route('dev-cow.audit-log.index') }}">
                        <i class="fa fa-archive"></i> {{ __('Audit Log') }}
                    </a>
                </li>
            </ul>
            @include('dev-cow.project-sidebar')
            @endhasrole
            </ul>
        </div>
    </div>
</nav>
@endif