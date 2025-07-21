@extends('layouts.app')
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />


@section('content')

    <div class="portlet box">
        <div class="portlet-body">
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-body">
                        <!-- BEGIN FORM-->

                        <div class="form-group">
                            <label for="form-select" class="control-label">انتخاب فورم:</label>
                            <div class="d-flex">
                                <select id="form-select" name="form-select" onchange="showForm()" class="form-control w-auto ml-2">
                                    <option value="">انتخاب</option>
                                    <option value="book_form">به اساس کتاب</option>
                                    <option value="student_form">به اساس محصل</option>
                                    {{-- <option value="doc_form">به اساس اسناد</option> --}}
                                </select>
                            </div>
                        </div>
                        
                      
                        
                        <div class="student_form" style="display: none;">
                            <div class="form-group">
                                <label for="form-report_type" class="control-label">نوعیت گزارش:</label>
                                <div class="d-flex">
                                    <select id="form-report_type" name="form-report_type" onchange="showForm1()" class="form-control w-auto ml-2">
                                        <option value="">انتخاب</option>
                                        <option value="1">راپور احصائیوی</option>
                                        <option value="2">راپور باجزئیات</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 20px; padding: 20px; border: 2px solid lightgray; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                            {!! Form::open(['method' => 'post', 'route' => 'reportresult', 'class' => 'form-horizontal', 'target' => 'new']) !!}
                        <div class="form-body" id="app">
                                <br>

                                <input type="hidden" id="reporttype" name="reporttype"/>
                                <input type="hidden" id="report_type" name="report_type" value="1"/>
                                <!-- Date Selection -->
                                <div class="row">
                                    <div class="col-md-3">
                                        <h4>از تاریخ:</h4>
                                        {!! Form::select('start_archive_year_id', $archiveyears, 1, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                        @if ($errors->has('archive_year_id'))
                                            <span class="help-block">
                                               <strong>{{ $errors->first('archive_year_id') }}</strong></span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <h4>الی تاریخ:</h4>
                                        {!! Form::select('end_archive_year_id', $archiveyears, null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                        @if ($errors->has('archive_year_id'))
                                            <span class="help-block">
                                               <strong>{{ $errors->first('archive_year_id') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <br>

                                <!-- University Selection -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group {{ $errors->has('university_id') ? ' has-error' : '' }}">
                                            {!! Form::label('university_id', trans('general.university'), ['class' => 'control-label col-sm-3']) !!}
                                            <div class="col-sm-8">
                                                {!! Form::select('university_id', $universities, null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                                @if ($errors->has('university_id'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('university_id') }}</strong></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div  class="student_form" style="display: none;">
                                    <!-- Faculty Selection -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group {{ $errors->has('faculty_id') ? ' has-error' : '' }}">
                                                {!! Form::label('faculty_id', trans('general.faculty'), ['class' => 'control-label col-sm-3']) !!}
                                                <div class="col-sm-8">
                                                    {!! Form::select('faculty_id', $faculty, null, ['class' => 'form-control select2-ajax-faculty', 'remote-url' => route('api.faculties'), 'remote-param' => '[name="university_id"]']) !!}
                                                    @if ($errors->has('faculty_id'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('faculty_id') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Department Selection -->

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group {{ $errors->has('department_id') ? ' has-error' : '' }}">
                                                {!! Form::label('department_id', trans('general.department'), ['class' => 'control-label col-sm-3']) !!}
                                                <div class="col-sm-8">
                                                    {!! Form::select('department_id', $department, null, [
                                                      'class' => 'form-control select2-d-ajax',
                                                    'data-remote-url' => route('api.departmentByFacultyArchive'),
                                                    'data-university-id' => '[name="university_id"]',
                                                    'data-faculty-id' => '[name="faculty_id"]',
                                                        ]) !!}
                                                    @if ($errors->has('department_id'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('department_id') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group {{ $errors->has('grade_id') ? ' has-error' : '' }}">
                                                {!! Form::label('grade_id', trans('general.grade'), ['class' => 'control-label col-md-3']) !!}
                                                <div class="col-md-8">
                                                    {!! Form::select('grade_id', $grades, null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                                    @if ($errors->has('grade_id'))
                                                        <span class="help-block">
                                                    <strong>{{ $errors->first('grade_id') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                   

                                </div>

                                <div  id="doc_form" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group {{ $errors->has('doc_type') ? ' has-error' : '' }}">
                                                {!! Form::label('doc_type', trans('general.archive_doc_type'), ['class' => 'control-label col-md-3']) !!}
                                                <div class="col-md-8">
                                                    {!! Form::select('doc_type', [ '1' => 'دیپلوم','2' => 'ترانسکریپت', '3' => 'حوض جاب',], null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                                   
                                                @if ($errors->has('doc_type'))
                                                        <span class="help-block">
                                                    <strong>{{ $errors->first('doc_type') }}</strong></span>
                                                    @endif
                                                 </div>
                                             </div>
                                         </div>
                                   </div>
                                </div>



                                <!-- Book Form -->
                                <div  id="book_form" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group {{ $errors->has('status_id') ? ' has-error' : '' }}">
                                                {!! Form::label('status_id', trans('general.status'), ['class' => 'control-label col-md-3']) !!}
                                                <div class="col-md-8">
                                                    {!! Form::select('status_id', ['4' => 'تکمیل', '3' => 'ناتکمیل', '2' => 'صرف نظر'], null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                                    @if ($errors->has('status_id'))
                                                         <span class="help-block">
                                                    <strong>{{ $errors->first('status_id') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group {{ $errors->has('qc_status_id') ? ' has-error' : '' }}">
                                                {!! Form::label('qc_status_id', trans('general.accept_or_refuse'), ['class' => 'control-label col-md-3']) !!}
                                                <div class="col-md-8">
                                                    {!! Form::select('qc_status_id', ['3' => 'تایید', '4' => 'رد', '2' => 'پروسس'], null, ['class' => 'form-control select2', 'placeholder' => trans('general.select')]) !!}
                                                    @if ($errors->has('qc_status_id'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('qc_status_id') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>





                            <!-- Form Actions -->
                            <div class="form-actions fluid">
                                <div class="row">
                                    <div class="col-md-offset-1 col-md-12">
                                        <button type="submit" class="btn green">{{ trans('general.generate_report') }}</button>


                                    </div>
                                </div>
                            </div>
                        </div>
                            <br>
                            {!! Form::close() !!}




                    </div>

                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12" id="statistics_data"></div>
            </div>
        </div>

        @php
            // Status and QC status mappings
            $statusMap = [
                '4' => 'تکمیل',
                '3' => 'ناتکمیل',
                '2' => 'صرف نظر',
            ];

            $qcStatusMap = [
                '3' => 'تایید',
                '4' => 'رد',
                '2' => 'پروسس',
            ];

              $archivedocMap = [
                '1' => 'دیپلوم',
                '2' => 'ترانسکریپت',
                '3' => 'حوض جاب',
            ];
        @endphp




        <div id="report-content">
        @if(isset($results) && $results->isNotEmpty())
            <table class="table table-bordered">
                <thead>
                <tr>
                    @if (isset($results[0]->archive_year_id))
                        <th>سال کتاب</th>
                    @endif

                    @if (isset($results[0]->university_id))
                        <th>پوهنتون</th>
                    @endif

                    @if (isset($results[0]->faculty_id))
                        <th>پوهنحی</th>
                    @endif

                    @if (isset($results[0]->department_id))
                        <th>دیپارتمنت</th>
                    @endif

                    @if (isset($results[0]->grade_id) && !empty($results[0]->grade_id))
                        <th>درجه</th> <!-- Only show the grade column if grade_id is selected -->
                    @endif

                    @if (isset($results[0]->doc_type) && !empty($results[0]->doc_type))
                        <th>ثبت اسناد</th> <!-- Only show the document type column if doc_type is selected -->
                    @endif

                    @if (isset($results[0]->status_id))
                        <th>وضعیت</th> <!-- Added a column header for status -->
                    @endif

                    @if (isset($results[0]->qc_status_id))
                        <th>موافقه وعدم موافقه</th> <!-- Added a column header for QC status -->
                    @endif

                    {{-- @if ($reporttype == 2)
                        <th>تعداد محصلین</th>
                    @else
                        <th>تعداد کتاب</th>
                    @endif

                    @if ($reporttype == 3)
                        <th>تعداد اسناد</th>
                
                    @endif --}}
                </tr>
                </thead>
                <tbody>
                @foreach($results as $item)
                    <tr>
                        @if (isset($item->archive_year_id))
                            <td>{{ $item->archive_year_id }}</td>
                        @endif

                        @if (isset($item->university_id))
                            <td>{{ $universities[$item->university_id] ?? 'Unknown University' }}</td>
                        @endif

                        @if (isset($item->faculty_id))
                            <td>{{ $faculties[$item->faculty_id] ?? 'Unknown Faculty' }}</td>
                        @endif

                        @if (isset($item->department_id))
                            <td>{{ $departments[$item->department_id] ?? 'Unknown Department' }}</td>
                        @endif

                        @if (isset($item->grade_id) && !empty($item->grade_id))
                            <td>{{ $grades[$item->grade_id] ?? 'Unknown Grade' }}</td>  <!-- Only show grade if grade_id is selected -->
                        @endif

                        @if (isset($item->doc_type) && !empty($item->doc_type))
                            <td>{{ $archivedocMap[$item->doc_type] ?? 'Unknown Document Type' }}</td>  <!-- Only show document type if doc_type is selected -->
                        @endif

                        @if (isset($item->status_id))
                            <td>{{ $statusMap[$item->status_id] ?? 'Unknown Status' }}</td> <!-- Display status name -->
                        @endif

                        @if (isset($item->qc_status_id))
                            <td>{{ $qcStatusMap[$item->qc_status_id] ?? 'Unknown QC Status' }}</td> <!-- Display QC status name -->
                        @endif

                        <td>{{ $item->total_count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <tr>
                <td colspan="6" class="text-center">
                    <div class="alert alert-warning" role="alert">
                        <strong>معلومات پیدا نشد</strong>
                    </div>
                </td>
            </tr>
            @endif


            </tbody>
            </table>
        </div>

    </div>



    <script>
         function showForm1() {

            // Show the selected form
            const selectedForm = document.getElementById("form-report_type").value;
            if (selectedForm) {
                // Set report type based on selected form
    
                console.log(selectedForm);
                const reportTypeInput = document.getElementById('report_type');
                if (reportTypeInput) {
                reportTypeInput.value = selectedForm; 
                }
    
            }
        }
        
        function showForm() {
            // Hide all forms
            document.getElementById("book_form").style.display = "none";
            document.getElementsByClassName("student_form")[0].style.display = "none";
            document.getElementsByClassName("student_form")[1].style.display = "none";
            document.getElementById("doc_form").style.display = "none";
    
            // Show the selected form
            const selectedForm = document.getElementById("form-select").value;
            if (selectedForm) {
                // Set report type based on selected form
                const reportTypeInput = document.getElementById('reporttype');
    
                if (selectedForm === 'book_form') {
                    reportTypeInput.value = 1;
                    document.getElementById("book_form").style.display = "block";
                } else if (selectedForm === 'student_form') {
                    reportTypeInput.value = 2;
                    
            document.getElementsByClassName("student_form")[0].style.display = "block";
            document.getElementsByClassName("student_form")[1].style.display = "block";
                } else if (selectedForm === 'doc_form') {
                    reportTypeInput.value = 3;
                    document.getElementById("doc_form").style.display = "block";
                }
    
                console.log(selectedForm);
            }
        }
    </script>
    
@endsection
@push('scripts')
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
    <script>
        function getUniversityActivity(){

            var archive_year_id = document.getElementById('archive_year_id').value;
            var startdate = document.getElementById('startdate').value;
            var enddate = document.getElementById('enddate').value;

            window.location.href = window.location.origin + "/activity/" + archive_year_id +'/' + startdate + '/' + enddate;

        }

        $(function () {
            $(".datepic").datepicker({
                autoclose: true,
                todayHighlight: true
            }).datepicker('update', new Date());
        });

    </script>



@endpush

