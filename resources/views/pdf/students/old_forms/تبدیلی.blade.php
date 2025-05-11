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
        html, body {
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
		margin:0;
	}
	.table td,.table th{
		font-size: 14px;	    
	    padding:4px 4px; 	        
		border: 0.5px solid #000;
	}
	.inner-table tr:first-child td, .inner-table tr:first-child th {
		border-top: 0;
		font-size: 11px;
	}
	.inner-table tr td:first-child, .inner-table tr th:first-child {
		border-right: 0;
		font-size: 11px;
	}
	.inner-table tr:last-child td, .inner-table tr:last-child th {
		border-bottom: 0;
		font-size: 11px;
	}
	.inner-table tr td:last-child, .inner-table tr th:last-child {
		border-left: 0;
		font-size: 11px;
	}
	table{
	    width:100%;
		border-collapse: collapse;
	}
	.bg-grey {
		background-color: #d8d8d8;
	}
	.center {
		text-align: center;
	}
</style>
</head>
<body>
@php
		$tazkira = explode('!@#', $student->tazkira);		
	@endphp
		<div class="page">
		<table class="header_table"  style="width:100%;">
			<tr>
				<td  style="text-align:right;width:33%;vertical-align:bottom;">
				<img src="{{ asset('img/afg_logo.jpg') }}"  style="max-width: 80px"/>
				<br>
				<br>
				<h4>{{trans('general.sequnce_number')}}</h4>
				<br>
                <h4>{{trans('general.date')}} :  &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;  / &nbsp; &nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;/</h4>

				</td>		
				<td style="text-align:center;width:33%;vertical-align:top;">
					<img src="{{ asset('img/wezarat-logo.jpg') }}"  style="max-width: 80px"/>
					<p style="margin-top:5px;font-size: 14px">{{trans('general.ministry_title')}}</p>				
					<p style="margin-top:5px;font-size: 14px">{{trans('general.general_governament_students_directorate')}}</p>				
					<p style="margin-top:5px;font-size: 14px">{{trans('general.transfer_process')}}</p>				
					<p style="margin-top:5px;font-size: 14px">{{trans('general.general_management_of_conversions')}}</p>				
					<p style="margin-top:5px; font-weight: bold;">{{trans('general.student_request_for_transfer') }}</p>				
				</td>	
				<td style="text-align:right;width:33%;padding-right:17%;vertical-align:top;padding-top:1%;" >					
					<img src="{{ file_exists($student->photo_url) ? asset($student->photo_url) : asset('img/avatar-placeholder.png') }}" style="max-width: 100px">
				</td>				
			</tr>
		</table>

		<table class="table" style="margin-top: 20px">
		<!-- student request for transliterator_create_from_rules -->
			<tr>
				<td class = "bg-grey"> ID :{{$student->form_no}}  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp; &nbsp;  &nbsp;
				&nbsp;  &nbsp;  &nbsp; &nbsp; &nbsp;  &nbsp;  &nbsp; &nbsp;  &nbsp;  &nbsp; 
				&nbsp; &nbsp;&nbsp;  &nbsp; &nbsp;  &nbsp;  &nbsp;  &nbsp; &nbsp;   &nbsp; &nbsp;   
				&nbsp;  &nbsp;   &nbsp; &nbsp;</td>
			</tr>
			<tr>
				<td style="padding: 0">
					<table class="table inner-table">
						<tr>
							<th class="bg-grey"  style = "width:10%" > {{trans('general.name')}}</th>
							<th class="bg-grey"  style = "width:10%" >{{trans('general.father_name')}}</th>
							<th class="bg-grey" colspan="4"  style = "width:30%" >{{ trans('general.first_univ') }}</th>
							<th class="bg-grey" colspan="4" style = "width:30%" >{{ trans('general.second_univ') }}</th>
							<th class="bg-grey" style = "width:10%" >{{ trans('general.student_sign') }}</th>
							<th class="bg-grey" colspan = "2" style = "width:10%" >{{ trans('general.total_time') }}</td>
						</tr>
						<tr>
							<td class style = "width:10%" rowspan = "2" >{{ $student->name}}</td>
							<td class style = "width:10%" rowspan = "2">{{ $student->father_name}}</td>
							<!-- first_uni -->
							<td class="bg-grey center" style = "width:2%" >{{ trans('general.class_year')}}</td>
							<td class="bg-grey center" style = "width:2%" >{{ trans('general.semester')}}</td>
							<td class="bg-grey center" style = "width:12%" >{{ trans('general.faculty')}}</td>
							<td class="bg-grey center" style = "width:12%" >{{ trans('general.university')}}</td>
							<!-- second_uni -->
							<td class="bg-grey center" style = "width:2%" >{{trans('general.class_year')}}</td>
							<td class="bg-grey center" style = "width:2%" >{{trans('general.semester')}}</td>
							<td class="bg-grey center" style = "width:12%" >{{trans('general.faculty')}}</td>
							<td class="bg-grey center" style = "width:12%" >{{trans('general.university')}}</td>
							<!-- student_sign -->
							<td class style = "width:10%" rowspan = "2"></td>
							<!-- total_time -->
							<td class="bg-grey center" style = "width:10%" >{{trans('general.at_least')}}</td>
							<td class="bg-grey center" style = "width:10%" >{{trans('general.maximum')}}</td>
						</tr>
						<tr>
							<td class style = "width:5%" ></td>
							<td class style = "width:5%" >{{ $request->semester}}</td>
							<td class style = "width:9%" >{{ $student->department->faculty}}</td>
							<td class style = "width:9%" >{{ $student->university->name}}</td>

							<td class style = "width:7%" ></td>						
                            <td class style = "width:7%" >{{ $request->semester}}</td>
							<td class style = "width:7%" >{{ $model ?  $model->toDepartment->faculty : '' }}</td>
							<td class style = "width:7%" >{{ $model ?  $model->toDepartment->university->name : '' }}</td>

							<td class style = "width:7%" ></td>
							<td class style = "width:7%" ></td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- university and faculty aggrement where student curently studying -->
			<tr>
				<th class = "bg-grey center"> {{trans('general.Agreement_between_the_university_and_the_faculty_where_the_student_is_studying') }}  </th>
			</tr>
			<tr>
				<td style="padding:0px">
					<table class="table inner-table">
						<tr>
							<th class="bg-grey"  style = "width:5%; font-size: 8px;"rowspan = "3"> {{trans('general.specified_time')}}</th>
							<th class="bg-grey"  style = "width:5%; font-size: 8px" rowspan = "3"> {{trans('general.enter_documents')}}</th>
							<th class="bg-grey"  style = "width:5%; font-size: 8px" rowspan = "3"> {{trans('general.issuance_of_documents')}}</th>
							<th class="bg-grey"  style = "width:10%"> {{trans('general.name')}}</th>
							<th class="bg-grey"  style = "width:10%"> {{trans('general.father_name')}}</th>
							<th class="bg-grey"  style = "width:10%"> {{trans('general.grandfather_name')}}</th>
							<th class="bg-grey"  style = "width:10%"> {{trans('general.class_year')}}</th>
							<th class="bg-grey"  style = "width:10%"> {{trans('general.semester')}}</th>
							<th class="bg-grey"  style = "width:10%"> {{trans('general.faculty')}}</th>
							<th class="bg-grey"  style = "width:15%"> {{trans('general.department')}}</th>
							<th class="bg-grey"  style = "width:10%"> {{trans('general.year')}}</th>
						</tr>
						<tr>	
							<td>{{$student->name}}</td>
							<td>{{$student->father_name}}</td>
							<td>{{$student->grandfather_name}}</td>
							<td></td>
							<td>{{$request->semester}}</td>
							<td>{{$student->department->faculty}}</td>
							<td>{{$student->department->name}}</td>
							<td></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="padding:0px">
					<table class="table inner-table">
						<tr>	
							<th class="bg-grey center" colspan = "2" style = "width :15%" >{{trans('general.is_failed')}}</th>
							<th class="bg-grey center" colspan = "2" style = "width :15%" >{{trans('general.is_absence')}}</th>
							<th class="bg-grey center" colspan = "2" style = "width :15%" >{{trans('general.is_deprivation')}}</th>
							<th class="bg-grey center" colspan = "2" style = "width :15%" >{{trans('general.is_previous_conversion')}}</th>
							<th class="bg-grey center" colspan = "2" style = "width :15%" >{{trans('general.curriculum_system')}}</th>
							<th class="bg-grey center" colspan = "2" style = "width :15%" >{{trans('general.is_something_remaining')}}</th>
							<th class="bg-grey center" style = "width :10%" >{{trans('general.faculty_agreement')}}</th>
						</tr>
						<tr>
							<td>{{trans ('general.yes')}}</td>
							<td>{{trans ('general.no')}}</td>
							<td>{{trans ('general.yes')}}</td>
							<td>{{trans ('general.no')}}</td>
							<td>{{trans ('general.yes')}}</td>
							<td>{{trans ('general.no')}}</td>
							<td>{{trans ('general.yes')}}</td>
							<td>{{trans ('general.no')}}</td>
							<td>{{trans ('general.yes')}}</td>
							<td>{{trans ('general.no')}}</td>
							<td>{{trans ('general.yes')}}</td>
							<td>{{trans ('general.no')}}</td>
							<td rowspan = "2" style = "width: 10%"></td>
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
						</tr> 
					</table>
				</td>
			</tr>
		</table>
		<table class="table" style= "border-top : none">
			<tr>
				<td class="bg-grey" height = "80" style = "width:5%;  font-size: 10px;">{{trans('general.duration')}}</td>
				<td style = "width:5%"></td>
				<td style = "width:5%"></td>
				<td style = "width:30%; vertical-align:top;">{{trans('general.sign_from_department_student_affairs')}}</td>
				<td style = "width:30%; vertical-align:top;">{{trans('general.sign_and_stamp_from_faculty_chairman')}}</td>
				<td style = "width:25%; vertical-align:top;">{{trans('general.sign_and_approval_from_university')}}</td>
			</tr>
		</table>
		<table class="table" style= "margin : 10px">
			
			<tr>
				<th class = "bg-grey center"> {{trans('general.the_agreement_of_the_university_and_the_faculty_to_which_the_student_has_applied_for_transfer') }} </th>
			</tr>
			<tr>
				<td style="padding:0px">
					<table class="table inner-table">
						<tr>
							<th class="bg-grey"  style = "width:5%; font-size: 8px;" rowspan = "2"> {{trans('general.time_t')}}</th>
							<th class="bg-grey"  style = "width:5%; font-size: 8px" rowspan = "2"> {{trans('general.log_in')}}</th>
							<th class="bg-grey"  style = "width:5%; font-size: 8px" rowspan = "2"> {{trans('general.issued')}}</th>
							<th class="bg-grey"  style = "width:10%" colspan = "2"> {{trans('general.curriculum_study')}}</th>
							<th class="bg-grey"  style = "width:10%" colspan = "2"> {{trans('general.express_agreement')}}</th>
							<th class="bg-grey"  style = "width:10%" colspan = "2"> {{trans('general.education_field')}}</th>
							<th class="bg-grey"  style = "width:28%" > {{trans('general.sign_and_stamp_from_faculty_chairman')}}</th>
							<th class="bg-grey"  style = "width:27%" > {{trans('general.sign_and_approval_from_university')}}</th>
						</tr>
						<tr>
							<td>{{trans('general.credit')}}</td>
							<td>{{trans('general.semester')}}</td>
							<td>{{trans('general.yes')}}</td>
							<td>{{trans('general.no')}}</td>
							<td>{{trans('general.yes')}}</td>
							<td>{{trans('general.no')}}</td>
							<td rowspan = "2"></td>
							<td rowspan = "2"></td>
						</tr>
						<tr>
							<td class="bg-grey" height = "40">{{trans('general.duration')}}</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
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
		<table class="table" style= "margin : 10px">
			<tr>
				<th class="bg-grey"  style = "width:5%; font-size: 8px;"> {{trans('general.time_t')}}</th>
				<th class="bg-grey"  style = "width:5%; font-size: 8px" > {{trans('general.log_in')}}</th>
				<th class="bg-grey"  style = "width:5%; font-size: 8px" > {{trans('general.issued')}}</th>
				<th class="bg-grey center" style = "width:85%" colspan = "9"> {{trans('general.fillout_the_student_transformation_form_based_on_kankor_result_by_mohe') }} </th>
			</tr>
			<tr>
				<td class="bg-grey center" height = "20" style = "width:5%">{{trans('general.durations')}}</td>
				<td class="bg-grey center" style = "width:5%"></td>
				<td class="bg-grey center" style = "width:5%"></td>
				<td class="bg-grey center" style = "width:10%">{{trans('general.student_name')}}</td>
				<td class="bg-grey center" style = "width:10%">{{trans('general.father_name')}}</td>
				<td class="bg-grey center" style = "width:10%">{{trans('general.grandfather_name')}}</td>
				<td class="bg-grey center" style = "width:10%">{{trans('general.number')}}</td>
				<td class="bg-grey center" style = "width:10%">{{trans('general.faculty')}}</td>
				<td class="bg-grey center" style = "width:10%">{{trans('general.university')}}</td>
				<td class="bg-grey center" style = "width:10%">{{trans('general.score_obtain')}}</td>
				<td class="bg-grey center" style = "width:5%">{{trans('general.standard_score')}}</td>
				<td class="bg-grey center" style = "width:10%">{{trans('general.year_of_inclusion')}}</td>
			</tr>
			<tr>
			<td height ="40"></td>
			<td ></td>
			<td ></td>
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
	<table class="table">
					<tr>
						<td height ="70" style = "width:5%"></td>
						<td style = "width:5%"></td>
						<td style = "width:5%"></td>
						<td class="center" style = "width:30%; vertical-align:top;">{{trans('general.name_and_signature_of_the_person_resposible_for_inserting_the_results')}}</td>
						<td class="center" style = "width:30%; vertical-align:top;">{{trans('general.name_and_signature_of_the_general_director_of_registration')}}</td>
						<td class="center" style = "width:25%; vertical-align:top;">{{trans('general.name_and_signature_of_the_student_affairs_director')}}</td>
						</tr>
					</table>
			<table class="table" style= "margin : 10px">
			<tr>
				<th class="bg-grey"  style = "width:5%; font-size: 8px;"> {{trans('general.time_t')}}</th>
				<th class="bg-grey"  style = "width:5%; font-size: 8px" > {{trans('general.log_in')}}</th>
				<th class="bg-grey"  style = "width:5%; font-size: 8px" > {{trans('general.issued')}}</th>
				<th class="bg-grey center" style = "width:85%" colspan = "9"> {{trans('general.final_decision_by_the_commission_of_mohe') }} </th>
			</tr>
			<tr>
			<td height ="90"></td>
			<td></td>
			<td></td>
			<td style ="width:90%"></td>
			</tr>
			</table>
    </div>	
</body>
</html>
	