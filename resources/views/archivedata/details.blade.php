@extends('layouts.app')

@section('content')

<div class="portlet box">
    <div class="portlet-body">
        <div class="panel-body">
            <div class="container-fluid">
   
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
                        {{-- <th>{{ trans('general.monograph_date') }}</th>
                        <th>{{ trans('general.monograph_number') }}</th>
                        <th>{{ trans('general.monograph_title') }}</th>
                        <th>{{ trans('general.monograph_doc_date') }}</th>
                        <th>{{ trans('general.monograph_doc_number') }}</th> --}}
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
                        {{-- <td>{{ $archivedata->monograph_date }}</td>
                        <td>{{ $archivedata->monograph_number }}</td>
                        <td>{{ $archivedata->monograph_title }}</td>
                        <td>{{ $archivedata->monograph_doc_date }}</td>
                        <td>{{ $archivedata->monograph_doc_number }}</td> --}}
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

              <!-- Results Table -->
                            <h2 class="table-title text-center mb-4 text-primary ">جدول باقیداری </h2>
                            <div class="results-table table-responsive">
                            <table class="table table-bordered table-striped table-hover shadow">
                                <thead>
                                    <tr>
                                        <th>سمستر</th>
                                        <th>مضمون / مونوگراف </th>
                                        <th>عنوان </th>
                                        <th>نمبر مونوگراف / مضمون</th>
                                        <th> چانس </th>
                                        <th>نمبر چانس</th>
                                        <th>ضریب کریدت چانس دوم</th>
                                        <th>تعداد کریدت</th>
                                        <th>ضریب کریدت</th>

                                        <th colspan="3"class="text-center">نتیجه سمستر قبل از دفاع</th>
                                        <th colspan="3"class="text-center">نتیجه سمستر بعد از دفاع</th>
                                        <th colspan="3"class="text-center">نتیجه هشت سمستر قبل از دفاع</th>
                                        <th colspan="3"class="text-center">نتیجه هشت سمستر بعد از دفاع</th>
                                        <th rowspan="1" class="text-center">عکس مکتوب باقیداری</th>

                                     
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($baqidaris as $index => $item)
                                        <tr>
                                            <td>
                                                    @if(isset($item->semester))
                                                        @php
                                                            $semesterNumber = str_replace('semester', '', $item->semester);
                                                            echo 'سمستر ' . $semesterNumber;
                                                        @endphp
                                                    @else
                                                        N/A
                                                    @endif
                                            </td>
                                            <td>{{ $item->subject ?? 'N/A' }}</td>
                                            <td>{{ $item->title ?? 'N/A' }}</td>
                                            <td>{{ $item->monoghraph ?? '0' }}</td>
                                             <td>{{ $item->chance ?? '0' }}</td>
                                            <td>{{ $item->chance_number ?? '0' }}</td>
                                            <td>{{ $item->zarib_chance ?? '0' }}</td>
                                            <td>{{ $item->credit ?? '0' }}</td>
                                            <td>{{ $item->zarib_credite ?? '0' }}</td>
                                            <th>مجموعه کریدت</th>
                                            <th>مجموع نمرات</th>
                                            <th>فیصدی سمستر</th>

                                            <th>مجموعه کریدت</th>
                                            <th>مجموع نمرات</th>
                                            <th>فیصدی سمستر</th>

                                            <th>مجموعه کریدت</th>
                                            <th>مجموع نمرات</th>
                                            <th> اوسط هشت سمستر</th>

                                            <th>مجموعه کریدت</th>
                                            <th>مجموع نمرات</th>
                                            <th> اوسط هشت سمستر</th>
                                            <th> </th> 
 
                                        </tr>
                                                <tr>
                                            <th>مجموع </th>
                                            <td></td>
                                            <td></td>
                                            <td>{{ $item->monoghraph ?? '0' }}</td>
                                            <td></td>
                                            <td>{{ $item->chance_number ?? '0' }}</td>
                                            <td>{{ $item->zarib_chance ?? '0' }}</td>
                                            <td>{{ $item->credit ?? '0' }}</td>
                                            <td>{{ $item->zarib_credite ?? '0' }}</td>
                                            <td>{{ $item->total_credit ?? '0' }}</td>
                                            <td>{{ $item->total_numbers ?? '0' }}</td>
                                            <td>{{ $item->semester_percentage ?? '0' }}</td>
                                            <td>{{ $item->total_credit2 ?? '0' }}</td>
                                            <td>{{ $item->total_numbers2 ?? '0' }}</td>
                                            <td>{{ $item->semester_percentage2 ?? '0' }}</td>
                                            <td>{{ $item->total_credit3 ?? '0' }}</td>
                                            <td>{{ $item->total_numbers3 ?? '0' }}</td>
                                            <td>{{ $item->eight_semester_percentage3 ?? '0' }}</td>
                                            <td>{{ $item->total_credit4 ?? '0' }}</td>
                                            <td>{{ $item->total_numbers4 ?? '0' }}</td>
                                            <td>{{ $item->eight_semester_percentage4 ?? '0' }}</td> 
                                            <td class="text-center">
                                            @if ($item->baqidari_img)
                                                <a href="{{ asset($item->baqidari_img) }}" 
                                                target="_blank"
                                                class="btn btn-sm btn-outline-primary"
                                                data-toggle="tooltip" 
                                                title="{{ trans('general.view_full_size') }}">
                                                     {{ trans('general.view') }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td> 
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="no-data">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            </div>


                                  <h2 class="table-title text-center mb-4 text-primary ">جدول ضمایم </h2>


                             @if($zamayems->isEmpty())
                        <div class="alert alert-info">هنوز تصویری آپلود نشده است.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">تصویر</th>
                                        <th width="25%">عنوان</th>
                                        <th width="20%">تاریخ آپلود</th>
                                        <th width="15%">عملیات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($zamayems as $index => $zamayem)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if ($zamayem->zamayem_img)
                                                    <a href="{{ asset($zamayem->zamayem_img) }}" target="_blank">
                                                        <img src="{{ asset($zamayem->zamayem_img) }}" class="img-thumbnail" width="100" alt="ضمیمه">
                                                    </a>
                                                @else
                                                    <span class="text-muted">بدون تصویر</span>
                                                @endif
                                            </td>
                                            <td>{{ $zamayem->title ?? 'بدون عنوان' }}</td>
                                            <td>{{ \Morilog\Jalali\Jalalian::fromDateTime($zamayem->created_at)->format('Y/m/d H:i') }}</td>
                                            <td>
                                                <form action="{{ route('archive_zamayem.destroy', $zamayem->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('آیا از حذف این تصویر مطمئن هستید؟')">
                                                        حذف
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                            {{-- <h2 class="table-title text-center mb-4 text-primary ">جدول مونوگراف </h2> --}}

                        {{-- <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover shadow">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>{{ trans('general.monograph_date') }}</th>
                                        <th>{{ trans('general.monograph_number') }}</th>
                                        <th>{{ trans('general.monograph_title') }}</th>
                                        <th>{{ trans('general.monograph_doc_date') }}</th>
                                        <th>{{ trans('general.monograph_doc_number') }}</th>
                                        <th>{{ trans('general.monograph_desc') }}</th>
                                        <th class="text-center">{{ trans('general.monograph_img') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $archive->monograph_date }}</td>
                                        <td>{{ $archive->monograph_number }}</td>
                                        <td>{{ $archive->monograph_title }}</td>
                                        <td>{{ $archive->monograph_doc_date }}</td>
                                        <td>{{ $archive->monograph_doc_number }}</td>
                                        <td>{{ $archive->monograph_desc ?: 'N/A' }}</td>
                                        <td class="text-center">
                                            @if ($archive->monograph_img)
                                                <a href="{{ asset($archive->monograph_img) }}" 
                                                target="_blank"
                                                class="btn btn-sm btn-outline-primary"
                                                data-toggle="tooltip" 
                                                title="{{ trans('general.view_full_size') }}">
                                                     {{ trans('general.view') }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        </div> --}}



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