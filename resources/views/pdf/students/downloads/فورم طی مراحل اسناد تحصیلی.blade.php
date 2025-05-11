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
            margin-bottom: 0;
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .page {
            width: 210mm;
            min-height: 310mm;
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
    
			<table class="header_table" style="width:100%;margin-top:-50px">
                <tr>
                    <td style="text-align:right;width:33%;vertical-align:top;text-align:left">
                        <img src="{{ public_path('img/emarat-logo.jpg') }}" style="max-width: 100px;height:100px;text-align:left"/>
                        <br>
                        <table>
                            <tr>
                                <td colspan="6">
                                    <h4 style="text-align:right">{{ trans('stages_academic_documents.sequnce_number') }}( &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; 
                                        &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;)</h4>
                                </td>
                            </tr>
                        </table>
               
                    </td>

                    <td style="text-align:center;width:38%;vertical-align:middle;font-weight:bold">

                        <p style="margin-top:5px;font-size: 14px;vertical-align:middle">{{ trans('stages_academic_documents.islamic_emarat') }}</p>
                        <p style="margin-top:5px; vertical-align:middle">{{ trans('stages_academic_documents.ministry_title') }}</p>
                        <p style="margin-top:5px; vertical-align:middle">{{ trans('stages_academic_documents.university') }}({{ $student->university->name }})</p>
                        <p style="margin-top:5px; vertical-align:middle">{{ trans('stages_academic_documents.stages_academic_documents') }}</p>
                    </td>
                    <td style="text-align:right;width:33%;vertical-align:top;">
                            <img src="{{ public_path('img/wezarat-logo.jpg') }}" style="max-width: 80px;text-align:right;height:120px"/>
                            <br>
                            <table>
                                <tr>
                                    <td colspan="4"><h4 style="text-align:left;">{{ trans('general.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
                                        &nbsp;&nbsp;&nbsp; / &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; / </h4></td>
                                </tr>
                            </table>
                    </td>
                </tr>
            </table>
            <table class="table" style="margin-top: 10px;">
                <tr>
                    <td style="padding: 0">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="9" style="height: 25px;font-size:16px">
                                   شهرت متقاضی
                                </th>

                            </tr>

                            <tr>
                                <th style="width:100px">{{ trans('stages_academic_documents.name') }}</th>
                                <td style="width:130px">{{ $student->name }}</td>
                                <th colspan="3" style="width:130px">{{ trans('stages_academic_documents.permanent_addr') }}</th>
                                <th colspan="3" style="width:130px">{{ trans('stages_academic_documents.current_addr') }} </th>
                                <td rowspan="5" style="width:150px">  <img src="{{ public_path($student->photo_relative_path()) }}" style="width: 120px;height:140px"></td>
                            </tr>
                            <tr>
                                <th>{{ trans('stages_academic_documents.lastname') }}</th>
                                <td style="width:80px">{{ $student->last_name }}</td>
                                <th style="width:40px">{{ trans('stages_academic_documents.village') }}</th>
                                <td colspan="2" style="width:120px">{{ $student->village }} </td>
                            
                            
                                <th style="width: 40px">{{ trans('stages_academic_documents.village') }}</th>
                                <td colspan="2" style="width:120px">{{ $student->village_current }}</td>
                               
                                
                            </tr>
                            <tr>
                                <th>{{ trans('stages_academic_documents.father_name') }}</th>
                                <td style="width:80px;height:25px">{{ $student->father_name }}</td>
                            <th>{{ trans('stages_academic_documents.nahia') }}</th>
                            <td colspan="2"></td>
                            <th>{{ trans('stages_academic_documents.nahia') }}</th>
                            <td colspan="2"></td>
                                
                            </tr>
                            <tr>

                                <th style="height: 25px">{{ trans('stages_academic_documents.grandfather_name') }}</th>
                                <td style="width:80px;height:25px">{{ $student->grandfather_name }}</td>
                                <th style="width:80px">{{ trans('stages_academic_documents.district') }}</th>
                                <td colspan="2">{{ $student->district }}</td>
                                <th style="width:100px">{{ trans('stages_academic_documents.district') }}</th>
                                <td colspan="2">{{ $student->district_current }}</td>
                                
                               
                                
                            
                             


                            </tr>
                            <tr>
                                <th style="height: 25px">{{ trans('stages_academic_documents.gender') }}</th>
                                <td style="width:80px">{{ $student->gender }}</td>
                                <th style="width:80px;height:25px">ولایت</th>
                                <td colspan="2">{{ $student->originalProvince ? $student->originalProvince->name : '' }}</td>
                                <th style="width:100px;height:25px">ولایت</th>
                                <td colspan="2" style="height:25px">{{ $student->currentProvince ? $student->currentProvince->name : '' }}</td>
                                
								
                            </tr>
                            <tr>
                                <th style="height:25px">{{ trans('stages_academic_documents.degree') }}</th>
                                <td style="width:80px">{{ $student->grade->name }}</td>
                                
								<td colspan="8" rowspan="3" style="padding: 0">
									<table>
                                        <tr>
                                            <th colspan="6" style="text-align:center;height:30px;border-top:none">{{ trans('stages_academic_documents.nid') }}  </th>
                                        </tr>
										<tr>
											
											<th style="width: 120px;height:30px;text-align:center;border-top:none">{{ trans('stages_academic_documents.page') }} </th>
											<th style="width: 120px;text-align:center;border-top:none"> {{ trans('stages_academic_documents.cover') }}</th>
											<th style="width: 120px;text-align:center;border-top:none">{{ trans('stages_academic_documents.register_no') }} </th>
											<th style="width: 120px;text-align:center;border-top:none"> {{ trans('stages_academic_documents.general_no') }}</th>
                                            <th style="width: 160px;text-align:center;border-top:none" colspan="2">{{ trans('stages_academic_documents.dob') }}  </th>
										</tr>
										<tr>
											
											<td style="height: 30px">{{ $tazkira[2] ?? '' }}</td>
											<td>{{ $tazkira[0] ?? '' }}</td>
											<td>{{ $tazkira[1] ?? '' }}</td>
											<td>{{ $tazkira[3] ?? '' }}</td>
                                            <td colspan="2">{{ $student->birthdate }}</td>
										</tr>
									</table>
								</td>
						
                                
                            </tr>
                            <tr>
                                <th style="height:25px">{{ trans('stages_academic_documents.phone') }}</th>
                                <td style="width:80px;height:25px">{{ $student->phone }}</td>
                            </tr>
							
                            <tr>
                                <th style="width:70px;height:25px">{{ trans('stages_academic_documents.email') }}</th>
                                <td style="width:100px;word-wrap: break-word;height:25px;font-size:12px">{{ $student->email }}</td>
						
                            </tr>
						
                        </table>
                    </td>

                </tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="10" style="height:25px">
                              مشخصات تحصیلی متقاضی
                                </th>
                            </tr>
                            <tr>
                                <td colspan="2" style="height:25px">تبدیلی(در صورت موجودیت تبدیلی اسناد ضمیمه شود )</td>
                                <th style="height:25px">{{ trans('stages_academic_documents.enrollment_year') }}</th>
                                <th style="height:25px">{{ trans('stages_academic_documents.graduated_year') }}</th>
                                <th style="height:25px">{{ trans('stages_academic_documents.faculty') }}</th>
                                <th style="height:25px">{{ trans('stages_academic_documents.field_of_study') }}</th>
                                <th colspan="2" style="height:25px;font-size:12px">مونوگراف/ پایان نامه را خویش را دفاع نموده؟</th>
                                <th style="height:25px"> {{ trans('stages_academic_documents.kankor_id') }} </th>
                                <th style="height:25px">{{ trans('stages_academic_documents.kankor_type') }}</th>
                            </tr>
                            <tr>
                                <td style="height:25px"><input type="radio"></td>
                                <td style="height:25px"><input type="radio"></td>
                                <td style="height: 25px">{{ $student->kankor_year }}</td>
                                <td>
                                    {{ $student->graduatedStudents ? $student->graduatedStudents->graduated_year : '' }}</td>

                                {{-- @php
                                    dd($student->graduatedStudents());
                                @endphp --}}
                                
                                <td style="height:25px">{{ $student->department->facultyName->name }}</td>
                                <td style="height:25px">{{ $student->department->name }}</td>
                                <td style="height:25px">
                                    <label>بلی</label>
                                    <input type="radio">
                                </td>
                                <td style="height:25px">
                                    <label>نخیر</label>
                                    <input type="radio">
                                </td>
                                
                                <td style="height:25px">{{ $student->form_no }}</td>
                                <td style="height:25px">{{ $student->student_sheft }}</td>

                            </tr>
                  
                        </table>


                    </td>
                </tr>
                <tr>
                    <td style="padding: 0">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" style="height:25px" colspan="7">{{ trans('stages_academic_documents.personal_info_in_english') }}</th>
                            </tr>
                            <tr>
                                <th style="height: 25px;width:150px">Department</th>
                                <th style="height: 25px;width:100px">Graduation Year</th>
                                <td style="height: 25px;width:100px">Admission Year</td>
                                <th style="padding: 0;height:35px">
                                    <table>
                                        <tr>
                                            <td style="width:50px;border:none">
                                                Date Of Birth
                                            </td>
                                        </tr>
                                    </table>
                                </th>
                                <th style="height: 25px;width:125px">Father's name</th>
                                <th style="height: 25px;width:125px">Last Name</th>
                                <th style="height: 25px;width:125px">Name</th>
                            </tr>
                            <tr>
                                <td style="height: 30px">{{ $student->department->department_eng }}</td>
                                <td style="height:30px">{{ $student->graduatedStudents ? $student->graduatedStudents->graduated_year + 621 : '' }}</td>
                                <td style="height:30px">{{ $student->kankor_year ? $student->kankor_year + 621 : 'N/A' }}</td>
                                <td style="padding: 0">
                                    <table>
                                        <tr>
                                            @php
                                            if($student->birthdate !=null) {
                                                $birth_date_array = explode('/',$student->birthdate);
                                                $birth_date =  intval($birth_date_array[0]) + 621; 
                                            }
                                            else 
                                            {
                                                $birth_date = ''; 
                                            }
                                                
                                            @endphp
                                            <td style="border: none;width:50px">{{  $birth_date }}</td>
                                            
                                        </tr>
                                    </table>
                                </td>
                                <td style="height:30px;border-top:none">{{ $student->father_name_eng }}</td>
                                <td style="height:30px;border-top:none">{{ $student->last_name_eng }}</td>
                                <td style="height:30px;border-top:none">{{ $student->name_eng }}</td>
                               
                               
                              
                            </tr>
                            <tr>
                                <td colspan="7" style="height:40px;padding:0px">
                                <table class=" table inner-table">
                                    <tr>
                                        <td style="border: none;font-size:12px;text-align:right;width:200px;font-weight:bold"><label>نوعیت اسناد تحصیلی که متقاضی خواهان اخذ آن است: &nbsp; دیپلوم</label>
                                            <input type="radio"></td>
                                        <td style="border: none;font-size:12px;text-align:right;width:130px;font-weight:bold"><label>&nbsp;&nbsp;ترانسکریپ</label>
                                            <input type="radio"></td>
                                        <td style="border: none;font-size:12px;text-align:right;width:130px;font-weight:bold"><label>سرتیفکیت&nbsp;&nbsp;</label>
                                            <input type="radio"></td>
                                        <td style="border: none;font-size:12px;text-align:right;width:180px;font-weight:bold">محل امضای متفاضی</td>
                                    </tr>
                                </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                    
				<tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="4" style="height: 25px">
                                    {{ trans('stages_academic_documents.department_dean') }}(&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                                </th>
                            </tr>
                            <tr>
                                <td width="12%" style="height:35px">
                                   زمان مجموعی</td>
                                <td style="width:30px;height:25px">همان روز</td>
                                <td style="width:480px;text-align:right;vertical-align:top;border-bottom:none;height:30px;font-size:18px" rowspan="2">
                                محترم&nbsp;{{ $student->name }}&nbsp; از دیپارتمنت&nbsp;({{ $student->department->name }})&nbsp; پوهنحی &nbsp;({{ $student->department->facultyName->name }})&nbsp; بخش&nbsp;({{ $student->department->name }})&nbsp; به سویه ({{ $student->grade->name }}) فارغ و مونوگراف / پایان نامه خویش را تحت عنوان ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) تحت رهنمایی استاد رهنما محترم ( &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) 
                                به تاریخ ( &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) دفاع نموده و مورد تائید دیپارتمنت نیز قرار گرفته است. موضوغ فوقآ نگاشته شد بعد از پرداخت قیمت اسناد تحصیلی، درقسمت تهیه آن اقدام صورت گیرد.
                                </td>
                                <td style="width:120px;text-align:center;height:25px">اصلاح شهرت / سال تولد متقاضی(درصورتیکه سهوآ درج سوابق تحصیلی شده باشد)</td>
							
								
                              
                               

                            </tr>
                            <tr>
                                <td width="12%" style="height: 25px">
                                    {{ trans('stages_academic_documents.receive_date') }}</td>
                                <td> </td>
                               
                                <td style="text-align: right;height:25px">از:</td>
                    

                            </tr>
                            <tr>
                                <td width="12%" style="height: 25px">
                                    {{ trans('stages_academic_documents.issue_date') }}</td>
                                <td style="height:25px"> </td>
                                <th style="text-align:right;border-top:none;height:25px"> تائید و امضای مدیر ع تدریسی پوهنحی &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; امضاء و مهر رئیس پوهنحی</th>
                               
                           
                              
                             
                                <td style="text-align: right;height:25px">به:</td>
                            

                            </tr>
  
                       
                        </table>


                    </td>
                </tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="3" style="height: 25px">
                                  اجراآت بخش مالی
                                </th>
                                <th class="bg-grey center" colspan="3" style="height:25px">
                                  اجراآت بخش تهیه اسناد تحصیلی
                                </th>
                                <th class="bg-grey center" colspan="3" style="height:25px">
                                  اجراآت پوهنحی
                                </th>
                           
                            </tr>
                            <tr>
                                <td style="height: 25px">زمان مجموعی</td>
                                <td style="height:25px">تاریخ دریافت</td>
                                <td style="height:25px">تاریخ صدور</td>
                                <td style="height:25px">زمان مجموعی</td>
                                <td style="width:83px">تاریخ دریافت</td>
                                <td style="width:83px">تاریخ صدور</td>
                                <td style="height:25px">زمان مجموعی</td>
                                <td style="height:25px">تاریخ دریافت</td>
                                <td style="height:25px">تاریخ صدور</td>
                              
                            </tr>
                            <tr>
                                <td style="height:25px">همان روز</td>
                                <td style="height:25px"></td>
                                <td style="height:25px"></td>
                                <td style="height:25px">1 روز</td>
                                <td style="height:25px"></td>
                                <td style="height:25px"></td>
                                <td style="height:25px">همان روز</td>
                                <td style="height:25px"></td>
                                <td style="height:25px"></td>
                            </tr>
                            <tr>
                                <td colspan="3" style="height:80px;text-align:right;vertical-align:top;width:80px;border-bottom:none">مبلغ ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;) افغانی بابت( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;) اخذگردید تائیداست.
                             
                              
                                </td>
                                <td colspan="3" style="height:80px;text-align:right;vertical-align:top;width:70px;border-bottom:none">اسناد تحصیلی ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;)  مطابق مشخصات درج فورم و سوابق تحصیلی متقاضی ترتیب
                                     گردید صحت است.
                               

                                
                                </td>
                                <td colspan="3" style="height:80px;text-align:right;vertical-align:top;width:150px;border-bottom:none">شماره مسلسل دیپلوم: (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                  
                                   ) <br>
                                    شماره مسلسل سرتیفکت: (
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br>
                                    شماره مسلسل ترانسکرپت: (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br>
                                    شماره مسلسل صفحه کتاب فراغت: (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br> 
                                    اوسط نمرات ترانسکرپت: (&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br>
                                   
                                </td>
                                <tr>
                                    <td colspan="3" style="text-align: center;border-top:none;height:25px">  <p style="text-align: center"><b>امضاء و مهر بخش مالی پوهنتون</b></p></td>
                                    <td colspan="3" style="text-align: right;border-top:none;height:25px"><p><b>تائید و امضای مسؤل تهیه اسناد تحصیلی پوهنتون</b></p></td>
                                    <td colspan="3" style="text-align: center; border-top:none;height:25px"> <p><b>امضای مدیر ع تدریسی</b></p></td>
                                </tr>
                            </tr>
						

                        </table>
                    </td>
                </tr>
                
				<tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12" style="height:25px">
                                    {{ trans('stages_academic_documents.university_dean') }}(&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
                                </th>
                            </tr>
                            <tr>
                                <td style="width:85px">زمان مجموعی </td>
                                <td style="width:85px">همان روز-1روز</td>
                                <td colspan="10" rowspan="2" style="text-align: right;vertical-align:top;border-bottom:none;height:25px"><b>تائیدی بخش فارغان:</b></td> 
                            </tr>
                            <tr>
                                <td style="height: 25px">تاریخ دریافت</td>
                                <td style="height:25px"></td>
                            </tr>
                            <tr>
                                <td style="height:25px">تاریخ صدور</td>
                                <td style="height:25px"></td>
                                <td colspan="10" style="border-top: none;vertical-align:top;padding-bottom:15px;height:25px"><b>امضای مدیر ع امور فارغان / امضای مدیرع ثبت و راجستر</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <b>امضای آمر امورمحصلان / فارغان و مهر پوهنتون</b>                                  
                                </td>                       
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="12" style="height:25px;">
                                  ریاست امور محصلان دولتی وزارت تحصیلات عالی
                                </th>
                            </tr>
                            <tr>
                                <td style="width:85px;height:25px">زمان مجموعی </td>
                                <td style="width:85px;height:25px">2-3</td>
                                <td colspan="4" rowspan="2" style="text-align: right;vertical-align:top;border-bottom:none;height:25px">چگونگی شمولیت کانکور متقاضی صحت است.</td>
                                <td colspan="6" rowspan="2" style="text-align: right;vertical-align:top;border-bottom:none;height:25px">اسناد تحصیلی ضمیمه باکتاب فارغان تطبیق و ثبت گردید صحت است.</td>
                              
                            </tr>
                            <tr>
                                <td style="height: 25px">تاریخ دریافت</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td style="height: 25px">تاریخ صدور</td>
                                <td></td>
                                <td colspan="4" style="border-top: none;vertical-align:top;padding-bottom:15px;height:20px"><b>امضای آمر دیتابیس</b> 
                                   
                                    
                                </td>
                                <td colspan="6" style="border-top: none;vertical-align:top;padding-bottom:15px;height:20px"><b>  امضای مدیر ع امور فارغان لیسانس/ فوق لیسانس/ مدیر ع متفرقه</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                   
                                    
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
