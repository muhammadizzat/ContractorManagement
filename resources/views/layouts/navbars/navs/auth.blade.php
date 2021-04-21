<!-- Top navbar -->
<nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
    <div class="container-fluid">
        <!-- Brand -->
        {{ Breadcrumbs::render() }}
        <!-- Form -->
        <ul class="navbar-nav ml-lg-auto">  
            @hasrole('cow|contractor|dev-admin')
            <li id="notifications-dropdown" class="nav-item dropdown">
                <?php $notification_count = Auth::user()->unreadNotifications()->count(); ?>
                <a class="nav-link nav-link-icon" href="#"  role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span id="notification-badge" class="badge badge-pill py-2"><i class="fas fa-bell"></i>
                        <span id="notifications-count">
                                @if($notification_count > 0)
                                {{ $notification_count }}
                                @endif
                        </span>
                    </span> 
                </a>
                <div id="notifications-list-container" class="dropdown-menu dropdown-menu-right"
                    aria-labelledby="notifications-dropdown">
                    <div class="px-3">
                        <h5>Notifications</h5>
                    </div>
                    <div class="dropdown-divider m-0"></div>
                    <div id="notifications-list">

                    </div>
                    <div class="dropdown-divider m-0"></div>
                    <div class="notifications-footer pt-1 px-3">
                        <a href="{{ route('notifications.page') }}">
                            <h5 class="mb-0">View all notifications</h5>
                        </a>
                    </div>
                </div>
            </li>
            @endhasrole
        </ul>

        <span class="badge badge-pill badge-white mr-3 d-none d-md-flex px-3 py-2 l-space">
            @role('super-admin')
            super-admin
            @endrole
            @role('admin')
            Linkzzapp admin
            @endrole
            @role('dev-admin')
            developer admin
            @endrole
            @role('contractor')
            contractor
            @endrole
            @role('cow')
            clerk of work
            @endrole
        </span>

        <!-- User -->
        <ul class="navbar-nav align-items-center d-none d-md-flex">
            <li class="nav-item dropdown">
                <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true"
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
                    <div class="media align-items-center dropdown-header text-dark">
                            <span class="avatar avatar-sm rounded-circle">
                                @if (Auth::user()->profile_pic_media_id != null)
                                <img class="logo-media-icon"
                                    src="data:{{ Auth::user()->profile_pic_media->mimetype }};base64,{{ base64_encode(Auth::user()->profile_pic_media->data) }}">
                                @else
                                <img alt="Image placeholder"
                                    src="{{ asset('argon') }}/img/theme/profile-pic-placeholder.png">
                                @endif
                            </span>
                        <div class="media-body ml-2 d-none d-block">
                            <span class="mb-0 text-sm  font-weight-bold">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    @if (Auth::user()->change_password == 1)
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="ni ni-single-02"></i>
                        <span>{{ __('Profile Settings') }}</span>
                    </a>
                    @endif
                    <a href="{{ route('profile.password') }}" class="dropdown-item">
                        <i class="ni ni-settings-gear-65"></i>
                        <span>{{ __('Change Password') }}</span>
                    </a>
                    <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                        <i class="ni ni-user-run"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<div id="notification-templates" hidden="true">
    <a class="defect-activity-comment-notification dropdown-item py-1">
        <div class="header d-flex flex-row align-items-center">
            <div class="details-container flex-fill">
                <span class="defect-label">Defect</span>
                <span class="project-name"></span><span class="defect-no badge-warning"></span>
            </div>
            <div class="date-time">
                
            </div>
        </div>
        <div class="d-flex flex-row pt-1">
            <div class="pr-2">
                <i class="far fa-comment-alt"></i>
            </div>
            <div class="notification-info">
                <div class="content"></div>
            </div>
        </div>
    </a>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('#notifications-dropdown').on('show.bs.dropdown', function(event) {
            getNotifications(10, function(notifications) {
                console.log("Notifications: ", notifications);
                var notificationsListEl = $('#notifications-list');
                notificationsListEl.html("");

                for(notification of notifications) {
                    var newNotificationEl = $('#notification-templates .defect-activity-comment-notification').clone();

                    newNotificationEl.find('.date-time').text(moment(notification.created_at).format("LT DD MMM YYYY"));
                    newNotificationEl.find('.project-name').text(notification.data.project_name);

                    switch(notification.type) {
                        case "App\\Notifications\\DefectDueDateExtended":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                                notification.data.defect_id,
                            ));   
                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> extended the due date");
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                            break;

                        case "App\\Notifications\\DefectImageUpdated":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                                notification.data.defect_id,
                            ));   
                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> updated the images");
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                            break;

                        case "App\\Notifications\\PinUpdated":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                                notification.data.defect_id,
                            ));   
                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> updated the pins");
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                            break;

                        case "App\\Notifications\\DefectRequestResponse":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                                notification.data.defect_id,
                            ));   
                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> responded to the request");
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                            break;

                        case "App\\Notifications\\ContractorDefectRequest":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                                notification.data.defect_id,
                            ));   
                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> requested for status update");
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                            break;

                        case "App\\Notifications\\NewDefect":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                                notification.data.defect_id,
                            ));   
                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> created a new defect");
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                            break;
                            
                        case "App\\Notifications\\DefectContractorUnassign":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                                notification.data.defect_id,
                            ));   
                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> unassigned contractor " + notification.data.assigned_contractor.name + " to defect");
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                            break;

                        case "App\\Notifications\\DefectContractorAssign":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                                notification.data.defect_id,
                            ));    

                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> assigned contractor " + notification.data.assigned_contractor.name + " to defect")
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                            break;

                        case "App\\Notifications\\CaseCowAssigned":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                            ));    


                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> assigned you to " + notification.data.case_title )
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no);

                            break;

                        case "App\\Notifications\\CaseCowUnassigned":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                            ));    


                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> unassigned you from " + notification.data.case_title )
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no);

                            break;

                        case "App\\Notifications\\NewCaseStatus":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                            ));    


                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> changed the status of case to " + notification.data.case_status )
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no);

                            break;

                        case "App\\Notifications\\NewDefectStatus":
                            newNotificationEl.attr('href', getUrlForCase(
                                notification.data.project_id,
                                notification.data.case_id,
                                notification.data.defect_id,
                            ));    

                            newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> changed the status of defect to " + notification.data.defect_status )
                            newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

                            break;

                        case "App\\Notifications\\NewDefectActivity":
                            switch(notification.data.type) {
                                case "comment":
                                    newNotificationEl.attr('href', getUrlForCase(
                                        notification.data.project_id,
                                        notification.data.case_id,
                                        notification.data.defect_id,
                                    ));    

                                    newNotificationEl.find('.content').html("<strong>" + notification.data.user_name + "</strong> added a comment")
                                    newNotificationEl.find('.defect-no').text("C" + notification.data.case_ref_no + "-D" + notification.data.defect_ref_no);

        
                                    break;
                            }
                            break;
                        }
                    notificationsListEl.append(newNotificationEl);
                    }
                postNotificationsMarkAllAsRead(function() {
                });
            })
        });

        // API
        function getNotifications(limit, onSuccess) {
            var getNotificationsRoute = "{{ route('notifications.get') }}";
            $.ajax({
                url: getNotificationsRoute,
                type: 'GET',
                data: {
                    limit: limit
                },
                success: function(notifications) {
                    onSuccess(notifications);
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

        var notificationsStatsUpdateCount = 0;
        var notificationsStatsUpdateInterval = setInterval(function() {
            getNotificationsStats(function(stats) {
                var notificationsCountEl = $('#notifications-count');
                if(stats.unread > 0) {
                    notificationsCountEl.text(stats.unread);
                } else {
                    notificationsCountEl.text("");
                }
            });

            if(++notificationsStatsUpdateCount > 60) {
                clearInterval(notificationsStatsUpdateInterval);
            }
        }, 60000);

        function getNotificationsStats(onSuccess) {
            var getNotificationsStatsRoute = "{{ route('notifications.stats.get') }}";
            $.ajax({
                url: getNotificationsStatsRoute,
                type: 'GET',
                success: function(stats) {
                    onSuccess(stats);
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

        function postNotificationsMarkAllAsRead(onSuccess) {
            var postNotificationsStatsRoute = "{{ route('notifications.mark-all-read.post') }}";
            $.ajax({
                url: postNotificationsStatsRoute,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function() {
                    onSuccess();
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

        @role('dev-admin')
        var getCaseRouteTemplate = "{{ route('dev-admin.projects.cases.view', ['proj_id' => '<<proj_id>>', 'case_id' => '<<case_id>>']) }}";
        @endrole
        @role('cow')
        var getCaseRouteTemplate = "{{ route('dev-cow.projects.cases.view', ['proj_id' => '<<proj_id>>', 'case_id' => '<<case_id>>']) }}";
        @endrole
        @role('contractor')
        var getCaseRouteTemplate = "{{ route('contractor.dashboard', ['id' => '<<id>>']) }}";
        @endrole
        
        function getUrlForCase(projectId, caseId, defectId) {
            var getCaseRoute = getCaseRouteTemplate.replace(encodeURI('<<proj_id>>'), projectId);
            getCaseRoute = getCaseRoute.replace(encodeURI('<<case_id>>'), caseId);
            getCaseRoute = getCaseRoute.replace(encodeURI('<<id>>'), defectId);
            
            return getCaseRoute;
        }
    });
    
    
</script>
@endpush
