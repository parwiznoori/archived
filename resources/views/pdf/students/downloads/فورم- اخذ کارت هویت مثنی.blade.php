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
        $class=ceil($student->semester/2);
    @endphp
    <div class="page">
        <div class="center">
            <table class="header_table" style="width:100%;margin-top:-40px">
                <tr>
                    <td style="text-align: right">

                        <h4 style="text-align:right">{{ trans('id_card.sequnce_number1') }}:( &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;)</h4>
                        <br>
                        <h4 style="text-align:right">{{ trans('id_card.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
                            &nbsp;&nbsp;&nbsp; / &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; / </h4>

                    </td>
                    <td style="text-align:right;width:9%;vertical-align:top;">
                        <img src="{{ public_path('img/emarat-logo.jpg') }}" style="max-width: 100px;height:100px;vertical-align:top;"/></td>
                        
                    <td style="text-align:center;width:32%;vertical-align:middle;padding-right:10%;padding-top:-44px">
                        {{-- <img src="{{ asset('img/wezarat-logo.jpg') }}" style="max-width: 80px" /> --}}
                      
                            <p style="font-size: 14px;font-weight:bold;padding-bottom:-4px">{{ trans('id_card.islamic_emarat') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('id_card.ministry_title') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('id_card.except_id_card') }}</p>
                     
                    </td>
					<td style="text-align: left;width:18%">
						<img src="{{ public_path('img/wezarat-logo.jpg') }}" style="max-width: 80px;height:80px;margin-top:-2%" />	
                        
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
                       خانه پوری توسط محصل
                    </th>

                </tr>
                <tr>
                    <th colspan="5" style="text-align: center">شهرت محصل</th>
                </tr>
                <tr>
                    <td style="padding: 0;position: relative;">
                        <table class="table inner-table">

                            <tr>
                                <th class="center">{{ trans('id_card.name') }}</th>
                                <th class="center">{{ trans('id_card.father_name') }}</th>
                                <th class="center">{{ trans('id_card.grandfather_name') }}</th>
                                <th class="center">{{ trans('id_card.gender') }}</th>
                                <th class="center">{{ trans('id_card.kankor_id') }}</th>


                            </tr>
                            <tr>
                                <td class="center">{{ $student->getFullNameAttribute() }}</td>
                                <td class="center">{{ $student->father_name }}</td>
                                <td class="center">{{ $student->grandfather_name }}</td>
                                <td class="center">{{ $student->gender }}</td>
                                <td class="center" >{{ $student->form_no }}</td>
                            </tr>
                            <tr>
                                <th class="center">{{ trans('id_card.kankor_year') }}</th>
                                <th class="center">{{ trans('id_card.faculty') }}</th>
                                <th class="center">{{ trans('id_card.department') }}</th>
                             
                                <th class="center">{{ trans('id_card.class') }}</th>
                                <th class="center">{{ trans('id_card.semester') }}</th>
                              
                            </tr>
                            <tr>
                                <td class="center">{{ $student->kankor_year }}</td>
                                <td class="center">{{ $student->department->facultyName->name}}</td>
                                <td class="center">{{ $student->department->name }}</td>
                               
                                <td class="center">{{ $class }}</td>
                                <td class="center">{{ $student->semester }}</td>
                              
                            </tr>
                            <tr>
                                <td colspan="5" style="border:none"><b>{{ trans('id_card.reason_for_taking_card') }}:</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="border:none;height:70px"></td>
                                <td style="border:none"></td>
                                <td style="border:none"></td>
                                <td style="border:none"><b>{{ trans('id_card.student_sign') }}</b> </td>
                                <td style="height:40px;border:none"> <h4>{{trans('general.date')}} :  &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; / &nbsp;   &nbsp;&nbsp;&nbsp; / </h4></td>
                            </tr>

                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12">
                                    {{ trans('id_card.department_dean') }}(&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                                </th>
                            </tr>

                            <tr>
                                <td class="textdir" rowspan="4" width="3%" height="60px">همان روز-1روز</td>
                                <td class="textdir" rowspan="4" width="3%" height="60px"> </td>
                                <td class="textdir" rowspan="4" width="3%" height="60px"> </td>
                            </tr>


                            <tr>

                                <th style="text-align: right;border:none;vertical-align:center;"colspan="6">
                                    <span>{{ trans('id_card.to_student_deputy_affairs') }}</span>
                              

                                    <br>
                                    <span>{{ trans('id_card.openion_on_student_academic_records') }}</span>



                                </th>
                                <td class="center" style="text-align: right;border:none;height:30px">
                                </td>
                                <td class="center" style="text-align: right;border:none;height:30px">
                                </td>

                            </tr>

                            <tr>

                                <td style="text-align: right;border:none" height="20px"></td>
                                <td style="text-align: right;border:none" height="20px"></td>
                                <td style="text-align: right;border:none" height="20px"></td>

                            </tr>
                            <tr>
                                <td style="text-align: right;border:none"></td>
                                <td style="text-align: right;border:none"></td>
                                <td style="text-align: right;border:none"></td>
                            </tr>
                            <tr>
                                <td class="textdir" style="text-align: right;width:2%;height:50px" rowspan="2"
                                    height="100px" width="2%"><strong> {{ trans('tajeel.total_time') }}</strong></td>
                                <td class="textdir" rowspan="2" style="text-align: right;width:2%;height:50px" width="2%"><strong>{{ trans('tajeel.receive_date') }}</strong></td>
                                <td class="textdir" rowspan="2" style="text-align: right;width:2%;height:50px" width="2%"><strong>{{ trans('tajeel.issue_date') }}</strong></td>
                            </tr>
                            <tr>

                                <th style="text-align: left;width:30%;border:none;vertical-align:bottom;padding-bottom: 10px"
                                    height="80px">{{ trans('id_card.sign_of_lesson_general_manager') }}<br><br>
                                </th>
                                <td style="text-align: right;width:30%;border:none;vertical-align:bottom"
                                    height="25px"></td>
                                <th style="text-align: right;border:none;vertical-align:bottom;padding-bottom: 10px;"height="40px"> {{ trans('id_card.sign_and_stamp_of_faculty_dean') }}
                                       <br><br>
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
                                    {{ trans('id_card.deputy_student_affairs') }}

                                </th>
                            </tr>

                            <tr>
                                <td class="textdir" rowspan="4" width="3%" height="40px" >1روز</td>
                                <td class="textdir" rowspan="4" width="3%" height="40px"> </td>
                                <td class="textdir" rowspan="4" width="3%" height="40px"> </td>
                            </tr>


                            <tr>

                                <th style="text-align: right;vertical-align:top;border:none;" colspan="6">
                                    {{ trans('id_card.register_general_manager') }} <br>
                                    {{ trans('id_card.id_card_payment') }}
                               </th>


                            </tr>

                            <tr>

                               
                                <td style="text-align: right;border:none" height="-5px"></td>
                                

                            </tr>



                            <tr>

                                <td style="text-align: right;border:none"></td>
                                <td style="text-align: right;border:none"></td>
                                <td style="text-align: right;border:none"></td>


                            </tr>
                            <tr>
                                <td class="textdir" rowspan="2" height="90px" width="3%">{{ trans('tajeel.total_time') }}
                                   </td>
                                <td class="textdir" rowspan="2" height="90px" width="3%">
                                  {{ trans('tajeel.receive_date') }}</td>
                                <td class="textdir" rowspan="2" height="90px" width="3%">
                                    {{ trans('tajeel.issue_date') }}</td>
                            </tr>
                            <tr>

                                <td style="text-align: center;border:none;" height="15px"></td>
                                
                                <th style="text-align: left;border:none;vertical-align:middle;width:75%;margin-left:100px;"
                                    height="80px"> 
                                    بااحترام&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <br>
                                  {{ trans('id_card.sign_of_deputy_student_affairs') }}
                                   
                                </th>
                                <td style="text-align: right;border:none;"height="35px"></td>

                            </tr>
                           


                        </table>

                    </td>
                </tr>
                {{-- <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12">
                                    تائیدی آمریت/مدیریت عمومی مالی و اداری از پرداخت تعرفه به بانک

                                </th>
                            </tr>

                            <tr>
                                <td class="textdir" rowspan="4" width="3%" height="40px" >1روز</td>
                                <td class="textdir" rowspan="4" width="3%" height="40px"> </td>
                                <td class="textdir" rowspan="4" width="3%" height="40px"> </td>
                            </tr>


                            <tr>

                                <th style="text-align: right;vertical-align:top;border:none;" colspan="6">
                                  
                                    </th>


                            </tr>

                            <tr>

                               
                                <td style="text-align: right;border:none" height="70px"></td>
                                

                            </tr>



                            <tr>

                                <td style="text-align: right;border:none"></td>
                                <td style="text-align: right;border:none"></td>
                                <td style="text-align: right;border:none"></td>


                            </tr>
                            <tr>
                                <td class="textdir" rowspan="2" height="90px" width="3%">{{ trans('tajeel.total_time') }}
                                   </td>
                                <td class="textdir" rowspan="2" height="90px" width="3%">
                                  {{ trans('tajeel.receive_date') }}</td>
                                <td class="textdir" rowspan="2" height="90px" width="3%">
                                    {{ trans('tajeel.issue_date') }}</td>
                            </tr>
                            <tr>

                                <td style="text-align: center;border:none;" height="15px"></td>
                                
                                <th style="text-align: center;border:none;vertical-align:bottom;width:60%;margin-left:100px;"
                                    height="80px"> 
                                   
                                    امضاء و مهر آمر/مدیریت عمومی مالی وحسابی/مالی و اداری
                                   
                                </th>
                                <td style="text-align: right;border:none;"height="35px"></td>

                            </tr>
                           


                        </table>

                    </td>
                </tr> --}}
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12">
                                    {{ trans('id_card.confirmation_of_manager') }}

                                </th>
                            </tr>

                            <tr>
                                <td class="textdir" rowspan="2" width="3%" height="15px" >1روز</td>
                                <td class="textdir" rowspan="2" width="3%" height="15px"> </td>
                                <td class="textdir" rowspan="2" width="3%" height="15px"> </td>
                            </tr>


                            <tr>

                                <th style="text-align: right;vertical-align:top;border:none;visibility:hidden" colspan="6">
                                    <p style="visibility:hidden">
                                    مدیریت محترم عمومی ثبت و راجستر/مدیریت محترم بانک معلومات دیتابیس!
                                    <br>
                                    بعداز تحویلی پول تعرفه به گونه رسمی به آمریت/مدیریت عمومی مالی و حسابی/مالی و اداری در قسمت ترتیب کاریت هویت مثنی موصوف اصولآ اجراآت نمائید
                                </p>
                                </th>


                            </tr>

                       



                        
                            <tr>
                                <td class="textdir" rowspan="2" height="60px" width="3%">{{ trans('tajeel.total_time') }}
                                   </td>
                                <td class="textdir" rowspan="2" height="60px" width="3%">
                                  {{ trans('tajeel.receive_date') }}</td>
                                <td class="textdir" rowspan="2" height="60px" width="3%">
                                    {{ trans('tajeel.issue_date') }}</td>
                            </tr>
                            <tr>

                                <td style="text-align: center;border:none;" height="10px"></td>
                                
                                <th style="text-align: left;border:none;vertical-align:middle;margin-left:100px;width:80%"
                                    height="80px"> 
                                    
                                    امضاء و مهر آمر/مدیریت عمومی مالی وحسابی/مالی و اداری
                                   
                                </th>
                               

                            </tr>
                           


                        </table>

                    </td>
                </tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="8">
                                    {{ trans('tajeel.deputy_student_affairs') }}

                                </th>
                            </tr>
                            <tr>
                                <th style="width:400px;text-align:center" colspan="2">اجراآت</th>
                                <th style="width:300px;text-align:right" rowspan="2">{{ trans('id_card.sign_of_print_card_incharge') }}</th>
                            </tr>
                            <tr>
                                <th style="text-align:center" rowspan="2">{{ trans('id_card.exception_card_print') }}</th>
                                <td style="padding-left:65px;padding-right:65px;text-align:center;width:200px;height:30px" rowspan="2">
                                    <table class="table inner-table">
                                        <tr>
                                            <td style="height:20px"></td>
                                        </tr>
                                    </table>
                                </td>
                               
                            </tr>
                            <tr>
                               
                               
                                <th style="width:300px;text-align:right" rowspan="2"> {{ trans('id_card.student_sign') }}</th>
                            </tr>
                            <tr>
                                <th style="width:200px">{{ trans('id_card.handover_to_student') }}</th>
                                <td style="padding-left:65px;padding-right:65px;width:200px;height:30px">
                                    <table class="table inner-table">
                                        <tr>
                                            <td style="height:20px"></td>
                                        </tr>
                                    </table>
                                </td>
                                
                            </tr>
                   
                        </table>


                    </td>
                </tr>
           

            </table>

        </div>
    </div>
</body>

</html>
