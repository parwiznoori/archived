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
    $class=ceil($student->semester/2);
@endphp
    <div class="page">
        <div class="center">
            <table class="header_table" style="width:100%;margin-top:-50px">
                <tr>
                   
                    <td style="text-align: right">

                        <h4 style="text-align:right">{{ trans('correction-form.number') }}:( &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;)</h4>
                        <br>
                        <h4 style="text-align:right">{{ trans('correction-form.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
                            &nbsp;&nbsp;&nbsp; / &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; / </h4>

                    </td>
                    <td style="text-align:right;width:9%;vertical-align:top;">
                        <img src="{{ public_path('img/emarat-logo.jpg') }}" style="max-width: 100px;height:100px;vertical-align:top;"/></td>
                        
                    <td style="text-align:center;width:32%;vertical-align:middle;padding-right:10%;padding-top:-44px">
                        {{-- <img src="{{ asset('img/wezarat-logo.jpg') }}" style="max-width: 80px" /> --}}
                      
                            <p style="font-size: 14px;font-weight:bold;padding-bottom:-4px">{{ trans('correction-form.islamic_emarat') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('correction-form.ministry_title') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('correction-form.personal_info_correction') }}</p>
                     
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
                                خانه پری توسط محصل
                            </th>
        
                        </tr>
					
						<tr>
							<th style = "width:100px">{{trans('correction-form.name')}}</th>
                            <th style="width:  100px">{{ $student->name }}</th>
							{{-- <td style="width:100px">{{ $student->getFullNameAttribute() }}</td> --}}
							<th style="width:100px">{{trans('correction-form.university')}}</th>
							<th style="width:100px">{{trans('correction-form.faculty')}}</th>
							<th style="width:100px">{{trans('correction-form.department')}}</th>
							<th style="width:70px">{{trans('correction-form.class')}} </th>
							<th style="width:70px">{{trans('correction-form.semester')}}</th>
							<th colspan="3" style="width:70px">سال</th>
							
							
						</tr>
                        <tr>
							<th>{{trans('correction-form.father_name')}}</th>
							<td style = "width:80px">{{ $student->father_name }}</td>
                            <td style="width:100px">{{$student->university->name}}</td>
							<td style="width:100px">{{$student->department->facultyName->name}}</td>
							<td style="width:100px">{{$student->department->name}}</td>
							<td style="width: 90px">{{ $class }}</td>
							<td  style="width:70px">{{$student->semester}}</td>
							<td colspan="3" style="width:70px"></td>
							
							
						</tr>
                        <tr>
							<th>{{trans('correction-form.lastname')}}</th>
							<td style = "width:80px">{{ $student->last_name }}</td>
							<th colspan="8">تذکره تابعیت</th>
						</tr>
                        <tr>
                           
							<th>{{trans('correction-form.grandfather_name')}}</th>
							<td style = "width:80px">{{ $student->grandfather_name }}</td>
                            <th width="70px">سال تولد</th>
							<th width="70px">صفحه</th>
							<th style="width: 60px">جلد</th>
							<th style="width: 40px"colspan="3">نمبرثبت</th>
							<th style="width: 70px" colspan="2">نمبرعمومی</th>

						
						</tr>
                        <tr>
							<th>{{trans('correction-form.school_name')}}</th>
							<td style = "width:80px">{{ $student->school_name }}</td>
                            <td width="70px">{{ $student->birthdate }}</td>
							<td width="70px">{{ $tazkira[2] ?? '' }}</td>
							<td style="width: 60px">{{ $tazkira[0] ?? '' }}</td>
							<td style="width: 40px" colspan="3">{{ $tazkira[1] ?? '' }}</td>
							<td style="width: 70px"colspan="2">{{ $tazkira[3] ?? '' }}</td>
						
						</tr>
                        <tr>
							<th>{{trans('correction-form.school_graduation_year')}}</th>
							<td style = "width:80px">{{ $student->school_graduation_year }}</td>
                            <th colspan="8" style="text-align:center">معلومات اصلاحی</th>
						</tr>
                        <tr>							
							<th style="width:80px">{{trans('correction-form.kankor_id')}}</th>
							<td style = "width:80px">{{ $student->form_no }}</td>
                            <th colspan="1" style="width: 50px">مورد اشتباه</th>
							<th colspan="2" style="width: 100px">اشتباه که صورت گرفته</th>
							<th colspan="2" style="width: 100px">تصحیح که صورت گیرد</th>
							<th style="width: 50px" colspan="3">امضای محصل</th>
							
						
						</tr>
                      
                     
                        <tr>
                            <th>{{trans('correction-form.kankor_year')}}</th>
							<td style = "width:80px">{{ $student->kankor_year }}</td>
                            <td rowspan="2" style="width: 50px" colspan="1"></td>
                            <td rowspan="2" style="width: 100px" colspan="2"></td>
                            <td rowspan="2" style="width: 100px" colspan="2"></td>
                            <td rowspan="2" style="width: 50px" colspan="3"></td>
						</tr>
                        <tr>
							<th>{{trans('correction-form.phone')}}</th>
							<td> {{ $student->phone }}</td>
						</tr>
                       
					</table>
				</td>
			
            </tr>
            <tr>
                <td style="padding:0px">
                    <table class="table inner-table">
                        <tr>
                            <th class="center" colspan="12">
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
                            <td class="textdir" rowspan="4" width="3%" height="45px">همان روز-1روز</td>
                            <td class="textdir" rowspan="4" width="3%" height="45px"> </td>
                            <td class="textdir" rowspan="4" width="3%" height="45px"> </td>
                        </tr>


                        <tr>

                            <th style="text-align: right;border:none;vertical-align:center;"colspan="6">
                                <span>به معاونیت محترم امور محصلان!</span>
                          

                                <br>
                                <span>ابراز نظر برویت سوابق تحصیلی محصل:</span>



                            </th>
                            <td class="center" style="text-align: right;border:none;height:25px">
                            </td>
                            <td class="center" style="text-align: right;border:none;height:25px">
                            </td>

                        </tr>

                        <tr>

                            <td style="text-align: right;border:none" height="45px"></td>
                            <td style="text-align: right;border:none" height="45px"></td>
                            <td style="text-align: right;border:none" height="45px"></td>

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
                                height="100px">امضای مدیر عمومی تدریسی <br><br>
                            </th>
                            <th style="text-align: right;width:30%;border:none;vertical-align:bottom"
                                height="25px"></th>
                            <th style="text-align: right;border:none;vertical-align:bottom;padding-bottom: 10px"height="40px">امضاء و
                                مهر رئیس پوهنحی <br><br>
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
                            <td class="textdir" rowspan="4" width="3%" height="90px" >1روز</td>
                            <td class="textdir" rowspan="4" width="3%" height="90px"> </td>
                            <td class="textdir" rowspan="4" width="3%" height="90px"> </td>
                        </tr>


                        <tr>

                            <th style="text-align: right;border:none;vertical-align:top" colspan="6">
                                به ریاست محترم پوهنحی({{ $student->department->facultyName->name }})!
                                <br>
                               ابراز نظر برویت سوابق تحصیلی محصل / معلومات اخذ شده از مراجع معلومات دهنده:
                            </th>


                        </tr>

                        <tr>

                            <td style="text-align: right;border:none" height="55px"></td>
                            <td style="text-align: right;border:none" height="55px"></td>
                            <td style="text-align: right;border:none" height="55px"></td>

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

                            <td style="text-align: center;border:none;" height="55px"></td>
                            
                            <th style="text-align: center;border:none;vertical-align:middle;width:60%;margin-left:100px;"height="80px"> 
                              امضاء آمر امور محصلان
                            </th>
                            <td style="text-align: right;border:none;width:25%"height="55px">امضاء و مهر معاون امور محصلان</td>

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
                        <td rowspan="2" colspan="4" style="height: 25px;font-size:16px;width:400px;text-align:right">ارسال مکتوب/ استعلام عنوانی مراجع معلومات دهنده جهت اخذ معلومات(در صورت ضرورت و لزوم دید)</td>
                        <td rowspan="2" colspan="2" style="height: 25px;font-size:12px;width:35px">شماره و تاریخ <br>صدور </td>
                        <td colspan="2" style="width:65px"></td>
                        <td rowspan="2" colspan="2" style="height: 25px;font-size:12px;width:35px"> شماره و تاریخ <br>دریافت</td>
                        <td style="height: 25px;width:65px" colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="height: 25px"></td>
                        <td colspan="2" style="height: 25px"></td>
                       
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
                        <th colspan="12" style="border-top:none;height:50px;padding-bottom:13px;vertical-align:top;border-left:none;border-bottom:none">امضای رئیس پوهنحی<br><br></th>
                    </tr>
                  
                </table>
            </td>
           </tr>
            
           <tr>
            <td rowspan="2"
                style="text-align:right;border-right:none;border-left:none;border-bottom:none;margin-right:5px">
                <br><span>نوت:اصل فورم همرا بایک کاپی مکتوب / استعلام مرجع معلومات دهنده به پوهنحی مربوطه ارسال ویک کاپی فورم در معاونیت امور محصلان حفظ شود. </span>
            </td>
        </tr>
       
         </table>

        </div>
    </div>
</body>

</html>
