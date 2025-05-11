@extends('layouts.app')

@section('content')

    <div class="portlet box">
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            <div class="panel-body">

                <div class="col-md-6">
                    @if($archive)
{{--                    <!-- Navigation Buttons -->--}}
                    <a href="{{ route('archiveqcheckdata', ['archive_id' => $archive->id, 'image_archive_id' =>(  $archiveqcheckimage->id-1)]) }}"
                        class="btn btn-primary">
                        صفحه قبلی
                    </a>

                    <a href="{{ route('archiveqcheckdata', ['archive_id' =>  $archive->id, 'image_archive_id' => ( $archiveqcheckimage->id+1)]) }}"
                        class="btn btn-primary">
                        صفحه بعدی
                    </a>
                    @endif
                    &nbsp;&nbsp;&nbsp; |
                        <strong>حالت این صفحه:</strong>

                            @if ($archiveqcheckimage->qc_status_id == 1)
                                <span class="badge badge-primary m-1 p-1">حالت اولی</span>
                            @elseif ($archiveqcheckimage->qc_status_id == 3)
                                <span class="badge badge-success m-1 p-1">تایید</span>
                            @elseif ($archiveqcheckimage->qc_status_id == 4)
                                <span class="badge badge-danger m-1 p-1">رد</span>
                            @endif

                    &nbsp;&nbsp;&nbsp; |
                <span>صفحه {{ $archiveqcheckimage->book_pagenumber}} از کل {{$totalPages}} صفحه</span>

                </div>
                    <br/>
                </div>


                <!-- Check if image status is '2' -->
                @if($archiveqcheckimage->status_id == 2)
                    <div class="col-sm-2">
                        <td style="padding: 10px; white-space: nowrap" >
                            {!! Form::open(['route' => 'archiveqcheckImageSetStatus', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
                            {!! Form::text('recordId', $archiveqcheckimage->id, ['style' => 'display:none']) !!}
                            {!! Form::select('approvalStatus', $archiveStatusList, $archiveqcheckimage->qc_status_id, [
                                'class' => 'form-control',
                                'placeholder' => trans('general.select')
                            ]) !!}
                            <button type="submit" class="btn btn-success">{{ trans('general.save') }}</button>
                            {!! Form::close() !!}
                        </td>
                    </div>
                @else
                <!-- Display archive data -->
                    <h2>{{ trans('general.archivedata_list') }}</h2>
                    <div style="overflow-x:auto;">
                        <table class="table table-info table-striped table-hover table-responsive">
                            <thead>
                            <tr class="info">
                                <th>{{ trans('general.name') }}</th>
                                <th>{{ trans('general.last_name') }}</th>
                                <th>{{ trans('general.father_name') }}</th>
                                <th>{{ trans('general.grandfather_name') }}</th>
                                <th>{{ trans('general.school_name') }}</th>
                                <th>{{ trans('general.school_graduation_year') }}</th>
                                <th>{{ trans('general.tazkira_number') }}</th>
                                <th>{{ trans('general.birth_date') }}</th>
                                <th>{{ trans('general.birth_place') }}</th>
                                <th>{{ trans('general.time') }}</th>
                                <th>{{ trans('general.kankor_id') }}</th>
                                <th>{{ trans('general.half_year') }}</th>
                                <th>{{ trans('general.year_of_inclusion') }}</th>
                                <th>{{ trans('general.graduated_year') }}</th>
                                <th>{{ trans('general.transfer_year') }}</th>
                                <th>{{ trans('general.leave_year') }}</th>
                                <th>{{ trans('general.failled_year') }}</th>
                                <th>{{ trans('general.monograph_date') }}</th>
                                <th>{{ trans('general.monograph_number') }}</th>
                                <th>{{ trans('general.monograph_title') }}</th>
                                <th>{{ trans('general.averageOfScores') }}</th>
                                <th>{{ trans('general.grade') }}</th>
                                <th>{{ trans('general.description') }}</th>
                                <th>{{ trans('general.status') }}</th>
                                <th>{{ trans('general.accept_or_refuse') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($archiveqcheckdata as $record)
                                <tr>
                                    <td>{{ $record->name }}</td>
                                    <td>{{ $record->last_name }}</td>
                                    <td>{{ $record->father_name }}</td>
                                    <td>{{ $record->grandfather_name }}</td>
                                    <td>{{ $record->school }}</td>
                                    <td>{{ $record->school_graduation_year }}</td>
                                    <td>{{ $record->tazkira_number }}</td>
                                    <td>{{ $record->birth_date }}</td>
                                    <td>{{ $record->birth_place }}</td>
                                    <td>{{ $record->time }}</td>
                                    <td>{{ $record->kankor_id }}</td>
{{--                                    <td>{{ $record->semester_type->name }}</td>--}}
                                    <td>{{$record->semester_type ? $record->semester_type->name : ''}}</td>
                                    <td>{{ $record->year_of_inclusion }}</td>
                                    <td>{{ $record->graduated_year }}</td>
                                    <td>{{ $record->transfer_year }}</td>
                                    <td>{{ $record->leave_year }}</td>
                                    <td>{{ $record->failled_year }}</td>
                                    <td>{{ $record->monograph_date }}</td>
                                    <td>{{ $record->monograph_number }}</td>
                                    <td>{{ $record->monograph_title }}</td>
                                    <td>{{ $record->averageOfScores }}</td>
{{--                                    <td>{{ $record->grade->name }}</td>--}}
                                    <td>{{$record->grade ? $record->grade->name : ''}}</td>

                                    <td>{{ $record->description }}</td>

                                    <!-- Status Badge Display -->
                                    <td style="padding: 10px; white-space: nowrap;">
                                        @if ($record->qc_status_id == 3)
                                            <span class="badge badge-success m-1 p-1">{{ $record->qc_status }}</span>
                                        @elseif ($record->qc_status_id == 4)
                                            <span class="badge badge-danger m-1 p-1">{{ $record->qc_status }}</span>
                                        @else
                                            <span class="badge badge-secondary m-1 p-1">{{ $record->qc_status }}</span>
                                        @endif
                                    </td>

                                    <!-- Approval Status Dropdown -->
                                    <td style="padding: 10px; white-space: nowrap">
                                        {!! Form::open(['route' => 'archiveqcheckdataSetStatus', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
                                        {!! Form::text('recordId', $record->id, ['style' => 'display:none']) !!}
                                        {!! Form::select('approvalStatus', $archiveStatusList, $record->qc_status_id, []) !!}
                                        <button type="submit" class="btn btn-success">{{ trans('general.save') }}</button>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Display Image if available -->
            @if($archiveqcheckimage && !is_bool($archiveqcheckimage))
                <img src="{{ asset($archiveqcheckimage->path) }}" alt="Image" style="width: 100%; height: 800px;">
            @else
                <p>عکس کتاب موجود نیست</p>
            @endif

        </div>
    </div>

@endsection
