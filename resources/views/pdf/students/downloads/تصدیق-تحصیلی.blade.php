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
        padding: 5mm;
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
            page-break-after: always;
        }
    }
    
	p {
		margin:0;
	}
	.table td,.table th{	    
	    padding:4px 4px; 	        
		border: 0.5px solid #000;
	}
	.inner-table tr:first-child td, .inner-table tr:first-child th {
		border-top: 0
	}
	.inner-table tr td:first-child, .inner-table tr th:first-child {
		border-right: 0
	}
	.inner-table tr:last-child td, .inner-table tr:last-child th {
		border-bottom: 0
	}
	.inner-table tr td:last-child, .inner-table tr th:last-child {
		border-left: 0
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
	<div class="page">
		<table class="header_table"  style="width:100%;">
			<tr>
				<td  style="text-align:right;width:33%;vertical-align:bottom;">
                    <h4>شماره مکتوب : {{ \App\Models\Number::getNextDocumentNumber($student, 'student') }}</h4>
				</td>
				<hr>
				<td style="text-align:center;width:33%;vertical-align:top;">
					<img src="{{ public_path('img/wezarat-logo.jpg') }}"  style="max-width: 80px"/>
					<p style="margin-top:5px;font-size: 14px">{{trans('general.ministry_title')}}</p>				
					<p style="margin-top:5px;">پوهنتون {{ $student->university->name }}</p>
					<p style="margin-top:5px;">دیپارتمنت : {{ $student->department->name }}</p>				
				</td>	
				<td style="text-align:right;width:33%;padding-right:17%;vertical-align:top;padding-top:1%;" >					
					<img src="{{ public_path($student->photo_relative_path()) }}" style="max-width: 100px">
				</td>				
			</tr>
		</table>
		<hr>
		<br>
		<br>
		<br>
		<p style="text-align: justify">	 	
		{{trans('general.this_will_be_varified')}} {{ $student->name }} فرزند {{ $student->father_name }} {{trans('general.having_of_id_number')}}
	 	{{ $student->form_no }}  {{trans('general.student_of_semister')}} {{$request->semister}}  پوهنتون {{ $student->university->name }}  {{trans('general.feild')}} {{ $student->department->name }} {{trans('general.current_university_and_department')}}.
	</p>
		<br>
		<p style="text-align:center">{{trans('general.with_respect')}}</p>
	<h3 style="text-align:center"> آمر امور محصلان</h3>
</div>
</body>
</html>
	