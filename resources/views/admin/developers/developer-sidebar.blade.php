@if(!empty($developer))
<hr class="my-3">
<h6 class="navbar-heading text-muted">Developer: {{ $developer->name }}</h6>
<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link {{ (request()->is('home')) ? 'text-primary' : '' }}"
            href="{{ route('admin.developers.projects.index', $developer->id) }}">
            <i class="ni ni-building mr-1"></i> {{ __('Projects') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ (request()->is('home')) ? 'text-primary' : '' }}"
            href="{{ route('admin.developers.admins.index', $developer->id) }}">
            <i class="fa fa-users"></i> {{ __('Developer Admins') }}
        </a>
    </li>

</ul>
@endif