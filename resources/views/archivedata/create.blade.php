@extends('layouts.app')

@section('content')
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

<style>
    /* Custom CSS for Required Field Asterisk */
    .control-label.required::after {
    content: " *";
    color: red;
    font-weight: bold;
    }
</style>
    <div class="portlet box">
        <div class="portlet-body">

            <!-- Table for Book Information -->
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-primary">
                <tr>
                    <th style="text-align: center;">نام کتاب</th>
                    <th style="text-align: center;">پوهنتون</th>
                    <th style="text-align: center;">صفحه کتاب</th>
                </tr>
                </thead>
                <tbody>
                <tr>


                    <td style="text-align: center;">{{ $archiveRecord->book_name }}</td>
                    <td style="text-align: center;">{{ $archiveRecord->university->name ?? 'N/A' }}</td>
                    <td style="text-align: center;">صفحه {{ $archiveImage->book_pagenumber }} از کل {{ $totalPages }} صفحه</td>
                </tr>
                </tbody>
            </table>

            <!-- Table for Faculty and Department -->
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-primary">
                <tr>
                    <th style="text-align: center;">پوهنحی</th>
                    <th style="text-align: center;">دیپارتمنت</th>
                </tr>
                </thead>
                <tbody>
                @foreach($archivedataRecords as $record)
                    <tr>
                        <td style="text-align: center;">{{$record->faculty->name ?? 'N/A'  }}</td>
                        <td style="text-align: center;">{{ $record->department->name ?? 'N/A' }}</td>

                    </tr>
                @endforeach
                </tbody>
            </table>

            @if($archiveimagerejectqc->isEmpty())
                <div style="font-family: 'New Times Roman'; font-size: 16px; color: #333;">
                    صفحات مسترد شده: <span style="color: red;">دیتا وجود ندارد</span>
                </div>
            @else
                <div style="font-family: 'New Times Roman'; font-size: 16px; color: #333;">
                    <strong>صفحات مسترد شده:</strong>
                    <span>
            @foreach($archiveimagerejectqc as $item)
                            <span style="color: red;">{{ $item->book_pagenumber }}</span>@if(!$loop->last), @endif
                        @endforeach
        </span>
                </div>
            @endif




            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8  border border-1 border-dark" style="overflow-y: auto;margin-top: 100px">
                        <div style="background-color: #ccc;">
                            <img src="{{ asset($archiveImage->path) }}" id="img"   style="height: 600px; position: relative;" />

                        </div>
                    </div>

                    <!-- Form for searching Page Number -->
                {!! Form::open(['route' => 'archiveBookDataEntrySearch', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal col-md-4']) !!}
                    {!! Form::hidden('archiveId', $archiveRecord->id) !!}
                    <div class="form-group">
                        {!! Form::number('pageNo', '', ['class' => 'form-control', 'placeholder' => 'جستجوی صفحه']) !!}

                    <button type="submit" class="btn btn-primary">جستجو </button>
                    </div>
                {!! Form::close() !!}




                    <div class="col-md-4 " >
                        <a href="{{ route('archiveBookDataEntryPage', ['pageId' => $archiveImage->id, 'id' => $archiveImage->archive_id, 'step' => -1]) }}" target="new" class="btn btn-primary">
                            صفحه قبلی
                        </a>

                        <a href="{{ route('archiveBookDataEntryPage', ['pageId' => $archiveImage->id, 'id' => $archiveImage->archive_id, 'step' => 1]) }}" target="new" class="btn btn-primary">
                            صفحه بعدی
                        </a>

                        حالت این صفحه:

                        @if($archiveImage->status_id==1)
                            <span class="badge-primary m-1 p-1 "> حالت اولی</span>
                        @endif
                        @if($archiveImage->status_id==2)
                            <span  class="badge-danger m-1 p-1 ">صرف نظر</span>
                        @endif
                        @if($archiveImage->status_id==3)
                            <span  class="badge-warning m-1  p-1 ">نا تکمیل</span>
                        @endif
                        @if($archiveImage->status_id==4)
                            <span  class="badge-success m-1 p-1 ">تکمیل</span>
                        @endif
                        <br/>

                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                                   aria-controls="home" aria-selected="true">فاقد محصل</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                                   aria-controls="profile" aria-selected="false">شامل محصل</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade " id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <h4> نوت:ستون که شامل محصل باشد اگر آن ستون را از سیستم حذف کنید معلومات محصل نیز پاک میگردد.</h4>

                                <div style="display: flex">

                                    <h4>این صفحه شامل</h4>
                                    <h4> محصل می باشد.</h4>
                                    &ensp;  &ensp;
                                    {!! Form::open(['route' => 'archiveBookDataEntryApprove','id'=>'form-1','name'=>'form-data', 'method' => 'post','class' => 'form-horizontal']) !!}
                                    {!! Form::text('approve_page_id',$archiveImage->id, ['style' => 'display:none']) !!}
                                    {!! Form::text('approve_archive_id', $archiveImage->archive_id, ['style' => 'display:none']) !!}
                                    {!! Form::select('approve_total_student',$total_student, ['class' => 'form-control select2',
                                                   'placeholder' => trans('general.select')]) !!}

                                    <button type="submit" role="button" class="btn btn-primary ">تائید</button>
                                    {!! Form::close() !!}
                                    <div id="btn-save-total_students"></div>
                                </div>

                                    <ul class="nav nav-tabs" id="title_tab" role="tablist"></ul>
                                    <div class="tab-content" id="content_tab"></div>

                            </div>
                            <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                                    <h4>این صفحه شامل هیچ محصل نمی باشد.</h4>
                                    <h4>در صورتیکه صفحه شامل محصل باشد و آن را فاقد محصل بسازید دیتای آن  پاک میگردد. </h4>

                                    <a href="{{ route('archiveBookDataEntryPageReject', ['pageId' => $archiveImage->id, 'id' => $archiveImage->archive_id, 'step' => 1]) }}" target="new" class="btn btn-primary">تائید</a>

                            </div>
                        </div>
                        <div style="overflow-y: auto; width: 100%!important;overflow-x: hidden; height: 600px;padding-top: 10px">
                                    @for($i=1;$i<=$archiveImage->total_students;$i++)
                                        <a href="{{ route('archiveBookDataEntryPageData', ['pageId' => $archiveImage->id, 'id' => $archiveImage->archive_id, 'column' => $i]) }}" target="new" class="btn btn-primary">{{$i}}</a>
                                    @endfor

                                    @if($archiveData1 != null)
                                        @include('archivedata.showform', ['column_number' => $column_number])
                                    @endif
                                    @if($archiveData1 == null && $column_number !=null)
                                        @include('archivedata.showformModal')
                                    @endif





                        </div>
                    </div>

                </div>

            </div>



        <div class="row panel-body" >
            <div class="table-responsive" >
                <table  class="table table-info table-striped table-hover ">
                    <thead>
                    <tr class="info">

                        <th>{{ trans('general.name') }}</th>
                        <th>{{ trans('general.last_name') }}</th>
                        <th>{{ trans('general.father_name') }}</th>
                        <th>{{ trans('general.grandfather_name') }}</th>
                        <th>{{ trans('general.department') }}</th>
                        <th>{{ trans('general.school_name') }}</th>
                        <th>{{ trans('general.school_graduation_year') }}</th>
                        <th>{{ trans('general.tazkira_number') }}</th>
                        <th>{{ trans('general.birth_date') }}</th>
                        <th>{{ trans('general.birth_place') }}</th>
                        <th>{{ trans('general.time') }}</th>
                        <th>{{ trans('general.kankor_id') }}</th>
                        {{-- <th>{{ trans('general.half_year') }}</th>--}}
                        <th>{{ trans('general.year_of_inclusion') }}</th>
                        <th>{{ trans('general.graduated_year') }}</th>
                        <th>{{ trans('general.transfer_year') }}</th>
                        <th>{{ trans('general.leave_year') }}</th>
                        <th>{{ trans('general.failled_year') }}</th>
                        <th>{{ trans('general.monograph_date') }}</th>
                        <th>{{ trans('general.monograph_number') }}</th>
                        <th>{{ trans('general.monograph_title') }}</th>
                        <th>{{ trans('general.averageOfScores') }}</th>
                        {{--  <th>{{ trans('general.grade') }}</th>--}}
                        <th>{{ trans('general.description') }}</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($archivedataRecords as $record)
                        <tr>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->last_name }}</td>
                            <td>{{ $record->father_name }}</td>
                            <td>{{ $record->grandfather_name }}</td>
                            <td>{{ $record->department->name ?? 'N/A' }}</td>
                            <td>{{ $record->school }}</td>
                            <td>{{ $record->school_graduation_year }}</td>
                            <td>{{ $record->tazkira_number }}</td>
                            <td>{{ $record->birth_date }}</td>
                            <td>{{ $record->birth_place }}</td>
                            <td>{{ $record->time }}</td>
                            <td>{{ $record->kankor_id }}</td>
                            {{-- <td>{{ $record->semester_type_id }}</td>--}}
                            <td>{{ $record->year_of_inclusion }}</td>
                            <td>{{ $record->graduated_year }}</td>
                            <td>{{ $record->transfer_year }}</td>
                            <td>{{ $record->leave_year }}</td>
                            <td>{{ $record->failled_year }}</td>
                            <td>{{ $record->monograph_date }}</td>
                            <td>{{ $record->monograph_number }}</td>
                            <td>{{ $record->monograph_title }}</td>
                            <td>{{ $record->averageOfScores }}</td>
                            <td>{{ $record->description }}</td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{-- End Record of table--}}

        </div>
    </div>


@endsection('content')

    @push('styles')
        <link rel="stylesheet" href="{{ url('assets/plugins/persiandatepicker') }}/css/persianDatepicker-default.css"/>
    @endpush

    @push('scripts')
    <script src="{{ url('assets/plugins/persiandatepicker') }}/js/persianDatepicker.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $("#").persianDatepicker({
                months: ["حمل","ثور","جوزا","سرطان","اسد","سنبله","میزان","عقرب","قوس","جدی","دلو","حوت"],
                dowTitle: ["شنبه", "یکشنبه", "دوشنبه", "سه شنبه", "چهارشنبه", "پنج شنبه", "جمعه"],
                shortDowTitle: ["ش", "ی", "د", "س", "چ", "پ", "ج"],
                showGregorianDate: !1,
                persianNumbers: !0,
                formatDate: "YYYY/MM/DD",
                selectedBefore: !1,
                selectedDate: null,
                startDate: null,
                endDate: null,
                prevArrow: '\u25c4',
                nextArrow: '\u25ba',
                theme: 'default',
                alwaysShow: !1,
                selectableYears: null,
                selectableMonths: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                cellWidth: 25, // by px
                cellHeight: 25, // by px
                fontSize: 15, // by px
                isRTL: !1,
                calendarPosition: {
                    x: 0,
                    y: 0,
                },
                onShow: function () { },
                onHide: function () { },
                onSelect: function () { }
            });
        });


        function setImage(image, id, pageId, total_students, page_status) {
            document.getElementById("page_id").value = id;
            document.getElementById("archiveimage_id").value = id;
            document.getElementById("total_students").value = total_students;
            document.getElementById("page_status").value = page_status;
            console.log('page_status');

            // کود پیین نشان دهنده انتخاب نمبر صفحه است
            const elements = document.querySelectorAll('.selectPage');
            elements.forEach((element) => {
                element.classList.remove('selectPage');
            });

            var v = image.split("::");
            $("#img").attr('src', v[1]);
            // کلاس ادد نشان دهنده انتخاب نمبر صفحه است
            $("#" + pageId).addClass("selectPage");
            $(".formdata").removeClass("formdatastyle");
            $("#archive_image_id").val(v[0]);
        }


        //search page no
        function goToPage(archiveId) {
            var pageNumber = document.getElementById("pageNumber").value;

            $.ajax({
                url: '/archiveBookDataEntrySearch/'+archiveId+"/" + pageNumber, // The endpoint to your Laravel route
                method: 'GET',
                success: function(response) {
                    $('#pageDisplay').html('صفحه ' + response.book_pagenumber + ' از کل ' + response.totalPages + ' صفحه');
                    $('#result').html(response.html); // Update with the relevant data
                },
                error: function() {
                    alert('An error occurred while searching for the page.');
                }
            });
        }
    </script>

    @endpush