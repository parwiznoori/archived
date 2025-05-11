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
    
			<table class="header_table" style="width:100%;margin-top:-40px">
                <tr>
                   
                    <td style="text-align: right">
    
                        <h4 style="text-align:right">{{ trans('hostel_enrollment_form.sequnce_number1') }}:( &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;)</h4>
                        <br>
                        <h4 style="text-align:right">{{ trans('hostel_enrollment_form.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
                            &nbsp;&nbsp;&nbsp; / &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; / </h4>
    
                    </td>
                    <td style="text-align:right;width:9%;vertical-align:top;">
                        <img src="{{ public_path('img/emarat-logo.jpg') }}" style="max-width: 100px;height:100px;vertical-align:top;"/></td>
                        
                    <td style="text-align:center;width:32%;vertical-align:middle;padding-right:10%;padding-top:-44px">
                        {{-- <img src="{{ asset('img/wezarat-logo.jpg') }}" style="max-width: 80px" /> --}}
                      
                            <p style="font-size: 14px;font-weight:bold;padding-bottom:-4px">{{ trans('hostel_enrollment_form.islamic_emarat') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('hostel_enrollment_form.ministry_title') }}</p> <br>
                            <p style="font-size: 14px;font-weight:bold">{{ trans('hostel_enrollment_form.hostel_enrollment_form') }}</p>
                     
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
                    <td style="padding: 0">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="10">
                                   شهرت محصل 
                                </th>

                            </tr>

                            <tr>
                                <th  style="width:100px">{{ trans('hostel_enrollment_form.name') }}</th>
                                <td style="width:120px">{{ $student->getFullNameAttribute() }}</td>
                               
                                <th colspan="8" style="width:100px;text-align:center">{{ trans('hostel_enrollment_form.residance') }} </th>
								

                            </tr>
                            <tr>
                                <th>{{ trans('hostel_enrollment_form.lastname') }}</th>
                                <td style="width:80px">{{ $student->last_name }}</td>
                                <th colspan="4" style="width:100px">{{ trans('hostel_enrollment_form.permanent_addr') }}</th>
                                <th colspan="4" style="width:100px">{{ trans('hostel_enrollment_form.current_addr') }}</th>
                            </tr>
                            <tr>
                                <th>{{ trans('hostel_enrollment_form.father_name') }}</th>
                                <td style="width:80px">{{ $student->father_name }}</td>
                                <th style="width:80px">{{ trans('hostel_enrollment_form.village') }}</th>
                                <th style="width:80px">{{ trans('hostel_enrollment_form.district') }}</th>
                                <th colspan="2" style="width:80px">{{ trans('hostel_enrollment_form.province') }}</th>
                                <th>{{ trans('hostel_enrollment_form.village') }}</th>
                                <th style="width:100px">{{ trans('hostel_enrollment_form.district') }}</th>
                                <th colspan="2" style="width:100px">{{ trans('hostel_enrollment_form.province') }}</th>
                                
                            </tr>
                            <tr>

                                <th>{{ trans('hostel_enrollment_form.grandfather_name') }}</th>
                                <td style="width:80px">{{ $student->grandfather_name }}</td>
                                <td>{{ $student->village }} </td>
                                <td>{{ $student->district }}</td>
                                <td colspan="2">{{ $student->originalProvince ? $student->originalProvince->name : '' }}</td>
                                <td>{{ $student->village_current }}</td>
                                <td>{{ $student->district_current }}</td>
                                <td colspan="2">{{ $student->currentProvince ? $student->currentProvince->name : '' }}</td>
                             


                            </tr>
                            <tr>
                                <th>{{ trans('hostel_enrollment_form.gender') }}</th>
                                <td style="width:80px">{{ $student->gender }}</td>
								<th colspan="8" style="text-align:center;">{{ trans('hostel_enrollment_form.nid') }}</th>
                            </tr>
                            <tr>
                                <th>{{ trans('hostel_enrollment_form.school_name') }}</th>
                                <td style="width:80px">{{ $student->school_name }}</td>
								<td colspan="8" rowspan="2" style="padding: 0">
									<table>
										<tr>
											<th style="width: 120px;text-align:center;border-top:none" colspan="2">{{ trans('hostel_enrollment_form.dob') }}</th>
											<th style="width: 120px;text-align:center;border-top:none">{{ trans('hostel_enrollment_form.page') }} </th>
											<th style="width: 120px;text-align:center;border-top:none"> {{ trans('hostel_enrollment_form.cover') }}</th>
											<th style="width: 120px;text-align:center;border-top:none">{{ trans('hostel_enrollment_form.register_no') }} </th>
											<th style="width: 120px;text-align:center;border-top:none"> {{ trans('hostel_enrollment_form.general_no') }}</th>
										</tr>
										<tr>
											<td colspan="2">{{ $student->birthdate }}</td>
											<td>{{ $tazkira[2] ?? '' }}</td>
											<td>{{ $tazkira[0] ?? '' }}</td>
											<td>{{ $tazkira[1] ?? '' }}</td>
											<td>{{ $tazkira[3] ?? '' }}</td>
										</tr>
									</table>
								</td>
						
                                
                            </tr>
                            <tr>
                                <th >{{ trans('hostel_enrollment_form.school_graduation_year') }}</th>
                                <td style="width:80px">{{ $student->school_graduation_year }}</td>
							
							
                          
                              

                            </tr>
							
                            <tr>
                                <th style="width:70px">{{ trans('hostel_enrollment_form.kankor_id') }}</th>
                                <td style="width:80px">{{ $student->form_no }}</td>
								<th class="bg-grey" colspan="8" style="text-align:center">{{ trans('hostel_enrollment_form.fillout_by_student') }}  </th>
                              

                            </tr>
							<tr>
								<th>{{ trans('hostel_enrollment_form.kankor_score') }}</th>
								<td>{{ $student->kankor_score }}</td>
								<th colspan="4" style="width:100px">{{ trans('hostel_enrollment_form.place_of_education') }}  </th>
                                <th colspan="4" style="width:100px"> {{ trans('hostel_enrollment_form.place_of_study') }} </th>
							</tr>
							<tr>
								<th rowspan="2">{{ trans('hostel_enrollment_form.kankor_year') }}</th>
								<td rowspan="2">{{ $student->kankor_year }}</td>
								<th style="width:60px">{{ trans('hostel_enrollment_form.class') }}</th>
								<th style="width:60px">{{ trans('hostel_enrollment_form.department') }}</th>
								<th style="width:70px">{{ trans('hostel_enrollment_form.faculty') }}</th>
								<th style="width:70px">{{ trans('hostel_enrollment_form.university') }}</th>
								<th style="width:80px">{{ trans('hostel_enrollment_form.tenth_class') }} </th>
								<th style="width:80px">{{ trans('hostel_enrollment_form.eleventh_class') }}  </th>
								<th style="width:60px">{{ trans('hostel_enrollment_form.twilfth_class') }} </th>
							</tr>
							<tr>
								
								<td>{{ $class }}</td>
								<td>{{ $student->department->name }}</td>
								<td>{{ $student->department->facultyName->name }}</td>
								<td>{{ $student->university->name }}</td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<th colspan="2" style="height:30px;border-top:none">محل وظیفه پدر محصل</th>
								<th>شماره تماس</th>
								<th colspan="2">دارایی پدر محصل</th>
								<th colspan="2">دارایی محصل</th>
								<th colspan="2">دارایی شوهر/خانم محصل</th>
							
							</tr>
							<tr>
								<td colspan="2" style="height:30px"></td>
								<td></td>
								<td colspan="2"></td>
								<td colspan="2"></td>
								<td colspan="2"></td>
							
							</tr>
							<tr>
								
									<th class="bg-grey center" colspan="10">
									  تعهد نامه و درخواست شمولیت محصل در لیلیه پوهنتون
									</th>
	
								
							</tr>
						
					
							<tr>
								<td colspan="10" style="text-align: right;border:none;font-weight:18px"><b>آنچه در فورم شمولیت لیلیه درج شده صحت دارد، هرگاه میان معلومات من در فورم 
									ثبت دارایی و دیگر اسناد رسمی اختلاف ظاهر گردد خود را سزاوار اخراج از لیلیه دانسته و پرداخت همه
									 مصارف لیلیه را متقبل میشوم، بنآ خواهشمندم که جهت اعاشه و اباته به آمریت /مدیریت عمومی لیلیه معرفی گردم.</b></td>
							</tr>
							<tr>
								<td style="border:none"></td>
								<th style="border:none"></th>
								<td style="border:none"></td>
								<th style="border:none" colspan="2">بااحترام</th>
							</tr>
						
							<tr>
								<td style="width: 50px;border:none;text-align:right;height: 70px"> </td>
								<td colspan="4" style="height: 70px;border:none;text-align:left;vertical-align:bottom">امضای محصل(&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
									&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
									&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
									) </td>
								<th colspan="4" style="text-align: center;border:none;height: 70px;vertical-align:bottom">  <h4>{{ trans('general.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; / &nbsp;
									&nbsp;&nbsp;&nbsp; / </h4></th>
							</tr>
							
						
                        </table>
                    </td>

                </tr>
				<tr>
                    <td style="padding:0px">
                        <table class="table inner-table">
                            <tr>
                                <th class="bg-grey center" colspan="13">
                                   تصدیق ریاست پوهنحی (&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) از صورت ثبت نام محصل 
                                </th>
                            </tr>
                            <tr>
                                <td width="12%" height="50px">
                                   زمان مجموعی</td>
                                <td style="width:85px;height:40px"> همان روز-1 روز</td>
								<td colspan="8" rowspan="2" style="vertical-align:top;text-align:right;border:none;font-weight:20px"> <p><b>به معاونیت محترم امور محصلان!</b></p>
								
								پوهنحی ({{ $student->department->facultyName->name }}
								) از شمولیت محصل فوق الذکر در دیپارتمنت ({{ $student->department->name }}) سمستر({{ $student->semester }}
								) و سال ({{ $student->kankor_year }}
								) تصدیق میمائید.
								</td>
                              
                               

                            </tr>
                            <tr>
                                <td width="12%" style="height: 50px">
                                    {{ trans('tajeel.receive_date') }}</td>
                                <td> </td>
                                <td style="border:none;vertical-align:top"></td>
                    

                            </tr>
                            <tr>
                                <td width="12%" style="height: 50px">
                                    {{ trans('tajeel.issue_date') }}</td>
                                <td> </td>
                                <td style="border:none"> </td>
                                <th style="border:none;vertical-align:bottom;text-align:left" colspan="2">  امضای مدیرعمومی تدریسی</th>
                                <td style="border:none"> </td>
								<th colspan="3" style="border:none;vertical-align:bottom;text-align:left"> امضاء و مهر رئیس پوهنحی</th>
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
                                  فیصله نهایی کمسیون لیلیه
                                </th>
                            </tr>
                            <tr>
                                <td width="12%" height="50px">
                                   زمان مجموعی</td>
                                <td style="width:85px;">1-2 روز </td>
								<td style="border:none;vertical-align:top;text-align:right"> 
								</td>
                              
                               

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
                                    {{ trans('tajeel.issue_date') }}</td>
                                <td> </td>
                                <td style="border:none"> </td>
                                <td style="border:none;vertical-align:bottom">  </td>
                                <td style="border:none"> </td>
								<th colspan="2" style="border:none;vertical-align:bottom;text-align:right"> </th>
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
					<td style="padding: 0">
						<table>
							
<tr>
					<th class="bg-grey" colspan="5">{{ trans('hostel_enrollment_form.name_of_commission_member') }}</th>
				</tr>
				<tr>
					<th style="width: 35px">{{ trans('hostel_enrollment_form.number') }}</th>
					<th>{{ trans('hostel_enrollment_form.name') }}</th>
					<th>{{ trans('hostel_enrollment_form.job') }}</th>
					<th>موقف اعضای کمسیون</th>
					<th>محل امضاء</th>
				</tr>
				<tr>
					<td style="height: 35px">1</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td style="height: 35px">2</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td style="height: 35px">3</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				
							</tr>
						</table>
					</td>
				</tr>
				
			
			</table>

        </div>
    </div>
</body>

</html>
