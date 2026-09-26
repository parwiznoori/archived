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

                    <!-- BEGIN USER + NOTIFICATION (same line) -->
                    <li class="dropdown dropdown-user dropdown-dark header-user-notification" style="float: right; display: flex !important; align-items: center; height: 75px; position: relative;">
                        @if(auth('user')->check())
                        <div class="header-notification" style="display: flex; align-items: center; position: relative; padding: 0 6px; margin-left: 15px;">
                            <a href="javascript:;" class="notification-bell dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" style="text-decoration: none; color: #555; font-size: 18px; position: relative; cursor: pointer; padding: 8px; line-height: 1; display: flex; align-items: center;">
                                <i class="icon-bell"></i>
                                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                                @if($unreadCount > 0)
                                    <span class="badge badge-danger" style="position: absolute; top: 2px; right: 0; font-size: 10px; padding: 1px 5px; border-radius: 10px; background: #e74c3c; color: #fff; min-width: 16px; text-align: center;">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu" style="min-width: 420px; max-height: 500px; overflow-y: auto; left: 0; right: auto; top: 100%; bottom: auto; margin-top: 0; padding: 0; position: absolute; z-index: 9999;">
                                <li class="external" style="padding: 12px 15px; background: #f8f9fa; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 10;">
                                    <h5 style="margin: 0; font-weight: 600; font-size: 14px;">
                                        <i class="icon-bell"></i> {{ __('general.notifications') }}
                                        @if($unreadCount > 0)
                                            <span class="badge badge-danger" style="background: #e74c3c;">{{ $unreadCount }}</span>
                                        @endif
                                    </h5>
                                </li>
                                <li style="max-height: 430px; overflow-y: auto; padding: 0; margin: 0;">
                                    <ul style="padding: 0; margin: 0; list-style: none;">
                                        <div id="notification-live" style="margin-bottom: 2px !important;"></div>
                                        @forelse(auth()->user()->notifications->take(50) as $notification)
                                            @php
                                                $ndata = $notification->data;
                                                $ntype = $notification->type;
                                                $action = $ndata['action'] ?? '';
                                                $isUnread = $notification->read_at == null;
                                                $nurl = $ndata['url'] ?? '#';
                                                $notifDate = '';
                                                if ($notification->created_at) {
                                                    $nc = \Carbon\Carbon::parse($notification->created_at);
                                                    $njParts = \Morilog\Jalali\CalendarUtils::toJalali($nc->year, $nc->month, $nc->day);
                                                    $notifDate = implode('/', $njParts) . ' ' . $nc->format('H:i');
                                                }
                                            @endphp
                                            <li onclick="makeNotificationAsRead('{{ $notification->id }}')"
                                                style="padding: 10px 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; {{ $isUnread ? 'background-color: #FFF8E1;' : 'background-color: #fff;' }}">

                                                {{-- نوتیفیکیشن رد شدن صفحه --}}
                                                @if($action === 'page_rejected')
                                                    <a href="{{ $nurl }}" target="_blank" style="text-decoration: none; color: #333; display: block;" onclick="event.stopPropagation(); makeNotificationAsRead('{{ $notification->id }}');">
                                                        <span style="display: block; font-size: 13px; font-weight: 600; color: #e74c3c;">
                                                            <i class="fa fa-times-circle"></i> {{ __('general.page_rejected') }}
                                                            @if($isUnread) <span style="background: #e74c3c; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 8px; margin-right: 4px;">{{ __('general.new') }}</span> @endif
                                                        </span>
                                                        <span style="display: block; font-size: 12px; color: #555; margin-top: 3px;">
                                                            {{ __('general.book') }}: <strong>{{ $ndata['book_name'] ?? '-' }}</strong> | {{ $ndata['page_title'] ?? '-' }}
                                                        </span>
                                                        <span style="display: block; font-size: 11px; color: #888; margin-top: 2px;">
                                                            {{ __('general.rejected_by') }}: {{ $ndata['rejected_by'] ?? '-' }}
                                                        </span>
                                                        <span style="display: block; font-size: 11px; color: #888; margin-top: 2px;">
                                                            {{ __('general.reason') }}: {{ \Illuminate\Support\Str::limit($ndata['reject_comment'] ?? '-', 60, '...') }}
                                                        </span>
                                                        <span style="display: block; font-size: 11px; color: #888; margin-top: 2px;">
                                                            {{ __('general.rejected_at') }}: {{ $notifDate }}
                                                        </span>
                                                    </a>

                                                {{-- نوتیفیکیشن اصلاح شدن صفحه --}}
                                                @elseif($action === 'page_fixed')
                                                    <a href="{{ $nurl }}" target="_blank" style="text-decoration: none; color: #333; display: block;" onclick="event.stopPropagation(); makeNotificationAsRead('{{ $notification->id }}');">
                                                        <span style="display: block; font-size: 13px; font-weight: 600; color: #27ae60;">
                                                            <i class="fa fa-check-circle"></i> {{ __('general.page_fixed') }}
                                                            @if($isUnread) <span style="background: #27ae60; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 8px; margin-right: 4px;">{{ __('general.new') }}</span> @endif
                                                        </span>
                                                        <span style="display: block; font-size: 12px; color: #555; margin-top: 3px;">
                                                            {{ __('general.book') }}: <strong>{{ $ndata['book_name'] ?? '-' }}</strong> | {{ $ndata['page_title'] ?? '-' }}
                                                        </span>
                                                        <span style="display: block; font-size: 11px; color: #888; margin-top: 2px;">
                                                            {{ __('general.fixed_by') }}: {{ $ndata['fixed_by'] ?? '-' }}
                                                        </span>
                                                        <span style="display: block; font-size: 11px; color: #888; margin-top: 2px;">
                                                            {{ __('general.fixed_at') }}: {{ $notifDate }}
                                                        </span>
                                                    </a>

                                                {{-- نوتیفیکیشن قدیمی: مشکل --}}
                                                @elseif($ntype == "App\Notifications\IssueCreatedNotication")
                                                    <a target="_blank" href="{{ route('issues.show', $ndata['issueCreated']['id']) }}" style="text-decoration: none; color: #333; display: block;" onclick="event.stopPropagation(); makeNotificationAsRead('{{ $notification->id }}');">
                                                        <span style="display: block; font-size: 13px; font-weight: 600; color: #5b9bd1;">
                                                            <i class="fa fa-bug"></i> {{ $ndata['user']['name'] ?? '-' }}
                                                            @if($isUnread) <span style="background: #5b9bd1; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 8px; margin-right: 4px;">{{ __('general.new') }}</span> @endif
                                                        </span>
                                                        <span style="display: block; font-size: 12px; color: #555; margin-top: 3px;">
                                                            {{ \Illuminate\Support\Str::limit($ndata['issueCreated']['title'] ?? '', 60, '...') }}
                                                        </span>
                                                    </a>

                                                {{-- نوتیفیکیشن قدیمی: کتاب فراغت --}}
                                                @elseif($ntype == "App\Notifications\GraduateBookCreatedNotification")
                                                    <a target="_blank" href="{{ route('graduate-book.show', $ndata['graduateBookCreated']['id']) }}" style="text-decoration: none; color: #333; display: block;" onclick="event.stopPropagation(); makeNotificationAsRead('{{ $notification->id }}');">
                                                        <span style="display: block; font-size: 13px; font-weight: 600; color: #8e44ad;">
                                                            <i class="fa fa-book"></i> {{ __('general.graduates-book') }} {{ $ndata['graduateBookCreated']['graduated_year'] ?? '' }}
                                                            @if($isUnread) <span style="background: #8e44ad; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 8px; margin-right: 4px;">{{ __('general.new') }}</span> @endif
                                                        </span>
                                                    </a>

                                                {{-- نوتیفیکیشن سایر --}}
                                                @else
                                                    <a href="{{ $nurl }}" target="_blank" style="text-decoration: none; color: #333; display: block;" onclick="event.stopPropagation(); makeNotificationAsRead('{{ $notification->id }}');">
                                                        <span style="display: block; font-size: 13px; font-weight: 600; color: #5b9bd1;">
                                                            {{ \Illuminate\Support\Str::limit($ndata['message'] ?? ($ndata['issueCreated']['title'] ?? 'اعلان'), 80, '...') }}
                                                            @if($isUnread) <span style="background: #5b9bd1; color: #fff; font-size: 9px; padding: 1px 5px; border-radius: 8px; margin-right: 4px;">{{ __('general.new') }}</span> @endif
                                                        </span>
                                                    </a>
                                                @endif

                                                <span style="display: block; font-size: 10px; color: #aaa; margin-top: 4px; text-align: left;">
                                                    {{ $notification->created_at ? \Carbon\Carbon::parse($notification->created_at)->diffForHumans() : '' }}
                                                </span>
                                            </li>
                                        @empty
                                            <li id="no_notification" style="text-align: center; padding: 25px 15px; color: #999;">
                                                <i class="icon-bell" style="font-size: 35px; display: block; margin-bottom: 8px; opacity: 0.2;"></i>
                                                {{ __('general.no_notifications') }}
                                            </li>
                                        @endforelse
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        @endif

                        <a href="{{ auth('user')->check() ? route('profile.password') : (auth('teacher')->check() ? route('teacher.profile.password') : route('student.profile.password') )  }}" class="dropdown-toggle" style="display: flex; align-items: center; height: 75px; padding: 0 15px 0 10px;">
                            <span class="username username-hide-on-mobile"> {{ Auth::user()->name }} </span>
                        </a>
                    </li>
                    <!-- END USER + NOTIFICATION -->

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
