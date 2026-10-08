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
                            @php
                                // فقط اعلان‌های خوانده‌نشده در لیست نمایش داده می‌شوند
                                $unreadCount = auth()->user()->unreadNotifications()->count();
                                $notificationList = auth()->user()->unreadNotifications()->latest()->take(50)->get();

                                // نگاشت کتاب‌ها برای تشخیص «کاربر مربوط» (درج‌کننده / کنترول‌کننده)
                                $archiveMap = [];
                                $archiveIds = $notificationList->pluck('data')
                                    ->map(function ($d) { return isset($d['archive_id']) ? $d['archive_id'] : null; })
                                    ->filter()->unique()->values()->all();

                                if (count($archiveIds)) {
                                    foreach (\DB::table('archives')->whereIn('id', $archiveIds)->get(['id', 'de_user_id', 'qc_user_id']) as $row) {
                                        $archiveMap[$row->id] = $row;
                                    }
                                }
                            @endphp
                            <a href="javascript:;" class="notification-bell dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" style="text-decoration: none; color: #555; font-size: 18px; position: relative; cursor: pointer; padding: 8px; line-height: 1; display: flex; align-items: center;">
                                <i class="icon-bell"></i>
                                @if($unreadCount > 0)
                                    <span class="js-notification-badge badge badge-danger" style="position: absolute; top: 2px; right: 0; font-size: 10px; padding: 1px 5px; border-radius: 10px; background: #e74c3c; color: #fff; min-width: 16px; text-align: center;">{{ $unreadCount }}</span>
                                @else
                                    <span class="js-notification-badge badge badge-danger" style="display: none; position: absolute; top: 2px; right: 0; font-size: 10px; padding: 1px 5px; border-radius: 10px; background: #e74c3c; color: #fff; min-width: 16px; text-align: center;">0</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu" style="min-width: 420px; max-height: 500px; overflow-y: auto; left: 0; right: auto; top: 100%; bottom: auto; margin-top: 0; padding: 0; position: absolute; z-index: 9999;">
                                <li class="external" style="padding: 12px 15px; background: #f8f9fa; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 10;">
                                    <h5 style="margin: 0; font-weight: 600; font-size: 14px;">
                                        <i class="icon-bell"></i> {{ __('general.notifications') }}
                                        <span class="js-notification-badge badge badge-danger" style="{{ $unreadCount > 0 ? '' : 'display: none;' }}background: #e74c3c;">{{ $unreadCount }}</span>
                                    </h5>
                                </li>
                                <li style="max-height: 430px; overflow-y: auto; padding: 0; margin: 0;">
                                    <ul id="notification-list" data-empty-text="{{ __('general.no_notifications') }}" style="padding: 0; margin: 0; list-style: none;">
                                        <div id="notification-live" style="margin-bottom: 2px !important;"></div>
                                        @forelse($notificationList as $notification)
                                            @php
                                                $ndata = $notification->data;
                                                $ntype = $notification->type;
                                                $action = $ndata['action'] ?? '';
                                                $isUnread = $notification->read_at == null;
                                                $nurl = $ndata['url'] ?? '#';
                                                // لینک بعضی اعلان‌ها (مشکلات / کتاب فراغت) در data نیست و باید ساخته شود
                                                if ($action === '' && $ntype === 'App\Notifications\IssueCreatedNotication' && isset($ndata['issueCreated']['id'])) {
                                                    $nurl = route('issues.show', $ndata['issueCreated']['id']);
                                                } elseif ($action === '' && $ntype === 'App\Notifications\GraduateBookCreatedNotification' && isset($ndata['graduateBookCreated']['id']) && Route::has('graduate-book.show')) {
                                                    $nurl = route('graduate-book.show', $ndata['graduateBookCreated']['id']);
                                                }
                                                $notifDate = '';
                                                if ($notification->created_at) {
                                                    $nc = \Carbon\Carbon::parse($notification->created_at);
                                                    $njParts = \Morilog\Jalali\CalendarUtils::toJalali($nc->year, $nc->month, $nc->day);
                                                    $notifDate = implode('/', $njParts) . ' ' . $nc->format('H:i');
                                                }

                                                // فقط کاربرِ مربوط (درج‌کننده / کنترول‌کننده) اجازه باز کردن لینک را دارد
                                                $canOpen = true;
                                                $archiveRow = isset($ndata['archive_id']) ? ($archiveMap[$ndata['archive_id']] ?? null) : null;

                                                if ($action === 'page_rejected') {
                                                    $canOpen = $archiveRow
                                                        && $archiveRow->de_user_id
                                                        && (int) $archiveRow->de_user_id === (int) auth()->id();
                                                } elseif ($action === 'page_fixed') {
                                                    $canOpen = $archiveRow
                                                        && $archiveRow->qc_user_id
                                                        && (int) $archiveRow->qc_user_id === (int) auth()->id();
                                                } elseif ($ntype === 'App\Notifications\GraduateBookCreatedNotification' && !Route::has('graduate-book.show')) {
                                                    $canOpen = false;
                                                }
                                            @endphp
                                            <li id="notif-{{ $notification->id }}" class="notif-item"
                                                style="padding: 10px 15px; border-bottom: 1px solid #f0f0f0; cursor: pointer; background-color: #FFF8E1;"
                                                onclick="makeNotificationAsRead('{{ $notification->id }}', this)">

                                                @if($canOpen)
                                                    <a href="{{ $nurl }}" target="_blank" style="text-decoration: none; color: #333; display: block;"
                                                       onclick="event.stopPropagation(); makeNotificationAsRead('{{ $notification->id }}', this);">
                                                        @include('layouts.notification-body')
                                                    </a>
                                                @else
                                                    <div style="display: block;">
                                                        @include('layouts.notification-body')
                                                        <span style="display: inline-block; margin-top: 6px; font-size: 10px; color: #856404; background: #fff3cd; padding: 2px 8px; border-radius: 8px;">
                                                            <i class="fa fa-lock"></i> فقط برای کاربر مربوط — کلیک برای بستن
                                                        </span>
                                                    </div>
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
