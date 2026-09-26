<ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">

    @if (auth('user')->check())
        @php
            $isArchiveReport = request()->is('archive_report*')
                || request()->is('archivebook_report*')
                || request()->is('archive-user-activity-report*');

            $isArchive = request()->is('archive')
                || request()->is('archive/create')
                || request()->is('archive/edit*')
                || request()->is('archive/show*')
                || request()->is('archive/view*')
                || request()->is('archivedatas*')
                || request()->is('archiveBookDataEntry*')
                || request()->is('archiveLoadPage*')
                || request()->is('download-archive-template')
                || request()->is('import/*')
                || request()->is('print-*')
                || request()->is('archivedata_detials*')
                || request()->is('archive_doc_type*')
                || request()->is('archive_form_print*')
                || request()->is('archive_monograph*')
                || request()->is('archiveimage*')
                || request()->is('update-name');

            $isArchiveUser = request()->is('archiveuser*') || request()->is('archiverole*');
            $isArchiveQc = request()->is('archiveqc') || request()->is('archiveqcheck*');
            $isArchiveData = request()->is('archivedata') || request()->is('archivedata/*');
            $isUsersSection = request()->is('users*')
                || request()->is('roles*')
                || request()->is('permissions*')
                || request()->is('all-logs*');
        @endphp
        <li class="nav-item start {{ request()->is('noticeboard') ? 'active' : '' }}"></li>

        @can(['view-announcement'])
            <li class="nav-item start {{ request()->is('announcements') ? 'active' : '' }}"></li>
        @endcan

        <li class="nav-item start {{ request()->is('home') ? 'active' : '' }}"></li>

        <li class="nav-item start {{ request()->is('home*') || request()->is('statistics*') ? 'active' : '' }}">
            <ul class="sub-menu"></ul>
        </li>

        <li class="nav-item start {{ request()->is('issues*') ? 'active' : '' }}"></li>

        <li class="nav-item start {{ request()->is('kankor_results*') ? 'active' : '' }}">
            <ul class="sub-menu"></ul>
        </li>

        <!-- Archive Report Menu -->
        @if (auth()->user()->can(['archive_report']))
        <li class="nav-item start {{ $isArchiveReport ? 'active' : '' }}">
            <a href="#" class="nav-link nav-toggle">
                <i class="icon-bar-chart"></i>
                <span class="title">{{ trans('general.archive_report') }}</span>
                <span class="arrow {{ $isArchiveReport ? 'open' : '' }}"></span>
            </a>
            <ul class="sub-menu">
                @if (auth()->user()->can(['archive_report']))
                <li class="nav-item {{ request()->is('archive_report') ? 'active' : '' }}">
                    <a href="{{ route('archive_report') }}" class="nav-link">
                        <i class="icon-speedometer"></i>
                        <span class="title">{{ trans('general.dashboard') }}</span>
                    </a>
                </li>
                @endif

                @if (auth()->user()->can(['archive_report']))
                    <li class="nav-item {{ request()->is('archive_report2') ? 'active' : '' }}">
                        <a href="{{ route('archive_report2') }}" class="nav-link ">
                            <i class="icon-graph"></i>
                            <span class="title">{{ trans('general.generate_statistics_simple') }}</span>
                        </a>
                    </li>
                @endif

                @if (auth()->user()->can(['archive_report']))
                    <li class="nav-item {{ request()->is('archive_report3') ? 'active' : '' }}">
                        <a href="{{ route('archive_report3') }}" class="nav-link ">
                            <i class="icon-docs"></i>
                            <span class="title">{{ trans('general.generate_statistics_doc') }}</span>
                        </a>
                    </li>
                @endif

                 @if (auth()->user()->can(['archivebook_report']))
                    <li class="nav-item {{ request()->is('archivebook_report*') ? 'active' : '' }}">
                        <a href="{{ route('archivebook_report') }}" class="nav-link ">
                            <i class="icon-book-open"></i>
                            <span class="title">{{ trans('general.archivebook_report') }}</span>
                        </a>
                    </li>
                @endif
                    @if (auth()->user()->can(['archiveuser_activity_report']))
                    <li class="nav-item {{ request()->is('archive-user-activity-report*') ? 'active' : '' }}">
                        <a href="{{ route('archiveuser_activity_report') }}" class="nav-link ">
                            <i class="icon-user-follow"></i>
                            <span class="title">{{ trans('general.archiveuser_report') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
        @endif
        <!-- Archive Section -->
        @if (auth()->user()->can(['view-archive']))
            <li class="nav-item start {{ $isArchive ? 'active' : '' }}">
                <a href="#" class="nav-link nav-toggle">
                    <i class="icon-folder"></i>
                    <span class="title">{{ trans('general.archive') }}</span>
                    <span class="arrow {{ $isArchive ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ request()->is('archive') ? 'active' : '' }}">
                        <a href="{{ route('archive.index') }}" class="nav-link">
                            <i class="icon-list"></i>
                            <span class="title">{{ trans('general.archive_list') }}</span>
                        </a>
                    </li>
                    @if (auth()->user()->can(['create-archive']))
                        <li class="nav-item {{ request()->is('archive/create') ? 'active' : '' }}">
                            <a href="{{ route('archive.create') }}" class="nav-link">
                                <i class="icon-plus"></i>
                                <span class="title">{{ trans('general.create_archive') }}</span>
                            </a>
                        </li>
                    @endif

{{--                    @if (auth()->user()->can(['view-archiveimage']))--}}
{{--                    <li class="nav-item {{ request()->is('archiveimage') ? 'active' : '' }}">--}}
{{--                        <a href="{{ route('archiveimage.index') }}" class="nav-link">--}}
{{--                            <i class="icon-list"></i>--}}
{{--                            <span class="title">{{ trans('general.archiveimage_list') }}</span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                    @endif--}}
{{--                    @if (auth()->user()->can(['create-archive']))--}}
{{--                        <li class="nav-item {{ request()->is('archiveimage/create') ? 'active' : '' }}">--}}
{{--                            <a href="{{ route('archiveimage.create') }}" class="nav-link">--}}
{{--                                <i class="icon-plus"></i>--}}
{{--                                <span class="title">{{ trans('general.create_archiveimage') }}</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endif--}}




                {{-- @if (auth()->user()->can(['archivedata']))
                        <li class="nav-item {{ request()->is('archivedatas') ? 'active' : '' }}">
                            <a href="{{ route('archivedatas') }}" class="nav-link">
                                <i class="icon-list"></i>
                                <span class="title">{{ trans('general.archivesearch') }}</span>
                            </a>
                        </li>
                    @endif --}}



                </ul>
            </li>
        @endif

        @if (auth()->user()->can(['view-archivedata']))
            <li class="nav-item {{ $isArchiveData ? 'active' : '' }}">
                <a href="{{ route('archivedata.index') }}" class="nav-link ">
                    <i class="icon-doc"></i>
                    <span class="title">{{ trans('general.archivedata_list') }}</span>
                </a>
            </li>

        @endif



    <!-- New Archive Permissions -->
        @if (auth()->user()->can(['view-archiveqc']))
            <li class="nav-item {{ $isArchiveQc ? 'active' : '' }}">
                <a href="{{ route('archiveqc') }}" class="nav-link">
                    <i class="icon-shield"></i>
                    <span class="title">{{ trans('general.archiveqc') }}</span>
                </a>
            </li>
        @endif



        @if (auth()->user()->can(['view-archiveuser']))
            <li class="nav-item {{ $isArchiveUser ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-user"></i>
                    <span class="title">{{ trans('general.archiveuser') }}</span>
                    <span class="arrow {{ $isArchiveUser ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ request()->is('archiveuser*') ? 'active' : '' }}">
                        <a href="{{ route('archiveuser.index') }}" class="nav-link">
                            <i class="icon-users"></i>
                            <span class="title">{{ trans('general.archiveuserlist') }}</span>
                        </a>
                    </li>

                    @if (auth()->user()->can(['view-archiverole']))
                        <li class="nav-item {{ request()->is('archiverole*') ? 'active' : '' }}">
                            <a href="{{ route('archiverole.index') }}" class="nav-link">
                                <i class="icon-badge"></i>
                                <span class="title">{{ trans('general.roles_list') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif

        @if (auth()->user()->can(['archive_doc_form_manage']))
            <li class="nav-item {{ request()->is('archive_doc_form*') ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-docs"></i>
                    <span class="title">{{ trans('general.archive_doc_form_manage') }}</span>
                    <span class="arrow {{ request()->is('archive_doc_form*') ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item {{ request()->is('archive_doc_form/create') ? 'active' : '' }}">
                        <a href="{{ route('archive_doc_form.create') }}" class="nav-link">
                            <i class="icon-pencil"></i>
                            <span class="title">{{ trans('general.archive_doc_form') }}</span>
                        </a>
                    </li>

{{--                    @if (auth()->user()->can(['archive_form_print']))--}}
{{--                        <li class="nav-item {{ request()->is('archive_doc_form') ? 'active' : '' }}">--}}
{{--                            <a href="{{ route('archive_doc_form.index') }}" class="nav-link">--}}
{{--                                <i class="icon-users"></i>--}}
{{--                                <span class="title">{{ trans('general.archive_form_print') }}</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    @endif--}}
                </ul>
            </li>
        @endif


    <!-- Users, Roles, and Permissions -->
        @if (auth()->user()->hasAnyPermission(['view-user', 'view-role']))
            <li class="nav-item start {{ $isUsersSection ? 'active' : '' }}">
                <a href="javascript:;" class="nav-link nav-toggle">
                    <i class="icon-users"></i>
                    <span class="title">{{ trans('general.users') }}</span>
                    <span class="arrow {{ $isUsersSection ? 'open' : '' }}"></span>
                </a>
                <ul class="sub-menu">
                    @if (auth()->user()->can(['view-user']))
                        <li class="nav-item {{ request()->is('users') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}" class="nav-link">
                                <i class="icon-user"></i>
                                <span class="title">{{ trans('general.users_list') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->can(['create-user']))
                        <li class="nav-item {{ request()->is('users/create') ? 'active' : '' }}">
                            <a href="{{ route('users.create') }}" class="nav-link">
                                <i class="icon-user-follow"></i>
                                <span class="title">{{ trans('general.create_account') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->can(['view-role']))
                        <li class="nav-item {{ request()->is('roles') ? 'active' : '' }}">
                            <a href="{{ route('roles.index') }}" class="nav-link">
                                <i class="icon-badge"></i>
                                <span class="title">{{ trans('general.roles_list') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->can(['create-role']))
                        <li class="nav-item {{ request()->is('roles/create') ? 'active' : '' }}">
                            <a href="{{ route('roles.create') }}" class="nav-link">
                                <i class="icon-badge"></i>
                                <span class="title">{{ trans('general.create_role') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->can(['view-permission']))
                        <li class="nav-item {{ request()->is('permissions') ? 'active' : '' }}">
                            <a href="{{ route('permissions.index') }}" class="nav-link">
                                <i class="icon-key"></i>
                                <span class="title">{{ trans('general.list') }} {{ trans('models/permissions.plural') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (auth()->user()->can(['create-permission']))
                        <li class="nav-item {{ request()->is('permissions/create') ? 'active' : '' }}">
                                <a href="{{ route('permissions.create') }}" class="nav-link">
                                    <i class="icon-key"></i>
                                    <span class="title">{{ trans('crud.create') }} {{ trans('models/permissions.singular') }}</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->hasRole('system-developer'))
                        <li class="nav-item {{ request()->is('all-logs*') ? 'active' : '' }}">
                            <a href="{{ route('allLogs') }}" class="nav-link">
                                <i class="icon-clock"></i>
                                <span class="title">{{ trans('general.activities') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>


                 @if (auth()->user()->can(['update-kankor-result']))
                    <li class="nav-item start {{ request()->is('kankor_results*') ? 'active' : '' }}">
                        <a href="#" class="nav-link nav-toggle">
                            <i class="icon-grid"></i>
                            <span class="title">{{ trans('general.kankorResults') }}</span>
                            <span class="arrow {{ request()->is('kankor_results*') ? 'open' : '' }}"></span>
                        </a>
                        <ul class="sub-menu">
                            
                            {{-- @if (auth()->user()->hasRole('super-admin')|| auth()->user()->hasRole('system-developer'))
                                <li class="nav-item {{ request()->is('kankor_results/university_id') ? 'active' : '' }}">
                                    <a href="{{ route('kankor_results.show_university') }}" class="nav-link">
                                        <i class="icon-grid"></i>
                                        <span class="title">{{ trans('general.update_university_id') }}</span>
                                    </a>
                                </li>
                                <hr>
                                <li class="nav-item {{ request()->is('kankor_results/university_id_by_kankor_results') ? 'active' : '' }}">
                                    <a href="{{ route('kankor_results.show_university_by_kankor_results') }}" class="nav-link">
                                        <i class="icon-grid"></i>
                                        <span class="title">{{ trans('general.update_university_id_by_kankor_results') }}</span>
                                    </a>
                                </li>
                                <hr>
                                <li class="nav-item {{ request()->is('kankor_results/koochi/university_id_by_kankor_results') ? 'active' : '' }}">
                                    <a href="{{ route('kankor_results.show_university_by_kankor_results_koochi') }}" class="nav-link">
                                        <i class="icon-grid"></i>
                                        <span class="title">{{ trans('general.koochi_kankor_results') }}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{ request()->is('kankor_results/show-enrollment-type-form') ? 'active' : '' }}">
                                    <a href="{{ route('kankor_results.show_enrollment_type_form') }}" class="nav-link">
                                        <i class="icon-grid"></i>
                                        <span class="title">{{ trans('general.update_enrollment_type') }}</span>
                                    </a>
                                </li>
                            @endif --}}


                            @if (auth()->user()->can(['update-department-after-kankor']))
                                <li class="nav-item {{ request()->is('kankor_results/department_id') ? 'active' : '' }}">
                                    <a href="{{ route('kankor_results.show_department') }}" class="nav-link">
                                        <i class="icon-grid"></i>
                                        <span class="title">{{ trans('general.update_department_id') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                 @endif


                  


                            @if (auth()->user()->can('update-name'))
                                <li class="nav-item {{ request()->is('update-name') ? 'active' : '' }}">
                                    <a href="{{ route('archivedata.select-for-update') }}" class="nav-link">
                                        <i class="icon-pencil"></i>
                                        <span class="title">{{ trans('general.update-name') }}</span>
                                    </a>
                                </li>
                            @endif
                  



            @if (auth()->user()->can(['view-university']))
                <li class="nav-item start {{ request()->is('universities*') || request()->is('departmentType*') || request()->is('universities/departments/merge*') ? 'active' : '' }}">
                    <a href="#" class="nav-link nav-toggle">
                        <i class="icon-graduation"></i>
                        <span class="title">{{ trans('general.universities') }}</span>
                        <span class="arrow {{ request()->is('universities*') ? 'open' : '' }}"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item {{ request()->is('universities') ? 'active' : '' }}">
                            <a href="{{ route('universities.index') }}" class="nav-link">
                                <i class="icon-globe"></i>
                                <span class="title">{{ trans('general.universities_list') }}</span>
                            </a>
                        </li>

                        @if (auth()->user()->can(['create-university']))
                            <li class="nav-item {{ request()->is('universities/create') ? 'active' : '' }}">
                                <a href="{{ route('universities.create') }}" class="nav-link">
                                    <i class="icon-plus"></i>
                                    <span class="title">{{ trans('general.create_university') }}</span>
                                </a>
                            </li>
                        @endif


                    </ul>
                </li>
        @endif





    @endif
@endif

<!-- Teacher and Student sections would go here... -->

</ul>
