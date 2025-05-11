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
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    @php
    $tazkira = explode('!@#', $student->tazkira);
@endphp
    <div class="page">
        <div class="center">
            <table class="header_table" style="width:100%;margin-top:-40px">
                <tr>
                   
                    <td style="text-align: right">

                        <h4 style="text-align:right">{{ trans('graduated-correction-form.number') }}:( &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;)</h4>
                        <br>
                        <h4 style="text-align:right">{{ trans('graduated-correction-form.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
                            &nbsp;&nbsp;&nbsp; / &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; / </h4>

                    </td>
                    <td style="text-align:right;width:9%;vertical-align:top;">
                        <img src="{{ public_path('img/emarat-logo.jpg') }}" style="max-width: 100px;height:100px;vertical-align:top;"/></td>
                        
                    <td style="text-align:center;width:35%;vertical-align:middle;padding-right:10%;padding-top:-44px">
                        {{-- <img src="{{ asset('img/wezarat-logo.jpg') }}" style="max-width: 80px" /> --}}
                      
                            <p style="font-size: 14px;font-weight:bold;padding-bottom:-4px">{{ trans('id_card.islamic_emarat') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('graduated-correction-form.ministry_title') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('graduated-correction-form.correction_form_for_graduated_student') }}</p>
                     
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
            <table class="table" style="margin-top: 10px">
                <tr>
                <td style="padding: 0">
					<table class="table inner-table">
                        <tr>
                            <th class="bg-grey center" colspan="10">
                                {{ trans('graduated-correction-form.fill_by_student') }}
                            </th>
        
                        </tr>
						<tr>
							<th style = "width:80px">{{ trans('graduated-correction-form.name') }}</th>
							<td style="width:100px">{{ $student->name }}</td>
							{{-- <td style="width:100px">{{ $student->getFullNameAttribute() }}</td> --}}
							<th colspan="2" style="">{{ trans('graduated-correction-form.university') }}</th>
							<th colspan="3" style="">{{ trans('graduated-correction-form.faculty') }}</th>
							<th colspan="3" style="">{{ trans('graduated-correction-form.department') }}</th>
							
							
							
						</tr>
                        <tr>
							<th>{{trans('graduated-correction-form.father_name')}}</th>
							<td style = "width:80px">{{ $student->father_name }}</td>
                            <td colspan="2" style="">{{$student->university->name}}</td>
							<td colspan="3" style="">{{$student->department->facultyName->name}}</td>
							<td colspan="3" style="">{{$student->department->name}}</td>
							
							
							
						</tr>
                        <tr>
							<th>{{trans('graduated-correction-form.lastname')}}</th>
							<td style = "width:80px">{{ $student->last_name }}</td>
							<th colspan="8"> {{ trans('graduated-correction-form.nid') }}</th>
						</tr>
                        <tr>
                           
							<th>{{ trans('graduated-correction-form.grandfather_name') }}</th>
							<td style = "width:80px">{{ $student->grandfather_name }}</td>
                            <th width="70px">{{ trans('graduated-correction-form.dob') }} </th>
							<th width="70px">{{ trans('graduated-correction-form.page') }}</th>
							<th style="width: 130px">{{ trans('graduated-correction-form.cover') }}</th>
							<th style="width: 20px"colspan="3">{{ trans('graduated-correction-form.register_no') }}</th>
							<th style="width: 130px" colspan="2">{{ trans('graduated-correction-form.general_no') }}</th>

						
						</tr>
                        <tr>
							<th>{{trans('graduated-correction-form.school_name')}}</th>
							<td style = "width:80px">{{ $student->school_name }}</td>
                            <td width="70px">{{ $student->birthdate }}</td>
							<td width="70px">{{ $tazkira[2] ?? '' }}</td>
							<td style="width: 130px">{{ $tazkira[0] ?? '' }}</td>
							<td style="width: 20px" colspan="3">{{ $tazkira[1] ?? '' }}</td>
							<td style="width: 130px"colspan="2">{{ $tazkira[3] ?? '' }}</td>
						
						</tr>
                        <tr>
							<th>{{ trans('graduated-correction-form.school_graduation_year1') }}</th>
							<td style = "width:80px">{{ $student->school_graduation_year }}</td>
                            <th colspan="8" style="text-align:center"> {{ trans('graduated-correction-form.correction_info') }}</th>
						</tr>
                        <tr>							
							<th>{{ trans('graduated-correction-form.kankor_id') }}</th>
							<td style = "width:80px;height:30px">{{ $student->form_no }}</td>
                            <td colspan="4"  style="width: 50px;border:none;text-align:right"> <b>موارد مشکل:</b></td>
						
							
						
						</tr>
                      
                     
                        <tr>
                            <th style="width: 120px">{{ trans('graduated-correction-form.university_graduation_year') }}</th>
							<td style = "width:80px"></td>
                            <td rowspan="2" style="width: 50px;border:none" colspan="1"></td>
                            <td rowspan="2" style="width: 100px;border:none" colspan="2"></td>
                            <td rowspan="2" style="width: 100px;border:none;vertical-align:bottom" colspan="2"><b>{{ trans('graduated-correction-form.graduated_student_sign') }}</b></td>
                            <td rowspan="2" style="width: 50px;border:none;vertical-align:bottom" colspan="3">    <h4>{{ trans('general.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; / &nbsp;
                                &nbsp;&nbsp;&nbsp; / </h4></td>
						</tr>
                        <tr>
							<td style="height:30px">{{ trans('graduated-correction-form.phone') }}</td>
							<td> </td>
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
                            <td class="textdir" rowspan="4" width="3%" height="40px">همان روز-1روز</td>
                            <td class="textdir" rowspan="4" width="3%" height="40px"> </td>
                            <td class="textdir" rowspan="4" width="3%" height="40px"> </td>
                        </tr>


                        <tr>

                            <th style="text-align: right;border:none;vertical-align:center;"colspan="6">
                                <span>به معاونیت محترم امور محصلان!</span>
                          

                                <br>
                                <span>ابراز نظر برویت سوابق تحصیلی فارغ التحصیل:</span>



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
                            <td class="textdir" style="text-align: right;width:2%;height:50px" rowspan="2"
                                height="100px" width="2%"><strong> {{ trans('tajeel.total_time') }}</strong></td>
                            <td class="textdir" rowspan="2" style="text-align: right;width:2%;height:50px"><strong>{{ trans('tajeel.receive_date') }}</strong></td>
                            <td class="textdir" rowspan="2" style="text-align: right;width:2%;height:50px"><strong>{{ trans('tajeel.issue_date') }}</strong></td>
                        </tr>
                        <tr>

                            <th style="text-align: left;width:30%;border:none;vertical-align:bottom;padding-bottom: 10px"
                                height="30px"><b>{{ trans('graduated-correction-form.sign_of_lesson_general_manager') }}</b> <br><br>
                            </th>
                            <th style="text-align: right;width:30%;border:none;vertical-align:bottom"
                                height="30px"></th>
                            <th style="text-align: right;border:none;vertical-align:bottom;padding-bottom: 10px"height="30px"><b> {{ trans('graduated-correction-form.sign_and_stamp_of_faculty_dean') }}</b>
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
                                {{ trans('tajeel.deputy_student_affairs') }}

                            </th>
                        </tr>

                        <tr>
                            <td class="textdir" rowspan="4" width="3%" height="80px" >1روز</td>
                            <td class="textdir" rowspan="4" width="3%" height="80px"> </td>
                            <td class="textdir" rowspan="4" width="3%" height="80px"> </td>
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
                                ابراز نظر برویت سوابق تحصیلی فارغ التحصیل / معلومات اخذ شده از مراجع معلومات دهنده:
                            </th>


                        </tr>

                        <tr>

                            <td style="text-align: right;border:none" height="35px"></td>
                            <td style="text-align: right;border:none" height="35px"></td>
                            <td style="text-align: right;border:none" height="35px"></td>

                        </tr>



                        <tr>

                            <td style="text-align: right;border:none"></td>
                            <td style="text-align: right;border:none"></td>
                            <td style="text-align: right;border:none"></td>
                        </tr>
                        <tr>
                            <td class="textdir" rowspan="2" height="120px" width="3%">{{ trans('tajeel.total_time') }}
                               </td>
                            <td class="textdir" rowspan="2" height="120px" width="3%">
                              {{ trans('tajeel.receive_date') }}</td>
                            <td class="textdir" rowspan="2" height="120px" width="3%">
                                {{ trans('tajeel.issue_date') }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;border:none;" height="80px"></td>
                            
                            <th style="text-align: center;border:none;vertical-align:middle;width:60%;margin-left:100px;"height="80px"> 
                              امضاء آمر امور محصلان
                            </th>
                            <td style="text-align: right;border:none;width:200px"height="80px">امضاء و مهر معاون امور محصلان </td>

                        </tr>
                    </table>

                </td>
            </tr>
           <tr>
            <td style="padding:0px">
                <table class="table inner-table">
                    <tr>
                        <th colspan="12" class="bg-grey center">اخذ معلومات از مراجع معلومات دهنده در صورتیکه مشکل در سطح پوهنتون قابل حل نباشد</th>
                    </tr>
                    <tr>
                        <td rowspan="2" colspan="4" style="height: 25px;font-size:13px;width:400px;text-align:right">ارسال مکتوب استعلام عنوانی مراجع معلومات دهنده جهت اخذ معلومات(در صورت لزوم دید)</td>
                        <td rowspan="2" colspan="2" style="height: 25px;font-size:13px;width:20px">شماره و تاریخ <br>صدور </td>
                        <td colspan="2" style="width: 50px"></td>
                        <td rowspan="2" colspan="2" style="height: 25px;font-size:13px"> شماره و تاریخ <br>دریافت</td>
                        <td style="height: 25px;width:50px" colspan="2"></td> 
                    </tr>
                  
                    <tr>
                        <td colspan="2" style="height: 25px"></td>
                        <td colspan="2" style="height: 25px"></td>
                       
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right">ارسال مکتوب عنوانی وزارت تحصیلات عالی جهت آگاهی و تصحیح مشخصات فارغ التحصیل</td>
                        <td colspan="2" style="height: 25px;font-size:13px;width:20px">شماره و تاریخ <br>صدور </td>
                        <td colspan="6"></td>
                    </tr>
                
                </table>

            </td>
           </tr>
           <tr>
            <td style="padding:0px">
                <table class="table inner-table">
                    <tr>
                        <th class="bg-grey center" colspan="14">
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
                        <td style="width:80px">تاریخ دریافت</td>
                        <td colspan="12" style="font-size:13px;text-align:right;border-bottom:none;border-left:none">مدیریت عمومی تدریسی به اساس معلومات معاونیت محترم امور محصلان در زمینه اجراآت نمائید.</td>
                        
                    </tr>
                    <tr>
                        <td style="font-size:13px;text-align:right;border-bottom:none;border-left:none"></td>
                        <th colspan="12" style="border-top:none;height:45px;padding-bottom:14px;vertical-align:top;border-left:none;border-bottom:none">امضای رئیس پوهنحی<br><br></th>
                    </tr>
                  
                </table>
            </td>
           </tr>
           <tr>

            <td rowspan="2"
                style="text-align:right;border-right:none;border-left:none;border-bottom:none;margin-right:5px">
                <br><span>نوت:اصل فورم همرا بایک کاپی مکتوب/استعلام مرجع معلومات دهنده به پوهنحی مربوطه ارسال ویک کاپی فورم در معاونیت امور محصلان حفظ شود. </span>
            </td>
        </tr>
       
         </table>

        </div>
    </div>
</body>

</html>
