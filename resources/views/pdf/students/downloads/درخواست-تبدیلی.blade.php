<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            direction: rtl;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
            font-family: 'nazanin';
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 2mm;
            margin: 10mm auto;
            border: 0.5px #D3D3D3 solid;
            border-radius: 2px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
            }
        }

        p {
            margin: 0;
        }

        .table td,
        .table th {
            font-size: 14px;
            padding: 4px 4px;
            border: 0.5px solid #000;
        }

        .inner-table tr:first-child td,
        .inner-table tr:first-child th {
            border-top: 0;
            font-size: 11px;
        }

        .inner-table tr td:first-child,
        .inner-table tr th:first-child {
            border-right: 0;
            font-size: 11px;
        }

        .inner-table tr:last-child td,
        .inner-table tr:last-child th {
            border-bottom: 0;
            font-size: 11px;
        }

        .inner-table tr td:last-child,
        .inner-table tr th:last-child {
            border-left: 0;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .bg-grey {
            background-color: #d8d8d8;
        }

        .center {
            text-align: center;
        }


        .textdir {
            writing-mode: vertical-rl;
            text-rotate: -90;
            text-align: center;
            vertical-align: middle;
        }
        td{
            text-align: center;
            vertical-align: middle;
        }
	
    </style>
</head>

<body>
    @php
        $tazkira = explode('!@#', $student->tazkira);
        $class=ceil($student->semester/2);
    @endphp

    <div class="page">
        <div class="center">
            <table class="header_table" style="width:100%;margin-top:-60px">
                <tr>
                   
                    <td style="text-align: right;vertical-align:bottom">

                        <h4 style="text-align:right;vertical-align:bottom">{{ trans('transfer.form_no') }}:( &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;)</h4>
                        <br>
                        <h4 style="text-align:right;vertical-align:bottom">{{ trans('transfer.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
                            &nbsp;&nbsp;&nbsp; / &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; / </h4>
                    </td>
                    <td style="text-align:right;width:9%;vertical-align:middle;">
                        <img src="{{ public_path('img/emarat-logo.jpg') }}" style="max-width: 100px;height:100px;margin-top:-2%" /></td>
                        
                    <td style="text-align:center;width:32%;vertical-align:middle;padding-right:10%;padding-top:-44px">
                        {{-- <img src="{{ asset('img/wezarat-logo.jpg') }}" style="max-width: 80px" /> --}}
                            <p style="font-size: 14px;font-weight:bold;padding-bottom:-4px">{{ trans('transfer.islamic_emarat') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('transfer.ministry_title') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('transfer.student_transfer_process') }}</p>
                    </td>
					<td style="text-align: left;width:18%">
						<img src="{{ public_path('img/wezarat-logo.jpg') }}" style="max-width: 80px; height:80px;margin-top:-2%" />	
					</td>
                    <td style="text-align:left;width:17%;vertical-align:top;">
                        <img src="{{ public_path($student->photo_relative_path()) }}"
                            style="max-width: 80px">
                            <br><br>
                            ID:{{ $student->form_no }}
                      

                    </td>
                </tr>
            </table>


            <table class="table" style="margin-top: 10px">
                <!-- student request for transliterator_create_from_rules -->
                <tr>
                    <th class="bg-grey center" style="font-size: 14px"><b>مشخصات محصل متقاضی</b></th>

                </tr>
                <tr>
                    <td style="padding: 0">
                        <table class="table inner-table">
                            <tr>
                                <th class="center" style="width:14%;font-size:12px"> {{ trans('transfer.name') }}</th>
                                <th class="center" style="width:10%;font-size:12px">{{ trans('transfer.father_name') }}</th>
                                <th class="center" style="width:12%;font-size:12px">{{ trans('transfer.grandfather_name') }}</th>
                                <th class="center" style="width:12%;font-size:12px">{{ trans('transfer.class_year') }}</th>
                                <th class="center" style="width:12%;font-size:12px">{{ trans('transfer.semester') }}</th>
                                <th class="center" style="width:12%;font-size:12px">{{ trans('transfer.department') }}</th>
                                <th class="center" style="width:12%;font-size:12px">{{ trans('transfer.faculty') }}</th>
                                <th class="center" style="width:12%;font-size:12px">{{ trans('transfer.university') }}</th>
                                <th class="center" style="width:12%;font-size:12px">{{ trans('transfer.kankor_year') }}</th>
                                <th class="center" style="width:12%;font-size:12px">{{ trans('transfer.kankor_type') }}</th>
                          
                            
                            </tr>
                            <tr>
                                <td style="font-size:12px">{{ $student->getFullNameAttribute() }}</td>
                                <td style="font-size:12px">{{ $student->father_name }}</td>
                                <td style="font-size:12px">{{ $student->grandfather_name }}</td>
                                <td style="font-size:12px">{{ $class}}</td>
                                <td style="font-size:12px">{{ $student->semester }}</td>
                                <td style="font-size:12px">{{ $student->department->name }}</td>
                                <td style="font-size:12px">{{ $student->department->facultyName->name }}</td>
                                <td style="font-size:12px">{{ $student->university->name }}</td>
                                <td style="font-size:12px">{{ $student->kankor_year }}</td>
                                <td style="font-size:12px">{{ $student->student_sheft }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <table style=”width:100%; border:1px”>
                    <!-- <caption>Titulo de tabla</caption> -->

                    <thead>
                        <tr>
                            <th colspan="2" style="font-size: 12px"><b>تبدیلی محصل</b></th>
                            <th rowspan="7" style="position: relative;">
                                <table class="table inner-table">
                                    <tr>
                                        <td style="text-align:right;width:16%; border:none;vertical-align:top;padding-top:-10px;font-size:12px"><b>دلیل تبدیلی:</b></td>
                                        <td style="border:none"></td>
                                        <td style="border:none"></td>
                                    </tr>
                                    <tr>
                                        <td style="border:none" colspan="3"></td>

                                    </tr>
                                    <tr>
                                        <td style="border:none" colspan="3"></td>

                                    </tr>
                                    <tr>
                                        <td style="border:none" colspan="3"></td>

                                    </tr>
                                    <tr>
                                        <td style="border:none" colspan="3"></td>

                                    </tr>
                                
                                  


                                    <tr>
                                        <td style="border:none"></td>
                                        <td style="border:none"></td>
                                        <td style="text-align:right;width:30%; border:none; font-size:12px"><b>{{ trans('transfer.student_sign') }}</b> </td>
                                    </tr>
                                </table>
                            </th>

                        </tr>
                        <tr>
                            <th style="font-size:12px"><b>{{ trans('transfer.from_university') }}</b></th>
                            <th style="font-size:12px"><b>{{ trans('transfer.to_university') }}</b></th>

                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td style="height:45px; width:113px"></td>
                            <td style="height:45px; width:90px"></td>


                        </tr>

                    </tbody>
                </table>
                <!-- university and faculty aggrement where student curently studying -->
                <tr>
                    <th class="bg-grey center" style="font-size:14px">
                        {{ trans('transfer.Agreement_between_the_university_and_the_faculty_where_the_student_is_studying') }}
                    </th>
                </tr>


                <tr>

                    <td style="padding:0px">

                        <table class="table inner-table">
                            <tr>
                                <td class="textdir" rowspan="4" width="2.8%" height="50px">زمان</td>
                                <td class="textdir" rowspan="4" width="2.8%" height="50px">{{ trans('transfer.receive_date') }}</td>
                                <td class="textdir" rowspan="4" width="2.8%" height="50px">{{ trans('transfer.issue_date') }}</td>
                            </tr>
                            <tr>

                                <th class="center" colspan="2" style="width :13%;font-size:12px">
                                    {{ trans('transfer.is_failed') }}</th>
                                <th class="center" colspan="2" style="width :13%;font-size:12px">
                                    {{ trans('transfer.is_absence') }}</th>
                                <th class="center" colspan="2" style="width :13%;font-size:12px">
                                    {{ trans('transfer.is_deprivation') }}</th>
                                <th class="center" colspan="2" style="width :13%;font-size:12px">
                                    {{ trans('transfer.is_previous_conversion') }}</th>
                                <th class="center" colspan="2" style="width :13%;font-size:12px">
                                    {{ trans('transfer.curriculum_system') }}</th>
                                <th class="center" colspan="2" style="width :13%;font-size:12px">
                                    {{ trans('transfer.is_something_remaining') }}</th>
                                <th class="center" colspan="2" style="width :18%;font-size:12px">
                                    {{ trans('transfer.faculty_agreement') }}</th>
                            </tr>

                            <tr>

                                <td>{{ trans('transfer.yes') }}</td>
                                <td>{{ trans('transfer.no') }}</td>
                                <td>{{ trans('transfer.yes') }}</td>
                                <td>{{ trans('transfer.no') }}</td>
                                <td>{{ trans('transfer.yes') }}</td>
                                <td>{{ trans('transfer.no') }}</td>
                                <td>{{ trans('transfer.yes') }}</td>
                                <td>{{ trans('transfer.no') }}</td>
                                <td>{{ trans('transfer.yes') }}</td>
                                <td>{{ trans('transfer.no') }}</td>
                                <td>{{ trans('transfer.yes') }}</td>
                                <td>{{ trans('transfer.no') }}</td>
                                <td>{{ trans('transfer.yes') }}</td>
                                <td>{{ trans('transfer.no') }}</td>
                                <!-- <td rowspan = "2" style = "width: 10%"></td> -->
                            </tr>
                            <tr>
                                <td height="25"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table class="table inner-table">

                <tr>
                    <td class="textdir" rowspan="3" height="100px" width="3%">1- روز</td>
                    <td class="textdir" rowspan="3" height="100px" width="3%"></td>
                    <td class="textdir" rowspan="3" height="100px" width="3%"></td>
                </tr>

                <tr>
                    <!-- <td class="bg-grey" height = "80" colspan="3" style = "width:5%;  font-size: 10px;">{{ trans('general.duration') }}</td> -->

                    <td style="width:29.3%; vertical-align:top;text-align:center;font-size:12px">
                        <b>{{ trans('transfer.sign_from_department_student_affairs') }}</b></td>
                    <td style="width:29.3%; vertical-align:top;text-align:center;font-size:12px">
                        <b>{{ trans('transfer.sign_and_stamp_from_faculty_chairman') }}</b></td>
                    <td style="width:29.3%; vertical-align:top;text-align:center;font-size:12px">
                        <b>{{ trans('transfer.sign_and_approval_from_university') }}</b></td>
                </tr>
                <tr>
                    <td style="height:60px;"></td>
                    <td style="height:60px"></td>
                    <td style="height:60px"></td>
                </tr>
            </table>
            <table class="table" style="margin : 10px;border:double">

                <tr>
                    <th class="bg-grey center" style="font-size:16px">
                        {{ trans('transfer.the_agreement_of_the_university_and_the_faculty_to_which_the_student_has_applied_for_transfer') }}
                    </th>
                </tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th style="width:5%; font-size: 8px;font-size:10px"> {{ trans('transfer.time_t') }}</th>
                                <th style="width:5%; font-size: 8px;font-size:10px"> {{ trans('transfer.receive_date') }}</th>
                                <th style="width:5%; font-size: 8px;font-size:10px"> {{ trans('transfer.issue_date') }}</th>
                                <th style="width:9%;font-size:14px"> {{ trans('transfer.curriculum_system') }}</th>
                                <th style="width:9%;font-size:14px"> {{ trans('transfer.semester') }}</th>
                                <th style="width:9%;font-size:14px"> {{ trans('transfer.department') }}</th>
                                <th style="width:10%;font-size:14px"> {{ trans('transfer.faculty') }}</th>
                               
                                <th style="width:9%;font-size:14px"> نوع تبدیلی </th>
                                <th style="width:21%;font-size:14px" colspqn="5">
                                    {{ trans('transfer.sign_and_stamp_of_from_faculty_chairman') }}</th>
                                <th style="width:20%;font-size:14px" colspqn="5">
                                    {{ trans('transfer.sign_and_stamp_approval_from_university') }}</th>
                            </tr>
                            <!-- <tr>
       <td>{{ trans('transfer.credit') }}</td>
       <td>{{ trans('transfer.semester') }}</td>
       <td>{{ trans('transfer.yes') }}</td>
       <td>{{ trans('transfer.no') }}</td>
       <td>{{ trans('transfer.yes') }}</td>
       <td>{{ trans('transfer.no') }}</td>
       <td rowspan = "2"></td>
       <td rowspan = "2"></td>
      </tr> -->
                            <tr>
                                <td class="textdir" style="font-size:14px;width:5%" height="40">1- روز</td>
                                <td style="height:55px;font-size:12px"></td>
                                <td style="height:55px;font-size:12px"></td>
                                <td style="height:55px;font-size:14px">{{ $student->curriculum_study }}</td>
                                <td style="height:55px;font-size:14px">{{ $student->semester}}</td>
                                <td style="height:55px;font-size:14px">{{ $student->department->name }}</td>
                                <td style="height:55px;font-size:14px">{{ $student->department->facultyName->name }}</td>
                                <td style="height:55px;font-size:12px"> </td>
                                <td style="height:55px;font-size:12px"></td>
                                <td style="height:55px;font-size:12px"></td>

                            </tr>
                </tr>
            </table>

            </td>

            </tr>
            </table>
            <table class="table" style="margin : 10px;">
                <tr>
                    <th style="width:5%; font-size: 8px; border-bottom: none;"> </th>
                    <th style="width:5%; font-size: 8px;font-weight: bold;"><b>{{ trans('transfer.receive_date') }}</b></th>
                    <th style="width:5%; font-size: 8px;font-weight: bold;"><b>{{ trans('transfer.issue_date') }}</b></th>
                    <th class="bg-grey center" style="width:85%; font-size:14px" colspan="9"><b>
                            {{ trans('transfer.fillout_the_student_transformation_form_based_on_kankor_result_by_mohe') }}
                        </b></th>
                </tr>
                <tr>
                    <td rowspan="5" class="textdir" style="width:5%;border-top:none;text-align:center">1- روز</td>
                    <td rowspan="4" style="width:5%"></td>
                    <td rowspan="4" style="width:5%"></td>
                    <td class="center" style="width:9%;font-size:12px"><b>{{ trans('transfer.name') }}<b></td>
                    <td class="center" style="width:9%;font-size:12px"><b>{{ trans('transfer.father_name') }}</b></td>
                    <td class="center" style="width:9%;font-size:12px"><b>{{ trans('transfer.grandfather_name') }}</b></b></td>
                    <td class="center" style="width:7%;font-size:12px"><b>{{ trans('transfer.faculty') }}</b></td>
                    <td class="center" style="width:8%;font-size:12px"><b>{{ trans('transfer.department') }}</b></td>
                    <td class="center" style="width:8%;font-size:12px"><b>{{ trans('transfer.university') }}</b></td>
                    <td class="center" style="width:10%;font-size:12px"><b>{{ trans('transfer.obtained_score') }}</b></td>
                    <td class="center" style="width:9%;font-size:12px"><b>{{ trans('transfer.standard_score') }}</b></td>
                    <td class="center" style="width:10%;font-size:12px"><b>{{ trans('transfer.kankor_year') }}</b></td>
                </tr>
                <tr>
                    <td height="30" style="font-size: 12px">{{ $student->name }}</td>
                    <td style="font-size: 12px">{{ $student->father_name }}</td>
                    <td style="font-size: 12px">{{ $student->grandfather_name }}</td>
                    <td style="font-size: 12px">{{ $student->department->facultyName->name }}</td>
                    <td style="font-size: 12px">{{ $student->department->name }}</td>
                    <td style="font-size: 12px">{{ $student->university->name }}</td>
                    <td style="font-size: 12px">{{ $student->kankor_score }}</td>
                    <td style="font-size: 12px"></td>
                    <td style="font-size: 12px">{{ $student->kankor_year }}</td>



                </tr>

                <tr>
                    <td colspan="9" style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="center" colspan="3" style="width:20%; vertical-align:top;font-size:12px"><b>  {{ trans('transfer.head_of_database') }}
                                        </b></th>
                                <th class="center" colspan="3" style="width:40%; vertical-align:top;font-size:12px"><b>اسم
                                        و امضای مدیر ع تبدیلی های محصلان</b></th>
                                <th class="center" colspan="3" style="width:25%; vertical-align:top;font-size:12px"><b>اسم و
                                        امضای آمر امور محصلان</b></th>
                            </tr>
                            <tr>
                                <td colspan="3" style="height:40px;width:20%;"></td>
                                <td colspan="3" style="height:40px;width:30%"></td>
                                <td colspan="3" style="height:40px;width:25%"></td>
                            </tr>
                        </table>
                    </td>

                </tr>

            </table>
            <table class="table" style="margin : 10pxکpadding:0px">
                <tr>
                    <th style="width:5%; font-size: 8px;font-weight: bold;"><b> {{ trans('transfer.time_t') }}</b></th>
                    <!-- <th class="bg-grey"  style = "width:5%; font-size: 8px" > {{ trans('transfer.log_in') }}</th>
    <th class="bg-grey"  style = "width:5%; font-size: 8px" > {{ trans('transfer.issued') }}</th> -->
                    <th class="bg-grey center" style="width:80%;font-size:14px" colspan="9">
                        {{ trans('transfer.final_decision_by_the_commission_of_mohe') }} </th>
                </tr>
                <tr>
                    <td class="textdir" style="width:5%;text-align:center" height="180px">1- روز</td>
                    <!-- <td></td>
   <td></td> -->
                    <td style="width:96%"></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
