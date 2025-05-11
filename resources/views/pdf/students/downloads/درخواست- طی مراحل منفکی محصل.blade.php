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
@endphp
    <div class="page">
        <div class="center">
    
			<table class="header_table" style="width:100%;margin-top:-60px">
                <tr>
                    <td style="text-align:right;width:33%;vertical-align:top;text-align:left">
                        <img  src="{{ public_path('img/emarat-logo.jpg') }}" style="max-width: 100px;text-align:right;height:170px"/>
                 
                    </td>

                    <td style="text-align:center;width:38%;vertical-align:middle;font-weight:bold">

                        {{-- {{ asset('img/wezarat-logo.jpg') }} --}}
                        <p style="margin-top:5px;font-size: 14px;vertical-align:middle">{{ trans('drop-out.islamic_emarat') }}</p>
                        <p style="margin-top:5px; vertical-align:middle">{{ trans('drop-out.ministry_title') }}</p>
                        <p style="margin-top:5px; vertical-align:middle">فورم طی مراحل منفکی محصل</p>
                    </td>
                    <td style="text-align:right;width:33%;vertical-align:top;">
                        {{-- <img src="{{ file_exists($student->photo_url) ? asset($student->photo_url) : asset('img/avatar-placeholder.png') }}"
                            style="max-width: 90px;height:90px"> --}}
                            <img src="{{ public_path('img/wezarat-logo.jpg') }}" style="max-width: 80px;text-align:right;height:80px"/>
                          
                            
                    </td>
                </tr>
                <tr>
                    <td style="width:33%;vertical-align:bottom">
                        <br>
                        <h4 style="text-align:right">{{ trans('drop-out.sequnce_number') }}:( &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;)</h4>
                    </td>
                    <td style="width:38%">

                    </td>
                    <td style="width:33% ;vertical-align:bottom">
                        <br>
                        
                        <h4 style="text-align:right;padding-left:5px">{{ trans('drop-out.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
                            &nbsp;&nbsp;&nbsp; / &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; / </h4>
                    </td>
                </tr>
                
            </table>
            <table class="table" style="margin-top: 20px">
                <tr>
                    <td style="padding: 0">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="10">
                                   شهرت محصل متقاضی
                                </th>

                            </tr>

                            <tr>
                                <th style="width:80px">{{ trans('drop-out.name') }}</th>
                                <td style="width:100px">{{ $student->name}}</td>
                                <td style="width:100px"><b>نام لیسه</b></td>
                                <td style="width:100px">{{ $student->school_name }}</td>
                                <td colspan="2" style="width:100px"><b>سکونت اصلی</b></td>
								<td style="width:70px" rowspan="5" colspan="4"> <img src="{{ public_path($student->photo_relative_path()) }}"
                                    style="max-width: 120px;height:180px"></td>
                          


                            </tr>
                            <tr>
                                <th>{{ trans('drop-out.lastname') }}</th>
                                <td style="width:80px">{{ $student->last_name }}</td>
                                <td style="width:100px"><b>سال شمولیت در پوهنتون</b></td>
                                <td style="width:100px">{{$student->kankor_year}}</td>
                                <td style="width: 90px"><b>قریه/گذر</b></td>
                                <td style="width:70px">{{ $student->village }}</td>
                              
                                


                            </tr>
                            <tr>
                                <th>{{ trans('drop-out.father_name') }}</th>
                                <td style="width:80px">{{ $student->father_name }}</td>
                                <td><b>سمسترفعلی</b></td>
                                <td>{{ $student->semester }}</td>
                                <td><b>ناحیه</b></td>
                                <td></td>
                            </tr>
                            <tr>

                                <th>{{ trans('drop-out.grandfather_name') }}</th>
                                <td style="width:80px">{{ $student->grandfather_name }}</td>
                                <td width="70px"><b>دیپارتمنت</b> </td>
                                <td width="70px;font-size:10px">{{ $student->department->name }}</td>
                                <td style="width: 60px"><b>ولسوالی</b></td>
                                <td style="width: 40px">{{ $student->district }}</td>
                             


                            </tr>
                            <tr>
                                <th>{{ trans('drop-out.gender') }}</th>
                                <td style="width:80px">{{ $student->gender }}</td>
                                <td width="70px"><b>پوهنحی</b></td>
                                <td width="70px;font-size:10px">{{ $student->department->facultyName->name }}</td>
                                <td style="width: 60px"><b>ولایت</b></td>
                                <td style="width: 40px">{{ $student->originalProvince ? $student->originalProvince->name : '' }}</td>
                               {{-- <div></div> --}}

                            </tr>
                            <tr>
                                <th>{{ trans('drop-out.kankor_id') }}</th>
                                <td style="width:80px">{{ $student->form_no }}</td>
                                <th colspan="8" style="text-align:center">تذکره تابعیت </th>
                            </tr>
                            <tr>
                                <th >{{ trans('drop-out.kankor_score') }}</th>
                                <td style="width:80px">{{ $student->kankor_score }}</td>
                                <th style="width: 50px;text-align:center">صفحه </th>
                                <th style="width: 50px;text-align:center"> جلد</th>
                                <th style="width: 50px;text-align:center">نمبرثبت </th>
                                <th style="width: 50px;text-align:center" colspan="2"> نمبرعمومی</th>
                                <th colspan="3" style="width: 50px;text-align:center">تاریخ تولد </th>

                            </tr>
							
                            <tr>
                                <th>{{ trans('drop-out.school_graduation_year1') }}</th>
                                <td style="width:80px">{{ $student->school_graduation_year }}</td>
                                <td style="width: 50px;">{{ $tazkira[2] ?? '' }}</td>
                                <td style="width: 50px;"> {{ $tazkira[0] ?? '' }} </td>
                                <td style="width: 50px;"> {{ $tazkira[1] ?? '' }} </td>
                                <td style="width: 50px;" colspan="2"> {{ $tazkira[3] ?? '' }} </td>
                                <td colspan="3" style="width: 50px;">
                                   {{ $student->birthdate }}
                                </td>

                            </tr>
							<tr>
								<th colspan="10" style="text-align:right;border:none">دلایل منفکی: </th>
							</tr>
							<tr>
								<td colspan="4" style="width: 50px;border:none;text-align:right;height: 100px"> </td>
								<td style="height: 100px;border:none"></td>
								<th colspan="5" style="text-align: right;border:none;height: 100px;vertical-align:bottom">محل امضای محصل متقاضی</th>
							</tr>
							
						
                        </table>
                    </td>

                </tr>
				<tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12">
                                    {{ trans('drop-out.department_dean') }}(&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                                </th>
                            </tr>
                            <tr>
                                <td width="12%" height="50px">
                                   زمان مجموعی</td>
                                <td style="width:85px;height:40px"> همان روز</td>
								<td style="border:none;vertical-align:top"> </td>
                              
                               

                            </tr>
                            <tr>
                                <td width="12%" style="height: 50px">
                                    {{ trans('tajeel.receive_date') }}</td>
                                <td> </td>
                                <td style="border:none;vertical-align:top"> </td>
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
                                <td width="12%" style="height: 50px">
                                    {{ trans('drop-out.issue_date') }}</td>
                                <td> </td>
                                <td style="border:none"> </td>
                                <th style="border:none;vertical-align:bottom">  تائید و امضای مدیر ع تدریسی</th>
                                <td style="border:none"> </td>
								<th colspan="2" style="border:none;vertical-align:bottom"> امضاء و مهر رئیس پوهنحی</th>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
								
                                <td style="border:none"> </td>
								
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
                                
                                

                            </tr>
               
                      


                      

                       
                        </table>


                    </td>
                </tr>
				<tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12">
                                    {{ trans('drop-out.university_dean') }}(&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                                </th>
                            </tr>
                            <tr>
                                <td width="12%" height="50px">
                                   زمان مجموعی</td>
                                <td style="width:85px;">1 روز</td>
								<td style="border:none;vertical-align:top;text-align:right"> منفکی محصل تائید و منظور است.</td>
                              
                               

                            </tr>
                            <tr>
                                <td width="12%" style="height:50px">
                                    {{ trans('tajeel.receive_date') }}</td>
                                <td> </td>
                                <td style="border:none;vertical-align:top"> </td>
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
                                <td width="12%" style="height:50px">
                                    {{ trans('drop-out.issue_date') }}</td>
                                <td> </td>
                                <td style="border:none"> </td>
                                <td style="border:none;vertical-align:bottom">  </td>
                                <td style="border:none"> </td>
								<th colspan="2" style="border:none;vertical-align:bottom;text-align:right"> امضاء و مهر رئیس پوهنتون</th>
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
								
                                <td style="border:none"> </td>
                                <td style="border:none"> </td>
								
                                <td style="border:none"> </td>
                                
                               

                            </tr>
               
                      


                      

                       
                        </table>


                    </td>
                </tr>
				<tr>
					<td style="text-align: right;border:none">
						نوت: آمریت امور محصلان پوهنتون مربوطه مکلف است اصل فورم طی مراحل منفکی محصل را بعداز منظوری رئیس پوهنتون ذریعه مکتوب به پوهنحی مربوطه وکاپی فورم را به (ریاست تدریسات ثانوی وزارت معارف و اداره ملی امتحانات)غرض آگاهی ارسال نماید و همچنان معلومات منفکی محصل را در سیستم الکترونیکی (HEMIS) نیز درج نماید.
					</td>
				</tr>
			</table>

        </div>
    </div>
</body>

</html>
