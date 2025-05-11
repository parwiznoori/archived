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
     
    </style>
</head>
<body>	
	@php
    $tazkira = explode('!@#', $student->tazkira);
	$grade=ceil($student->semester/2);
@endphp
	<div class="page">
		<table class="header_table" style="width:100%;margin-top:-40px">
			<tr>
			   
				<td style="text-align: right">

					<h4 style="text-align:right">{{ trans('raf_tajeel.sequnce_number1') }}:( &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;)</h4>
					<br>
					<h4 style="text-align:right">{{ trans('raf_tajeel.date') }} : &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;
						&nbsp;&nbsp;&nbsp; / &nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; / </h4>

				</td>
				<td style="text-align:right;width:9%;vertical-align:top;">
					<img src="{{ public_path('img/emarat-logo.jpg') }}" style="max-width: 100px;height:100px;vertical-align:top;"/></td>
					
				<td style="text-align:center;width:32%;vertical-align:middle;padding-right:10%;padding-top:-44px">
					{{-- <img src="{{ asset('img/wezarat-logo.jpg') }}" style="max-width: 80px" /> --}}
				  
						<p style="font-size: 14px;font-weight:bold;padding-bottom:-4px">{{ trans('re-enrollment-form.islamic_emarat') }}</p> <br>
						<p style="font-size: 14px;font-weight:bold">{{ trans('re-enrollment-form.ministry_title') }}</p> <br>
						<p style="font-size: 14px;font-weight:bold">{{ trans('re-enrollment-form.re_enrollment_form') }}</p>
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
					{{ trans('tajeel.personal_info') }}
				</th>

			</tr>
			<tr>
				<td style="padding: 0;position: relative;">
					<table class="table inner-table">

						<tr>
							<th class="center">{{ trans('re-enrollment-form.name') }}</th>
							<th class="center">{{ trans('re-enrollment-form.father_name') }}</th>
							<th class="center">{{ trans('re-enrollment-form.grandfather_name') }}</th>
							<th class="center">{{ trans('re-enrollment-form.gender') }}</th>
							<th class="center">{{ trans('re-enrollment-form.kankor_id') }}</th>
							<th class="center">{{ trans('re-enrollment-form.student_sign') }}</th>
						</tr>
						<tr>
							
							<td class="center">{{ $student->getFullNameAttribute() }}</td>
							<td class="center">{{ $student->father_name }}</td>
							<td class="center">{{ $student->grandfather_name }}</td>
							<td class="center">{{ $student->gender }}</td>
							<td class="center">{{ $student->form_no }}</td>
							<td class="center" rowspan="3" style="width: 90px"></td>
						</tr>
						<tr>
							<th class="center">{{ trans('re-enrollment-form.kankor_year') }}</th>
							<th class="center">{{ trans('re-enrollment-form.department') }}</th>
							<th class="center">{{ trans('re-enrollment-form.class_year') }}</th>
							<th class="center">{{ trans('re-enrollment-form.semester') }}</th>
							<th class="center">{{ trans('re-enrollment-form.faculty') }}</th>
							
						</tr>
						<tr>
							<td class="center" height="40px" style="width:100px">{{ $student->kankor_year}}</td>
							<td class="center" height="40px" style="width:100px">{{ $student->department->name }}</td>
							<td class="center" height="40px"> {{ $grade   }}</td>
							<td class="center" height="40px">{{ $student->semester }}</td>
							<td class="center" height="40px">{{ $student->department->facultyName->name}}</td>
						</tr>

					</table>
				</td>
			</tr>
			
	
			<tr>
				<td style="padding:0px">
					<table class="table inner-table">
						<tr>
							<th class="bg-grey center" colspan="12" style="">
								{{ trans('re-enrollment-form.general_teaching_mgmt') }}
							</th>
						</tr>

						<tr>
							<td class="textdir" rowspan="4" width="3%" height="140px" >همان روز</td>
							<td class="textdir" rowspan="4" width="3%" height="140px"> </td>
							<td class="textdir" rowspan="4" width="3%" height="140px"> </td>
						</tr>


						<tr>

							<th style="text-align: right;vertical-align:top;height:50px;border:none;" colspan="9">
							قرار ملاحظه سوابق محترم/محترمه:
								<br>
								<br>
								<p style="visibility:hidden">مدیریت محترم عمومی تدریسی برویت اسناد و سوابق اجراآت قانونی و اصولی نمایند.</p>
							</th>


						</tr>

						<tr>

							<td style="text-align: right;border:none" height="90px"></td>
							{{-- <td style="text-align: right;border:none" height="30px"></td>
							<td style="text-align: right;border:none" height="30px"></td> --}}

						</tr>



						<tr>

							<td style="text-align: center;border:none;vertical-align:bottom;margin-left:100px;height:30px"></td>
							<td style="text-align: right;border:none;height:30px"><p style="visibility:hidden">امضای مدیرعمومی تدریسی</p></td>
							<td style="text-align: right;border:none;height:30px"></td>


						</tr>
						<tr>
							<td class="textdir" rowspan="2" height="150px" width="3%">{{ trans('tajeel.total_time') }}
							   </td>
							<td class="textdir" rowspan="2" height="150px" width="3%">
							  {{ trans('tajeel.receive_date') }}</td>
							<td class="textdir" rowspan="2" height="150px" width="3%">
								{{ trans('tajeel.issue_date') }}</td>
						</tr>
						<tr>

							<td style="text-align: center;border:none" height="100px"></td>
							
							<th style="text-align: center;vertical-align:bottom;width:60%;margin-left:100px;border:none"
								height="100px" colspan="3"> 
								امضای مدیرعمومی تدریسی
							</th>
							<td style="text-align: right;border:none"height="100px"></td>

						</tr>
					   


					</table>

				</td>
			</tr>
			<tr>
				<td style="padding:0px">
					<table class="table inner-table">
						<tr>
							<th class="bg-grey center" colspan="12">
								{{ trans('tajeel.department_dean') }}(
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								)
							</th>
						</tr>

						<tr>
							<td class="textdir" rowspan="4" width="2%" height="40px" >1روز</td>
							<td class="textdir" rowspan="4" width="2%" height="40px"> </td>
							<td class="textdir" rowspan="4" width="2%" height="40px"> </td>
						</tr>


						<tr>

							<th style="text-align: right;vertical-align:top;border:none" colspan="8">
							ملاحظه شد:
								<br>
								مدیریت محترم عمومی تدریسی برویت اسناد و سوابق اجراآت قانونی و اصولی نمایند.
								<br>
							</th>


						</tr>

						<tr>

							<td style="text-align: right;border:none" height="20px"></td>
							{{-- <td style="text-align: right;border:none" height="30px"></td>
							<td style="text-align: right;border:none" height="30px"></td> --}}

						</tr>



						<tr>

							<td style="text-align: center;vertical-align:bottom;border:none;margin-left:100px;"></td>
							<td style="text-align: center;border:none"><b>امضاء و مهر رئیس پوهنحی</b></td>
							<td style="text-align: right;border:none"></td>


						</tr>
						<tr>
							<td class="textdir" rowspan="2" height="120px" width="2%">{{ trans('tajeel.total_time') }}
							   </td>
							<td class="textdir" rowspan="2" height="120px" width="2%">
							  {{ trans('tajeel.receive_date') }}</td>
							<td class="textdir" rowspan="2" height="120px" width="2%">
								{{ trans('tajeel.issue_date') }}</td>
						</tr>
						<tr>

							<td style="text-align: center;border:none;" height="80px"></td>
							
							<th style="text-align: center;border:none;vertical-align:bottom;width:60%;margin-left:100px;"
								height="80px"> 
								
							</th>
							<td style="text-align: right;border:none;"height="80px"></td>

						</tr>
					   


					</table>

				</td>
			</tr>
		

		</table>

	</div>
</div>
</body>

</html>
