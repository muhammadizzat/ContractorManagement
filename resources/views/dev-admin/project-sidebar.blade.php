@if(!empty($project))
<hr class="my-3">
<h6 class="navbar-heading text-muted">Project: {{ $project->name }}</h6>
<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link {{ (request()->is('dev-admin/projects/*/units')) ? 'text-primary' : '' }}"
            href="{{ route('dev-admin.projects.units.index', $project->id) }}">
            <i class="fas fa-building"></i> {{ __('Units') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (request()->is('dev-admin/projects/*/cases')) ? 'text-primary' : '' }}"
            href="{{ route('dev-admin.projects.cases.index', $project->id) }}">
            <i class="fas fa-folder-open"></i> {{ __('Cases') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#assigned" data-toggle="collapse" role="button"
            aria-expanded="{{ (request()->is('dev-admin/projects/dev-admins/assign*') || request()->is('dev-admin/projects/*/clerks-of-work') || request()->is('dev-admin/projects/*/assignees')) ? 'true' : 'false' }}"
            aria-controls="assigned">
            <i class="fa fa-users"></i>
            <span class="nav-link-text">{{ __('Assigned Users') }}</span>
        </a>
        <div class="collapse {{ (request()->is('dev-admin/projects/*/dev-admins') || request()->is('dev-admin/projects/*/clerks-of-work') || request()->is('dev-admin/projects/*/assignees')) ? 'show' : '' }}"
            id="assigned">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('dev-admin/projects/*/dev-admins')) ? 'text-primary' : '' }}"
                        href="{{ route('dev-admin.projects.dev-admins.index', $project->id) }}">
                        {{ __('Developer Admins') }}
                    </a>
                    <a class="nav-link {{ (request()->is('dev-admin/projects/*/clerks-of-work')) ? 'text-primary' : '' }}"
                        href="{{ route('dev-admin.projects.dev-cows.index', $project->id) }}">
                        {{ __('Clerks of Work') }}
                    </a>
                    <a class="nav-link {{ (request()->is('dev-admin/projects/*/assignees')) ? 'text-primary' : '' }}"
                        href="{{ route('dev-admin.projects.assignees.index', $project->id) }}">
                        {{ __('Contractors') }}
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#project-configuration" data-toggle="collapse" role="button"
            aria-expanded="{{ (request()->is('dev-admin/projects/*/unit-types')) ? 'text-primary' : '' }}"
            aria-controls="project-configuration">
            <i class="fas fa-cog"></i>
            <span class="nav-link-text">{{ __('Configuration') }}</span>
        </a>
        <div class="collapse {{ (request()->is('dev-admin/projects/*/unit-types')) ? 'show' : '' }}"
            id="project-configuration">
            <ul class="nav nav-sm flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ (request()->is('dev-admin/projects/*/unit-types')) ? 'text-primary' : '' }}"
                        href="{{ route('dev-admin.projects.unit-types.index', $project->id) }}">
                        {{ __('Unit Types') }}
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>
@endif