@extends('layouts.app')

@section('content')


    <div class="portlet box shadow-sm">
        <div class="portlet-body p-4">
            <div class="text-left mb-4">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="actionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-folder-open"></i> بخش اسناد
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionDropdown">
                        <!-- Dropdown Items -->
                        @if (auth()->user()->can('archive_doc_type'))
                            <a class="dropdown-item" href="{{ route('archive_doc_type', $archivedata) }}" target="_blank" style="font-size: 20px;">
                                <i class="fa fa-file text-primary mr-2"></i> {{ trans('general.archive_doc_type') }}
                            </a>
                        @endif

                        @if (auth()->user()->can('archive_form_print'))
                            <a class="dropdown-item" href="{{ route('archive_form_print', $archivedata) }}" target="_blank" style="font-size: 20px;">
                                <i class="fa fa-folder text-success mr-2"></i> {{ trans('general.archive_form_print') }}
                            </a>
                        @endif

                        @if (auth()->user()->can('archive_monograph'))
                            <a class="dropdown-item" href="{{ route('archive_monograph', $archivedata) }}" target="_blank" style="font-size: 20px;">
                                <i class="fa fa-pencil text-warning mr-2"></i> {{ trans('general.archive_monograph') }}
                            </a>
                        @endif

                        @if (auth()->user()->can('print-archivedoc'))
{{--                            <a class="dropdown-item" href="{{ route('print-archivedoc', $archivedata) }}" target="_blank" style="font-size: 20px;">--}}
{{--                                <i class="fa fa-download text-info mr-2"></i> {{ trans('general.archivedoc-pa') }}--}}
{{--                            </a>--}}
                            <a class="dropdown-item" href="{{ route('print-archivedocf', $archivedata) }}" target="_blank" style="font-size: 20px;">
                                <i class="fa fa-download text-info mr-2"></i> {{ trans('general.archivedoc-f') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('print-archivedestalam', $archivedata) }}" target="_blank" style="font-size: 20px;">
                                <i class="fa fa-download text-info mr-2"></i> {{ trans('general.archivedestalam') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('print-archivedestalam2', $archivedata) }}" target="_blank" style="font-size: 20px;">
                                <i class="fa fa-download text-info mr-2"></i> {{ trans('general.archivedestalam2') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
            <br>
            <hr>
            <!-- Archive Data List Section -->
            <h2 class="text-center mb-4 text-primary">{{ trans('general.archivedata_list') }}</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover shadow">
                    <thead class="thead-dark">
                    <tr>
                        <th>{{ trans('general.archive_id') }}</th>
                        <th>{{ trans('general.archive') }}</th>
                        <th>{{ trans('general.university') }}</th>
                        <th>{{ trans('general.faculty') }}</th>
                        <th>{{ trans('general.department') }}</th>
                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('general.last_name') }}</th>
                        <th>{{ trans('general.father_name') }}</th>
                        <th>{{ trans('general.grandfather_name') }}</th>
                        <th>{{ trans('general.school_name') }}</th>
                        <th>{{ trans('general.school_graduation_year') }}</th>
                        <th>{{ trans('general.tazkira_number') }}</th>
                        <th>{{ trans('general.birth_date') }}</th>
                        <th>{{ trans('general.birth_place') }}</th>
                        <th>{{ trans('general.transfer_year') }}</th>
                        <th>{{ trans('general.leave_year') }}</th>
                        <th>{{ trans('general.failled_year') }}</th>
                        <th>{{ trans('general.monograph_date') }}</th>
                        <th>{{ trans('general.monograph_number') }}</th>
                        <th>{{ trans('general.monograph_title') }}</th>
                        <th>{{ trans('general.monograph_doc_date') }}</th>
                        <th>{{ trans('general.monograph_doc_number') }}</th>
                        <th>{{ trans('general.time') }}</th>
                        <th>{{ trans('general.kankor_id') }}</th>
                        <th>{{ trans('general.half_year') }}</th>
                        <th>{{ trans('general.year_of_inclusion') }}</th>
                        <th>{{ trans('general.graduated_year') }}</th>
                        <th>{{ trans('general.averageOfScores') }}</th>
                        <th>{{ trans('general.grade') }}</th>
                        <th>{{ trans('general.status') }}</th>
                        <th>{{ trans('general.accept_or_refuse') }}</th>
                        <th>{{ trans('general.description') }}</th>
                        <th>{{ trans('general.column_number') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $archivedata->archive_id }}</td>
                        <td>
                            @if($archives->count() > 0)
                                {{ $archives->first() }}
                            @else
                                No book found
                            @endif
                        </td>
                        <td>{{ $archivedata->university->name ?? 'N/A' }}</td>
                        <td>{{ $archivedata->faculty->name ?? 'N/A' }}</td>
                        <td>{{ $archivedata->department->name ?? 'N/A' }}</td>
                        <td>{{ $archivedata->name }}</td>
                        <td>{{ $archivedata->last_name }}</td>
                        <td>{{ $archivedata->father_name }}</td>
                        <td>{{ $archivedata->grandfather_name }}</td>
                        <td>{{ $archivedata->school }}</td>
                        <td>{{ $archivedata->school_graduation_year }}</td>
                        <td>{{ $archivedata->tazkira_number }}</td>
                        <td>{{ $archivedata->birth_date }}</td>
                        <td>{{ $archivedata->birth_place }}</td>
                        <td>{{ $archivedata->transfer_year }}</td>
                        <td>{{ $archivedata->leave_year }}</td>
                        <td>{{ $archivedata->failled_year }}</td>
                        <td>{{ $archivedata->monograph_date }}</td>
                        <td>{{ $archivedata->monograph_number }}</td>
                        <td>{{ $archivedata->monograph_title }}</td>
                        <td>{{ $archivedata->monograph_doc_date }}</td>
                        <td>{{ $archivedata->monograph_doc_number }}</td>
                        <td>{{ $archivedata->time }}</td>
                        <td>{{ $archivedata->kankor_id }}</td>
                        <td>{{ $semester_types[$archivedata->semester_type_id] ?? 'N/A' }}</td>
                        <td>{{ $archivedata->year_of_inclusion }}</td>
                        <td>{{ $archivedata->graduated_year }}</td>
                        <td>{{ $archivedata->averageOfScores }}</td>
                        <td>{{ $grades[$archivedata->grade_id] ?? 'N/A' }}</td>
                        <td>{{ $archivedatastatus[$archivedata->status_id] ?? 'N/A' }}</td>
                        <td>{{ $archiveqcstatus[$archivedata->qc_status_id] ?? 'N/A' }}</td>
                        <td>{{ $archivedata->description }}</td>
                        <td>{{ $archivedata->column_number }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Image Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title text-center">تصویر</h3>
                </div>
                <div class="card-body text-center">
                    @if ($archiveimage)
                        <img src="{{ asset($archiveimage->path) }}" class="img-fluid shadow" style="max-height: 800px; border: 4px solid #ddd;" alt="Archive Image">
                    @else
                        <p class="text-muted">No image available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection