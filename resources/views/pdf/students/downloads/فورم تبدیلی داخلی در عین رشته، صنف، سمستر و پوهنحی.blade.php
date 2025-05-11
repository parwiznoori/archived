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

        table,
        td,
        th {
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
            <table class="header_table" style="width:100%;margin-top:-30px">
                <tr>
                   
                    <td style="text-align: right">

                        <h4 style="text-align:right">{{ trans('internal_transfer.register_number') }}:( &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;)</h4>
                        <br>
                        <h4 style="text-align:right">{{ trans('internal_transfer.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
                            &nbsp;&nbsp;&nbsp; / &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; / </h4>

                    </td>
                    <td style="text-align:right;width:9%;vertical-align:top;">
                        <img src="{{ public_path('img/emarat-logo.jpg') }}" style="max-width: 100px;height:100px;vertical-align:top;"/></td>
                        
                    <td style="text-align:center;width:32%;vertical-align:middle;padding-right:10%;padding-top:-44px">
                        {{-- <img src="{{ asset('img/wezarat-logo.jpg') }}" style="max-width: 80px" /> --}}
                      
                            <p style="font-size: 14px;font-weight:bold;padding-bottom:-4px">{{ trans('internal_transfer.islamic_emarat') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('internal_transfer.ministry_title') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('internal_transfer.internal_transfer_form') }}</p>
                     
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
                                <th class="bg-grey center" colspan="10" style="font-size: 14px">
                                    خانه پری توسط محصل
                                </th>
                            </tr>

                            <tr>
                                <th style="width:100px;font-size:13px">{{ trans('internal_transfer.name') }}</th>
                                <td style="width:90px;font-size:13px">{{ $student->getFullNameAttribute() }}</td>
                                <th style="width:100px;font-size:13px">{{ trans('internal_transfer.university') }}</th>
                                <th style="width:100px;font-size:13px">{{ trans('internal_transfer.faculty') }}</th>
                                <th style="width:100px;font-size:13px">{{ trans('internal_transfer.department') }}</th>
                                <th style="width:70px;font-size:13px">{{ trans('internal_transfer.class') }} </th>
                                <th style="width:70px;font-size:13px">{{ trans('internal_transfer.semester') }}</th>
                                <th colspan="3" style="width:70px;font-size:13px">{{ trans('internal_transfer.year') }}</th>


                            </tr>
                            <tr>
                                <th style="font-size:13px">{{ trans('internal_transfer.lastname') }}</th>
                                <td style="width:80px;font-size:13px">{{ $student->last_name }}</td>
                                <td style="width:100px;font-size:13px">{{ $student->university->name }}</td>
                                <td style="width:100px;font-size:13px">{{ $student->department->facultyName->name }}</td>
                                <td style="width:100px;font-size:13px">{{ $student->department->name }}</td>
                                <td style="width: 90px;font-size:13px">{{ $class }}</td>
                                <td style="width:70px;font-size:13px">{{ $student->semester }}</td>
                                <td colspan="3" style="width:70px">
                                    @php
                                    $date1 = Date('Y-m-d');
                                    $jalali_date=explode('-',$date1);
                                    $jDate = \Morilog\Jalali\CalendarUtils::toJalali($jalali_date[0],$jalali_date[1],$jalali_date[2]);
                                    $date=implode('/',$jDate);
                    
                                    $time = Date('h:i:sa')
                                    @endphp
                                     {{$date}}</td>
                            </tr>
                            <tr>
                                <th style="font-size:13px">{{ trans('internal_transfer.father_name') }}</th>
                                <td style="width:80px;font-size:13px">{{ $student->father_name }}</td>
                                <th colspan="8" style=";font-size:13px">{{ trans('internal_transfer.nid') }}</th>
                            </tr>
                            <tr>

                                <th style="font-size:13px">{{ trans('internal_transfer.grandfather_name') }}</th>
                                <td style="width:80px;font-size:13px">{{ $student->grandfather_name }}</td>
                                <th width="70px" style="font-size:13px"> {{ trans('internal_transfer.dob') }}</th>
                                <th width="70px" style="font-size:13px">{{ trans('internal_transfer.page') }}</th>
                                <th style="width: 60px;font-size:13px">{{ trans('internal_transfer.cover') }}</th>
                                <th style="width: 40px;font-size:13px">{{ trans('internal_transfer.register_no') }}</th>
                                <th style="width: 70px;font-size:13px" colspan="4">{{ trans('internal_transfer.general_no') }}</th>


                            </tr>
                            <tr>
                                <th style="font-size:13px">{{ trans('internal_transfer.kankor_year') }}</th>
                                <td style="width:80px;font-size:13px">{{ $student->kankor_year }}</td>
                                <td width="70px;font-size:13px">{{ $student->birthdate }}</td>
                                <td width="70px;font-size:13px">{{ $tazkira[2] ?? '' }}</td>
                                <td style="width: 60px;font-size:13px">{{ $tazkira[0] ?? '' }}</td>
                                <td style="width: 40px;font-size:13px">{{ $tazkira[1] ?? '' }}</td>
                                <td style="width: 70px;font-size:13px"colspan="4">{{ $tazkira[3] ?? '' }}</td>

                            </tr>
                            <tr>
                                <th style="font-size:13px">{{ trans('internal_transfer.kankor_id') }}</th>
                                <td style="width:80px;font-size:13px">{{ $student->form_no }}</td>
                                <th colspan="8" style="text-align:right;font-size:13px">دلیل تبدیلی: </th>
                            </tr>
                            <tr>
                                <th style="font-size:13px">{{ trans('internal_transfer.kankor_score') }}</th>
                                <td style="width:80px;font-size:13px">{{ $student->kankor_score }}</td>
                                <td colspan="8" style="width: 50px;border:none;text-align:right">اینجانب که شهرتم در
                                    فوق ذکر شده، صحت بوده، امید در قسمت تبدیلی ام از بخش روزانه به شبانه
                                    در عین رشته ، صنف، سمستر و پوهنحی بالای مرجع مربوطه هدایت فرموده ممنون سازید. </td>

                            </tr>


                            <tr>
                                <th style="height:20px;font-size:13px">{{ trans('internal_transfer.phone') }}</th>
                                <td style="width:10px;height:20px;font-size:13px"></td>
                                <td style="width: 10px;border:none"></td>
                                <td style="width: 50px;border:none"> </td>
                                <td style="width: 10px;border:none;font-size:13px">امضای محصل</td>
                                <td style="width: 80px;border:none" colspan="3">
                                    <h4>{{ trans('internal_transfer.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; / &nbsp;
                                        &nbsp;&nbsp;&nbsp; / </h4>
                                </td>

                            </tr>
                        </table>
                    </td>

                </tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12" style="font-size: 14px">
                                    {{ trans('internal_transfer.department_dean') }}(&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                                </th>
                            </tr>
                            <tr>
                                <td style="font-size:13px" width="12%" height="25px">
                                   زمان مجموعی</td>
                                <td style="width:85px;font-size:13px"> همان روز-1روز</td>
                                <th colspan="2" style="font-size:13px">قبلا تبدیلی دارد یا خیر </th>
                                <td colspan="3"><input type="radio">
								<label for="">بلی</label>&nbsp;&nbsp;
                                <input type="radio">
								<label for="">نخیر</label>
								</td>
                              
                                <th colspan="2" style="font-size:13px"> شمولیت مجدد دارد</th>
                                
                                <th colspan="3"><input type="radio">
									<label for="">بلی</label>
									<input type="radio">&nbsp;&nbsp;
									<label for="">نخیر</label> </th>
                               

                            </tr>
                            <tr>
                                <td width="12%" style="height: 30px;font-size:13px">
                                    {{ trans('internal_transfer.receive_date') }}</td>
                                <td> </td>
                                <td style="border:none;vertical-align:top;font-size:13px">ابراز نظر: </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>

                            </tr>
                            <tr>
                                <td width="12%" style="height: 30px;font-size:13px">
                                    {{ trans('internal_transfer.issue_date') }}</td>
                                <td> </td>
                                <td style="border:none"> </td>
                                <td style="border:none;vertical-align:bottom;font-size:13px"> امضای مدیرعمومی تدریسی</td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                
                                <td colspan="2" style="border:none;vertical-align:bottom;font-size:13px"> امضاء و مهر رئیس پوهنحی</td>

                            </tr>
               
                      


                      

                       
                        </table>


                    </td>
                </tr>
				<tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12" style="font-size: 14px">
                                فیصله نهای کمسیون تبدیلی ها در سطح پوهنتون
                                </th>
                            </tr>
                            <tr>
                                <td style="font-size:13px"width="12%" height="25px">
                                   زمان مجموعی</td>
                                <td> 1 روز</td>
                         
                               

                            </tr>
                            <tr>
                                <td width="12%" style="height: 25px;font-size:13px">
                                    {{ trans('internal_transfer.receive_date') }}</td>
                                <td> </td>
                        

                            </tr>
                            <tr>
                                <td width="12%" style="height: 25px;font-size:13px">
                                    {{ trans('internal_transfer.issue_date') }}</td>
                                <td> </td>
                             

                            </tr>
               
                      


                      

                       
                        </table>


                    </td>
                </tr>
				<tr>
					<td style="padding: 0">
						<table class="table inner-table">
							<tr>
								<th class="bg-grey center" colspan="6" style="font-size: 14px">شهرت اعضای کمسیون تبدیلی ها در سطح پوهنتون</th>
							</tr>
							<tr>
								<th style="width:9%;font-size:13px">شماره</th>
								<th style="width:26%;font-size:13px">اسم</th>
								<th style="width:35%;font-size:13px">وظیفه</th>
								<th style="width:35%;font-size:13px">موقف اعضای کمسیون</th>
								<th style="width:35%;font-size:13px" colspan="2">محل امضاء و مهر</th>
							</tr>
							<tr>
								<td style="width:9%">1</td>
								<td style="width:26%"></td>
								<td style="width:35%"></td>
								<td style="width:35%;font-size:13px">عضوکمسیون</td>
								<td style="width:35%" colspan="2"></td>
							</tr>
							<tr>
								<td style="width:9%">2</td>
								<td style="width:26%"></td>
								<td style="width:35%"></td>
								<td style="width:35%;font-size:13px">عضوکمسیون</td>
								<td style="width:35%" colspan="2"></td>
							</tr>
							<tr>
								<td style="width:9%">3</td>
								<td style="width:26%"></td>
								<td style="width:35%"></td>
								<td style="width:35%;font-size:13px">عضوکمسیون</td>
								<td style="width:35%" colspan="2"></td>
							</tr>
							<tr>
								<td style="width:9%">4</td>
								<td style="width:26%"></td>
								<td style="width:35%"></td>
								<td style="width:35%;font-size:13px">عضوکمسیون</td>
								<td style="width:35%" colspan="2"></td>
							</tr>
							<tr>
								<td style="width:9%">5</td>
								<td style="width:26%"></td>
								<td style="width:35%"></td>
								<td style="width:35%;font-size:13px">رئیس کمسیون</td>
								<td style="width:35%" colspan="2"></td>
							</tr>
							
					
						</table>
					</td>
				</tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12" style="font-size: 14px">
                                            معاونیت امور محصلان                                </th>
                            </tr>
                            <tr>
                                <td width="12%" height="35px" style="font-size:13px">
                                   زمان مجموعی</td>
                                <td style="width:80px;font-size:13px"> 1 روز</td>
                                <td rowspan="3" colspan="10" style="text-align: right;vertical-align:top;border:none;font-size: 12px">
                                   <b> به ریاست محترم پوهنحی</b>({{ $student->department->facultyName->name }}
                                   )!
                                   <br>
                                    فورم تبدیلی محترم/محترمه ({{ $student->getFullNameAttribute() }}
                                  ) ولد/بنت(
                                 
                                  {{ $student->father_name }}
                                    
                                    )محصل فوق الذکر بعداز فیصله کمسیون تبدیلی ها اجراآت بعدی خدمت شما ارسال است. <br>
                                   <table>
                                    <tr>
                                        <td style="border:none;font-size:13px">
                                            <b>   بااحترام
                                                <br>
                                                امضای معاون امور محصلان</b>
                                        </td>
                                    </tr>
                                   </table>
                                 
                                </td>
                            
                         
                               

                            </tr>
                            <tr>
                                <td width="12%" style="height: 41px;font-size:13px">
                                    {{ trans('internal_transfer.receive_date') }}</td>
                                <td> </td>
                        

                            </tr>
                            <tr>
                                <td width="12%" style="height: 40px;font-size:13px">
                                    {{ trans('internal_transfer.number_and_issue_date') }}</td>
                                <td> </td>
                             

                            </tr>
               
                        </table>


                    </td>
                </tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12" style="font-size: 14px">
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
                                <td width="12%" height="25px" style="font-size:13px">
                                   زمان مجموعی</td>
                                <td style="width:80px;font-size:13px">همان روز- 1روز</td>
                                <td colspan="10" rowspan="3" style="text-align: right;vertical-align:top">مدیرت محترم عمومی تدریسی در زمینه اجراآت اصولی نمائید. 
                                    <br>
                                    <table>
                                        <tr>
                                            <td style="border:none;font-size:13px"><b>امضای رئیس پوهنحی</b></td>
                                        </tr>
                                    </table>

                                </td>

                         
                               

                            </tr>
                            <tr>
                                <td colspan="2" width="80px" style="height: 30px;font-size:13px"> {{ trans('internal_transfer.number_and_recieve_date') }}  
                                   </td>
                                
                        

                            </tr>
                            <tr>
                                <td colspan="2" width="80px" style="height: 30px">
                                   </td>
                            </tr>
                        </table>


                    </td>
                </tr>
                <tr>
                    <td style="text-align: right;border:none;width:100%" colspan="3">نوت: اصل فورم به ریاست پوهنحی مربوطه ارسال  و یک کاپی فورم در معاونیت امور محصلان حفظ شود.</td>
                </tr>
                       


            </table>

        </div>
    </div>
</body>

</html>
