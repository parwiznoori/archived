<?php 
use Carbon\Carbon;
Carbon::setLocale('da');
?>
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner ">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            @php
                $logo_url_redirect = '';
                if(auth('user')->check())
                {
                    $logo_url_redirect = '';
                }
                elseif(auth('teacher')->check())
                {
                    $logo_url_redirect = 'teacher/noticeboard';
                }
                elseif(auth('student')->check())
                {
                    $logo_url_redirect = 'student/profile';
                }
            @endphp
            

            <a href="{{ url($logo_url_redirect) }}">
                <img src="{{  asset('img/hemis-logo.png') }}" alt="logo" class="logo-default" style="margin: 10px 20px" height="50" /> </a>
            <div class="menu-toggler sidebar-toggler">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN PAGE ACTIONS -->
        <!-- DOC: Remove "hide" class to enable the page header actions -->
        <div class="page-actions">
            <div class="btn-group">
                <button type="button" class="btn red-haze btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <span class="hidden-sm hidden-xs">{{trans('general.select_language')}}&nbsp;</span>
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>
                    @if(auth('user')->check())
                        <a href="{{route('locale' , 'pa')  }}">
                    @elseif(auth('teacher')->check())
                        <a href="{{route('teacher.locale' , 'pa')  }}">
                    @else
                    <a href="{{route('student.locale' , 'pa')  }}">
                    @endif
                        <i class="icon-pencil"></i> {{trans('general.pashto')}} </a>
                    </li>
                    <li>
                        @if(auth('user')->check())
                            <a href="{{route('locale' , 'da')  }}">
                        @elseif(auth('teacher')->check())
                            <a href="{{route('teacher.locale' , 'da')  }}">
                        @else
                            <a href="{{route('student.locale' , 'da')  }}">
                        @endif
                            <i class="icon-pencil"></i> {{trans('general.dari')}} </a>
                    </li>
                </ul>
            </div>
            <div class="shamsi-date">
                @php
                $date1 = Date('Y-m-d');
                $jalali_date=explode('-',$date1);
                $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
                $date=implode('/',$jDate);

                $time = Date('h:i:sa')
                @endphp
                تاریخ: {{$date}}
            </div>
            <div class="role">
                {{ trans('general.role') }} :
                {{  
                    auth('user')->check() ? (Auth::user()->roles ? (Auth::user()->roles->pluck('title')[0] ?? '') :  '' ) 
                                        : ( auth('teacher')->check() ? trans('general.teacher_role') : trans('general.student') )
                 }}
               
            </div>
        </div>
        <!-- END PAGE ACTIONS -->
        <!-- BEGIN PAGE TOP -->
        <div class="page-top">
            <!-- BEGIN HEADER SEARCH BOX -->
            <!-- DOC: Apply "search-form-expanded" right after the "search-form" class to have half expanded search box -->
            <form class="search-form hide" action="page_general_search_2.html" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control input-sm" placeholder="Search..." name="query">
                    <span class="input-group-btn">
                        <a href="javascript:;" class="btn submit">
                            <i class="icon-magnifier"></i>
                        </a>
                    </span>
                </div>
            </form>
            <!-- END HEADER SEARCH BOX -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <li class="separator hide"> 
                       
                    </li>
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    <!-- BEGIN INBOX DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->


                   @if(auth()->check())

                        <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="icon-bell"></i>
                                <span class="badge badge-danger" id ="notification_count"> {{auth()->user()->unreadNotifications->count()}}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="external">
                                    <h3>شما به تعداد
                                        <span class="bold" id= "notification_bottom_count">{{auth()->user()->unreadNotifications->count()}} </span> اطلاعیه دارید</h3>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                                        <div id="notification" style ="margin-bottom:2px !important"></div>
                                        @forelse(auth()->user()->notifications as $notification)
                                            @php
                                                $type = $notification->type;
                                            @endphp
                                            @if($notification->read_at == null)
                                                <li onclick="makeNotificationAsRead('{{$notification->id}}')" style ="background-color: #F0FFF0">
                                                    @if ($type == "App\Notifications\IssueCreatedNotication")
                                                        <a target ="_blank" href="{{ route ('issues.show', $notification->data['issueCreated']['id'])}}">
                                                <span class="subject">
                                                    <span class="from">{{$notification->data['user']['name']}}</span>
                                                    <span class="time">{{Carbon::parse($notification->data['issueCreated']['created_at'])->diffForHumans()}} </span>
                                                </span>
                                                            <span class="message"> {{ str_limit($notification->data['issueCreated']['title'], 50,'...') }}</span>
                                                        </a>
                                                    @elseif ($type == "App\Notifications\GraduateBookCreatedNotification")
                                                        @php
                                                            $title = __('general.graduates-book').' '. __('general.year').' '.$notification->data['graduateBookCreated']['graduated_year'].' '.__('general.has_been_created');
                                                        @endphp
                                                        <a target ="_blank" href="{{ route ('graduate-book.show', $notification->data['graduateBookCreated']['id'])}}">
                                                <span class="subject">
                                                    <span class="from">{{$notification->data['user']['name']}}</span>
                                                    <span class="time">{{Carbon::parse($notification->data['graduateBookCreated']['created_at'])->diffForHumans()}} </span>
                                                </span>

                                                            <span class="message"> {{ str_limit($title, 50,'...') }}</span>
                                                        </a>
                                                    @elseif ($type == "App\Notifications\UniversitiesActivitesExcelCreatedNotification")
                                                        @php
                                                            $title = __('general.universities_activities').' '. __('general.year').' '.$notification->data['universitiesActivitesExcelCreated']['education_year'].' '.__('general.has_been_created');
                                                        @endphp
                                                        <a target ="_blank" href="{{ route ('universities_activities.show', $notification->data['universitiesActivitesExcelCreated']['id'])}}">
                                                <span class="subject">
                                                    <span class="from">{{$notification->data['user']['name']}}</span>
                                                    <span class="time">{{Carbon::parse($notification->data['universitiesActivitesExcelCreated']['created_at'])->diffForHumans()}} </span>
                                                </span>

                                                            <span class="message"> {{ str_limit($title, 50,'...') }}</span>
                                                        </a>
                                                    @endif
                                                </li>
                                            @else
                                                <li onclick="makeNotificationAsRead('{{$notification->id}}')">
                                                    @if ($type == "App\Notifications\IssueCreatedNotication")
                                                        <a target ="_blank" href="{{ route ('issues.show', $notification->data['issueCreated']['id'])}}">
                                                <span class="subject">
                                                    <span class="from">{{$notification->data['user']['name']}}</span>
                                                    <span class="time">{{Carbon::parse($notification->data['issueCreated']['created_at'])->diffForHumans()}} </span>
                                                </span>
                                                            <span class="message"> {{ str_limit($notification->data['issueCreated']['title'], 50,'...') }}</span>
                                                        </a>
                                                    @elseif ($type == "App\Notifications\GraduateBookCreatedNotification")
                                                        @php
                                                            $title = __('general.graduates-book').' '. __('general.year').' '.$notification->data['graduateBookCreated']['graduated_year'].' '.__('general.has_been_created');
                                                        @endphp
                                                        <a target ="_blank" href="{{ route ('graduate-book.show', $notification->data['graduateBookCreated']['id'])}}">
                                                <span class="subject">
                                                    <span class="from">{{$notification->data['user']['name']}}</span>
                                                    <span class="time">{{Carbon::parse($notification->data['graduateBookCreated']['created_at'])->diffForHumans()}} </span>
                                                </span>
                                                            <span class="message"> {{ str_limit( $title, 50,'...') }}</span>
                                                        </a>
                                                    @elseif ($type == "App\Notifications\UniversitiesActivitesExcelCreatedNotification")
                                                        @php
                                                            $title = __('general.universities_activities').' '. __('general.year').' '.$notification->data['universitiesActivitesExcelCreated']['education_year'].' '.__('general.has_been_created');
                                                        @endphp
                                                        <a target ="_blank" href="{{ route ('universities_activities.show', $notification->data['universitiesActivitesExcelCreated']['id'])}}">
                                                <span class="subject">
                                                    <span class="from">{{$notification->data['user']['name']}}</span>
                                                    <span class="time">{{Carbon::parse($notification->data['universitiesActivitesExcelCreated']['created_at'])->diffForHumans()}} </span>
                                                </span>

                                                            <span class="message"> {{ str_limit($title, 50,'...') }}</span>
                                                        </a>
                                                    @endif
                                                </li>
                                            @endif
                                        @empty
                                            <li id = "no_notification"> {{trans('general.unread_notifications')}}</li>
                                        @endforelse
                                    </ul>
                                </li>
                            </ul>
                        </li>
                @endif 

                {{-- @if(auth()->check())
                <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <i class="icon-users"></i>
                        <span class="badge badge-primary" id="user_role_count">
                            {{ Auth::user()->archiveRoles->count() }}
                        </span>
                        
                    </a>
                    <ul class="dropdown-menu">
                        <li class="external">
                            <h3>
                                وظایف کاربران یوزر های آرشیف
                                <span class="badge badge-primary" id="user_role_count">
                                    @if(Auth::user()->archiveRoles)
                                        {{ Auth::user()->archiveRoles->count() }}
                                    @else
                                        {{ Auth::user()->name }}
                                    @endif
                                </span>
                            </h3>
                        </li>
                        <li>
                            <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                                @forelse(Auth::user()->archiveRoles as $role)
                                    <li style="padding: 10px; border-bottom: 1px solid #ddd;">
                                        <a target="_blank" href="{{ route('archiverole', ['name' => $role->role->name ?? 'unknown', 'id' => $role->id ?? 0]) }}">
                                            <span class="subject">
                                                <span class="from">{{ $role->user->name ?? 'کاربر نامشخص' }}</span>
                                                <span class="time">{{ \Carbon\Carbon::parse($role->created_at)->diffForHumans() }}</span>
                                            </span>
                                            <span class="message">
                                                نقش: {{ $role->role->name ?? 'نقش نامشخص' }}<br>
                                                کتاب آرشیف: {{ $role->archive->book_name ?? 'آرشیف نامشخص' }}<br>
                                                وضعیت: {{ $role->archivedatastatus->name ?? 'وضعیت نامشخص' }}<br>
                                                QC وضعیت: {{ $role->archiveqcstatus->name ?? 'QC وضعیت نامشخص' }}
                                            </span>
                                        </a>
                                    </li>
                                @empty
                                    <li id="no_roles" style="text-align: center; padding: 15px;">
                                        {{ __('general.no_records_found') }}
                                    </li>
                                @endforelse
                            </ul>
                        </li>
                    </ul>
                </li>
            @endif
            
                
             --}}



                <!-- END INBOX DROPDOWN -->

                    <!-- BEGIN USER LOGIN DROPDOWN -->
                    <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                    <li class="dropdown dropdown-user dropdown-dark">
                        <a href="{{ auth('user')->check() ? route('profile.password') : (auth('teacher')->check() ? route('teacher.profile.password') : route('student.profile.password') )  }}" class="dropdown-toggle" >
                            <span class="username username-hide-on-mobile"> {{ Auth::user()->name }} </span>
                            <!-- DOC: Do not remove below empty space(&nbsp;) as its purposely used -->
                           {{-- <img alt="" class="img-circle" src="img/avatar.png" />   --}}
                        </a>
                        
                    </li>
                    
                    <li class="dropdown dropdown-extended dropdown-tasks dropdown-dark" id="header_task_bar">
                        <a href="{{ route('logout') }}" onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();" 
                           class="dropdown-toggle" 
                           title="{{ trans('general.logout') }}">
                            <i class="icon-logout"></i>                            
                        </a>                      
                        
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                    <!-- END USER LOGIN DROPDOWN -->
                    <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                    <li class="dropdown dropdown-extended quick-sidebar-toggler hide">
                        <span class="sr-only">Toggle Quick Sidebar</span>
                        <i class="icon-logout"></i>
                    </li>
                    <!-- END QUICK SIDEBAR TOGGLER -->
                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END PAGE TOP -->
    </div>
    <!-- END HEADER INNER -->
</div>
