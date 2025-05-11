<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Leave Document</title>
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
            padding: 4px 4px;
            border: 0.5px solid #000;
        }

        .inner-table tr:first-child td,
        .inner-table tr:first-child th {
            border-top: 0
        }

        .inner-table tr td:first-child,
        .inner-table tr th:first-child {
            border-right: 0
        }

        .inner-table tr:last-child td,
        .inner-table tr:last-child th {
            border-bottom: 0
        }

        .inner-table tr td:last-child,
        .inner-table tr th:last-child {
            border-left: 0
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
            text-rotate: -90deg;
            transform: rotate(-90deg);
            text-align: center;
            vertical-align: middle;
        }
        table,td,th{
            display: block;
        }
    </style>
</head>

<body>

    @php
        $grade=ceil($student->semester/2)
    @endphp

    <div class="page">
        <div class="center">
            <table class="header_table" style="width:100%;margin-top:-40px">
                <tr>
                   
                    <td style="text-align: right">

                        <h4 style="text-align:right">{{ trans('tajeel.sequnce_number1') }}:( &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;)</h4>
                        <br>
                        <h4 style="text-align:right">{{ trans('tajeel.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
                            &nbsp;&nbsp;&nbsp; / &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; / </h4>

                    </td>
                    <td style="text-align:right;width:9%;vertical-align:top;">
                        <img src="{{ public_path('img/emarat-logo.jpg') }}" style="max-width: 100px;height:100px;vertical-align:top;"/></td>
                        
                    <td style="text-align:center;width:32%;vertical-align:middle;padding-right:10%;padding-top:-44px">
                        {{-- <img src="{{ asset('img/wezarat-logo.jpg') }}" style="max-width: 80px" /> --}}
                      
                            <p style="font-size: 14px;font-weight:bold;padding-bottom:-4px">{{ trans('tajeel.islamic_emarat') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('tajeel.ministry_title') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('tajeel.tajeel') }}</p>
                     
                    </td>
					<td style="text-align: left;width:18%">
						<img src="{{ public_path('img/wezarat-logo.jpg') }}" style="max-width:80px;height:80px;margin-top:-2%" />	
                        
					</td>
                    <td style="text-align:left;width:17%;vertical-align:top;">
                        <img src="{{ public_path($student->photo_relative_path()) }}"
                            style="max-width: 80px">
                      

                    </td>
                </tr>
            </table>
            <table class="table" style="margin-top: 20px">
                <tr>
                    <th class="bg-grey center">
                        {{ trans('tajeel.personal_info') }}
                    </th>

                </tr>
                <tr>
                    <td style="padding: 0;position: relative;">
                        <table class="table inner-table">

                            <tr>
                                <th class="center">{{ trans('tajeel.name') }}</th>
                                <th class="center">{{ trans('tajeel.father_name') }}</th>
                                <th class="center">{{ trans('tajeel.grandfather_name') }}</th>
								<th class="center">{{ trans('tajeel.kankor_id') }}</th>
                                <th class="center">{{ trans('tajeel.kankor_year') }}</th>
                            </tr>
                            <tr>
                                <td class="center">{{ $student->getFullNameAttribute() }}</td>
                                <td class="center">{{ $student->father_name }}</td>
                                <td class="center">{{ $student->grandfather_name }}</td>
								<td class="center" >{{ $student->form_no }}</td>
                                <td class="center">{{ $student->kankor_year }}</td>
                              
                            </tr>
                            <tr>
                                <th class="center">{{ trans('tajeel.faculty') }}</th>
                                <th class="center" colspan="2">{{ trans('tajeel.department') }}</th>
                                <th class="center">{{ trans('tajeel.class_year') }}</th>
                                <th class="center">{{ trans('tajeel.current-semester') }}</th>
                               
                            </tr>
                            <tr>
                                <td class="center" height="40px" style="font-size:12px">{{ $student->department->facultyName->name}}</td>
                                <td class="center" height="40px" colspan="2">{{ $student->department->name }}</td>
                                <td class="center" id="year" height="40px">
                           
                                    {{ $grade   }}                               
                                </td>
                                <td class="center" height="40px">{{ $student->semester }}</td>
                              
                            </tr>
							<tr>
								<th colspan="2" style="text-align: center">تاجیل</th>
								<td rowspan="3" colspan="3" style="text-align: right;vertical-align:top">دلیل اخذ تاجیل:</td>
								
							</tr>
							<tr>
								<th style="text-align: center;width:130px">از کدام تاریخ</th>
								<th style="text-align: center">تا کدام تاریخ</th>
								
							</tr>
							<tr>
								<td style="height:23px"></td>
								<td style="height:23px"></td>
							</tr>
							<tr>
								<td rowspan="2" colspan="4" style="height:40px;border-bottom:none"><b>{{ trans('tajeel.student_from_form_entry') }}</b></td>
								<th style="width:170px">{{ trans('tajeel.form_distribution_sign') }}</th>
							</tr>
							<tr>
								<td rowspan="2" style="height:50px;border-bottom:none"></td>
								
							</tr>
							<tr>
								<td style="height:40px;text-align:left;border:none">امضای محصل</td>
								<td style="height:40px;border:none"></td>
								<td style="height:40px;border:none"> <h4>{{trans('tajeel.date')}} :  &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; / &nbsp;   &nbsp;&nbsp;&nbsp; / </h4></td>
							</tr>

                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12">
                                    {{ trans('tajeel.department_dean') }}(&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                                </th>
                            </tr>

                            <tr>
                                <td class="textdir" rowspan="4" width="3%" height="55px">همان روز-1روز</td>
                                <td class="textdir" rowspan="4" width="3%" height="55px"> </td>
                                <td class="textdir" rowspan="4" width="3%" height="55px"> </td>
                            </tr>


                            <tr>

                                <th style="text-align: right;border:none;vertical-align:center;"colspan="6">
                                    <span>به معاونیت محترم امور محصلان!</span>
                                    <br>
                                    <span>موافقه:</span>
                                </th>
                                <td class="center" style="text-align: right;border:none;height:10px">
                                </td>
                                <td class="center" style="text-align: right;border:none;height:10px">
                                </td>

                            </tr>

                            <tr>

                                <td style="text-align: right;border:none" height="30px"></td>
                                <td style="text-align: right;border:none" height="30px"></td>
                                <td style="text-align: right;border:none" height="30px"></td>

                            </tr>
                            <tr>
                                <td style="text-align: right;border:none"></td>
                                <td style="text-align: right;border:none"></td>
                                <td style="text-align: right;border:none"></td>
                            </tr>
                            <tr>
                                <td class="textdir" style="text-align: right;width:2%;height:80px" rowspan="2"
                                    height="100px" width="2%"><strong> {{ trans('tajeel.total_time') }}</strong></td>
                                <td class="textdir" rowspan="2" style="text-align: right;width:2%;height:80px" height="100px" width="2%"><strong>{{ trans('tajeel.receive_date') }}</strong></td>
                                <td class="textdir" rowspan="2" style="text-align: right;width:2%;height:80px" height="100px" width="2%"><strong>{{ trans('tajeel.issue_date') }}</strong></td>
                            </tr>
                            <tr>

                                <th style="text-align: left;width:30%;border:none;vertical-align:bottom;padding-bottom: 10px"
                                    height="100px">{{ trans('tajeel.sign_of_lesson_general_manager') }}<br><br>
                                </th>
                                <td style="text-align: right;width:30%;border:none;vertical-align:bottom"
                                    height="25px"></td>
                                <th style="text-align: right;border:none;vertical-align:bottom;padding-bottom: 10px;"height="40px">{{ trans('tajeel.sign_and_stamp_of_faculty_dean') }}<br><br>
                                </th>

                            </tr>


                        </table>

                    </td>
                </tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12">
                                    {{ trans('tajeel.deputy_student_affairs') }}

                                </th>
                            </tr>

                            <tr>
                                <td class="textdir" rowspan="4" width="3%" height="50px" >1روز</td>
                                <td class="textdir" rowspan="4" width="3%" height="50px"> </td>
                                <td class="textdir" rowspan="4" width="3%" height="50px"> </td>
                            </tr>


                            <tr>

                                <th style="text-align: right;border:none;vertical-align:top" colspan="6">
                                    به ریاست محترم پوهنحی(&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)!
                                    <br>
                                    ابراز نظر:
                                </th>


                            </tr>

                            <tr>

                                <td style="text-align: right;border:none" height="30px"></td>
                                <td style="text-align: right;border:none" height="30px"></td>
                                <td style="text-align: right;border:none" height="30px"></td>

                            </tr>



                            <tr>

                                <td style="text-align: right;border:none"></td>
                                <td style="text-align: right;border:none"></td>
                                <td style="text-align: right;border:none"></td>


                            </tr>
                            <tr>
                                <td class="textdir" rowspan="2" height="100px" width="3%">{{ trans('tajeel.total_time') }}
                                   </td>
                                <td class="textdir" rowspan="2" height="100px" width="3%">
                                  {{ trans('tajeel.receive_date') }}</td>
                                <td class="textdir" rowspan="2" height="100px" width="3%">
                                    {{ trans('tajeel.issue_date') }}</td>
                            </tr>
                            <tr>

                                <td style="text-align: center;border:none;" height="55px"></td>
                                
                                <th style="text-align: center;border:none;vertical-align:bottom;width:60%;margin-left:100px;"
                                    height="80px"> {{ trans('tajeel.sign_and_stamp_of_student_deputy_affairs') }}
                                   
                                </th>
                                <td style="text-align: right;border:none;"height="55px"></td>

                            </tr>
                           


                        </table>

                    </td>
                </tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12">
                                    {{ trans('tajeel.department_dean') }}(&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                                </th>
                            </tr>
                            <tr>
                                <td class="textdir" rowspan="4" width="4%" height="100px">
                                    {{ trans('tajeel.receive_date') }}</td>
                                <td class="textdir" rowspan="4" width="4%" height="100px"> </td>

                            </tr>


                            <tr>



                                <td style="text-align:right;vertical-align:top;border:none;padding-bottom:20px" colspan="10">
                                   
                                   

                                  <strong>  مدیریت عمومی تدریسی به اساس ابراز نظر معاونیت محترم امور محصلان در زمینه اجراآت
                                    نمائید.</strong>
                                </td>


                            </tr>

                            <tr>
                                <td style="border: none"></td>
                                <td style="border: none"></td>
                                <td style="border: none"></td>
                                

                                <th style="text-align: left;margin-right:44px;height:80px;border:none"><strong>
                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;امضای
                                    رئیس پوهنحی </strong><br><br><br><br><br> </th>


                            </tr>






                        </table>


                    </td>
                </tr>
                <tr>

                    <td rowspan="2"
                        style="text-align:right;border-right:none;border-left:none;border-bottom:none;margin-right:5px">
                        <br><span>نوت: بعد از طی مراح اصل فورم به ریاست پوهنحی مربوطه ارسال، یک کاپی فورم در معاونیت امور محصلان حفظ یک کاپی به آمریت لیلیه ها درصورتیکه که محصل شامل لیلیه باشد اسم شان را از لیست لیلیه حذف نمائید و یک کاپی به محصل داده شود.
                            </span>
                    </td>
                </tr>

            </table>

        </div>
    </div>
</body>


</html>
