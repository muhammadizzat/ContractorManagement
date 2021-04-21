@if (count($breadcrumbs))

    {{-- <ol class="breadcrumb d-flex justify-content-end px-5"> --}}
        @foreach ($breadcrumbs as $breadcrumb)

            @if ($breadcrumb->url && !$loop->last)
                {{-- <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li> --}}
                @if(!empty($breadcrumb->developer_bc))
               
                <a class="ml-3 ml-md-0 mr-3" href="{{ $breadcrumb->url }}"><span class="text-white badge  badge-primary d-md-flex  px-3 py-2 l-space">
                        Developer: {{ $breadcrumb->title }}</span></a>
                @elseif(!empty($breadcrumb->project_bc))
                     <a class="ml-3 ml-md-0 mr-3" href="{{ $breadcrumb->url }}"><span class="text-white badge  badge-primary d-md-flex  px-3 py-2 l-space">
                                Project: {{ $breadcrumb->title }}</span></a>
                @else
                <a class="{{ $loop->first? 'ml-3 ml-md-0': '' }} h4 mb-0 text-light text-uppercase d-inline-block" href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>                
                    <span>&nbsp;/&nbsp;</span>
                @endif
            @else
                <span class="{{ $loop->first? 'ml-3 ml-md-0': '' }} h4 mb-0 text-white text-uppercase d-inline-block">{{ $breadcrumb->title }}</span>
                {{-- <li class="breadcrumb-item active">{{ $breadcrumb->title }}</li> --}}
            @endif

        @endforeach
        <span class="mr-3"></span>
    {{-- </ol> --}}
@endif
